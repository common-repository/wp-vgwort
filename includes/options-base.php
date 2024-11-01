<?php
/**
 * Product: Prosodia VGW OS
 * URL: https://prosodia.de/
 * Author: Dr. Ronny Harbich
 * Copyright: Dr. Ronny Harbich
 * License: GPLv2 or later
 */
 abstract class WPVGW_OptionsBase
 {
     protected $options;
     protected $defaultOptions;
     protected $optionsChanged = false;
     private $optionDBSlug;
     protected function get_option_db_slug()
     {
         return $this->optionDBSlug;
     }
     public function init($option_db_slug)
     {
         $options = $this->load_from_db($option_db_slug);
         if (is_array($options)) {
             foreach ($this->defaultOptions as $optionKey => $defaultOption) {
                 if (array_key_exists($optionKey, $options) && ($defaultOption === null || gettype($options[$optionKey]) === gettype($defaultOption))) {
                     $this->options[$optionKey] = $options[$optionKey];
                 } else {
                     $this->options[$optionKey] = $defaultOption;
                 }
             }
         } else {
             $this->options = $this->defaultOptions;
         }
         $this->optionDBSlug = $option_db_slug;
     }
     abstract protected function load_from_db($option_db_slug);
     abstract public function store_in_db();
 }
