<?php

namespace oat\udirTestChecker\controller;

/*
    Potential checks:
    - maxscore
    - wcag
    - correct response set?
    - pcis in use
    - normal interactions in use
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

    public function checkMaxscore()
    {

        $testService = \taoTests_models_classes_TestsService::singleton();
        $qtiTestService = \taoQtiTest_models_classes_QtiTestService::singleton();

        $info = '<h1 style="width: 100%;">Oversikt MAXSCORE:</h1><br>';
        $test = new \core_kernel_classes_Resource($this->getRequestParameter('id'));

        // get the items contained in the test
        $items = $testService->getTestItems($test);

        $totalNumberOfTasks = 0;
        $tasksWithoutMaxscore = 0;
        $tasksWithOver1Maxscore = 0;

        $tasksWithoutMaxscoreArray = array();
        $tasksWithOver1MaxscoreArray = array();

        // Iterate over the items
        foreach ($items as $index => $item) {

            $qtiXmlFileContent = \oat\taoQtiItem\helpers\QtiFile::getQtiFileContent($item);
            $dom = new \DOMDocument();
            $dom->loadXML($qtiXmlFileContent);
            $xpath = new \DOMXPath($dom);

            $label = $item->getLabel();

            $maxScore = (int)$this->getMaxscore($xpath);

            //$info .= "LABEL: " . $label . '<br>';
            //$info .= "MAXSCORE: " . $maxScore . '<br>';

            $totalNumberOfTasks++;
            if ($maxScore == 0) {
                $tasksWithoutMaxscore++;
                $tasksWithoutMaxscoreArray[] = $label;
            } else if ($maxScore > 1) {
                $tasksWithOver1Maxscore++;
                $tasksWithOver1MaxscoreArray[] = $label;
            }
        }

        $info .= "<h2 style='width: 100%;'>Items total:</h2> " . $totalNumberOfTasks . '<br>';

        $info .= "<br><h2 style='width: 100%;'>Items without maxscore:</h2> " . $tasksWithoutMaxscore . '<br>';
        $info .= "Items:" . '<br>';
        foreach ($tasksWithoutMaxscoreArray as $string) {
            $info .= $string . "<br>";
        }

        $info .= "<br><h2 style='width: 100%;'>Items with maxscore over 1:</h2> " . $tasksWithOver1Maxscore . '<br>';
        $info .= "Items:" . '<br>';
        foreach ($tasksWithOver1MaxscoreArray as $string) {
            $info .= $string . "<br>";
        }

        echo $info;
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
}