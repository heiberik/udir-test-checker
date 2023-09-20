<?php

namespace oat\udirTestChecker\controller;



class Checker extends \tao_actions_CommonModule
{
    /**
     * initialize the services
     */
    public function __construct()
    {
        parent::__construct();
    }


    public function wcagCheck()
    {

        $testService = \taoTests_models_classes_TestsService::singleton();
        $qtiTestService = \taoQtiTest_models_classes_QtiTestService::singleton();
        $itemsService = \taoItems_models_classes_ItemsService::singleton();
        $qtiItemService = \oat\taoQtiItem\model\qti\Service::singleton();

        $info = '';
        $test = new \core_kernel_classes_Resource($this->getRequestParameter('id'));

        // get the items contained in the test
        $items = $testService->getTestItems($test);
        $xmlTest = $qtiTestService->getDoc($test);


        // iterare over the items
        foreach ($items as $item) {


            //Parse it and build the QTI_Data_Item
            $file = $qtiItemService->getXmlByRdfItem($item);
            $qtiParser = new Parser($file);
            $returnValue = $qtiParser->load();


            //$itemXml = $qtiItemService->getXmlByRdfItem($item);

            //$sanitized = \oat\taoQtiItem\helpers\Authoring::sanitizeQtiXml($itemXml);
            //$qtiItem = $qtiItemService->getXmlToItemParser()->parse($sanitized);

            // convert the XML to a string
            /*
            $dom = new \DOMDocument();
            $dom->preserveWhiteSpace = true;
            $dom->formatOutput = true;
            $dom->loadXML($itemXml);
            $itemXml = $dom->saveXML();
            */

            $info .= $qtiItem->getBody() . '<br>';
        }


        echo $info;
    }
}