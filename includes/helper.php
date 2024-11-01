<?php
/**
 * Product: Prosodia VGW OS
 * URL: https://prosodia.de/
 * Author: Dr. Ronny Harbich
 * Copyright: Dr. Ronny Harbich
 * License: GPLv2 or later
 */
 class WPVGW_Helper
 {
     public static $shortcodeRegex = '/(?<!\[)\[[^\[\]]+\](?!\])/i';
     public static $captionShortcodeRegex = '%(?<!\[)\[caption\s.*?\[/caption\](?!\])%i';
     public static $imageTagRegex = '/<img\s.*?>/i';
     public static $mySqlDateRegex = '^[1-9][0-9]{3}-[01][0-9]-[0-3][0-9]$';
     private static $vgWortTimeZone;
     private static $otherVgWortPlugins = array( 'vgw-vg-wort-zahlpixel-plugin/vgw.php', 'tl-vgwort/tl-vgwort.php', 'prosodia-vgw/prosodia-vgw.php', 'wp-worthy/wp-worthy.php', );
     private static $otherActiveVgWortPlugins;
     public static function die_cheating()
     {
         wp_die(__('Cheatin&#8217; uh?'));
     }
     public static function get_other_active_vg_wort_plugins()
     {
         if (self::$otherActiveVgWortPlugins !== null) {
             return self::$otherActiveVgWortPlugins;
         }
         self::$otherActiveVgWortPlugins = array();
         foreach (self::$otherVgWortPlugins as $otherPlugin) {
             if (is_plugin_active($otherPlugin)) {
                 self::$otherActiveVgWortPlugins[] = $otherPlugin;
             }
         }
         return self::$otherActiveVgWortPlugins;
     }
     public static function get_vg_wort_time_zone()
     {
         if (self::$vgWortTimeZone === null) {
             self::$vgWortTimeZone = new DateTimeZone('Europe/Berlin');
         }
         return self::$vgWortTimeZone;
     }
     public static function get_vg_wort_order_date_format()
     {
         return 'd.m.Y';
     }
     public static function get_vg_wort_order_date_sql_format()
     {
         return '%d.%m.%Y';
     }
     public static function convert_to_string($value)
     {
         return strval($value);
     }
     public static function convert_to_int($value)
     {
         return intval($value);
     }
     public static function convert_to_int_or_null($value)
     {
         if ($value === null) {
             return null;
         }
         return intval($value);
     }
     public static function convert_to_bool_or_null($value)
     {
         if ($value === null) {
             return null;
         }
         return boolval($value);
     }
     public static function convert_to_int_array(array $array, $index_of_item_array = null)
     {
         $intArray = array();
         foreach ($array as $item) {
             if ($index_of_item_array === null) {
                 $intArray[] = (int)$item;
             } else {
                 $intArray[] = (int)$item[$index_of_item_array];
             }
         }
         return $intArray;
     }
     public static function clean_word_press_text($text, $apply_filters, $is_title = false, $plain_text = true, $remove_images = false)
     {
         $text = preg_replace(self::$captionShortcodeRegex, '', $text);
         if ($remove_images) {
             $text = preg_replace(self::$captionShortcodeRegex, '', $text);
         }
         if ($apply_filters) {
             ob_start();
             if ($is_title) {
                 $text = apply_filters('the_title', $text);
             } else {
                 $text = apply_filters('the_content', $text);
             }
             ob_end_clean();
         }
         if ($plain_text) {
             $text = preg_replace('%<br\s*/?>%i', ' ', $text);
             $text = strip_tags($text);
         }
         $text = preg_replace(array( self::$shortcodeRegex, '/\s{2,}/i' ), array( '', ' ' ), $text);
         if ($plain_text) {
             $text = html_entity_decode($text);
         }
         return trim($text);
     }
     public static function http_header_csv($file_name)
     {
         header('Pragma: public');
         header('Expires: 0');
         header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
         header('Cache-Control: private', false);
         header('Content-Type: application/octet-stream');
         header("Content-Disposition: attachment; filename=\"$file_name\";");
         header('Content-Transfer-Encoding: binary');
     }
     public static function null_data_text($value = null)
     {
         if ($value === null) {
             return __('–', WPVGW_TEXT_DOMAIN);
         }
         return $value;
     }
     public static function get_html_checkbox_checked($checked)
     {
         return $checked === true ? 'checked="checked"' : '';
     }
     public static function get_html_option_selected($selected)
     {
         return $selected === true ? 'selected="selected"' : '';
     }
     public static function render_html_selects(array $html_selects)
     {
         foreach ($html_selects as $htmlSelect => $options) {
             $htmlSelect = WPVGW . '_' . $htmlSelect;
             echo(sprintf('<select id="%1$s" name="%1$s">', $htmlSelect));
             $currentOptionIndex = isset($_REQUEST[$htmlSelect]) ? intval($_REQUEST[$htmlSelect]) : 0;
             foreach ($options as $optionIndex => $option) {
                 echo(sprintf('<option value="%s" %s>%s</option>', esc_attr($optionIndex), self::get_html_option_selected($currentOptionIndex === $optionIndex), esc_html($option['label'])));
             }
             echo('</select>');
         }
     }
     public static function render_admin_message($html_message, $type, $escape = true)
     {
         $htmlMessage = $escape ? esc_html($html_message) : $html_message;
         if ($type === WPVGW_ErrorType::Error) { ?>
			<div class='notice notice-error error settings-error is-dismissible'>
				<p><strong><?php echo($htmlMessage) ?></strong></p>
			</div>
			<?php
 } elseif ($type === WPVGW_ErrorType::Update) { ?>
			<div class='notice notice-success updated settings-error is-dismissible'>
				<p><?php echo($htmlMessage) ?></p>
			</div>
			<?php
 } elseif ($type === WPVGW_ErrorType::Warning) { ?>
			<div class='notice notice-warning settings-error is-dismissible'>
				<p><?php echo($htmlMessage) ?></p>
			</div>
			<?php
 }
     }
     public static function render_admin_messages(array $admin_messages)
     {
         foreach ($admin_messages as $adminMessage) {
             self::render_admin_message($adminMessage['message'], $adminMessage['type'], $adminMessage['escape']);
         }
     }
     public static function implode_keys($separator, array $array)
     {
         $separator_internal = '';
         $output = '';
         foreach ($array as $key => $value) {
             $output .= $separator_internal . $key;
             $separator_internal = $separator;
         }
         return $output;
     }
     public static function sql_setters(array $array)
     {
         $separator_internal = '';
         $output = '';
         foreach ($array as $key => $value) {
             $output .= $separator_internal . $key . ' = ' . self::get_format_literal($value);
             $separator_internal = ', ';
         }
         return $output;
     }
     public static function sql_values(array $array)
     {
         $separator_internal = '';
         $output = '';
         foreach ($array as $value) {
             $output .= $separator_internal . self::get_format_literal($value);
             $separator_internal = ', ';
         }
         return $output;
     }
     public static function sql_array_integral_values(array $values)
     {
         $output = array();
         foreach ($values as $key => $value) {
             if ($value === null || is_int($value) || is_float($value) || is_string($value) || is_bool($value)) {
                 $output[] = $value;
             } elseif ($value instanceof DateTime) {
                 $output[] = $value->format('Y-m-d');
             } else {
                 throw new Exception(sprintf('Type "%s" of array element "%s" is not supported.', gettype($value), $key));
             }
         }
         return $output;
     }
     public static function sql_columns_on_duplicate(array $array)
     {
         $separator_internal = '';
         $output = '';
         foreach ($array as $key => $value) {
             $output .= $separator_internal . $key . ' = VALUES(' . $key . ')';
             $separator_internal = ', ';
         }
         return $output;
     }
     public static function sql_where_logic($logical_operator, array $array)
     {
         $logical_operator = ' ' . $logical_operator . ' ';
         $separator_internal = '';
         $output = '';
         foreach ($array as $value) {
             $output .= $separator_internal . $value[0] . ' ' . $value[1] . ' ' . self::get_format_literal($value[2]);
             $separator_internal = $logical_operator;
         }
         return $output;
     }
     public static function sql_set_big_selects($is_enabled)
     {
         global $wpdb;
         $sqlValue = $is_enabled ? '1' : 'DEFAULT';
         $wpdb->query("SET SESSION SQL_BIG_SELECTS = $sqlValue;");
         if ($wpdb->last_error !== '') {
             self::throw_database_exception();
         }
     }
     public static function throw_database_exception()
     {
         global $wpdb;
         if ($wpdb->show_errors) {
             $errorText = $wpdb->last_error;
         } else {
             $errorText = __('Fehlerdetails dürfen nur im Debug-Modus angezeigt werden. Bitte kontaktieren Sie ihren Administrator oder die VG-WORT-Plugin-Entwickler.', WPVGW_TEXT_DOMAIN);
         }
         throw new Exception(sprintf(__('Datenbankfehler: %s', WPVGW_TEXT_DOMAIN), $errorText));
     }
     public static function validate_regex_result($regex_result)
     {
         if ($regex_result === false) {
             throw new Exception(__('Ein regulärer Ausdruck ist ungültig. Bitte wenden Sie sich an die VG-WORT-Plugin-Entwickler.'), WPVGW_TEXT_DOMAIN);
         }
         return $regex_result;
     }
     public static function throw_unknown_result_exception()
     {
         throw new Exception('Unknown result.');
     }
     public static function remove_prefix($text, $prefix, &$found = null)
     {
         $found = false;
         if (0 === stripos($text, $prefix)) {
             $found = true;
             $text = substr($text, strlen($prefix));
         }
         return $text;
     }
     public static function remove_suffix($text, $suffix)
     {
         $textLength = strlen($text);
         $suffixLength = strlen($suffix);
         if ($suffixLength > $textLength) {
             return $text;
         }
         if (substr_compare($text, $suffix, -$suffixLength, $textLength, true) === 0) {
             return substr($text, 0, -$suffixLength);
         }
         return $text;
     }
     public static function is_null_or_empty($string)
     {
         if ($string === null) {
             return true;
         }
         if (!is_string($string)) {
             throw new InvalidArgumentException('Type of $string must be string.');
         }
         if ($string === '') {
             return true;
         }
         return false;
     }
     public static function is_null_or_int($integer)
     {
         return $integer === null || is_int($integer);
     }
     public static function strictly_equal($value1, $value2)
     {
         return is_object($value1) && is_object($value2) ? $value1 == $value2 : $value1 === $value2;
     }
     public static function in_array($value, array $array)
     {
         foreach ($array as $item) {
             if (self::strictly_equal($item, $value)) {
                 return true;
             }
         }
         return false;
     }
     public static function array_contains(array $array1, array $array2)
     {
         foreach ($array1 as $key => $value) {
             if (array_key_exists($key, $array2)) {
                 if (!self::strictly_equal($array1[$key], $array2[$key])) {
                     return false;
                 }
             }
         }
         return true;
     }
     public static function array_search_recursive($key, $haystack)
     {
         $results = array();
         if (is_array($haystack)) {
             if (array_key_exists($key, $haystack)) {
                 $results = is_array($haystack[$key]) ? $haystack[$key] : array( $haystack[$key] );
             }
             foreach ($haystack as $hay) {
                 if (is_array($hay)) {
                     $results = array_merge($results, self::array_search_recursive($key, $hay));
                 }
             }
         }
         return $results;
     }
     public static function get_format_literal($value)
     {
         if (is_int($value)) {
             return '%d';
         }
         if (is_float($value)) {
             return '%f';
         }
         return '%s';
     }
     public static function prepare_with_null($query, $args)
     {
         global $wpdb;
         $arguments = func_get_args();
         array_shift($arguments);
         if (isset($arguments[0]) && is_array($arguments[0])) {
             $arguments = $arguments[0];
         }
         if ($query === '') {
             return '';
         }
         $argumentCounter = count($arguments) - 1;
         $argumentsWithoutNull = array();
         $queryWithNull = '';
         for ($i = strlen($query) - 1; $i > -1; $i--) {
             $addCurrentCharToResult = true;
             $currentChar = $query[$i];
             if ($currentChar === 's' || $currentChar === 'd' || $currentChar === 'f') {
                 $percentCounter = 0;
                 for ($j = $i - 1; $j > -1; $j--) {
                     if ($query[$j] === '%') {
                         $percentCounter++;
                     } else {
                         break;
                     }
                 }
                 if ($percentCounter > 0) {
                     if ($percentCounter % 2 === 0) {
                         $queryWithNull = $currentChar . $queryWithNull;
                     } else {
                         if ($argumentCounter > -1) {
                             $currentArgument = $arguments[$argumentCounter];
                             if ($currentArgument === null) {
                                 $queryWithNull = 'NULL' . $queryWithNull;
                                 $i -= $percentCounter;
                                 $addCurrentCharToResult = false;
                             } else {
                                 array_unshift($argumentsWithoutNull, $currentArgument = $arguments[$argumentCounter]);
                             }
                         }
                         $argumentCounter--;
                     }
                 }
             }
             if ($addCurrentCharToResult) {
                 $queryWithNull = $currentChar . $queryWithNull;
             }
         }
         if (count($argumentsWithoutNull) === 0) {
             return $queryWithNull;
         } else {
             return $wpdb->prepare($queryWithNull, $argumentsWithoutNull);
         }
     }
     public static function get_user_ids_from_db(array $roles)
     {
         global $wpdb;
         $blogId = get_current_blog_id();
         $metaKey = $wpdb->get_blog_prefix($blogId) . 'capabilities';
         $replacements = array( $metaKey );
         $whereArray = array();
         foreach ($roles as $role) {
             $role = '%' . $role . '%';
             $whereArray[] = array( 'mt.meta_value', 'like', $role );
             $replacements[] = $role;
         }
         $whereSql = self::sql_where_logic('OR', $whereArray);
         $usersInDb = $wpdb->get_col(self::prepare_with_null("SELECT DISTINCT u.ID FROM {$wpdb->users} AS u INNER JOIN {$wpdb->usermeta} AS mt ON u.ID = mt.user_id WHERE mt.meta_key = %s AND ($whereSql)", $replacements));
         if ($wpdb->last_error !== '') {
             self::throw_database_exception();
         }
         $users = array();
         foreach ($usersInDb as $userInDb) {
             $users[] = (int)$userInDb;
         }
         return $users;
     }
     public static function show_debug_info()
     {
         return defined('WP_DEBUG') && WP_DEBUG === true && defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY === true;
     }
     public static function get_test_marker_in_post_link($post_id, $expected_marker)
     {
         return sprintf('https://test.prosodia-vgw.de/marke-finden?url=%s&marker=%s&privacy=1', urlencode(get_permalink($post_id)), urlencode($expected_marker));
     }
     public static function is_media($postTypeOrPostTypeName)
     {
         if (is_string($postTypeOrPostTypeName)) {
             $postTypeName = $postTypeOrPostTypeName;
         } elseif ($postTypeOrPostTypeName instanceof WP_Post_Type) {
             $postTypeName = $postTypeOrPostTypeName->name;
         } elseif ($postTypeOrPostTypeName === null) {
             $postTypeName = null;
         } else {
             throw new InvalidArgumentException('Specified post type has not a valid type.');
         }
         return $postTypeName === 'attachment';
     }
     public static function is_media_attached_to_post(WP_Post $media)
     {
         return self::is_media($media->post_type) && $media->post_parent !== null && $media->post_parent >= 1;
     }
     public static function get_post_type_name(WP_Post_Type $postType, bool $is_plural = false)
     {
         if ($postType->name === 'page') {
             return ($is_plural ? __('Seiten', WPVGW_TEXT_DOMAIN) : __('Seite', WPVGW_TEXT_DOMAIN));
         }
         if ($postType->name === 'attachment') {
             return ($is_plural ? __('Dateien', WPVGW_TEXT_DOMAIN) : __('Datei', WPVGW_TEXT_DOMAIN));
         }
         return sprintf($is_plural ? __('%s-Seiten', WPVGW_TEXT_DOMAIN) : __('%s-Seite', WPVGW_TEXT_DOMAIN), $postType->labels->singular_name);
     }
 } class WPVGW_ErrorType
 {
     const Error = 0;
     const Update = 1;
     const Warning = 2;
 }
