<?php
/**
 * Product: Prosodia VGW OS
 * URL: https://prosodia.de/
 * Author: Dr. Ronny Harbich
 * Copyright: Dr. Ronny Harbich
 * License: GPLv2 or later
 */
 class WPVGW_AdminViewsManger
 {
     private $views;
     private $currentView;
     public static function get_default_view_slug(): string
     {
         return WPVGW_MarkersAdminView::get_slug_static();
     }
     public function get_views()
     {
         return $this->views;
     }
     public function get_current_view()
     {
         return $this->currentView;
     }
     public function is_init()
     {
         return $this->currentView !== null;
     }
     public function __construct(WPVGW_MarkersManager $markers_manager, WPVGW_PostsExtras $posts_extras, WPVGW_Options $options, WPVGW_UserOptions $user_options)
     {
         $this->views = array( WPVGW_MarkersAdminView::get_slug_static() => new WPVGW_MarkersAdminView($markers_manager, $posts_extras, $options, $user_options), WPVGW_ImportAdminView::get_slug_static() => new WPVGW_ImportAdminView($markers_manager, $options), WPVGW_ConfigurationAdminView::get_slug_static() => new WPVGW_ConfigurationAdminView($markers_manager, $options), WPVGW_OperationsAdminView::get_slug_static() => new WPVGW_OperationsAdminView($markers_manager, $posts_extras, $options), WPVGW_DataPrivacyAdminView::get_slug_static() => new WPVGW_DataPrivacyAdminView($options, $user_options), WPVGW_SupportAdminView::get_slug_static() => new WPVGW_SupportAdminView(), WPVGW_AboutAdminView::get_slug_static() => new WPVGW_AboutAdminView(), );
     }
     public function init(string $current_view_slug)
     {
         if ($current_view_slug === null || !array_key_exists($current_view_slug, $this->views)) {
             $current_view_slug = (string)key($this->views);
         }
         $this->currentView = $this->views[$current_view_slug];
         $this->currentView->init();
     }
     public static function create_admin_view_url($view_slug = null)
     {
         return admin_url('admin.php?page=' . WPVGW . '-' . ($view_slug ?? self::get_default_view_slug()));
     }
 }
