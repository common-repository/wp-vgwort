<?php
/**
 * Product: Prosodia VGW OS
 * URL: https://prosodia.de/
 * Author: Dr. Ronny Harbich
 * Copyright: Dr. Ronny Harbich
 * License: GPLv2 or later
 */
 class WPVGW_Advanced_Custom_Fields_Plugin_Extension extends WPVGW_Plugin_Extension
 {
     public static function is_active()
     {
         return is_plugin_active('advanced-custom-fields/acf.php') || is_plugin_active('advanced-custom-fields-pro/acf.php');
     }
     public function __construct()
     {
         add_action('acf/render_field_settings/type=text', array( $this, 'character_count_calculation_render_field_settings' ));
         add_action('acf/render_field_settings/type=textarea', array( $this, 'character_count_calculation_render_field_settings' ));
         add_action('acf/render_field_settings/type=wysiwyg', array( $this, 'character_count_calculation_render_field_settings' ));
         add_action('acf/render_field/type=text', array( $this, 'acf_render_field' ));
         add_action('acf/render_field/type=textarea', array( $this, 'acf_render_field' ));
         add_action('acf/render_field/type=wysiwyg', array( $this, 'acf_render_field' ));
         add_action(WPVGW . '_add_character_count', array( $this, 'add_character_count' ), 10, 4);
         add_action(WPVGW . '_add_javascript_post_view', array( $this, 'add_javascript_post_view' ), 10, 1);
     }
     public function add_javascript_post_view(&$return_javascript_array)
     {
         $return_javascript_array = array( 'file' => 'plugin-extensions/advanced-custom-fields-plugin-extension.js', 'slug' => 'advanced-custom-fields-plugin-extension', 'dependencies' => array( 'jquery', 'wp-hooks' ) );
     }
     public function add_character_count($markers_manager, $post, $additional_texts, &$return_custom_count)
     {
         if ($post === null) {
             foreach ($additional_texts as $additional_text) {
                 $return_custom_count += mb_strlen($markers_manager->clean_word_press_text($additional_text));
             }
         } else {
             if (!function_exists('get_field_objects')) {
                 return;
             }
             $fieldObjects = get_field_objects($post->ID);
             if ($fieldObjects === false) {
                 return;
             }
             foreach ($fieldObjects as $fieldObject) {
                 if (array_key_exists(WPVGW . '_character_count_calculation', $fieldObject) && $fieldObject[WPVGW . '_character_count_calculation'] === 1) {
                     $return_custom_count += mb_strlen($markers_manager->clean_word_press_text($fieldObject['value']));
                 }
                 if (array_key_exists('type', $fieldObject) && $fieldObject['type'] === 'flexible_content') {
                     $return_custom_count += $this->get_dynamic_fields_character_count($fieldObject['value'], WPVGW_Helper::array_search_recursive('sub_fields', $fieldObject));
                 }
             }
         }
     }
     private function get_dynamic_fields_character_count($field_values, $sub_fields)
     {
         $return_custom_count = 0;
         foreach ($sub_fields as $subField) {
             if (array_key_exists(WPVGW . '_character_count_calculation', $subField) && $subField[WPVGW . '_character_count_calculation'] === 1 && ($subField['type'] === 'text' || $subField['type'] === 'textarea' || $subField['type'] === 'wysiwyg')) {
                 $values = WPVGW_Helper::array_search_recursive($subField['name'], $field_values);
                 foreach ($values as $value) {
                     $return_custom_count += mb_strlen($value);
                 }
             }
         }
         return $return_custom_count;
     }
     public function acf_render_field($field)
     {
         if (array_key_exists(WPVGW . '_character_count_calculation', $field) && $field[WPVGW . '_character_count_calculation'] === 1) {
             echo(sprintf('<div data-wpvgw_character_count_calculation="true" data-wpvgw-afc-field-id="%s" style="display: none !important;"></div>', esc_attr($field['key'])));
         }
     }
     public function character_count_calculation_render_field_settings($field)
     {
         if (!function_exists('acf_render_field_setting')) {
             return;
         }
         acf_render_field_setting($field, array( 'label' => __('Zeichenanzahl mitzählen', WPVGW_TEXT_DOMAIN), 'instructions' => sprintf(__('Die Zeichenanzahl dieses Feldes soll zur Zeichenanzahl der Seite addiert werden. Ggf. bitte <a href="%s" target="_blank">%s</a>', WPVGW_TEXT_DOMAIN), esc_attr(WPVGW_AdminViewsManger::create_admin_view_url(WPVGW_OperationsAdminView::get_slug_static())), __('Zeichenanzahl aller Seiten neuberechnen.', WPVGW_TEXT_DOMAIN)), 'name' => WPVGW . '_character_count_calculation', 'type' => 'true_false', 'ui' => 1, ), true);
     }
 }
