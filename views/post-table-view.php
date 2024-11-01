<?php
/**
 * Product: Prosodia VGW OS
 * URL: https://prosodia.de/
 * Author: Dr. Ronny Harbich
 * Copyright: Dr. Ronny Harbich
 * License: GPLv2 or later
 */
 class WPVGW_PostTableView extends WPVGW_ViewBase
 {
     protected $markersManager;
     protected $postsExtras;
     protected $options;
     protected $userOptions;
     private $characterCountColumnName;
     private $postIdColumnName;
     private $isMarkerDisabledColumnName;
     private $postType;
     private $singularPostTypeName;
     private $pluralPostTypeName;
     private $escapedSingularPostTypeName;
     private $escapedPluralPostTypeName;
     private $screenId;
     private $isMediaScreen;
     private $filters;
     private $adminMessages = array();
     private $postsIdMap;
     public function set_post_type(WP_Post_Type $value)
     {
         $this->postType = $value;
     }
     public function set_screen_id($value)
     {
         $this->screenId = $value;
         $this->isMediaScreen = $value === 'upload';
     }
     public function __construct(WPVGW_MarkersManager $markers_manager, WPVGW_PostsExtras $posts_extras, WPVGW_Options $options, WPVGW_UserOptions $user_options)
     {
         parent::__construct();
         $this->markersManager = $markers_manager;
         $this->options = $options;
         $this->postsExtras = $posts_extras;
         $this->userOptions = $user_options;
         $this->characterCountColumnName = WPVGW . '_posts_extras_character_count';
         $this->postIdColumnName = WPVGW . '_markers_post_id';
         $this->isMarkerDisabledColumnName = WPVGW . '_markers_is_marker_disabled';
         add_action('admin_action_' . WPVGW . '_add_marker', array( $this, 'do_add_marker_action' ));
     }
     public function init()
     {
         if ($this->postType === null) {
             throw new Exception('Post type must be set before calling init().');
         }
         if ($this->screenId === null) {
             throw new Exception('Screen ID must be set before calling init().');
         }
         $this->init_base(array());
         $this->singularPostTypeName = WPVGW_Helper::get_post_type_name($this->postType);
         $this->pluralPostTypeName = WPVGW_Helper::get_post_type_name($this->postType, true);
         $this->escapedSingularPostTypeName = esc_html($this->singularPostTypeName);
         $this->escapedPluralPostTypeName = esc_html($this->pluralPostTypeName);
         $postsExtrasTableName = $this->postsExtras->get_post_extras_table_name();
         $markersTableName = $this->markersManager->get_markers_table_name();
         $characterCountSufficientSql = $this->markersManager->is_character_count_sufficient_sql($postsExtrasTableName . '.character_count', $this->options->get_vg_wort_minimum_character_count());
         $sufficientFilter = array( array( 'label' => __('Zeichenanzahl', WPVGW_TEXT_DOMAIN), ), array( 'label' => __('Genügend', WPVGW_TEXT_DOMAIN), 'where' => $characterCountSufficientSql, ), array( 'label' => __('Zu wenig', WPVGW_TEXT_DOMAIN), 'where' => "NOT $characterCountSufficientSql", ), );
         $markerFilter = array( array( 'label' => __('Zählmarke', WPVGW_TEXT_DOMAIN), ), array( 'label' => __('Zugeordnet', WPVGW_TEXT_DOMAIN), 'where' => "{$markersTableName}.post_id IS NOT NULL", ), array( 'label' => __('Nicht zugeordnet', WPVGW_TEXT_DOMAIN), 'where' => "{$markersTableName}.post_id IS NULL", ), array( 'label' => __('Aktiv', WPVGW_TEXT_DOMAIN), 'where' => "{$markersTableName}.is_marker_disabled != 1", ), array( 'label' => __('Inaktiv', WPVGW_TEXT_DOMAIN), 'where' => "{$markersTableName}.is_marker_disabled = 1", ), );
         $this->filters = array();
         if (!$this->isMediaScreen) {
             $this->filters['sufficient'] = $sufficientFilter;
         }
         $this->filters['marker'] = $markerFilter;
         $postTypeName = WPVGW_Helper::is_media($this->postType) ? 'media' : $this->postType->name . '_posts';
         add_filter("manage_{$postTypeName}_columns", array( $this, 'on_add_column' ));
         add_action("manage_{$postTypeName}_custom_column", array( $this, 'on_render_column' ), 10, 2);
         if (!$this->isMediaScreen) {
             add_filter("manage_{$this->screenId}_sortable_columns", array( $this, 'on_register_sortable_column' ));
         }
         add_filter('posts_fields', array( $this, 'on_wp_query_posts_fields' ));
         add_filter('posts_join', array( $this, 'on_wp_query_posts_join' ));
         add_filter('posts_where', array( $this, 'on_wp_query_posts_where' ));
         add_filter('posts_orderby', array( $this, 'on_wp_query_posts_order_by' ));
         add_action('restrict_manage_posts', array( $this, 'on_render_filter_html' ));
         add_filter('post_row_actions', array( $this, 'on_add_row_actions' ), 10, 2);
         add_filter('page_row_actions', array( $this, 'on_add_row_actions' ), 10, 2);
         add_filter('media_row_actions', array( $this, 'on_add_row_actions' ), 10, 2);
         add_filter("bulk_actions-{$this->screenId}", array( $this, 'on_bulk_actions' ));
         add_filter("handle_bulk_actions-{$this->screenId}", array( $this, 'on_handle_bulk_actions' ), 10, 3);
         add_action('admin_notices', array( $this, 'on_admin_notices' ));
     }
     private function add_admin_message($message, $type = WPVGW_ErrorType::Error, $escape = true)
     {
         $this->adminMessages[] = array( 'message' => $message, 'type' => $type, 'escape' => $escape );
     }
     public function on_bulk_actions($actions)
     {
         $actions[WPVGW . '-add-markers'] = __('Zählmarke zuordnen', WPVGW_TEXT_DOMAIN);
         $actions[WPVGW . '-enable-markers'] = __('Zählmarke aktiv setzen', WPVGW_TEXT_DOMAIN);
         $actions[WPVGW . '-disable-markers'] = __('Zählmarke inaktiv setzen', WPVGW_TEXT_DOMAIN);
         if (current_user_can('manage_options')) {
             $actions[WPVGW . '-remove-markers'] = __('Zählmarken-Zuordnung aufheben', WPVGW_TEXT_DOMAIN);
         }
         if (!$this->isMediaScreen) {
             $actions[WPVGW . '-recalculate-post_character-count'] = __('Zeichenanzahl neuberechnen', WPVGW_TEXT_DOMAIN);
         }
         return $actions;
     }
     public function on_handle_bulk_actions($redirect_url, $action, $post_ids)
     {
         switch ($action) { case WPVGW . '-add-markers': $this->do_add_markers_action($post_ids); break; case WPVGW . '-enable-markers': $this->do_enable_markers_action($post_ids); break; case WPVGW . '-disable-markers': $this->do_disable_markers_action($post_ids); break; case WPVGW . '-remove-markers': $this->do_remove_markers_action($post_ids); break; case WPVGW . '-recalculate-post_character-count': $this->do_recalculate_post_character_count($post_ids); break; default: break; }
         $this->userOptions->set_post_table_admin_messages($this->adminMessages);
         return $redirect_url;
     }
     public function on_wp_query_posts_fields($fields_statement)
     {
         $postsExtrasTableName = $this->postsExtras->get_post_extras_table_name();
         $markersTableName = $this->markersManager->get_markers_table_name();
         $fields_statement .= ", {$postsExtrasTableName}.character_count AS {$this->characterCountColumnName}, {$markersTableName}.post_id AS {$this->postIdColumnName}, {$markersTableName}.is_marker_disabled AS {$this->isMarkerDisabledColumnName}";
         return $fields_statement;
     }
     public function on_wp_query_posts_join($join_statement)
     {
         global $wpdb;
         $postsExtrasTableName = $this->postsExtras->get_post_extras_table_name();
         $markersTableName = $this->markersManager->get_markers_table_name();
         $join_statement .= " LEFT OUTER JOIN $postsExtrasTableName ON {$postsExtrasTableName}.post_id = {$wpdb->posts}.ID " . "LEFT OUTER JOIN $markersTableName ON {$markersTableName}.post_id = {$wpdb->posts}.ID";
         return $join_statement;
     }
     public function on_wp_query_posts_where($where_statement)
     {
         $sqlFilters = '';
         foreach ($this->filters as $htmlSelect => $options) {
             $htmlSelect = WPVGW . '_' . $htmlSelect;
             if (isset($_REQUEST[$htmlSelect])) {
                 $currentOption = (int)$_REQUEST[$htmlSelect];
                 if ($currentOption !== 0 && array_key_exists($currentOption, $options)) {
                     $sqlFilters .= sprintf(' AND (%s)', $options[$currentOption]['where']);
                 }
             }
         }
         return $where_statement . $sqlFilters;
     }
     public function on_wp_query_posts_order_by($order_by_statement)
     {
         $orderBy = get_query_var('orderby');
         if ($orderBy !== $this->characterCountColumnName) {
             return $order_by_statement;
         }
         $order = strtolower(get_query_var('order'));
         if (!($order === 'asc' || $order === 'desc')) {
             $order = 'asc';
         }
         $asColumnName = $this->characterCountColumnName;
         $order_by_statement = "$asColumnName $order";
         return $order_by_statement;
     }
     public function on_add_column($columns)
     {
         $columns[$this->characterCountColumnName] = __('Zeichen', WPVGW_TEXT_DOMAIN);
         return $columns;
     }
     public function on_register_sortable_column($columns)
     {
         $columns[$this->characterCountColumnName] = $this->characterCountColumnName;
         return $columns;
     }
     public function on_render_column($column_name, $post_id)
     {
         if ($column_name !== $this->characterCountColumnName) {
             return;
         }
         if ($this->postsIdMap === null) {
             global $wp_query;
             $this->postsIdMap = array();
             foreach ($wp_query->posts as $post) {
                 if (!property_exists($post, 'post_author')) {
                     $post->post_author = get_post($post->ID)->post_author;
                 }
                 $this->postsIdMap[$post->ID] = $post;
             }
         }
         $post = $this->postsIdMap[$post_id];
         if (!$this->markersManager->is_user_allowed((int)$post->post_author)) {
             _e('Autor nicht zugelassen', WPVGW_TEXT_DOMAIN);
         } else {
             $characterCount = $post->{$this->characterCountColumnName} === null ? null : (int)$post->{$this->characterCountColumnName};
             $hasMarker = $post->{$this->postIdColumnName} !== null;
             $isMakerDisabled = $post->{$this->isMarkerDisabledColumnName} === '1';
             if (!$this->isMediaScreen) {
                 if ($characterCount === null) {
                     echo(__('nicht berechnet', WPVGW_TEXT_DOMAIN));
                 } elseif ($this->markersManager->is_character_count_sufficient($characterCount, $this->options->get_vg_wort_minimum_character_count())) {
                     echo(sprintf(__('genügend, %s', WPVGW_TEXT_DOMAIN), number_format_i18n($characterCount)));
                 } else {
                     echo(sprintf(__('zu wenig, %s', WPVGW_TEXT_DOMAIN), number_format_i18n($characterCount)));
                 }
                 echo('<br />');
             }
             if ($hasMarker) {
                 echo(sprintf($this->options->get_post_table_view_use_colors() ? '<span class="wpvgw-has-marker">%s</span>' : '%s', __('Zählmarke zugeordnet', WPVGW_TEXT_DOMAIN)));
             } elseif ($characterCount !== null && $this->markersManager->is_character_count_sufficient($characterCount, $this->options->get_vg_wort_minimum_character_count())) {
                 echo(sprintf($this->options->get_post_table_view_use_colors() ? '<span class="wpvgw-marker-possible">%s</span>' : '<em>%s</em>', __('Zählmarke möglich', WPVGW_TEXT_DOMAIN)));
             }
             if ($isMakerDisabled) {
                 echo('<br />');
                 _e('Zählmarke inaktiv', WPVGW_TEXT_DOMAIN);
             }
         }
     }
     public function on_render_filter_html()
     {
         WPVGW_Helper::render_html_selects($this->filters);
     }
     public function on_add_row_actions($actions, $post)
     {
         $postTypeObject = get_post_type_object($post->post_type);
         if (!current_user_can($postTypeObject->cap->edit_post, $post->ID)) {
             return $actions;
         }
         if (!$this->markersManager->is_user_allowed((int)$post->post_author)) {
             return $actions;
         }
         $hasMarker = ($post->{$this->postIdColumnName} !== null);
         if (!$hasMarker) {
             $action = sprintf('<a href="%s" title="%s">%s</a>', wp_nonce_url(admin_url(sprintf('admin.php?action=%s_add_marker&amp;post=%s', WPVGW, $post->ID)), WPVGW . '_add_marker'), sprintf(__('Dieser %s automatisch eine Zählmarke zuordnen', WPVGW_TEXT_DOMAIN), $this->escapedSingularPostTypeName), __('Zählmarke zuordnen', WPVGW_TEXT_DOMAIN));
             $actions[WPVGW . '_add_marker'] = $action;
         }
         return $actions;
     }
     private function redirect_to_last_page()
     {
         $referer = wp_get_referer();
         if ($referer === false) {
             wp_safe_redirect(get_home_url());
         } else {
             wp_safe_redirect($referer);
             $this->userOptions->set_post_table_admin_messages($this->adminMessages);
         }
         exit;
     }
     private function iterate_posts_for_actions($post_ids, $check_user_allowed, $do_action)
     {
         $processedPostCount = 0;
         $userNotAllowedCount = 0;
         foreach ($post_ids as $postId) {
             $processedPostCount++;
             $post = get_post($postId);
             if ($post === null) {
                 continue;
             }
             $postTypeObject = get_post_type_object($post->post_type);
             if (!current_user_can($postTypeObject->cap->edit_post, $post->ID)) {
                 WPVGW_Helper::die_cheating();
             }
             $postUserId = (int)$post->post_author;
             if ($check_user_allowed && !$this->markersManager->is_user_allowed($postUserId)) {
                 $userNotAllowedCount++;
                 continue;
             }
             if (!$do_action($this->markersManager, $this->postsExtras, $this->options, $post)) {
                 break;
             }
         }
         if ($userNotAllowedCount > 0) {
             $this->add_admin_message(sprintf(_n('Eine %2$s / eine Zählmarke wurde nicht bearbeitet, da der Autor der %2$s keine Zählmarken verwenden darf.', '%1$s %3$s/Zählmarken wurden nicht bearbeitet, da der jeweilige Autor der %2$s keine Zählmarken verwenden darf.', $userNotAllowedCount, WPVGW_TEXT_DOMAIN), number_format_i18n($userNotAllowedCount), $this->singularPostTypeName, $this->pluralPostTypeName));
         }
         return $processedPostCount;
     }
     private function add_marker_to_post($post_ids)
     {
         $noFreeMarker = false;
         $markerAddedCount = 0;
         $markerAlreadyExistsCount = 0;
         $markerNotAddedCount = 0;
         $postCharacterCountUnknownCount = 0;
         $postCharacterCountNotSufficientCount = 0;
         $notPublishedPostsCount = 0;
         $notAttachedMediasCount = 0;
         $processedPostCount = $this->iterate_posts_for_actions($post_ids, true, function ($markersManager, $postsExtras, $options, $post) use (&$noFreeMarker, &$markerAddedCount, &$markerAlreadyExistsCount, &$markerNotAddedCount, &$postCharacterCountUnknownCount, &$postCharacterCountNotSufficientCount, &$notPublishedPostsCount, &$notAttachedMediasCount) {
             $postId = $post->ID;
             if (!WPVGW_Helper::is_media($post->post_type) && $options->get_post_view_set_marker_for_published_only() && $post->post_status !== 'publish' && $post->post_status !== 'future') {
                 $notPublishedPostsCount++;
                 return true;
             }
             if (!$this->isMediaScreen && !WPVGW_Helper::is_media($post->post_type)) {
                 $postExtras = $postsExtras->get_post_extras_from_db($postId);
                 $postCharacterCount = ($postExtras === false ? null : $postExtras['character_count']);
                 if ($postCharacterCount === null) {
                     $postCharacterCountUnknownCount++;
                 } elseif (!$markersManager->is_character_count_sufficient($postCharacterCount, $options->get_vg_wort_minimum_character_count())) {
                     $postCharacterCountNotSufficientCount++;
                 }
             } elseif (!WPVGW_Helper::is_media_attached_to_post($post)) {
                 $notAttachedMediasCount++;
             }
             $marker = $markersManager->get_free_marker_from_db();
             if ($marker === false) {
                 $marker = $markersManager->get_free_marker_from_db();
             }
             if ($marker === false) {
                 $noFreeMarker = true;
                 return false;
             }
             $markerUpdateResult = $markersManager->update_marker_in_db($marker['public_marker'], 'public_marker', array( 'post_id' => $post->ID, 'is_marker_disabled' => false ), array( 'post_id' => null ));
             switch ($markerUpdateResult) { case WPVGW_UpdateMarkerResults::Updated: $markerAddedCount++; break; case WPVGW_UpdateMarkerResults::UpdateNotNecessary: case WPVGW_UpdateMarkerResults::PostIdExists: $markerAlreadyExistsCount++; break; default: $markerNotAddedCount++; break; }
             return true;
         });
         if ($noFreeMarker) {
             $notProcessedPostCount = count($post_ids) - $processedPostCount;
             $this->add_admin_message(sprintf(__('Es sind nicht mehr genügend Zählmarken für einen oder mehrere Autoren vorhanden. Fügen Sie bitte zunächst neue Zählmarken für die betreffenden Autoren hinzu und wiederholen Sie den Vorgang. %s', WPVGW_TEXT_DOMAIN), sprintf('<a href="%s">%s</a>', esc_attr(WPVGW_AdminViewsManger::create_admin_view_url(WPVGW_ImportAdminView::get_slug_static())), __('Zählmarken hier importieren.', WPVGW_TEXT_DOMAIN))) . ' ' . sprintf(_n('Einer %2$s konnte daher keine Zählmarke zugeordnet werden.', '%1$s %3$s konnten daher keine Zählmarken zugeordnet werden.', $notProcessedPostCount, WPVGW_TEXT_DOMAIN), esc_html(number_format_i18n($notProcessedPostCount)), $this->escapedSingularPostTypeName, $this->pluralPostTypeName), WPVGW_ErrorType::Error, false);
         }
         if ($markerAddedCount > 0) {
             $this->add_admin_message(sprintf(_n('Einer %2$s wurde eine Zählmarke zugeordnet.', '%1$s %3$s wurden Zählmarken zugeordnet.', $markerAddedCount, WPVGW_TEXT_DOMAIN), number_format_i18n($markerAddedCount), $this->singularPostTypeName, $this->pluralPostTypeName), WPVGW_ErrorType::Update);
         }
         if ($markerAlreadyExistsCount > 0) {
             $this->add_admin_message(sprintf(_n('Einer %2$s ist bereits eine Zählmarke zugeordnet worden.', '%1$s %3$s sind bereits Zählmarken zugeordnet worden.', $markerAlreadyExistsCount, WPVGW_TEXT_DOMAIN), number_format_i18n($markerAlreadyExistsCount), $this->singularPostTypeName, $this->pluralPostTypeName), WPVGW_ErrorType::Warning);
         }
         if ($markerNotAddedCount > 0) {
             $this->add_admin_message(sprintf(_n('Einer %2$s konnte keine Zählmarke zugeordnet werden.', '%1$s %3$s konnten keine Zählmarken zugeordnet werden.', $markerNotAddedCount, WPVGW_TEXT_DOMAIN), $this->singularPostTypeName, $this->pluralPostTypeName));
         }
         if ($postCharacterCountNotSufficientCount > 0) {
             $this->add_admin_message(sprintf(_n('Einer %2$s enthält zu wenig Zeichen. Es wurde möglicherweise dennoch eine Zählmarke zugeordnet.', '%1$s %3$s enthalten zu wenig Zeichen. Es wurden möglicherweise dennoch Zählmarken zugeordnet.', $postCharacterCountNotSufficientCount, WPVGW_TEXT_DOMAIN), number_format_i18n($postCharacterCountNotSufficientCount), $this->singularPostTypeName, $this->pluralPostTypeName), WPVGW_ErrorType::Warning);
         }
         if ($postCharacterCountUnknownCount > 0) {
             $this->add_admin_message(sprintf(_n('Für eine %2$s konnte die Anzahl der Zeichen nicht ermittelt werden. Es wurde möglicherweise dennoch eine Zählmarke zugeordnet.', 'Für %1$s %3$s konnte die Anzahl der Zeichen nicht ermittelt werden. Es wurden möglicherweise dennoch Zählmarken zugeordnet.', $postCharacterCountUnknownCount, WPVGW_TEXT_DOMAIN), number_format_i18n($postCharacterCountUnknownCount), $this->singularPostTypeName, $this->pluralPostTypeName), WPVGW_ErrorType::Warning);
         }
         if ($notAttachedMediasCount > 0) {
             $this->add_admin_message(sprintf(_n('Eine Datei ist mit keiner Seite verknüpft, sodass die Zählmarke nicht ausgegeben wird. Es wurde möglicherweise dennoch eine Zählmarke zugeordnet.', '%1$s Dateien sind mit keiner Seite verknüpft, sodass die Zählmarke nicht ausgegeben wird. Es wurden möglicherweise dennoch Zählmarken zugeordnet.', $notAttachedMediasCount, WPVGW_TEXT_DOMAIN), number_format_i18n($notAttachedMediasCount)), WPVGW_ErrorType::Warning);
         }
         if ($notPublishedPostsCount > 0) {
             $this->add_admin_message(sprintf(_n('Einer %2$s wurde keine Zählmarke zugeordnet, da dieser noch nicht veröffentlicht wurde.', '%1$s %3$s wurden keine Zählmarken zugeordnet, da diese noch nicht veröffentlicht wurden.', $notPublishedPostsCount, WPVGW_TEXT_DOMAIN), number_format_i18n($notPublishedPostsCount), $this->singularPostTypeName, $this->pluralPostTypeName), WPVGW_ErrorType::Warning);
         }
     }
     public function do_add_marker_action()
     {
         check_admin_referer(WPVGW . '_add_marker');
         $postIds = (isset($_REQUEST['post'])) ? [ (int)$_REQUEST['post'] ] : [];
         $this->add_marker_to_post($postIds);
         $this->redirect_to_last_page();
     }
     public function do_add_markers_action($post_ids)
     {
         $this->add_marker_to_post($post_ids);
     }
     private function set_markers_status($post_ids, $marker_disabled)
     {
         $postHasNoMarkerCount = 0;
         $markerSetStatusCount = 0;
         $markerAlreadySetCount = 0;
         $markerNotSetStatusCount = 0;
         $this->iterate_posts_for_actions($post_ids, true, function ($markersManager, $postsExtras, $options, $post) use (&$marker_disabled, &$postHasNoMarkerCount, &$markerSetStatusCount, &$markerAlreadySetCount, &$markerNotSetStatusCount) {
             $postId = $post->ID;
             $marker = $markersManager->get_marker_from_db($postId, 'post_id');
             if ($marker === false) {
                 $postHasNoMarkerCount++;
             } else {
                 $markerUpdateResult = $markersManager->update_marker_in_db($marker['public_marker'], 'public_marker', array( 'is_marker_disabled' => $marker_disabled, ));
                 switch ($markerUpdateResult) { case WPVGW_UpdateMarkerResults::Updated: $markerSetStatusCount++; break; case WPVGW_UpdateMarkerResults::UpdateNotNecessary: $markerAlreadySetCount++; break; default: $markerNotSetStatusCount++; break; }
             }
             return true;
         });
         $markerStatusText = ($marker_disabled ? __('inaktiv', WPVGW_TEXT_DOMAIN) : __('aktiv', WPVGW_TEXT_DOMAIN));
         if ($markerSetStatusCount > 0) {
             $this->add_admin_message(sprintf(_n('Eine Zählmarke wurde auf %2$s gesetzt.', '%1$s Zählmarken wurden auf %2$s gesetzt.', $markerSetStatusCount, WPVGW_TEXT_DOMAIN), number_format_i18n($markerSetStatusCount), $markerStatusText), WPVGW_ErrorType::Update);
         }
         if ($postHasNoMarkerCount > 0) {
             $this->add_admin_message(sprintf(_n('Einer %3$s ist keine Zählmarke zugeordnet. Daher konnte sie nicht auf %2$s gesetzt werden.', '%1$s %4$s sind keine Zählmarken zugeordnet. Daher konnte sie nicht auf %2$s gesetzt werden.', $postHasNoMarkerCount, WPVGW_TEXT_DOMAIN), number_format_i18n($postHasNoMarkerCount), $markerStatusText, $this->singularPostTypeName, $this->pluralPostTypeName));
         }
         if ($markerAlreadySetCount > 0) {
             $this->add_admin_message(sprintf(_n('Eine Zählmarke war bereits auf %2$s gesetzt.', '%1$s Zählmarken waren bereits auf %2$s gesetzt.', $markerAlreadySetCount, WPVGW_TEXT_DOMAIN), number_format_i18n($markerAlreadySetCount), $markerStatusText));
         }
         if ($markerNotSetStatusCount > 0) {
             $this->add_admin_message(sprintf(_n('Eine Zählmarke konnte nicht auf %2$s gesetzt werden.', '%1$s Zählmarken konnten nicht auf %2$s gesetzt werden.', $markerNotSetStatusCount, WPVGW_TEXT_DOMAIN), number_format_i18n($markerNotSetStatusCount), $markerStatusText));
         }
     }
     private function do_enable_markers_action($post_ids)
     {
         $this->set_markers_status($post_ids, false);
     }
     private function do_disable_markers_action($post_ids)
     {
         $this->set_markers_status($post_ids, true);
     }
     private function do_remove_markers_action($post_ids)
     {
         if (!current_user_can('manage_options')) {
             WPVGW_Helper::die_cheating();
         }
         $removedPostFromMarkerCount = 0;
         $this->iterate_posts_for_actions($post_ids, false, function ($markersManager, $postsExtras, $options, $post) use (&$removedPostFromMarkerCount) {
             if ($markersManager->remove_post_from_marker_in_db($post->ID)) {
                 $removedPostFromMarkerCount++;
             }
             return true;
         });
         $failedRemovePostFromMarkerCount = count($post_ids) - $removedPostFromMarkerCount;
         if ($removedPostFromMarkerCount > 0) {
             $this->add_admin_message(_n('Eine Zählmarken-Zuordnung wurde aufgehoben.', sprintf('%s Zählmarken-Zuordnungen wurden aufgehoben.', number_format_i18n($removedPostFromMarkerCount)), $removedPostFromMarkerCount, WPVGW_TEXT_DOMAIN), WPVGW_ErrorType::Update);
         }
         if ($failedRemovePostFromMarkerCount > 0) {
             $this->add_admin_message(sprintf(_n('Einer %2$s wurde keine Zählmarke zugeordnet. Daher konnte keine Zählmarke-Zuordnung aufgehoben werden.', '%1$s %3$s wurden keine Zählmarken zugeordnet. Daher konnte keine Zählmarke-Zuordnungen aufgehoben werden.', $failedRemovePostFromMarkerCount, WPVGW_TEXT_DOMAIN), number_format_i18n($failedRemovePostFromMarkerCount), $this->singularPostTypeName, $this->pluralPostTypeName));
         }
     }
     private function do_recalculate_post_character_count($post_ids)
     {
         $postCharacterCountRecalculatedCount = 0;
         $this->iterate_posts_for_actions($post_ids, true, function ($markersManager, $postsExtras, $options, $post) use (&$postCharacterCountRecalculatedCount) {
             $postsExtras->recalculate_post_character_count_in_db($post);
             $postCharacterCountRecalculatedCount++;
             return true;
         });
         $failedPostCharacterCountRecalculatedCount = count($post_ids) - $postCharacterCountRecalculatedCount;
         if ($postCharacterCountRecalculatedCount > 0) {
             $this->add_admin_message(sprintf(_n('Die Zeichenanzahl einer %2$s wurde neuberechnet.', 'Die Zeichenanzahlen von %1$s %3$s wurden neuberechnet.', $postCharacterCountRecalculatedCount, WPVGW_TEXT_DOMAIN), number_format_i18n($postCharacterCountRecalculatedCount), $this->singularPostTypeName, $this->pluralPostTypeName), WPVGW_ErrorType::Update);
         }
         if ($failedPostCharacterCountRecalculatedCount > 0) {
             $this->add_admin_message(sprintf(_n('Die Zeichenanzahl einer %2$s konnte nicht neuberechnet werden.', 'Die Zeichenanzahlen von %1$s %3$s konnte nicht neuberechnet werden.', $failedPostCharacterCountRecalculatedCount, WPVGW_TEXT_DOMAIN), number_format_i18n($failedPostCharacterCountRecalculatedCount), $this->singularPostTypeName, $this->pluralPostTypeName));
         }
     }
     public function on_admin_notices()
     {
         $adminMessages = $this->userOptions->get_post_table_admin_messages();
         WPVGW_Helper::render_admin_messages($adminMessages);
     }
 }
