<?php

namespace oat\udirTestChecker\controller;

use oat\tao\model\service\core_kernel_classes_Resource;


class Checker extends \tao_actions_CommonModule {
    /**
     * initialize the services
     */
    public function __construct() {
        parent::__construct();
    }


    public function wcagCheck() {
        $name = '';

        if ($this->getRequestParameter('name')) {
            $name = $this->getRequestParameter('name');

            $test = new core_kernel_classes_Resource($name);
        }


        $test = new core_kernel_classes_Resource($this->getRequestParameter('id'));


        echo "heeheh";
    }
}