<?php
/**
 * Product: Prosodia VGW OS
 * URL: https://prosodia.de/
 * Author: Dr. Ronny Harbich
 * Copyright: Dr. Ronny Harbich
 * License: GPLv2 or later
 */
 class WPVGW_MarkersManager
 {
     private $markersTableName;
     private $allowedUserRoles;
     private $allowedPostStatuses = array( 'publish', 'pending', 'draft', 'future', 'private', 'trash' );
     private $possiblePostTypes;
     private $allowedPostTypes;
     private $removedPostTypes;
     private $doShortcodesForCharacterCountCalculation;
     private $considerExcerptForCharacterCountCalculation;
     public function get_markers_table_name()
     {
         return $this->markersTableName;
     }
     public function get_allowed_post_statuses()
     {
         return $this->allowedPostStatuses;
     }
     public function get_allowed_post_types()
     {
         return $this->allowedPostTypes;
     }
     public function set_allowed_post_types($value)
     {
         $this->allowedPostTypes = $value;
         $this->build_valid_post_type_arrays();
     }
     public function get_removed_post_types()
     {
         return $this->removedPostTypes;
     }
     public function set_removed_post_types($value)
     {
         $this->removedPostTypes = $value;
         $this->build_valid_post_type_arrays();
     }
     public function get_possible_post_types()
     {
         return $this->possiblePostTypes;
     }
     public function __construct($markers_table_name, $allowed_user_roles, $allowed_post_types, $removed_post_types, $do_shortcodes_for_character_count_calculation, $considerExcerptForCharacterCountCalculation)
     {
         $this->markersTableName = $markers_table_name;
         $this->allowedPostTypes = $allowed_post_types;
         $this->removedPostTypes = $removed_post_types;
         $this->allowedUserRoles = $allowed_user_roles;
         $this->possiblePostTypes = array_merge(array( 'post', 'page', 'attachment' ), array_values(get_post_types(array( 'public' => true, 'show_ui' => true, '_builtin' => false ))));
         $this->build_valid_post_type_arrays();
         $this->doShortcodesForCharacterCountCalculation = $do_shortcodes_for_character_count_calculation;
         $this->considerExcerptForCharacterCountCalculation = $considerExcerptForCharacterCountCalculation;
     }
     private function build_valid_post_type_arrays()
     {
         foreach ($this->allowedPostTypes as $key => $allowedPostType) {
             if (!WPVGW_Helper::in_array($allowedPostType, $this->possiblePostTypes)) {
                 unset($this->allowedPostTypes[$key]);
                 $this->removedPostTypes[] = $allowedPostType;
             }
         }
         foreach ($this->removedPostTypes as $key => $removedPostType) {
             if (WPVGW_Helper::in_array($removedPostType, $this->possiblePostTypes)) {
                 unset($this->removedPostTypes[$key]);
                 $this->allowedPostTypes[] = $removedPostType;
             }
         }
     }
     public function is_post_type_possible($post_type)
     {
         return WPVGW_Helper::in_array($post_type, $this->possiblePostTypes);
     }
     public function is_post_type_allowed($post_type)
     {
         return WPVGW_Helper::in_array($post_type, $this->allowedPostTypes);
     }
     public function is_user_allowed($user_id)
     {
         if ($user_id === null) {
             return true;
         }
         $user = get_userdata($user_id);
         if ($user === false) {
             return false;
         }
         return $this->is_user_role_allowed($user->roles);
     }
     private function is_user_role_allowed($roles)
     {
         if (is_array($roles)) {
             return count(array_intersect($this->allowedUserRoles, $roles)) > 0;
         }
         return WPVGW_Helper::in_array($roles, $this->allowedUserRoles);
     }
     public function is_post_status_allowed($post_status)
     {
         return WPVGW_Helper::in_array($post_status, $this->allowedPostStatuses);
     }
     public function calculate_character_count($post_title, $post_content, $post_excerpt, WP_Post $post = null, $ajax_custom_object = null, $additional_texts = array())
     {
         $returnCustomCount = null;
         do_action_ref_array(WPVGW . '_calculate_character_count', array( $post_title, $post_content, $post_excerpt, $this, $post, $ajax_custom_object, &$returnCustomCount ));
         if (is_int($returnCustomCount)) {
             return $returnCustomCount;
         }
         $post_title = $this->clean_word_press_text($post_title, true);
         $post_content = $this->clean_word_press_text($post_content);
         if ($this->considerExcerptForCharacterCountCalculation) {
             $post_excerpt = $this->clean_word_press_text($post_excerpt);
         } else {
             $post_excerpt = '';
         }
         $returnCustomCount = 0;
         do_action_ref_array(WPVGW . '_add_character_count', array( $this, $post, $additional_texts, &$returnCustomCount ));
         return (mb_strlen($post_title) + mb_strlen($post_content) + mb_strlen($post_excerpt) + $returnCustomCount);
     }
     public function clean_word_press_text($text, $is_title = false)
     {
         return WPVGW_Helper::clean_word_press_text($text, $this->doShortcodesForCharacterCountCalculation, $is_title);
     }
     public function cleanWordPressText($text, $is_title = false)
     {
         return WPVGW_Helper::clean_word_press_text($text, $this->doShortcodesForCharacterCountCalculation, $is_title);
     }
     public function is_character_count_sufficient($character_count, $minimum_character_count)
     {
         return $character_count >= $minimum_character_count;
     }
     public function is_character_count_sufficient_sql($character_count_column, $minimum_character_count)
     {
         return $character_count_column . ' >= ' . $minimum_character_count;
     }
     public function calculate_missing_character_count($character_count, $minimum_character_count)
     {
         if ($character_count < $minimum_character_count) {
             return $minimum_character_count - $character_count;
         } else {
             return 0;
         }
     }
     public function has_valid_marker_format_sql($marker_column)
     {
         return "BINARY($marker_column) REGEXP '^[a-z0-9]{32}$'";
     }
     public function key_exists_in_db($key, $column)
     {
         global $wpdb;
         if ($key === null) {
             return false;
         }
         $formatLiteral = WPVGW_Helper::get_format_literal($key);
         $exists = $wpdb->get_var(WPVGW_Helper::prepare_with_null("SELECT EXISTS(SELECT 1 FROM $this->markersTableName WHERE $column = $formatLiteral)", $key));
         if ($exists === null) {
             WPVGW_Helper::throw_database_exception();
         }
         return (bool)$exists;
     }
     private function make_marker_typesafe(array &$marker)
     {
         $marker['id'] = (int)$marker['id'];
         $marker['post_id'] = $marker['post_id'] === null ? null : (int)$marker['post_id'];
         $marker['user_id'] = null;
         $marker['order_date'] = $marker['order_date'] === null ? null : new DateTime($marker['order_date'], WPVGW_Helper::get_vg_wort_time_zone());
         $marker['is_marker_disabled'] = (bool)$marker['is_marker_disabled'];
         $marker['is_marker_blocked'] = (bool)$marker['is_marker_blocked'];
         $marker['is_post_deleted'] = (bool)$marker['is_post_deleted'];
     }
     public function get_marker_from_db($key, $column)
     {
         global $wpdb;
         if ($key === null) {
             return false;
         }
         $formatLiteral = WPVGW_Helper::get_format_literal($key);
         $marker = $wpdb->get_row(WPVGW_Helper::prepare_with_null("SELECT * FROM $this->markersTableName WHERE $column = $formatLiteral LIMIT 1", $key), ARRAY_A);
         if ($wpdb->last_error !== '') {
             WPVGW_Helper::throw_database_exception();
         }
         if ($marker === null) {
             return false;
         }
         $this->make_marker_typesafe($marker);
         return $marker;
     }
     public function get_markers_from_db($current_page, $rows_per_page)
     {
         global $wpdb;
         if (!is_int($current_page) || $current_page <= 0) {
             return false;
         }
         if (!is_int($rows_per_page) || $rows_per_page <= 0) {
             return false;
         }
         $limit = sprintf('%s, %s', ($current_page - 1) * $rows_per_page, $rows_per_page);
         $markers = $wpdb->get_results("SELECT * FROM $this->markersTableName LIMIT $limit", ARRAY_A);
         if ($wpdb->last_error !== '') {
             WPVGW_Helper::throw_database_exception();
         }
         if ($markers === null) {
             return false;
         }
         for ($i = 0; $i <= count($markers) - 1; $i++) {
             $this->make_marker_typesafe($markers[$i]);
         }
         return $markers;
     }
     public function get_free_marker_from_db()
     {
         global $wpdb;
         $marker = $wpdb->get_row("SELECT * FROM $this->markersTableName WHERE post_id IS NULL AND is_post_deleted = 0 AND is_marker_blocked = 0 LIMIT 1", ARRAY_A);
         if ($wpdb->last_error !== '') {
             WPVGW_Helper::throw_database_exception();
         }
         if ($marker === null) {
             return false;
         }
         $this->make_marker_typesafe($marker);
         return $marker;
     }
     public function remove_post_from_marker_in_db($id, $id_type = 'post')
     {
         if ($id === null || !is_int($id) || $id < 0) {
             throw new Exception('ID must be a non-negative integer.');
         }
         switch ($id_type) { case 'post': $keyColumn = 'post_id'; break; case 'marker': $keyColumn = 'id'; break; default: throw new Exception('Invalid ID type.'); }
         return $this->update_marker_in_db($id, $keyColumn, array( 'post_id' => null, 'is_marker_disabled' => false, 'is_marker_blocked' => false, 'is_post_deleted' => false, 'deleted_post_title' => null, ), array(), array( 'post_id' => null )) === WPVGW_UpdateMarkerResults::Updated;
     }
     private function is_marker_integrity_broken($key_column, $column, $value, array $oldMarker)
     {
         return $column !== $key_column && $value !== null && !WPVGW_Helper::strictly_equal($oldMarker[$column], $value) && $this->key_exists_in_db($value, $column);
     }
     public function update_marker_in_db($key, $key_column, array $update_marker, array $conditions = array(), array $negativConditions = array())
     {
         global $wpdb;
         if (count($update_marker) < 1) {
             throw new Exception('Too few elements in update marker.');
         }
         if ($key_column !== 'id' && $key_column !== 'post_id' && $key_column !== 'public_marker' && $key_column !== 'private_marker') {
             throw new Exception('Key column has an invalid column name.');
         }
         $oldMarker = $this->get_marker_from_db($key, $key_column);
         if ($oldMarker === false) {
             return WPVGW_UpdateMarkerResults::MarkerNotFound;
         }
         foreach ($conditions as $aKey => $value) {
             if (!array_key_exists($aKey, $oldMarker)) {
                 throw new Exception('Key in conditions does not exist in marker.');
             }
             if (is_array($value)) {
                 if (!WPVGW_Helper::in_array($oldMarker[$aKey], $value)) {
                     return WPVGW_UpdateMarkerResults::MarkerNotFound;
                 }
             } elseif (!WPVGW_Helper::strictly_equal($oldMarker[$aKey], $value)) {
                 return WPVGW_UpdateMarkerResults::MarkerNotFound;
             }
         }
         foreach ($negativConditions as $aKey => $value) {
             if (!array_key_exists($aKey, $oldMarker)) {
                 throw new Exception('Key in negative conditions does not exist in marker.');
             }
             if (is_array($value)) {
                 if (WPVGW_Helper::in_array($oldMarker[$aKey], $value)) {
                     return WPVGW_UpdateMarkerResults::MarkerNotFound;
                 }
             } elseif (WPVGW_Helper::strictly_equal($oldMarker[$aKey], $value)) {
                 return WPVGW_UpdateMarkerResults::MarkerNotFound;
             }
         }
         $newMarker = array_merge($oldMarker, $update_marker);
         if ($oldMarker['post_id'] !== null && array_key_exists('post_id', $update_marker) && $update_marker['post_id'] !== null && !WPVGW_Helper::strictly_equal($update_marker['post_id'], $oldMarker['post_id'])) {
             return WPVGW_UpdateMarkerResults::PostIdNotNull;
         }
         if ($newMarker['post_id'] !== null && $newMarker['is_marker_blocked'] === true) {
             return WPVGW_UpdateMarkerResults::BlockedMarkerNotPossible;
         }
         if ($newMarker['post_id'] === null && $newMarker['is_marker_disabled'] === true) {
             return WPVGW_UpdateMarkerResults::DisabledMarkerNotPossible;
         }
         if (array_key_exists('post_id', $update_marker) && $this->is_marker_integrity_broken($key_column, 'post_id', $update_marker['post_id'], $oldMarker)) {
             return WPVGW_UpdateMarkerResults::PostIdExists;
         }
         if (array_key_exists('public_marker', $update_marker)) {
             if ($update_marker['public_marker'] === null) {
                 throw new Exception('Public marker must not be null.');
             }
             if ($this->is_marker_integrity_broken($key_column, 'public_marker', $update_marker['public_marker'], $oldMarker)) {
                 return WPVGW_UpdateMarkerResults::PublicMarkerExists;
             }
         }
         if (array_key_exists('private_marker', $update_marker) && $this->is_marker_integrity_broken($key_column, 'private_marker', $update_marker['private_marker'], $oldMarker)) {
             return WPVGW_UpdateMarkerResults::PrivateMarkerExists;
         }
         if (WPVGW_Helper::array_contains($update_marker, $oldMarker)) {
             return WPVGW_UpdateMarkerResults::UpdateNotNecessary;
         }
         unset($update_marker['id']);
         $setters = WPVGW_Helper::sql_setters($update_marker);
         $update_marker[] = $key;
         $keyFormatLiteral = WPVGW_Helper::get_format_literal($key);
         $success = $wpdb->query(WPVGW_Helper::prepare_with_null("UPDATE $this->markersTableName SET $setters WHERE $key_column = $keyFormatLiteral LIMIT 1", WPVGW_Helper::sql_array_integral_values($update_marker)));
         if ($success === false) {
             WPVGW_Helper::throw_database_exception();
         }
         return WPVGW_UpdateMarkerResults::Updated;
     }
     public function delete_marker_in_db($marker_id)
     {
         global $wpdb;
         if (!(is_int($marker_id) && $marker_id >= 0)) {
             return false;
         }
         $markerToDelete = $this->get_marker_from_db($marker_id, 'id');
         if ($markerToDelete === false) {
             return false;
         }
         $successOrDeletedRows = $wpdb->query(WPVGW_Helper::prepare_with_null("DELETE FROM $this->markersTableName WHERE id = %d LIMIT 1", $marker_id));
         if ($successOrDeletedRows === false) {
             WPVGW_Helper::throw_database_exception();
         }
         return ($successOrDeletedRows >= 1);
     }
     public function insert_marker_in_db(array $insert_marker)
     {
         global $wpdb;
         $post_id = $insert_marker['post_id'] ?? null;
         $public_marker = $insert_marker['public_marker'] ?? null;
         $private_marker = $insert_marker['private_marker'] ?? null;
         if ($public_marker === null) {
             throw new Exception('Public marker must be specified and not be null.');
         }
         if ($this->key_exists_in_db($post_id, 'post_id') || $this->key_exists_in_db($public_marker, 'public_marker') || $this->key_exists_in_db($private_marker, 'private_marker')) {
             return WPVGW_InsertMarkerResults::IntegrityError;
         }
         if (array_key_exists('post_id', $insert_marker) && $insert_marker['post_id'] !== null && array_key_exists('is_marker_blocked', $insert_marker) && $insert_marker['is_marker_blocked'] === true) {
             $insert_marker['is_marker_blocked'] = false;
         }
         if (array_key_exists('post_id', $insert_marker) && $insert_marker['post_id'] === null && array_key_exists('is_marker_disabled', $insert_marker) && $insert_marker['is_marker_disabled'] === true) {
             $insert_marker['is_marker_disabled'] = false;
         }
         unset($insert_marker['id']);
         $columnNames = WPVGW_Helper::implode_keys(', ', $insert_marker);
         $columnValues = WPVGW_Helper::sql_values($insert_marker);
         $success = $wpdb->query(WPVGW_Helper::prepare_with_null("INSERT INTO $this->markersTableName ($columnNames) VALUES ($columnValues)", WPVGW_Helper::sql_array_integral_values($insert_marker)));
         if ($success === false) {
             WPVGW_Helper::throw_database_exception();
         }
         return WPVGW_InsertMarkerResults::Inserted;
     }
     public function get_marker_from_string($marker_string)
     {
         $numberOfMatches = WPVGW_Helper::validate_regex_result(preg_match('%(?:.*https?://(?P<server>[a-z0-9./-]+)/(?P<public_marker>[a-z0-9]+).*)|(?:.*?(?P<public_marker_alt>[a-z0-9]+).*)%i', $marker_string, $match));
         if ($numberOfMatches > 0) {
             return array( 'public_marker' => $match['public_marker'] !== '' ? $match['public_marker'] : $match['public_marker_alt'], 'private_marker' => null, 'server' => $match['server'] !== '' ? $match['server'] : null );
         }
         return false;
     }
     public function import_marker($default_server, $public_marker, $private_marker = null, $server = null, DateTime $order_date = null, $is_marker_disabled = false, $is_marker_blocked = false)
     {
         $importMarkersStats = new WPVGW_ImportMarkersStats();
         $importMarkersStats->numberOfMarkers = 1;
         if ($server === null) {
             $server = $default_server;
         } else {
             $server = $this->server_cleaner($server);
         }
         if (!self::public_marker_validator($public_marker) || ($private_marker !== null && !self::private_marker_validator($private_marker)) || (!self::server_validator($server)) || (!$this->is_marker_disabled_validator($is_marker_disabled)) || (!$this->is_marker_blocked_validator($is_marker_blocked))) {
             $importMarkersStats->numberOfFormatErrors++;
             return $importMarkersStats;
         }
         $marker = array( 'public_marker' => $public_marker, 'private_marker' => $private_marker, 'server' => $server, 'order_date' => $order_date, 'is_marker_disabled' => $is_marker_disabled, 'is_marker_blocked' => $is_marker_blocked, );
         switch ($this->insert_marker_in_db($marker)) { case WPVGW_InsertMarkerResults::Inserted : $importMarkersStats->numberOfInsertedMarkers++; break; case WPVGW_InsertMarkerResults::IntegrityError : $updateMarker = array( 'private_marker' => $private_marker, 'order_date' => $order_date ); switch ($this->update_marker_in_db($public_marker, 'public_marker', $updateMarker, array( 'private_marker' => array( null, $private_marker ), 'order_date' => array( null, $order_date ) ))) { case WPVGW_UpdateMarkerResults::Updated: $importMarkersStats->numberOfUpdatedMarkers++; break; case WPVGW_UpdateMarkerResults::UpdateNotNecessary: $importMarkersStats->numberOfDuplicateMarkers++; break; default: $importMarkersStats->numberOfIntegrityErrors++; break; } break; default: WPVGW_Helper::throw_unknown_result_exception(); break; }
         return $importMarkersStats;
     }
     public function import_markers_from_csv_file($is_author_csv, $markers_csv_file_path, $default_server, DateTime $order_date = null)
     {
         if (!file_exists($markers_csv_file_path)) {
             throw new Exception(__(sprintf('Die Datei %s existiert nicht.', WPVGW_TEXT_DOMAIN)));
         }
         $fileContents = file_get_contents($markers_csv_file_path);
         return $this->import_markers_from_csv($is_author_csv, $fileContents, $default_server, $order_date);
     }
     public function import_markers_from_csv($is_author_csv, $markers_csv, $default_server, DateTime $order_date = null)
     {
         $importMarkersStats = new WPVGW_ImportMarkersStats();
         if ($is_author_csv) {
             WPVGW_Helper::validate_regex_result(preg_match_all('%.*?;"?<img.*?""?http://(?P<server>[a-z0-9./-]+?)/(?P<public_marker>[a-z0-9]+?)".*?(?:\r\n|\r|\n){1,2};.*?;(?P<private_marker>[a-z0-9]+?)(?:;|\r\n|\r|\n|\Z)%i', $markers_csv, $matches, PREG_SET_ORDER));
             WPVGW_Helper::validate_regex_result(preg_match('/;Die unten angegebenen Z.+?hlmarken wurden am (\d\d\.\d\d.\d\d\d\d) um .+? bestellt\./si', $markers_csv, $order_matches));
             if (count($order_matches) >= 2) {
                 $order_date = new DateTime($order_matches[1], WPVGW_Helper::get_vg_wort_time_zone());
             }
         } else {
             WPVGW_Helper::validate_regex_result(preg_match_all('/^(?P<public_marker>[a-z0-9]+?);(?P<private_marker>[a-z0-9]+?)(?:\s|\Z)/im', $markers_csv, $matches, PREG_SET_ORDER));
         }
         foreach ($matches as $match) {
             $importMarkersStats = $importMarkersStats->add($this->import_marker($default_server, $match['public_marker'], $match['private_marker'], $match['server'] ?? null, $order_date));
         }
         return $importMarkersStats;
     }
     private function import_old_markers_and_posts($offset, $get_marker_function, $after_import_function, $default_server, array $query_override = array())
     {
         $importOldMarkersAndPostsStats = new WPVGW_ImportOldMarkersAndPostsStats();
         $importOldMarkersAndPostsStats->importMarkersStats = new WPVGW_ImportMarkersStats();
         if (!empty($this->allowedPostTypes)) {
             $postQuery = new WPVGW_Uncached_WP_Query(array_merge(array( 'post_status' => $this->allowedPostStatuses, 'post_type' => $this->allowedPostTypes, ), $query_override), $offset, 500);
             while ($postQuery->has_post()) {
                 $post = $postQuery->get_post();
                 $importOldMarkersAndPostsStats->numberOfIterations++;
                 $importOldMarkersAndPostsStats->numberOfPosts++;
                 $marker = $get_marker_function($post);
                 if ($marker !== false) {
                     $importOldMarkersAndPostsStats->importMarkersStats = $importOldMarkersAndPostsStats->importMarkersStats->add($this->import_marker($default_server, $marker['public_marker'], $marker['private_marker'], $marker['server'], null));
                     $updateMarkerResult = $this->update_marker_in_db($marker['public_marker'], 'public_marker', array( 'post_id' => $post->ID ), array( 'post_id' => array( null, $post->ID ) ));
                     switch ($updateMarkerResult) { case WPVGW_UpdateMarkerResults::Updated: $importOldMarkersAndPostsStats->numberOfUpdates++; if ($after_import_function !== null) {
                         $after_import_function($post);
                     } break; case WPVGW_UpdateMarkerResults::UpdateNotNecessary: $importOldMarkersAndPostsStats->numberOfDuplicates++; if ($after_import_function !== null) {
                         $after_import_function($post);
                     } break; default: $importOldMarkersAndPostsStats->numberOfIntegrityErrors++; break; }
                 }
             }
         }
         return $importOldMarkersAndPostsStats;
     }
     public function import_markers_and_posts_from_old_version($offset, $meta_name, $default_server)
     {
         $thisObject = $this;
         return $this->import_old_markers_and_posts($offset, function (WP_Post $post) use ($thisObject, $meta_name) {
             $markerString = get_post_custom_values($meta_name, $post->ID);
             $markerString = $markerString[0];
             return $thisObject->get_marker_from_string($markerString);
         }, null, $default_server, array( 'meta_key' => $meta_name ));
     }
     public function import_markers_and_posts_from_tl_vgwort_plugin($offset, $default_server)
     {
         return $this->import_old_markers_and_posts($offset, function (WP_Post $post) {
             $metaValue = get_post_custom_values('vgwort-public', $post->ID);
             $marker['public_marker'] = $metaValue[0];
             $metaValue = get_post_custom_values('vgwort-private', $post->ID);
             $marker['private_marker'] = ($metaValue === null ? null : $metaValue[0]);
             $metaValue = get_post_custom_values('vgwort-domain', $post->ID);
             $marker['server'] = ($metaValue === null ? null : $metaValue[0]);
             return $marker;
         }, null, $default_server, array( 'meta_key' => 'vgwort-public' ));
     }
     public function import_markers_and_posts_from_vgw_plugin($offset, $default_server)
     {
         $thisObject = $this;
         return $this->import_old_markers_and_posts($offset, function (WP_Post $post) use ($thisObject) {
             $metaValue = get_post_custom_values('vgwpixel', $post->ID);
             return $thisObject->get_marker_from_string($metaValue[0]);
         }, null, $default_server, array( 'meta_key' => 'vgwpixel' ));
     }
     public function import_markers_and_posts_from_posts($offset, $match_marker_regex, $default_server, $delete_manual_marker = false)
     {
         $thisObject = $this;
         return $this->import_old_markers_and_posts($offset, function (WP_Post $post) use ($thisObject, $match_marker_regex) {
             if (preg_match($match_marker_regex, $post->post_content, $matches) !== 1) {
                 return false;
             }
             return $thisObject->get_marker_from_string($matches[0]);
         }, function (WP_Post $post) use ($match_marker_regex, $delete_manual_marker) {
             if (!$delete_manual_marker) {
                 return;
             }
             $newPostContent = preg_replace($match_marker_regex, '', $post->post_content, 1);
             wp_update_post(array( 'ID' => $post->ID, 'post_content' => $newPostContent, ));
         }, $default_server);
     }
     public function import_markers_and_posts_from_wp_worthy($offset, $default_server)
     {
         global $wpdb;
         $importOldMarkersAndPostsStats = new WPVGW_ImportOldMarkersAndPostsStats();
         $importOldMarkersAndPostsStats->importMarkersStats = new WPVGW_ImportMarkersStats();
         $worthyMarkersTableName = $wpdb->prefix . 'worthy_markers';
         $worthyMarkersTableNameExists = $wpdb->get_var("SHOW TABLES LIKE '$worthyMarkersTableName'") !== null;
         if ($wpdb->last_error !== '') {
             WPVGW_Helper::throw_database_exception();
         }
         if (!$worthyMarkersTableNameExists) {
             return null;
         }
         $mySqlLimitSelect = new WPVGW_MySqlLimitSelect("SELECT * FROM $worthyMarkersTableName", $offset);
         $markers = $mySqlLimitSelect->get_results(ARRAY_A);
         foreach ($markers as $marker) {
             $importOldMarkersAndPostsStats->numberOfIterations++;
             $publicMarker = $marker['public'];
             $privateMarker = $marker['private'] === '' ? null : $marker['private'];
             $server = $marker['server'];
             $postId = is_numeric($marker['postid']) ? intval($marker['postid']) : null;
             $isMarkerDisabled = $marker['disabled'] === '1';
             $importOldMarkersAndPostsStats->importMarkersStats = $importOldMarkersAndPostsStats->importMarkersStats->add($this->import_marker($default_server, $publicMarker, $privateMarker, $server, null, $isMarkerDisabled));
             if ($postId !== null) {
                 $importOldMarkersAndPostsStats->numberOfPosts++;
                 $updateMarkerResult = $this->update_marker_in_db($publicMarker, 'public_marker', array( 'post_id' => $postId, ), array( 'post_id' => array( null, $postId ) ));
                 switch ($updateMarkerResult) { case WPVGW_UpdateMarkerResults::Updated: $importOldMarkersAndPostsStats->numberOfUpdates++; break; case WPVGW_UpdateMarkerResults::UpdateNotNecessary: $importOldMarkersAndPostsStats->numberOfDuplicates++; break; default: $importOldMarkersAndPostsStats->numberOfIntegrityErrors++; break; }
             }
         }
         return $importOldMarkersAndPostsStats;
     }
     public function import_markers_and_posts_from_prosodia_vgw($offset, $default_server)
     {
         global $wpdb;
         $importOldMarkersAndPostsStats = new WPVGW_ImportOldMarkersAndPostsStats();
         $importOldMarkersAndPostsStats->importMarkersStats = new WPVGW_ImportMarkersStats();
         $pvgwMarkersTableName = $wpdb->prefix . 'pvgw_markers';
         $pvgwMarkersTableNameExists = $wpdb->get_var("SHOW TABLES LIKE '$pvgwMarkersTableName'") !== null;
         if ($wpdb->last_error !== '') {
             WPVGW_Helper::throw_database_exception();
         }
         if (!$pvgwMarkersTableNameExists) {
             return null;
         }
         $mySqlLimitSelect = new WPVGW_MySqlLimitSelect("SELECT * FROM $pvgwMarkersTableName", $offset);
         $markers = $mySqlLimitSelect->get_results(ARRAY_A);
         $vgWortTimeZone = new DateTimeZone('Europe/Berlin');
         foreach ($markers as $marker) {
             $importOldMarkersAndPostsStats->numberOfIterations++;
             $postId = is_numeric($marker['post_id']) ? intval($marker['post_id']) : null;
             $publicMarker = $marker['public_marker'];
             $privateMarker = $marker['private_marker'] === '' ? null : $marker['private_marker'];
             $server = $marker['server'];
             $orderDate = array_key_exists('order_date', $marker) ? ($marker['order_date'] === '' ? null : $marker['order_date']) : null;
             $isMarkerDisabled = $marker['is_marker_disabled'] === '1';
             $isMarkerBlocked = $marker['is_marker_blocked'] === '1';
             $isPostDeleted = $marker['is_post_deleted'] === '1';
             $deletedPostTitle = $marker['deleted_post_title'] === '' ? null : $marker['deleted_post_title'];
             if ($orderDate !== null) {
                 $orderDate = new DateTime($orderDate, $vgWortTimeZone);
                 $orderDate = $orderDate === false ? null : $orderDate;
             }
             $importOldMarkersAndPostsStats->importMarkersStats = $importOldMarkersAndPostsStats->importMarkersStats->add($this->import_marker($default_server, $publicMarker, $privateMarker, $server, $orderDate, $isMarkerDisabled, $isMarkerBlocked));
             if ($postId !== null) {
                 $importOldMarkersAndPostsStats->numberOfPosts++;
                 $updateMarkerResult = $this->update_marker_in_db($publicMarker, 'public_marker', array( 'post_id' => $postId, 'is_post_deleted' => $isPostDeleted, 'deleted_post_title' => $deletedPostTitle, ), array( 'post_id' => array( null, $postId ), 'is_post_deleted' => false, 'deleted_post_title' => null ));
                 switch ($updateMarkerResult) { case WPVGW_UpdateMarkerResults::Updated: $importOldMarkersAndPostsStats->numberOfUpdates++; break; case WPVGW_UpdateMarkerResults::UpdateNotNecessary: $importOldMarkersAndPostsStats->numberOfDuplicates++; break; default: $importOldMarkersAndPostsStats->numberOfIntegrityErrors++; break; }
             }
         }
         return $importOldMarkersAndPostsStats;
     }
     public static function public_marker_validator($public_marker)
     {
         return WPVGW_Helper::validate_regex_result(preg_match('/\A[a-z0-9]+\z/im', $public_marker)) === 1;
     }
     public static function private_marker_validator($private_marker)
     {
         return WPVGW_Helper::validate_regex_result(preg_match('/\A[a-z0-9]+\z/im', $private_marker)) === 1;
     }
     public static function server_validator($server)
     {
         return WPVGW_Helper::validate_regex_result(preg_match('%\A[a-z0-9./-]+\z%im', $server)) === 1;
     }
     public function is_marker_disabled_validator($is_marker_disabled)
     {
         return is_bool($is_marker_disabled);
     }
     public function is_marker_blocked_validator($is_marker_blocked)
     {
         return is_bool($is_marker_blocked);
     }
     public function server_cleaner($server)
     {
         return WPVGW_Helper::remove_prefix(WPVGW_Helper::remove_prefix(WPVGW_Helper::remove_suffix($server, '/'), 'http://'), 'https://');
     }
     public function parse_vg_wort_order_date_from_string($order_date)
     {
         if ($order_date === null) {
             return null;
         }
         return DateTime::createFromFormat(WPVGW_Helper::get_vg_wort_order_date_format(), $order_date, WPVGW_Helper::get_vg_wort_time_zone());
     }
 } class WPVGW_ImportMarkersStats implements WPVGW_LongTaskStats
 {
     public $numberOfMarkers = 0;
     public $numberOfInsertedMarkers = 0;
     public $numberOfUpdatedMarkers = 0;
     public $numberOfDuplicateMarkers = 0;
     public $numberOfFormatErrors = 0;
     public $numberOfIntegrityErrors = 0;
     public function add(WPVGW_ImportMarkersStats $stats)
     {
         $newStats = new WPVGW_ImportMarkersStats();
         $newStats->numberOfMarkers = $this->numberOfMarkers + $stats->numberOfMarkers;
         $newStats->numberOfInsertedMarkers = $this->numberOfInsertedMarkers + $stats->numberOfInsertedMarkers;
         $newStats->numberOfUpdatedMarkers = $this->numberOfUpdatedMarkers + $stats->numberOfUpdatedMarkers;
         $newStats->numberOfDuplicateMarkers = $this->numberOfDuplicateMarkers + $stats->numberOfDuplicateMarkers;
         $newStats->numberOfFormatErrors = $this->numberOfFormatErrors + $stats->numberOfFormatErrors;
         $newStats->numberOfIntegrityErrors = $this->numberOfIntegrityErrors + $stats->numberOfIntegrityErrors;
         return $newStats;
     }
     public function __construct(array $array_data = null)
     {
         if ($array_data === null) {
             return;
         }
         $this->numberOfMarkers = intval($array_data[0]);
         $this->numberOfInsertedMarkers = intval($array_data[1]);
         $this->numberOfUpdatedMarkers = intval($array_data[2]);
         $this->numberOfDuplicateMarkers = intval($array_data[3]);
         $this->numberOfFormatErrors = intval($array_data[4]);
         $this->numberOfIntegrityErrors = intval($array_data[5]);
     }
     public function to_array_data()
     {
         return array( $this->numberOfMarkers, $this->numberOfInsertedMarkers, $this->numberOfUpdatedMarkers, $this->numberOfDuplicateMarkers, $this->numberOfFormatErrors, $this->numberOfIntegrityErrors );
     }
 } class WPVGW_ImportOldMarkersAndPostsStats implements WPVGW_LongTaskStats
 {
     public $numberOfIterations = 0;
     public $numberOfPosts = 0;
     public $numberOfUpdates = 0;
     public $numberOfDuplicates = 0;
     public $numberOfIntegrityErrors = 0;
     public $importMarkersStats;
     public function add(WPVGW_ImportOldMarkersAndPostsStats $stats)
     {
         $newStats = new WPVGW_ImportOldMarkersAndPostsStats();
         $newStats->numberOfIterations = $this->numberOfIterations + $stats->numberOfIterations;
         $newStats->numberOfPosts = $this->numberOfPosts + $stats->numberOfPosts;
         $newStats->numberOfUpdates = $this->numberOfUpdates + $stats->numberOfUpdates;
         $newStats->numberOfDuplicates = $this->numberOfDuplicates + $stats->numberOfDuplicates;
         $newStats->numberOfIntegrityErrors = $this->numberOfIntegrityErrors + $stats->numberOfIntegrityErrors;
         $newStats->importMarkersStats = $this->importMarkersStats->add($stats->importMarkersStats);
         return $newStats;
     }
     public function __construct(array $array_data = null)
     {
         if ($array_data === null) {
             return;
         }
         $this->numberOfIterations = intval($array_data[0]);
         $this->numberOfPosts = intval($array_data[1]);
         $this->numberOfUpdates = intval($array_data[2]);
         $this->numberOfDuplicates = intval($array_data[3]);
         $this->numberOfIntegrityErrors = intval($array_data[4]);
         $this->importMarkersStats = new WPVGW_ImportMarkersStats($array_data[5]);
     }
     public function to_array_data()
     {
         return array( $this->numberOfIterations, $this->numberOfPosts, $this->numberOfUpdates, $this->numberOfDuplicates, $this->numberOfIntegrityErrors, $this->importMarkersStats->to_array_data() );
     }
 } class WPVGW_UpdateMarkerResults
 {
     const Updated = 0;
     const UpdateNotNecessary = 1;
     const MarkerNotFound = 2;
     const UserNotAllowed = 3;
     const PostIdNotNull = 4;
     const PostIdExists = 5;
     const PublicMarkerExists = 6;
     const PrivateMarkerExists = 7;
     const BlockedMarkerNotPossible = 8;
     const DisabledMarkerNotPossible = 9;
 } class WPVGW_InsertMarkerResults
 {
     const Inserted = 0;
     const IntegrityError = 1;
 }
