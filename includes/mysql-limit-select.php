<?php
/**
 * Product: Prosodia VGW OS
 * URL: https://prosodia.de/
 * Author: Dr. Ronny Harbich
 * Copyright: Dr. Ronny Harbich
 * License: GPLv2 or later
 */
 class WPVGW_MySqlLimitSelect
 {
     private $query;
     private $countLimit;
     private $offset;
     public function __construct($query, $offset = 0, $count_limit = 1000)
     {
         if (!is_int($offset)) {
             throw new Exception('offset must be an integer.');
         }
         if (!is_int($count_limit)) {
             throw new Exception('count_limit must be an integer.');
         }
         $this->query = $query;
         $this->offset = $offset;
         $this->countLimit = $count_limit;
     }
     public function get_results($output = OBJECT)
     {
         global $wpdb;
         $limitQuery = sprintf('%s LIMIT %s, %s', $this->query, $wpdb->_escape($this->offset), $wpdb->_escape($this->countLimit));
         if ($wpdb->last_error !== '') {
             WPVGW_Helper::throw_database_exception();
         }
         return $wpdb->get_results($limitQuery, $output);
     }
 }
