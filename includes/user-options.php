<?php
/**
 * Product: Prosodia VGW OS
 * URL: https://prosodia.de/
 * Author: Dr. Ronny Harbich
 * Copyright: Dr. Ronny Harbich
 * License: GPLv2 or later
 */
 class WPVGW_UserOptions extends WPVGW_OptionsBase
 {
     private static $markersAdminViewAdminMessages = 'markers_admin_view_admin_messages';
     private static $postTableAdminMessages = 'post_table_admin_messages';
     private static $editPostErrorSetting = 'edit_post_error_setting';
     private static $privacyAllowTestMarker = 'privacy_allow_test_marker';
     private static $instance;
     public static function get_instance()
     {
         if (self::$instance === null) {
             self::$instance = new WPVGW_UserOptions();
         }
         return self::$instance;
     }
     private function __construct()
     {
     }
     public function init($option_db_slug)
     {
         if ($this->defaultOptions !== null) {
             return;
         }
         $this->defaultOptions = array( self::$markersAdminViewAdminMessages => array(), self::$postTableAdminMessages => array(), self::$editPostErrorSetting => array(), self::$privacyAllowTestMarker => false, );
         parent::init($option_db_slug);
     }
     protected function load_from_db($option_db_slug)
     {
         return get_user_option($option_db_slug, get_current_user_id());
     }
     public function store_in_db()
     {
         if ($this->optionsChanged) {
             update_user_option(get_current_user_id(), $this->get_option_db_slug(), $this->options);
         }
     }
     public function set_markers_admin_view_admin_messages(array $value)
     {
         if ($this->options[self::$markersAdminViewAdminMessages] !== $value) {
             $this->options[self::$markersAdminViewAdminMessages] = $value;
             $this->optionsChanged = true;
         }
     }
     public function get_markers_admin_view_admin_messages()
     {
         $adminMessage = $this->options[self::$markersAdminViewAdminMessages];
         $this->set_markers_admin_view_admin_messages($this->default_markers_admin_view_admin_messages());
         return $adminMessage;
     }
     public function default_markers_admin_view_admin_messages()
     {
         return $this->defaultOptions[self::$markersAdminViewAdminMessages];
     }
     public function set_post_table_admin_messages(array $value)
     {
         if ($this->options[self::$postTableAdminMessages] !== $value) {
             $this->options[self::$postTableAdminMessages] = $value;
             $this->optionsChanged = true;
         }
     }
     public function get_post_table_admin_messages()
     {
         $adminMessage = $this->options[self::$postTableAdminMessages];
         $this->set_post_table_admin_messages($this->default_post_table_admin_messages());
         return $adminMessage;
     }
     public function default_post_table_admin_messages()
     {
         return $this->defaultOptions[self::$postTableAdminMessages];
     }
     public function set_edit_post_error_setting(array $value)
     {
         if ($this->options[self::$editPostErrorSetting] !== $value) {
             $this->options[self::$editPostErrorSetting] = $value;
             $this->optionsChanged = true;
         }
     }
     public function get_edit_post_error_setting()
     {
         $adminMessage = $this->options[self::$editPostErrorSetting];
         $this->set_edit_post_error_setting($this->default_edit_post_error_setting());
         return $adminMessage;
     }
     public function default_edit_post_error_setting()
     {
         return $this->defaultOptions[self::$editPostErrorSetting];
     }
     public function set_privacy_allow_test_marker($value)
     {
         if ($this->options[self::$privacyAllowTestMarker] !== $value) {
             $this->options[self::$privacyAllowTestMarker] = $value;
             $this->optionsChanged = true;
         }
     }
     public function get_privacy_allow_test_marker()
     {
         return $this->options[self::$privacyAllowTestMarker];
     }
     public function default_privacy_allow_test_marker()
     {
         return $this->defaultOptions[self::$privacyAllowTestMarker];
     }
 }
