<?php

namespace oat\udirTestChecker\controller;

class Checker extends \tao_actions_CommonModule {
    /**
     * initialize the services
     */
    public function __construct() {
        parent::__construct();
    }


    public function wcagCheck() {
        $name = '';
        if($this->hasRequestParameter('uri')) {
            $uri = $this->getRequestParameter('uri');
            echo $uri;
        }
    }
}