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
            $qtiParser = new \oat\taoQtiItem\model\qti\Parser($file);
            $qtiItem = $qtiParser->load();

            //$info .= $qtiItem->getBody() . '<br>';
            //$info .= $qtiItem->getOutcomes() . '<br>';
            //s$info .= $qtiItem->getResponseProcessing() . '<br>';

            $itemXML = $qtiItem->toXML();
            $info .= $itemXML . '<br>';

            // find outcomedeclaration in xml document with identifier="MAXSCORE"
            $dom = new \DOMDocument();
            $dom->loadXML($itemXML);
            $xpath = new \DOMXPath($dom);
            $query = '<outcomedeclaration identifier="MAXSCORE"';
            $outcomeDeclaration = $xpath->query($query);

            // add all elements in the DOMNode list to the info string
            foreach ($outcomeDeclaration as $node) {
                $info .= $node->nodeValue . '<br>';
            }
        }


        echo $info;
    }
}