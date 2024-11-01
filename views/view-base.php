<?php
/**
 * Product: Prosodia VGW OS
 * URL: https://prosodia.de/
 * Author: Dr. Ronny Harbich
 * Copyright: Dr. Ronny Harbich
 * License: GPLv2 or later
 */
 abstract class WPVGW_ViewBase
 {
     private $isInit = false;
     protected $javaScripts = array();
     public function get_javascripts()
     {
         return $this->javaScripts;
     }
     public function is_init()
     {
         return $this->isInit;
     }
     public function __construct()
     {
     }
     abstract public function init();
     protected function init_base(array $javascript)
     {
         $this->isInit = true;
         $this->javaScripts = array_merge(array( array( 'file' => 'views/view-base.js', 'slug' => 'view-base', 'dependencies' => array( 'jquery' ) ) ), $javascript);
     }
 }
