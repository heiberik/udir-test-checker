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

        $info = 'HHE';
        $test = new \core_kernel_classes_Resource($this->getRequestParameter('id'));

        // get the items contained in the test
        $items = $testService->getTestItems($test);
        $xmlTest = $qtiTestService->getDoc($test);


        // Iterate over the items
        foreach ($items as $item) {
            // Parse the item and build the QTI_Item object
            $file = $qtiItemService->getXmlByRdfItem($item);
            $qtiParser = new \oat\taoQtiItem\model\qti\Parser($file);
            $qtiItem = $qtiParser->load();

            // Get the XML content of the item
            $itemXML = $qtiItem->toXML();

            //$info .= $itemXML . '<br>';


            // Use DOMDocument and DOMXPath to search for specific elements in the XML
            $dom = new \DOMDocument();
            $dom->loadXML($itemXML);
            $xpath = new \DOMXPath($dom);

            // Example: Find all elements with a specific tag name (replace 'tagName' with the actual tag name)
            $elements = $xpath->query('//outcomedeclaration');

            // Iterate over the found elements
            foreach ($elements as $element) {
                // Do something with the element, e.g., extract its value
                $elementValue = $element->nodeValue;
                $info .= 'Found element with value: ' . $elementValue . '<br>';
            }

            // Example: Find elements with a specific attribute (replace 'attributeName' and 'attributeValue' with actual values)
            $elementsWithAttribute = $xpath->query('//outcomedeclaration[@attributeName="MAXSCORE"]');

            // Iterate over the elements with the specified attribute
            foreach ($elementsWithAttribute as $element) {
                // Do something with the matching element
                $info .= 'Found element with attribute: ' . $element->getAttribute('attributeName') . '<br>';
            }
        }


        echo $info;
    }
}