<?php

namespace oat\udirTestChecker\controller;

/*
    Potential checks:
    - maxscore
    - responce processing
    - wcag
    - correct response set?
    - pcis in use
    - normal interactions in use
    - plagiantkontroll satt på?

    Ønsker å automatisere denne sjekken så mye som mulig. Første versjon greit med manuell leting og trykk på knapp. 
    Men til neste versjon er det bra om extensionen kan søke igjennom alle tester og gi en rapport.
*/

class Checker extends \tao_actions_CommonModule
{
    /**
     * initialize the services
     */
    public function __construct()
    {
        parent::__construct();
    }


    public function checkItemSettings()
    {

        $testService = \taoTests_models_classes_TestsService::singleton();
        $qtiTestService = \taoQtiTest_models_classes_QtiTestService::singleton();

        $test = new \core_kernel_classes_Resource($this->getRequestParameter('id'));

        // get the items contained in the test
        $items = $testService->getTestItems($test);

        $totalNumberOfTasks = 0;
        $tasksWithoutMaxscore = 0;
        $tasksWithOver1Maxscore = 0;

        $tasksWithoutMaxscoreArray = array();
        $tasksWithOver1MaxscoreArray = array();

        $itemArray = array();

        $interactionTypes = array();

        // Iterate over the items
        foreach ($items as $index => $item) {

            $qtiXmlFileContent = \oat\taoQtiItem\helpers\QtiFile::getQtiFileContent($item);
            $dom = new \DOMDocument();
            $dom->loadXML($qtiXmlFileContent);
            $xpath = new \DOMXPath($dom);

            $label = $item->getLabel();
            $uri = $item->getUri();
            $resourceString = $item->__toString();

            $maxScore = (int) $this->getMaxscore($xpath);
            $responceProcessing = $this->getResponceProcessing($xpath, $qtiXmlFileContent);

            $rpArray[] = $responceProcessing;

            $totalNumberOfTasks++;
            if ($maxScore == 0) {
                $tasksWithoutMaxscore++;
                $tasksWithoutMaxscoreArray[] = $resourceString;
            } else if ($maxScore > 1) {
                $tasksWithOver1Maxscore++;
                $tasksWithOver1MaxscoreArray[] = $resourceString;
            }

            $itemArray[] = [
                'label' => $label,
                'uri' => $uri,
                'resourceString' => $resourceString,
                'maxScore' => $maxScore,
                'responceProcessing' => $responceProcessing,
                'warningLevel' => 0,
            ];
        }

        $this->setData('totalNumberOfTasks', $totalNumberOfTasks);
        $this->setData('tasksWithoutMaxscore', $tasksWithoutMaxscore);
        $this->setData('tasksWithOver1Maxscore', $tasksWithOver1Maxscore);
        $this->setData('tasksWithoutMaxscoreArray', $tasksWithoutMaxscoreArray);
        $this->setData('tasksWithOver1MaxscoreArray', $tasksWithOver1MaxscoreArray);
        $this->setData('itemArray', $itemArray);

        $this->setView('Checker/settings.tpl');
    }

    public function checkWcag()
    {

        $testService = \taoTests_models_classes_TestsService::singleton();
        $qtiTestService = \taoQtiTest_models_classes_QtiTestService::singleton();
        $itemsService = \taoItems_models_classes_ItemsService::singleton();
        $qtiItemService = \oat\taoQtiItem\model\qti\Service::singleton();

        $info = '<h1 style="width: 100%;">Udirs prøvesjekker!:</h1> <br><br>';
        $test = new \core_kernel_classes_Resource($this->getRequestParameter('id'));

        // get the items contained in the test
        $items = $testService->getTestItems($test);
        $xmlTest = $qtiTestService->getDoc($test);


        // Iterate over the items
        foreach ($items as $index => $item) {


            $qtiXmlFileContent = \oat\taoQtiItem\helpers\QtiFile::getQtiFileContent($item);
            $qtiXmlDoc = new \qtism\data\storage\xml\XmlDocument();
            $qtiXmlDoc->loadFromString($qtiXmlFileContent);

            $dom = new \DOMDocument();
            $dom->loadXML($qtiXmlFileContent);
            $xpath = new \DOMXPath($dom);

            $maxScore = $this->getMaxscore($xpath);
            $info .= "MAXSCORE: " . $maxScore . '<br>';

            $info .= $qtiXmlFileContent . '<br>';
        }

        echo $info;
    }

    public function getMaxscore(\DOMXPath $xpath): string
    {

        $elements = $xpath->query("*[name(.) = 'outcomeDeclaration']");

        foreach ($elements as $element) {

            $elementValue = $element->nodeValue;
            $elementIdentifier = $element->getAttribute('identifier');

            if ($elementIdentifier == 'MAXSCORE') {
                return $elementValue;
            }

        }

        return "0";
    }

    public function getResponceProcessing(\DOMXPath $xpath, $qtiXmlFileContent)
    {

        $elements = $xpath->query("*[name(.) = 'responseProcessing']");

        foreach ($elements as $element) {

            $elementTemplate = $element->getAttribute('template');

            if ($elementTemplate !== "") {
                return $elementTemplate;
            }

            $pattern = '/<responseProcessing>(.*?)<\/responseProcessing>/s';

            if (preg_match($pattern, $qtiXmlFileContent, $matches)) {
                $content = $matches[1];
                return $content;
            } else {
                return "Ingen responsprosessering";
            }
        }

        return "Ingen responsprosessering";
    }
}