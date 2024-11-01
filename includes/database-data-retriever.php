<?php
/**
 * Product: Prosodia VGW OS
 * URL: https://prosodia.de/
 * Author: Dr. Ronny Harbich
 * Copyright: Dr. Ronny Harbich
 * License: GPLv2 or later
 */
 class WPVGW_DatabaseDataRetriever
 {
     private $markersManager;
     private $postsExtras;
     private $options;
     private $userOptions;
     private $sortableColumns;
     private $filters;
     public function get_sortable_columns(): array
     {
         return $this->sortableColumns;
     }
     public function get_filters(): array
     {
         return $this->filters;
     }
     public function __construct(WPVGW_MarkersManager $markers_manager, WPVGW_PostsExtras $posts_extras, WPVGW_Options $options, WPVGW_UserOptions $user_options)
     {
         $this->sortableColumns = array( 'id', 'post_title', 'post_date', 'um_display_name', 'up_display_name', 'order_date', 'e_character_count', );
         $allowedPostTypes = $markers_manager->get_allowed_post_types();
         if (empty($allowedPostTypes)) {
             $sqlAllowedPostTypesSql = '" "';
         } else {
             $sqlAllowedPostTypesSql = WPVGW_Helper::prepare_with_null(WPVGW_Helper::sql_values($allowedPostTypes), $allowedPostTypes);
         }
         $characterCountSufficientSql = $markers_manager->is_character_count_sufficient_sql('e.character_count', $options->get_vg_wort_minimum_character_count());
         $validMarkerFormatSql = sprintf('(%s) AND (%s)', $markers_manager->has_valid_marker_format_sql('m.public_marker'), $markers_manager->has_valid_marker_format_sql('m.private_marker'));
         $this->filters = array( 'marker' => array( 'by_public' => [ 'where' => 'm.public_marker = %s', 'args_count' => 1 ], 'by_private' => [ 'where' => 'm.private_marker = %s', 'args_count' => 1 ], ), 'has_marker' => array( 'true' => [ 'where' => 'm.post_id IS NOT NULL', 'args_count' => 0 ], 'false' => [ 'where' => 'm.post_id IS NULL', 'args_count' => 0 ], ), 'marker_disabled' => array( 'false' => [ 'where' => 'm.is_marker_disabled = 0', 'args_count' => 0 ], 'true' => [ 'where' => 'm.is_marker_disabled = 1', 'args_count' => 0 ], ), 'marker_blocked' => array( 'false' => [ 'where' => 'm.is_marker_blocked = 0', 'args_count' => 0 ], 'true' => [ 'where' => 'm.is_marker_blocked = 1', 'args_count' => 0 ], ), 'invalid_markers' => array( 'false' => [ 'where' => $validMarkerFormatSql, 'args_count' => 0 ], 'true' => [ 'where' => "NOT ($validMarkerFormatSql)", 'args_count' => 0 ], ), 'post_author' => array( 'deleted' => [ 'where' => 'p.post_author IS NOT NULL AND up.ID IS NULL', 'args_count' => 0 ], 'by_id' => [ 'where' => 'p.post_author = %d', 'args_count' => 1 ], ), 'post_type' => array( 'allowed' => [ 'where' => "p.post_type IN ($sqlAllowedPostTypesSql)", 'args_count' => 0 ], 'not_allowed' => [ 'where' => "p.post_type NOT IN ($sqlAllowedPostTypesSql)", 'args_count' => 0 ], 'by_type' => [ 'where' => 'p.post_type = %s', 'args_count' => 1 ], ), 'post' => array( 'not_deleted' => [ 'where' => 'm.is_post_deleted = 0', 'args_count' => 0 ], 'deleted' => [ 'where' => 'm.is_post_deleted = 1', 'args_count' => 0 ], 'is_media_attached_to_post' => [ 'where' => 'p.post_type = \'attachment\' AND post_parent >= 1', 'args_count' => 0 ], 'not_is_media_attached_to_post' => [ 'where' => 'p.post_type = \'attachment\' AND post_parent <= 0', 'args_count' => 0 ], ), 'sufficient_characters' => array( 'true' => [ 'where' => $characterCountSufficientSql, 'args_count' => 0 ], 'false' => [ 'where' => "NOT ($characterCountSufficientSql)", 'args_count' => 0 ], ), 'post_date' => array( 'by_before_date' => [ 'where' => 'DATE(p.post_date) <= %s', 'args_count' => 1 ], 'by_after_date' => [ 'where' => 'DATE(p.post_date) >= %s', 'args_count' => 1 ], 'by_between_dates' => [ 'where' => 'DATE(p.post_date) >= %s AND DATE(p.post_date) <= %s', 'args_count' => 2 ], ), 'order_date' => array( 'by_before_date' => [ 'where' => 'm.order_date <= %s', 'args_count' => 1 ], 'by_after_date' => [ 'where' => 'm.order_date >= %s', 'args_count' => 1 ], 'by_between_dates' => [ 'where' => 'm.order_date >= %s AND m.order_date <= %s', 'args_count' => 2 ], ), );
         $this->markersManager = $markers_manager;
         $this->postsExtras = $posts_extras;
         $this->options = $options;
         $this->userOptions = $user_options;
     }
     public function get_data_rows($current_page, $rows_per_page, $include_post_content, $get_total_rows_count, &$total_rows_count, array $filters = array(), $search_string = '', $order_by = '', $order = 'asc')
     {
         global $wpdb;
         if (($current_page !== null && $rows_per_page === null) || ($current_page === null && $rows_per_page !== null) || !WPVGW_Helper::is_null_or_int($current_page) || !WPVGW_Helper::is_null_or_int($rows_per_page)) {
             throw new InvalidArgumentException('The arguments $current_page and $rowsPerPage have to be integers or both null.');
         }
         if (WPVGW_Helper::is_null_or_empty($order_by) || !WPVGW_Helper::in_array($order_by, $this->sortableColumns)) {
             $order_by = 'id';
         }
         if ($order !== 'asc' && $order !== 'desc') {
             $order = 'asc';
         }
         if (!WPVGW_Helper::is_null_or_empty($search_string)) {
             $vgWortOrderDateSqlFormat = WPVGW_Helper::get_vg_wort_order_date_sql_format();
             $safeSearchString = WPVGW_Helper::prepare_with_null('%s', '%' . trim($search_string) . '%');
             $sqlWhere = "
				p.post_title LIKE $safeSearchString OR
				DATE_FORMAT(p.post_date, '%d.%m.%Y') LIKE $safeSearchString OR
				m.public_marker LIKE $safeSearchString OR
				m.private_marker LIKE $safeSearchString OR
				m.server LIKE $safeSearchString OR
				DATE_FORMAT(m.order_date, '$vgWortOrderDateSqlFormat') LIKE $safeSearchString
				";
         } else {
             $sqlWhere = '0=0';
         }
         $sqlFilters = '';
         foreach ($this->filters as $key => $filter) {
             if (array_key_exists($key, $filters)) {
                 $currentFilterParameter = $filters[$key]['parameter'];
                 $currentFilterValues = $filters[$key]['args'];
                 if (array_key_exists($currentFilterParameter, $filter)) {
                     if ($filter[$currentFilterParameter]['args_count'] <= 0) {
                         $currentWhere = $filter[$currentFilterParameter]['where'];
                     } else {
                         if (count($currentFilterValues) !== $filter[$currentFilterParameter]['args_count']) {
                             throw new InvalidArgumentException('Parameter count of filter does not match.');
                         }
                         $currentWhere = WPVGW_Helper::prepare_with_null($filter[$currentFilterParameter]['where'], $currentFilterValues);
                     }
                     $sqlFilters .= sprintf(' AND (%s)', $currentWhere);
                 }
             }
         }
         $sqlWhere = sprintf('(%s) %s', $sqlWhere, $sqlFilters);
         $markersTableName = $this->markersManager->get_markers_table_name();
         $postExtrasTableName = $this->postsExtras->get_post_extras_table_name();
         $postContentColumn = ($include_post_content ? 'p.post_content, ' : '');
         $sqlSelect = "
			m.id, m.post_id, m.user_id, m.public_marker, m.private_marker, m.server, m.order_date, m.is_marker_disabled, m.is_marker_blocked, m.is_post_deleted, m.deleted_post_title,
			p.post_author, p.post_title, p.post_type, DATE(p.post_date) as post_date, p.post_status, p.post_parent, $postContentColumn
			e.character_count as e_character_count,
			um.display_name as um_display_name,
			up.display_name as up_display_name,
			ummf.meta_value as ummf_first_name,
			umml.meta_value as umml_last_name,
			umpf.meta_value as umpf_first_name,
			umpl.meta_value as umpl_last_name
			";
         $sqlFrom = "
			$markersTableName AS m
			LEFT OUTER JOIN $wpdb->posts AS p ON m.post_id = p.ID
			LEFT OUTER JOIN $postExtrasTableName AS e ON m.post_id = e.post_id
			LEFT OUTER JOIN $wpdb->users AS um ON m.user_id = um.ID
			LEFT OUTER JOIN $wpdb->users AS up ON p.post_author = up.ID
			LEFT OUTER JOIN $wpdb->usermeta AS ummf ON m.user_id = ummf.user_id AND (ummf.meta_key LIKE 'first_name')
			LEFT OUTER JOIN $wpdb->usermeta AS umml ON m.user_id = umml.user_id AND (umml.meta_key LIKE 'last_name')
			LEFT OUTER JOIN $wpdb->usermeta AS umpf ON p.post_author = umpf.user_id AND (umpf.meta_key LIKE 'first_name')
			LEFT OUTER JOIN $wpdb->usermeta AS umpl ON p.post_author = umpl.user_id AND (umpl.meta_key LIKE 'last_name')
			";
         $limit = '';
         if ($current_page !== null) {
             $current_page = $current_page <= 0 ? 1 : $current_page;
             $rows_per_page = $rows_per_page <= 0 ? 1 : $rows_per_page;
             $limit = sprintf('LIMIT %s, %s', ($current_page - 1) * $rows_per_page, $rows_per_page);
         }
         WPVGW_Helper::sql_set_big_selects(true);
         $rows = $wpdb->get_results("SELECT $sqlSelect FROM $sqlFrom WHERE $sqlWhere ORDER BY $order_by $order $limit", ARRAY_A);
         if ($wpdb->last_error !== '') {
             WPVGW_Helper::throw_database_exception();
         }
         if ($get_total_rows_count) {
             $total_rows_count = (int)$wpdb->get_var("SELECT COUNT(*) FROM $sqlFrom WHERE $sqlWhere");
         } else {
             $total_rows_count = null;
         }
         if ($wpdb->last_error !== '') {
             WPVGW_Helper::throw_database_exception();
         }
         WPVGW_Helper::sql_set_big_selects(false);
         return $rows;
     }
 }
