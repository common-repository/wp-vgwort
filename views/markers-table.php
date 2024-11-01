<?php
/**
 * Product: Prosodia VGW OS
 * URL: https://prosodia.de/
 * Author: Dr. Ronny Harbich
 * Copyright: Dr. Ronny Harbich
 * License: GPLv2 or later
 */
 require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php'); class WPVGW_MarkersListTable extends WP_List_Table
 {
     private $dataRetriever;
     private $markersManager;
     private $postsExtras;
     private $options;
     protected $userOptions;
     private $columns;
     private $sortableColumnsWithSlugs;
     private $filterableColumnsSelects;
     private $bulkActions;
     private $viewLinks;
     private $urlActionUrlTemplate;
     private $abbrTemplate;
     private $invalidDataTemplate;
     protected $_column_headers;
     public function __construct(WPVGW_MarkersManager $markers_manager, WPVGW_PostsExtras $posts_extras, WPVGW_Options $options, WPVGW_UserOptions $user_options)
     {
         $this->markersManager = $markers_manager;
         $this->postsExtras = $posts_extras;
         $this->options = $options;
         $this->userOptions = $user_options;
         $this->dataRetriever = new WPVGW_DatabaseDataRetriever($markers_manager, $posts_extras, $options, $user_options);
         $this->columns = array( 'cb' => '<input type="checkbox" />', 'post_title' => __('Seite', WPVGW_TEXT_DOMAIN), 'marker_state' => __('Status', WPVGW_TEXT_DOMAIN), 'post_date' => __('Seitendatum', WPVGW_TEXT_DOMAIN), 'e_character_count' => __('Zeichenanzahl', WPVGW_TEXT_DOMAIN), 'up_display_name' => __('Seitenautor', WPVGW_TEXT_DOMAIN), 'order_date' => __('Bestelldatum', WPVGW_TEXT_DOMAIN), 'marker' => __('Zählmarke', WPVGW_TEXT_DOMAIN), );
         $this->sortableColumnsWithSlugs = array();
         foreach ($this->dataRetriever->get_sortable_columns() as $column) {
             $this->sortableColumnsWithSlugs[$column] = array( $column, false );
         }
         $postsAuthorsOptions = array( array( 'label' => __('Seitenautor', WPVGW_TEXT_DOMAIN), ), array( 'label' => __('Gelöscht', WPVGW_TEXT_DOMAIN), 'parameter' => 'deleted', 'args' => [], ), );
         $allowedPostTypesOptions = array( array( 'label' => __('Seitentyp', WPVGW_TEXT_DOMAIN), ), array( 'label' => __('Zugelassen', WPVGW_TEXT_DOMAIN), 'parameter' => 'allowed', 'args' => [], ), array( 'label' => __('Nicht Zugelassen', WPVGW_TEXT_DOMAIN), 'parameter' => 'not_allowed', 'args' => [], ), );
         $allowedPostTypes = $markers_manager->get_allowed_post_types();
         foreach ($allowedPostTypes as $allowedPostType) {
             $postTypeObject = get_post_type_object($allowedPostType);
             if ($postTypeObject !== null) {
                 $allowedPostTypesOptions[] = array( 'label' => sprintf(__('Typ: %s', WPVGW_TEXT_DOMAIN), $postTypeObject->labels->name), 'parameter' => 'by_type', 'args' => [ $allowedPostType ] );
             }
         }
         $postYearOptions = array( array( 'label' => __('Seitenjahr', WPVGW_TEXT_DOMAIN), ), );
         $currentYear = current_time('Y');
         for ($year = $currentYear + 2; $year >= $currentYear - 10; $year--) {
             $postYearOptions[] = array( 'label' => sprintf(__('%s', WPVGW_TEXT_DOMAIN), $year), 'parameter' => 'by_between_dates', 'args' => [ $year . '-01-01', $year . '-12-31' ], );
         }
         ++$year;
         $postYearOptions[] = array( 'label' => sprintf(__('vor %s', WPVGW_TEXT_DOMAIN), $year), 'parameter' => 'by_before_date', 'args' => [ $year . '-12-31' ], );
         $orderDateYearOptions = array( array( 'label' => __('Bestelldatum', WPVGW_TEXT_DOMAIN), ), );
         $currentYear = current_time('Y');
         for ($year = $currentYear + 1; $year >= $currentYear - 10; $year--) {
             $orderDateYearOptions[] = array( 'label' => sprintf(__('%s', WPVGW_TEXT_DOMAIN), $year), 'parameter' => 'by_between_dates', 'args' => [ $year . '-01-01', $year . '-12-31' ], );
         }
         ++$year;
         $orderDateYearOptions[] = array( 'label' => sprintf(__('vor %s', WPVGW_TEXT_DOMAIN), $year), 'parameter' => 'by_before_date', 'args' => [ $year . '-12-31' ], );
         $this->filterableColumnsSelects = array( 'has_marker' => array( array( 'label' => __('Zuordnung', WPVGW_TEXT_DOMAIN), ), array( 'label' => __('Zugeordnet', WPVGW_TEXT_DOMAIN), 'parameter' => 'true', 'args' => [], ), array( 'label' => __('Nicht zugeordnet', WPVGW_TEXT_DOMAIN), 'parameter' => 'false', 'args' => [], ), ), 'marker_disabled' => array( array( 'label' => __('Aktivierung', WPVGW_TEXT_DOMAIN), ), array( 'label' => __('Aktiv', WPVGW_TEXT_DOMAIN), 'parameter' => 'false', 'args' => [], ), array( 'label' => __('Inaktiv', WPVGW_TEXT_DOMAIN), 'parameter' => 'true', 'args' => [], ), ), 'marker_blocked' => array( array( 'label' => __('Zuordenbarkeit', WPVGW_TEXT_DOMAIN), ), array( 'label' => __('Zuordenbar', WPVGW_TEXT_DOMAIN), 'parameter' => 'false', 'args' => [], ), array( 'label' => __('Nicht zuordenbar', WPVGW_TEXT_DOMAIN), 'parameter' => 'true', 'args' => [], ), ), 'invalid_markers' => array( array( 'label' => __('Zählm.-Format', WPVGW_TEXT_DOMAIN), ), array( 'label' => __('Gültig', WPVGW_TEXT_DOMAIN), 'parameter' => 'false', 'args' => [], ), array( 'label' => __('Ungültig', WPVGW_TEXT_DOMAIN), 'parameter' => 'true', 'args' => [], ), ), 'post_author' => $postsAuthorsOptions, 'post_type' => $allowedPostTypesOptions, 'post' => array( array( 'label' => __('Seite', WPVGW_TEXT_DOMAIN), ), array( 'label' => __('Nicht gelöscht', WPVGW_TEXT_DOMAIN), 'parameter' => 'not_deleted', 'args' => [], ), array( 'label' => __('Gelöscht', WPVGW_TEXT_DOMAIN), 'parameter' => 'deleted', 'args' => [], ), array( 'label' => __('Verknüpft', WPVGW_TEXT_DOMAIN), 'parameter' => 'is_media_attached_to_post', 'args' => [], ), array( 'label' => __('Nicht verknüpft', WPVGW_TEXT_DOMAIN), 'parameter' => 'not_is_media_attached_to_post', 'args' => [], ), ), 'sufficient_characters' => array( array( 'label' => __('Zeichenanzahl', WPVGW_TEXT_DOMAIN), ), array( 'label' => __('Genügend', WPVGW_TEXT_DOMAIN), 'parameter' => 'true', 'args' => [], ), array( 'label' => __('Zu wenig', WPVGW_TEXT_DOMAIN), 'parameter' => 'false', 'args' => [], ), ), 'post_date' => $postYearOptions, 'order_date' => $orderDateYearOptions, );
         $this->bulkActions = array( 'edit' => __('Bearbeiten', WPVGW_TEXT_DOMAIN), WPVGW . '_enable_marker' => __('Aktiv setzen', WPVGW_TEXT_DOMAIN), WPVGW . '_disable_marker' => __('Inaktiv setzen', WPVGW_TEXT_DOMAIN), WPVGW . '_block_marker' => __('Nicht zuordenbar setzen', WPVGW_TEXT_DOMAIN), WPVGW . '_unblock_marker' => __('Zuordenbar setzen', WPVGW_TEXT_DOMAIN), WPVGW . '_remove_post_from_marker' => __('Zuordnung aufheben', WPVGW_TEXT_DOMAIN), WPVGW . '_delete_marker' => __('Löschen (nicht empfohlen)', WPVGW_TEXT_DOMAIN), WPVGW . '_recalculate_post_character_count' => __('Zeichenanzahl neuberechnen', WPVGW_TEXT_DOMAIN), );
         $markerTablePageUrl = esc_attr(WPVGW_AdminViewsManger::create_admin_view_url());
         $viewLinkTemplate = '<a href="%s" title="%s">%s</a>';
         $this->viewLinks = array( WPVGW . '_show_all_markers' => sprintf($viewLinkTemplate, $markerTablePageUrl, __('Alle Zählmarken anzeigen', WPVGW_TEXT_DOMAIN), __('Alle', WPVGW_TEXT_DOMAIN)), WPVGW . '_show_last_year_markers' => sprintf($viewLinkTemplate, $markerTablePageUrl . '&amp;' . WPVGW . '_post_date' . '=4' . '&amp;' . WPVGW . '_sufficient_characters' . '=1' . '&amp;' . WPVGW . '_has_marker' . '=1', __('Nur die zugeordneten Zählmarken, deren Seiten genügend Zeichen haben und vom letzten Jahr sind, anzeigen', WPVGW_TEXT_DOMAIN), __('Für Auszahlung im letzten Jahr geeignet', WPVGW_TEXT_DOMAIN)), WPVGW . '_show_current_year_markers' => sprintf($viewLinkTemplate, $markerTablePageUrl . '&amp;' . WPVGW . '_post_date' . '=3' . '&amp;' . WPVGW . '_sufficient_characters' . '=1' . '&amp;' . WPVGW . '_has_marker' . '=1', __('Nur die zugeordneten Zählmarken, deren Seiten genügend Zeichen haben und von diesem Jahr sind, anzeigen', WPVGW_TEXT_DOMAIN), __('Für Auszahlung in diesem Jahr geeignet', WPVGW_TEXT_DOMAIN)), );
         $this->urlActionUrlTemplate = esc_attr(WPVGW_AdminViewsManger::create_admin_view_url()) . '&amp;action=%s&amp;wpvgw_marker=%s&amp;_wpvgwadminviewnonce=' . esc_attr(wp_create_nonce('markers'));
         $this->abbrTemplate = '<abbr title="%s">%s</abbr>';
         $this->invalidDataTemplate = '<span class="wpvgw-invalid-data">%s</span>';
         parent::__construct(array( 'singular' => 'wpvgw_marker', 'plural' => 'wpvgw_markers', 'ajax' => false ));
     }
     private function is_marker_added_to_post(array $row)
     {
         return $row['post_id'] !== null;
     }
     private function is_post_not_found(array $row)
     {
         return $row['post_id'] !== null && $row['is_post_deleted'] === '0' && $row['post_title'] === null;
     }
     private function is_media_attached_to_post(array $row)
     {
         return $row['post_id'] !== null && $row['post_type'] !== null && WPVGW_Helper::is_media($row['post_type']) && $row['post_parent'] !== null && (int)$row['post_parent'] >= 1;
     }
     private function is_post_deleted(array $row)
     {
         return $row['is_post_deleted'] === '1';
     }
     private function is_marker_disabled(array $row)
     {
         if ($row['is_marker_disabled'] === '1') {
             if ($row['post_id'] === null) {
                 return null;
             } else {
                 return true;
             }
         }
         return false;
     }
     private function is_marker_blocked(array $row)
     {
         if ($row['is_marker_blocked'] === '1') {
             if ($row['post_id'] === null) {
                 return true;
             } else {
                 return null;
             }
         }
         return false;
     }
     private function is_post_type_known(array $row)
     {
         if ($row['post_type'] === null) {
             return null;
         }
         return get_post_type_object($row['post_type']) !== null;
     }
     private function is_post_type_allowed(array $row)
     {
         if ($row['post_type'] === null) {
             return null;
         }
         $postTypeObject = get_post_type_object($row['post_type']);
         return $postTypeObject === null ? null : $this->markersManager->is_post_type_allowed($row['post_type']);
     }
     private function is_post_published(array $row)
     {
         if ($row['post_status'] === null) {
             return null;
         }
         return $row['post_status'] === 'publish' || ($row['post_type'] !== null && WPVGW_Helper::is_media($row['post_type']) && $row['post_status'] === 'inherit');
     }
     protected function column_default($item, $column_name)
     {
         return esc_html($item[$column_name]);
     }
     protected function column_cb($item)
     {
         return sprintf('<input type="checkbox" name="%s[]" value="%s" />', $this->_args['singular'], $item['id']);
     }
     protected function column_post_title($row)
     {
         $actions = array();
         $linkTemplate = '<a href="%s" title="%s">%s</a>';
         $linkNewTabTemplate = '<a href="%s" title="%s" target="_blank">%s</a>';
         $jsLinkTemplate = '<a class="%s" data-object-id="%s" href="#" title="%s">%s</a>';
         $isMarkerAddedToPost = $this->is_marker_added_to_post($row);
         $isPostNotFound = $this->is_post_not_found($row);
         $isMediaAttachedToPost = $this->is_media_attached_to_post($row);
         $isPostDeleted = $this->is_post_deleted($row);
         $isDisabled = $this->is_marker_disabled($row);
         $isBlocked = $this->is_marker_blocked($row);
         $isPublished = $this->is_post_published($row);
         $copyActions = array( array( 'key' => 'private_marker', 'condition' => $row['private_marker'] !== null, 'class' => WPVGW . '-markers-view-copy-private-marker', 'object_id' => $row['id'], 'title' => __('Ermöglicht, die private Zählmarke in die Zwischenablage zu kopieren', WPVGW_TEXT_DOMAIN), 'text' => __('Priv.', WPVGW_TEXT_DOMAIN) ), array( 'key' => 'post_title', 'condition' => $isMarkerAddedToPost && !$isPostDeleted && !$isPostNotFound && $row['post_title'] !== null, 'class' => WPVGW . '-markers-view-copy-post-title', 'object_id' => $row['post_id'], 'title' => __('Ermöglicht, den Seitentitel in die Zwischenablage zu kopieren', WPVGW_TEXT_DOMAIN), 'text' => __('Titel', WPVGW_TEXT_DOMAIN) ), array( 'key' => 'post_content', 'condition' => $isMarkerAddedToPost && !$isPostDeleted && !$isPostNotFound, 'class' => WPVGW . '-markers-view-copy-post-content', 'object_id' => $row['post_id'], 'title' => __('Ermöglicht, den Seitentext in die Zwischenablage zu kopieren', WPVGW_TEXT_DOMAIN), 'text' => __('Text', WPVGW_TEXT_DOMAIN) ), array( 'key' => 'post_link', 'condition' => $isMarkerAddedToPost && !$isPostDeleted && !$isPostNotFound, 'class' => WPVGW . '-markers-view-copy-post-link', 'object_id' => $row['post_id'], 'title' => __('Ermöglicht, den Seiten-Link in die Zwischenablage zu kopieren', WPVGW_TEXT_DOMAIN), 'text' => __('Link', WPVGW_TEXT_DOMAIN) ), );
         foreach ($copyActions as $copyAction) {
             if ($copyAction['condition']) {
                 $actions[WPVGW . '_copy_' . $copyAction['key']] = sprintf($jsLinkTemplate, $copyAction['class'], esc_attr($copyAction['object_id']), esc_attr($copyAction['title']), $copyAction['text']);
             }
         }
         if ($isDisabled === true || $isDisabled === null) {
             $actions[WPVGW . '_enable_marker'] = sprintf($linkTemplate, sprintf($this->urlActionUrlTemplate, WPVGW . '_enable_marker', $row['id']), esc_attr(__('Diese Zählmarke aktiv setzen', WPVGW_TEXT_DOMAIN)), __('Aktiv setzen', WPVGW_TEXT_DOMAIN));
         } elseif ($isMarkerAddedToPost) {
             $actions[WPVGW . '_disable_marker'] = sprintf($linkTemplate, sprintf($this->urlActionUrlTemplate, WPVGW . '_disable_marker', $row['id']), esc_attr(__('Diese Zählmarke inaktiv setzen', WPVGW_TEXT_DOMAIN)), __('Inaktiv setzen', WPVGW_TEXT_DOMAIN));
         }
         if ($isBlocked === false && !$isMarkerAddedToPost) {
             $actions[WPVGW . '_block_marker'] = sprintf($linkTemplate, sprintf($this->urlActionUrlTemplate, WPVGW . '_block_marker', $row['id']), esc_attr(__('Diese Zählmarke als nicht zuordenbar setzen', WPVGW_TEXT_DOMAIN)), __('Nicht&nbsp;zuord.', WPVGW_TEXT_DOMAIN));
         } elseif ($isBlocked === true || $isBlocked === null) {
             $actions[WPVGW . '_unblock_marker'] = sprintf($linkTemplate, sprintf($this->urlActionUrlTemplate, WPVGW . '_unblock_marker', $row['id']), esc_attr(__('Diese Zählmarke als zuordenbar setzen', WPVGW_TEXT_DOMAIN)), __('Zuord.', WPVGW_TEXT_DOMAIN));
         }
         if ($isMarkerAddedToPost && !$isPostDeleted && !$isPostNotFound) {
             if ($this->userOptions->get_privacy_allow_test_marker()) {
                 $actions[WPVGW . '_test_marker'] = sprintf($linkNewTabTemplate, esc_attr(WPVGW_Helper::get_test_marker_in_post_link($isMediaAttachedToPost ? $row['post_parent'] : $row['post_id'], $row['public_marker'])), esc_attr(__('Prüfen ob, die zugeordnete Zählmarke in der Seite ausgegeben wird', WPVGW_TEXT_DOMAIN)), __('Prüfen', WPVGW_TEXT_DOMAIN));
             } else {
                 $actions[WPVGW . '_test_marker'] = sprintf($linkTemplate, sprintf($this->urlActionUrlTemplate, WPVGW . '_test_marker_not_allowed', $row['id']), esc_attr(__('Prüfen ob, die zugeordnete Zählmarke in der Seite ausgegeben wird', WPVGW_TEXT_DOMAIN)), __('Prüfen', WPVGW_TEXT_DOMAIN));
             }
             $actions[WPVGW . '_edit_post'] = sprintf($linkTemplate, get_edit_post_link($row['post_id'], 'display'), esc_attr(__('Die zugeordneten Seite bearbeiten', WPVGW_TEXT_DOMAIN)), __('Bearbeiten', WPVGW_TEXT_DOMAIN));
         }
         if (!$isMarkerAddedToPost) {
             $postTitleOutput = __('Keine Seite zugeordnet', WPVGW_TEXT_DOMAIN);
         } elseif ($isPostDeleted) {
             $postTitleOutput = sprintf(__('gelöscht (%s)', WPVGW_TEXT_DOMAIN), $row['deleted_post_title'] === null ? __('unbekannt', WPVGW_TEXT_DOMAIN) : esc_html($row['deleted_post_title'] === '' ? __('kein Titel)', WPVGW_TEXT_DOMAIN) : $row['deleted_post_title']));
         } elseif ($isPostNotFound) {
             $postTitleOutput = sprintf($this->abbrTemplate, __('Diese Zählmarke wurde einer Seite zugeordnet, der nicht existiert, aber existieren sollte.', WPVGW_TEXT_DOMAIN), sprintf('<strong>' . $this->invalidDataTemplate . '</strong>', __('gelöscht? (Titel unbekannt)', WPVGW_TEXT_DOMAIN)));
         } else {
             $postTitleOutput = sprintf('<strong><span class="%s" title="%s"><a id="%s" href="%s" target="_blank">%s</a></span></strong>', $row['is_marker_disabled'] ? 'wpvgw-marker-disabled' : 'wpvgw-marker-enabled', $row['is_marker_disabled'] ? __('Die Zugriffe auf dieser Seite werden nicht gezählt', WPVGW_TEXT_DOMAIN) : '', esc_attr(WPVGW . '-markers-view-post-title-link-' . $row['post_id']), esc_attr(($row['post_type'] !== null && WPVGW_Helper::is_media($row['post_type'])) ? wp_get_attachment_url($row['post_id']) : get_permalink($row['post_id'])), esc_html($row['post_title'] === '' ? __('(kein Titel)', WPVGW_TEXT_DOMAIN) : $row['post_title']));
         }
         $postTypeOutput = '';
         $isPostTypeKnown = $this->is_post_type_known($row);
         if ($isPostTypeKnown === false) {
             $postTypeOutput = '<br/>' . sprintf($this->abbrTemplate, __('Die Seite, der dieser Zählmarke zugeordnete ist, ist von einem Seitentypen, der unbekannt ist.', WPVGW_TEXT_DOMAIN), sprintf($this->invalidDataTemplate, sprintf(__('Seitentyp unbekannt (%s)', WPVGW_TEXT_DOMAIN), esc_html($row['post_type']))));
         } elseif ($isPostTypeKnown === true) {
             $postTypeObject = get_post_type_object($row['post_type']);
             $postTypeOutput = '<br/>' . esc_html($postTypeObject->labels->singular_name) . ($this->is_post_type_allowed($row) ? '' : sprintf($this->abbrTemplate, __('Die Seite, der dieser Zählmarke zugeordnete ist, ist von einem Seitentypen, der für Zählmarken nicht zugelassen wurde.', WPVGW_TEXT_DOMAIN), sprintf('<br/>' . $this->invalidDataTemplate, __('Seitentyp nicht zugelassen', WPVGW_TEXT_DOMAIN))));
         }
         $attachmentOutput = '';
         if ($row['post_type'] !== null && WPVGW_Helper::is_media($row['post_type']) && !$isMediaAttachedToPost) {
             $attachmentOutput = '<br/>' . sprintf($this->abbrTemplate, __('Diese Zählmarke wurde einer Datei zugeordnet, die mit keiner Seite verknüpft ist.', WPVGW_TEXT_DOMAIN), sprintf('<strong>' . $this->invalidDataTemplate . '</strong>', __('Seitenverknüpfung fehlt', WPVGW_TEXT_DOMAIN)));
         }
         $isPublishedOutput = '';
         if ($isPublished === false) {
             $isPublishedOutput = '<br/>' . sprintf($this->abbrTemplate, __('Die Seite ist nicht veröffentlicht, und Zugriffe werden daher nicht gezählt.', WPVGW_TEXT_DOMAIN), sprintf($this->invalidDataTemplate, __('nicht veröffentlicht', WPVGW_TEXT_DOMAIN)));
         }
         return $postTitleOutput . $postTypeOutput . $attachmentOutput . $isPublishedOutput . $this->row_actions($actions);
     }
     protected function column_marker_state($row)
     {
         $stats = array();
         if ($this->is_marker_added_to_post($row)) {
             $stats[] = sprintf($this->abbrTemplate, __('Diese Zählmarke ist einer Seite zugeordnet.', WPVGW_TEXT_DOMAIN), __('zugeordnet', WPVGW_TEXT_DOMAIN));
         } else {
             $stats[] = sprintf($this->abbrTemplate, __('Diese Zählmarke ist keiner Seite zugeordnet.', WPVGW_TEXT_DOMAIN), __('nicht zugeordnet', WPVGW_TEXT_DOMAIN));
         }
         $isDisabled = $this->is_marker_disabled($row);
         if ($isDisabled === null) {
             $stats[] = sprintf($this->abbrTemplate, __('Eine Zählmarke darf nicht inaktiv sein, wenn ihr keine Seite zugeordnet wurde. Sie sollte auf aktiv gesetzt werden.', WPVGW_TEXT_DOMAIN), sprintf($this->invalidDataTemplate, __('inaktiv', WPVGW_TEXT_DOMAIN)));
         } elseif ($isDisabled === true) {
             $stats[] = sprintf($this->abbrTemplate, __('Diese Zählmarke wird nicht ausgegeben (keine Zählung bei VG WORT).', WPVGW_TEXT_DOMAIN), __('inaktiv', WPVGW_TEXT_DOMAIN));
         }
         $isBlocked = $this->is_marker_blocked($row);
         if ($isBlocked === null) {
             $stats[] = sprintf($this->abbrTemplate, __('Eine Zählmarke muss zuordenbar sein, wenn ihr eine Seite zugeordnet ist. Sie sollte auf zuordenbar gesetzt werden.', WPVGW_TEXT_DOMAIN), sprintf($this->invalidDataTemplate, __('nicht zuordenbar', WPVGW_TEXT_DOMAIN)));
         } elseif ($isBlocked) {
             $stats[] = sprintf($this->abbrTemplate, __('Diese Zählmarke kann keiner Seite zugeordnet werden.', WPVGW_TEXT_DOMAIN), __('nicht zuordenbar', WPVGW_TEXT_DOMAIN));
         }
         return implode('<br/> ', $stats);
     }
     protected function column_post_date($row)
     {
         if ($row['post_date'] === null) {
             return WPVGW_Helper::null_data_text();
         }
         return sprintf(__('%s', WPVGW_TEXT_DOMAIN), esc_html(date_i18n(__('d.m.Y', WPVGW_TEXT_DOMAIN), strtotime($row['post_date']))));
     }
     protected function column_up_display_name($row)
     {
         if ($row['post_author'] === null) {
             return WPVGW_Helper::null_data_text();
         }
         $editUserLink = get_edit_user_link($row['user_id']);
         if (($row['up_display_name'] === null && $row['post_id'] !== null) || $editUserLink === '') {
             return sprintf($this->abbrTemplate, __('Der Seitenautor konnte nicht gefunden werden und wurde eventuell gelöscht.', WPVGW_TEXT_DOMAIN), sprintf($this->invalidDataTemplate, __('gelöscht?', WPVGW_TEXT_DOMAIN)));
         }
         $invalidUserMessage = '';
         if (!$this->markersManager->is_user_allowed((int)$row['post_author'])) {
             $invalidUserMessage = sprintf($this->abbrTemplate, __('Der Seitenautor wurde nicht für Zählmarken zugelassen.', WPVGW_TEXT_DOMAIN), sprintf('<br/>' . $this->invalidDataTemplate, __('Autor nicht zugelassen', WPVGW_TEXT_DOMAIN)));
         }
         return sprintf('<a href="%s">%s</a>%s', esc_attr($editUserLink), esc_html($row['up_display_name']), $invalidUserMessage);
     }
     protected function column_um_display_name($row)
     {
         if ($row['user_id'] === null) {
             return __('beliebiger', WPVGW_TEXT_DOMAIN);
         }
         $editUserLink = get_edit_user_link($row['user_id']);
         if ($row['um_display_name'] === null || $editUserLink === '') {
             return sprintf($this->abbrTemplate, __('Der Für-Autor konnte nicht gefunden werden und wurde eventuell gelöscht.', WPVGW_TEXT_DOMAIN), sprintf($this->invalidDataTemplate, __('gelöscht?', WPVGW_TEXT_DOMAIN)));
         }
         $invalidUserMessage = '';
         if (!$this->markersManager->is_user_allowed((int)$row['user_id'])) {
             $invalidUserMessage = sprintf($this->abbrTemplate, __('Der Für-Autor wurde nicht für Zählmarken zugelassen.', WPVGW_TEXT_DOMAIN), sprintf('<br/>' . $this->invalidDataTemplate, __('Autor nicht zugelassen', WPVGW_TEXT_DOMAIN)));
         }
         return sprintf('<a href="%s">%s</a>%s', esc_attr($editUserLink), esc_html($row['um_display_name']), $invalidUserMessage);
     }
     protected function column_order_date($row)
     {
         if ($row['order_date'] === null) {
             return WPVGW_Helper::null_data_text();
         }
         $orderDate = new DateTime($row['order_date'], WPVGW_Helper::get_vg_wort_time_zone());
         return sprintf(__('%s', WPVGW_TEXT_DOMAIN), esc_html($orderDate->format(WPVGW_Helper::get_vg_wort_order_date_format())));
     }
     protected function column_marker($row)
     {
         return sprintf('<span class="%s" title="%s">
                            <abbr title="%s">%s</abbr>:&nbsp;%s<br/>
                            <abbr title="%s">%s</abbr>:&nbsp;<span id="%s">%s</span><br/>
                            <abbr title="%s">%s</abbr>:&nbsp;%s<br/>
                            <abbr title="%s">%s</abbr>:&nbsp;%s
                         </span>', $row['is_marker_disabled'] ? 'wpvgw-marker-disabled' : 'wpvgw-marker-enabled', $row['is_marker_disabled'] ? __('Zählmarke ist inaktiv', WPVGW_TEXT_DOMAIN) : '', __('Öffentliche Zählmarke', WPVGW_TEXT_DOMAIN), __('Ö', WPVGW_TEXT_DOMAIN), esc_html($row['public_marker']), __('Private Zählmarke', WPVGW_TEXT_DOMAIN), __('P', WPVGW_TEXT_DOMAIN), esc_attr(WPVGW . '-markers-view-private-marker-' . $row['id']), esc_html(WPVGW_Helper::null_data_text($row['private_marker'])), __('Server', WPVGW_TEXT_DOMAIN), __('S', WPVGW_TEXT_DOMAIN), esc_html($row['server']), __('Übertragungsverschlüsselung', WPVGW_TEXT_DOMAIN), __('V', WPVGW_TEXT_DOMAIN), $this->options->get_use_tls() ? __('verschlüsselt (TLS/HTTPS)', WPVGW_TEXT_DOMAIN) : __('unverschlüsselt (HTTP)', WPVGW_TEXT_DOMAIN));
     }
     protected function column_e_character_count($row)
     {
         if ($row['post_type'] !== null && WPVGW_Helper::is_media($row['post_type'])) {
             return __('nicht ermittelbar', WPVGW_TEXT_DOMAIN);
         }
         if ($row['e_character_count'] === null) {
             if ($row['is_post_deleted'] === '1' || $row['post_id'] === null) {
                 return WPVGW_Helper::null_data_text();
             }
             return __('nicht berechnet', WPVGW_TEXT_DOMAIN);
         }
         $characterCount = (int)$row['e_character_count'];
         if ($this->markersManager->is_character_count_sufficient($characterCount, $this->options->get_vg_wort_minimum_character_count())) {
             return sprintf(__('genügend, %s', WPVGW_TEXT_DOMAIN), number_format_i18n($characterCount));
         } else {
             return sprintf($this->abbrTemplate, sprintf(__('Diese Seite enthält weniger als %s Zeichen und verstößt damit gegen die von der VG WORT vorgegebene Mindestzeichenanzahl.', WPVGW_TEXT_DOMAIN), number_format_i18n($this->options->get_vg_wort_minimum_character_count())), sprintf($this->invalidDataTemplate, sprintf(__('zu wenig, %s', WPVGW_TEXT_DOMAIN), number_format_i18n($characterCount))));
         }
     }
     public function get_columns()
     {
         return $this->columns;
     }
     protected function get_sortable_columns()
     {
         return $this->sortableColumnsWithSlugs;
     }
     public function display()
     {
         $oldRequestUri = $_SERVER['REQUEST_URI'];
         $_SERVER['REQUEST_URI'] = remove_query_arg('_wp_http_referer', $_SERVER['REQUEST_URI']);
         parent::display();
         $_SERVER['REQUEST_URI'] = $oldRequestUri;
     }
     protected function extra_tablenav($which)
     {
         if ($which !== 'top') {
             return;
         } ?>
		<div class="alignleft actions"><?php
 WPVGW_Helper::render_html_selects($this->filterableColumnsSelects);
         submit_button(__('Auswählen', WPVGW_TEXT_DOMAIN), 'button', WPVGW . '_filter_submit', false, array( 'id' => WPVGW . '_filter_submit' )); ?></div><?php
     }
     protected function get_bulk_actions()
     {
         return $this->bulkActions;
     }
     protected function get_views()
     {
         return $this->viewLinks;
     }
     private function prepare_items_internal($current_page, $rows_per_page, $include_post_content, $get_total_markers_count, &$total_markers_count)
     {
         $orderBy = $_REQUEST['orderby'] ?? 'id';
         $order = $_REQUEST['order'] ?? 'asc';
         $searchString = $_REQUEST['s'] ?? null;
         $filters = array();
         foreach ($this->dataRetriever->get_filters() as $key => $filter) {
             $requestKey = WPVGW . '_' . $key;
             if (isset($_REQUEST[$requestKey])) {
                 $index = intval($_REQUEST[$requestKey]);
                 if ($index >= 1 && array_key_exists($index, $this->filterableColumnsSelects[$key])) {
                     $filters[$key] = array( 'parameter' => $this->filterableColumnsSelects[$key][$index]['parameter'], 'args' => $this->filterableColumnsSelects[$key][$index]['args'] );
                 }
             }
         }
         return $this->dataRetriever->get_data_rows($current_page, $rows_per_page, $include_post_content, $get_total_markers_count, $total_markers_count, $filters, $searchString, $orderBy, $order);
     }
     public function prepare_items()
     {
         $rowsPerPage = $this->options->get_number_of_markers_per_page();
         $this->_column_headers = $this->get_column_info();
         $current_page = $this->get_pagenum();
         $markers = $this->prepare_items_internal($current_page, $rowsPerPage, false, true, $totalMarkersCount);
         $this->items = $markers;
         $this->set_pagination_args(array( 'total_items' => $totalMarkersCount, 'per_page' => $rowsPerPage, 'total_pages' => ceil($totalMarkersCount / $rowsPerPage) ));
     }
     public function get_all_items($include_post_content)
     {
         return $this->prepare_items_internal(null, null, $include_post_content, false, $totalMarkersCount);
     }
 }
