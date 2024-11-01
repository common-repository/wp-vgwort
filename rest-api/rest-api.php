<?php
/**
 * Product: Prosodia VGW OS
 * URL: https://prosodia.de/
 * Author: Dr. Ronny Harbich
 * Copyright: Dr. Ronny Harbich
 * License: GPLv2 or later
 */
 class WPVGW_MarkersRestRoute extends WP_REST_Controller
 {
     const API_KEY_LENGTH = 20;
     const API_KEY_ALPHABET = [ '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z' ];
     private static $apiKeyRegex;
     private $dataRetriever;
     private $markersManager;
     private $options;
     private $collectionParams;
     public static function init_static()
     {
         self::$apiKeyRegex = '^[' . implode('', self::API_KEY_ALPHABET) . ']{' . self::API_KEY_LENGTH . '}$';
     }
     public function __construct(WPVGW_MarkersManager $markers_manager, WPVGW_PostsExtras $posts_extras, WPVGW_Options $options, WPVGW_UserOptions $user_options)
     {
         $this->namespace = WPVGW . '/v1';
         $this->rest_base = 'markers';
         $this->markersManager = $markers_manager;
         $this->options = $options;
         $this->dataRetriever = new WPVGW_DatabaseDataRetriever($this->markersManager, $posts_extras, $this->options, $user_options);
         $filters = $this->dataRetriever->get_filters();
         $this->collectionParams = array( 'page' => [ 'description' => __('Die aktuelle Seitenzahl (aktueller Datensatz).', WPVGW_TEXT_DOMAIN), 'required' => false, 'type' => 'integer', 'default' => 1, 'minimum' => 1, ], 'per_page' => [ 'description' => __('Anzahl der Zählmarken (und Daten) pro Seite (Datensatzgröße).', WPVGW_TEXT_DOMAIN), 'required' => false, 'type' => 'integer', 'default' => 10, 'minimum' => 1, 'maximum' => 100, ], 'search' => [ 'description' => __('Ein Suchtext, um nur Zählmarken (und Daten) auszugeben, die den Suchtext enthalten.', WPVGW_TEXT_DOMAIN), 'required' => false, 'type' => 'string', 'default' => '', ], 'order_by' => [ 'description' => __('Die Sortierung der Zählmarken (und Daten).', WPVGW_TEXT_DOMAIN), 'required' => false, 'type' => 'string', 'default' => 'id', 'enum' => $this->dataRetriever->get_sortable_columns(), ], 'order' => [ 'description' => __('Die Reihenfolge der Sortierung der Zählmarken (und Daten).', WPVGW_TEXT_DOMAIN), 'required' => false, 'type' => 'string', 'default' => 'asc', 'enum' => [ 'asc', 'desc' ], ], 'f_has_marker' => [ 'description' => __('Filter: Nur Seiten (und Daten) erhalten, denen eine/keine Zählmarke zugeordnet ist.', WPVGW_TEXT_DOMAIN), 'required' => false, 'type' => 'string', 'enum' => array_keys($filters['has_marker']), ], 'f_marker_disabled' => [ 'description' => __('Filter: Nur inaktive/aktive Zählmarken (und Daten) erhalten.', WPVGW_TEXT_DOMAIN), 'required' => false, 'type' => 'string', 'enum' => array_keys($filters['marker_disabled']), ], 'f_marker_blocked' => [ 'description' => __('Filter: Nur nicht-zuordenbare/zuordenbare Zählmarken (und Daten) erhalten.', WPVGW_TEXT_DOMAIN), 'required' => false, 'type' => 'string', 'enum' => array_keys($filters['marker_blocked']), ], 'f_invalid_markers' => [ 'description' => __('Filter: Nur Zählmarken (und Daten) erhalten, deren Format ungültig/gültig ist.', WPVGW_TEXT_DOMAIN), 'required' => false, 'type' => 'string', 'enum' => array_keys($filters['invalid_markers']), ], 'f_post_author' => [ 'description' => __('Filter: Nur Seiten (und Daten), deren Autor gelöscht/ID ist. Für „by_id“, siehe „f_post_author_args“.', WPVGW_TEXT_DOMAIN), 'required' => false, 'type' => 'string', 'enum' => array_keys($filters['post_author']), ], 'f_post_author_args' => [ 'description' => __('Filter-Argumente für „f_post_author“: Für „by_id“ die Autor-ID.', WPVGW_TEXT_DOMAIN), 'required' => false, 'type' => 'array', 'minItems' => 1, 'maxItems' => 1, 'items' => [ 'type' => 'integer', 'minimum' => 1 ], ], 'f_post_type' => [ 'description' => __('Filter: Nur Seiten (und Daten), deren Typ zugelassen/nicht-zugelassen/Typ ist. Für „by_type“, siehe „f_post_type_args“.', WPVGW_TEXT_DOMAIN), 'required' => false, 'type' => 'string', 'enum' => array_keys($filters['post_type']), ], 'f_post_type_args' => [ 'description' => __('Filter-Argumente für „f_post_type“: Für „by_type“ ein Seitentyp.', WPVGW_TEXT_DOMAIN), 'required' => false, 'type' => 'array', 'minItems' => 1, 'maxItems' => 1, 'items' => [ 'type' => 'string' ], ], 'f_post' => [ 'description' => __('Filter: Nur Seiten (und Daten) erhalten, die gelöscht/nicht-gelöscht oder verknüpft/nicht-verknüpft sind.', WPVGW_TEXT_DOMAIN), 'required' => false, 'type' => 'string', 'enum' => array_keys($filters['post']), ], 'f_sufficient_characters' => [ 'description' => __('Filter: Nur Seiten (und Daten) erhalten, die aus genügend/ungenügend vielen Zeichen bestehen.', WPVGW_TEXT_DOMAIN), 'required' => false, 'type' => 'string', 'enum' => array_keys($filters['sufficient_characters']), ], 'f_post_date' => [ 'description' => __('Filter: Nur Seiten (und Daten) eines gewissen Veröffentlichungsdatums.', WPVGW_TEXT_DOMAIN), 'required' => false, 'type' => 'string', 'enum' => array_keys($filters['post_date']), ], 'f_post_date_args' => [ 'description' => __('Filter-Argumente für „f_post_date_args“: Für „by_after_date“ ein Datum (YYYY-MM-DD). Für „by_before_date“ ein Datum (YYYY-MM-DD). Für „by_between_dates“ zwei Daten (YYYY-MM-DD).', WPVGW_TEXT_DOMAIN), 'required' => false, 'type' => 'array', 'minItems' => 1, 'maxItems' => 2, 'items' => [ 'type' => 'string', 'pattern' => WPVGW_Helper::$mySqlDateRegex ], ], 'f_order_date' => [ 'description' => __('Filter: Nur Zählmarken (und Daten) eines gewissen Zählmarkenbestelldatums.', WPVGW_TEXT_DOMAIN), 'required' => false, 'type' => 'string', 'enum' => array_keys($filters['order_date']), ], 'f_order_date_args' => [ 'description' => __('Filter-Argumente für „f_order_date“: Für „by_after_date“ ein Datum (YYYY-MM-DD). Für „by_before_date“ ein Datum (YYYY-MM-DD). Für „by_between_dates“ zwei Daten (YYYY-MM-DD).', WPVGW_TEXT_DOMAIN), 'required' => false, 'type' => 'array', 'minItems' => 1, 'maxItems' => 2, 'items' => [ 'type' => 'string', 'pattern' => WPVGW_Helper::$mySqlDateRegex ], ], );
     }
     private static function create_internal_wp_error(Exception $e)
     {
         return new WP_Error('rest_internal_error', WPVGW_Helper::show_debug_info() ? sprintf(__('Es ist ein interner Fehler beim Abruf der Daten aufgetreten: %s', WPVGW_TEXT_DOMAIN), $e->getMessage()) : __('Es ist ein interner Fehler beim Abruf der Daten aufgetreten. Fehlerdetails dürfen nur im Debug-Modus angezeigt werden. Bitte kontaktieren Sie ihren Administrator oder die VG-WORT-Plugin-Entwickler.', WPVGW_TEXT_DOMAIN), WPVGW_Helper::show_debug_info() ? $e : null);
     }
     public function register_routes()
     {
         register_rest_route($this->namespace, '/' . $this->rest_base, array( [ 'methods' => WP_REST_Server::READABLE, 'callback' => [ $this, 'get_items' ], 'permission_callback' => [ $this, 'get_items_permissions_check' ], 'args' => $this->get_collection_params(), 'description' => 'TEST' ], 'schema' => [ $this, 'get_public_item_schema' ], ));
         register_rest_route($this->namespace, '/' . $this->rest_base . '/(?P<marker>[a-z0-9]+)', array( [ 'methods' => WP_REST_Server::READABLE, 'callback' => [ $this, 'get_item' ], 'permission_callback' => [ $this, 'get_item_permissions_check' ], 'args' => array( 'type' => [ 'description' => __('Gibt an, ob die angegebene Zählmarke eine öffentliche („public“) oder private („private“) ist.', WPVGW_TEXT_DOMAIN), 'required' => false, 'type' => 'string', 'default' => 'public', 'enum' => array( 'public', 'private' ), ], ) ], 'args' => array( 'marker' => [ 'description' => __('Öffentliche/Private Zählmarke (und Daten), die abgerufen werden soll.', WPVGW_TEXT_DOMAIN), 'type' => 'string', 'validate_callback' => function ($param, $request, $key) {
             return is_string($param) && (WPVGW_MarkersManager::public_marker_validator($param) || WPVGW_MarkersManager::private_marker_validator($param));
         } ], ), 'schema' => [ $this, 'get_public_item_schema' ], ));
     }
     public function get_collection_params()
     {
         return $this->collectionParams;
     }
     private function is_api_key_valid($request)
     {
         $apiKey = $request->get_header('x_wpvgw_api_key');
         if ($apiKey === null || !self::api_key_validator($apiKey)) {
             return false;
         }
         return $this->options->get_api_key() === $apiKey;
     }
     public static function api_key_validator($api_key)
     {
         if ($api_key === null) {
             return true;
         }
         return WPVGW_Helper::validate_regex_result(preg_match('/' . self::$apiKeyRegex . '/', $api_key)) === 1;
     }
     public static function generate_api_key()
     {
         $alphabetMaxIndex = count(self::API_KEY_ALPHABET) - 1;
         $apiKey = '';
         for ($i = 1; $i <= self::API_KEY_LENGTH; $i++) {
             $apiKey .= self::API_KEY_ALPHABET[random_int(0, $alphabetMaxIndex)];
         }
         return $apiKey;
     }
     public function get_item_schema()
     {
         if ($this->schema) {
             return $this->schema;
         }
         $userProperties = [ 'id' => [ 'description' => __('ID des Autors.', WPVGW_TEXT_DOMAIN), 'type' => [ 'integer', 'null' ], 'minimum' => 0, ], 'display_name' => [ 'description' => __('Anzeigename des Autors.', WPVGW_TEXT_DOMAIN), 'type' => [ 'string', 'null' ], ], 'first_name' => [ 'description' => __('Vorname des Autors.', WPVGW_TEXT_DOMAIN), 'type' => [ 'string', 'null' ], ], 'last_name' => [ 'description' => __('Names des Autors.', WPVGW_TEXT_DOMAIN), 'type' => [ 'string', 'null' ], ], ];
         $this->schema = array( '$schema' => 'http://json-schema.org/draft-04/schema#', 'title' => 'markers', 'type' => 'object', 'properties' => [ 'marker' => [ 'description' => __('Ein Zählmarken-Objekt.', WPVGW_TEXT_DOMAIN), 'type' => 'object', 'properties' => [ 'id' => [ 'description' => __('ID der Zählmarke.', WPVGW_TEXT_DOMAIN), 'type' => 'integer', 'minimum' => 0, ], 'public_marker' => [ 'description' => __('Öffentliche Zählmarke.', WPVGW_TEXT_DOMAIN), 'type' => 'string', ], 'private_marker' => [ 'description' => __('Private Zählmarke.', WPVGW_TEXT_DOMAIN), 'type' => [ 'string', 'null' ], ], 'server' => [ 'description' => __('Server (mit Pfad) der Zählmarke.', WPVGW_TEXT_DOMAIN), 'type' => [ 'string', 'null' ], ], 'order_date' => [ 'description' => __('Bestelldatum der Zählmarke.', WPVGW_TEXT_DOMAIN), 'type' => [ 'string', 'null' ], ], 'is_marker_disabled' => [ 'description' => __('Zählmarke deaktiviert?', WPVGW_TEXT_DOMAIN), 'type' => 'boolean', ], 'is_marker_blocked' => [ 'description' => __('Zählmarke nicht zuordenbar?', WPVGW_TEXT_DOMAIN), 'type' => 'boolean', ], 'is_post_deleted' => [ 'description' => __('Seite der Zählmarke gelöscht?', WPVGW_TEXT_DOMAIN), 'type' => 'boolean', ], 'deleted_post_title' => [ 'description' => __('Titel der gelöschten Seite.', WPVGW_TEXT_DOMAIN), 'type' => [ 'string', 'null' ], ], ], ], 'post' => [ 'description' => __('Ein Seiten-Objekt (auch für Beiträge usw.)', WPVGW_TEXT_DOMAIN), 'type' => 'object', 'properties' => [ 'id' => [ 'description' => __('ID der Seite.', WPVGW_TEXT_DOMAIN), 'type' => [ 'integer', 'null' ], 'minimum' => 0, ], 'type' => [ 'description' => __('Der Seitentyp.', WPVGW_TEXT_DOMAIN), 'type' => [ 'string', 'null' ], ], 'date' => [ 'description' => __('Veröffentlichungsdatum der Seite.', WPVGW_TEXT_DOMAIN), 'type' => [ 'string', 'null' ], ], 'status' => [ 'description' => __('Status der Seite.', WPVGW_TEXT_DOMAIN), 'type' => [ 'string', 'null' ], ], 'title' => [ 'description' => __('Titel/Überschrift der Seite.', WPVGW_TEXT_DOMAIN), 'type' => 'string', ], 'content' => [ 'description' => __('Inhalt/Text der Seite.', WPVGW_TEXT_DOMAIN), 'type' => 'string', ], 'character_count' => [ 'description' => __('Zeichenanzahl der Seite.', WPVGW_TEXT_DOMAIN), 'type' => [ 'integer', 'null' ], 'minimum' => 0, ], 'character_count_sufficient' => [ 'description' => __('Zeichenanzahl der Seite ausreichend für Zählmarke?', WPVGW_TEXT_DOMAIN), 'type' => [ 'boolean', 'null' ], ], 'permalink' => [ 'description' => __('Permalink (URL) der Seite.', WPVGW_TEXT_DOMAIN), 'type' => [ 'string', 'null' ], 'format' => 'uri', ], ], ], 'post_user' => [ 'description' => __('Der Autor der Seite.', WPVGW_TEXT_DOMAIN), 'type' => 'object', 'properties' => $userProperties, ], ], );
         return $this->schema;
     }
     public function get_items_permissions_check($request)
     {
         return $this->is_api_key_valid($request);
     }
     public function get_items($request)
     {
         $page = intval($request->get_param('page'));
         $page = $page < $this->collectionParams['page']['minimum'] ? $this->collectionParams['page']['minimum'] : $page;
         $perPage = intval($request->get_param('per_page'));
         $perPage = $perPage < $this->collectionParams['per_page']['minimum'] ? $this->collectionParams['per_page']['default'] : min($perPage, $this->collectionParams['per_page']['maximum']);
         $dataRetrieverFilters = $this->dataRetriever->get_filters();
         $filters = array();
         foreach ($dataRetrieverFilters as $key => $filter) {
             $fKey = 'f_' . $key;
             if ($request->has_param($fKey)) {
                 $parameter = strval($request->get_param($fKey));
                 if (array_key_exists($parameter, $dataRetrieverFilters[$key])) {
                     $argCount = $dataRetrieverFilters[$key][$parameter]['args_count'];
                     $args = array();
                     if ($argCount >= 1) {
                         if ($request->has_param($fKey . '_args')) {
                             $requestArgs = $request->get_param($fKey . '_args');
                             if (count($requestArgs) !== $argCount) {
                                 return new WP_Error('rest_invalid_arguments', sprintf(__('Argumentenanzahl für Filter „%s“ stimmt nicht überein.', WPVGW_TEXT_DOMAIN), $key));
                             }
                             foreach ($requestArgs as $requestArg) {
                                 $args[] = strval($requestArg);
                             }
                         }
                     }
                     $filters[$key] = array( 'parameter' => $parameter, 'args' => $args );
                 }
             }
         }
         $search = $request->get_param('search');
         $orderBy = $request->get_param('order_by');
         $order = $request->get_param('order');
         try {
             $rows = $this->dataRetriever->get_data_rows($page, $perPage, true, false, $total_rows_count, $filters, $search, $orderBy, $order);
         } catch (Exception $e) {
             return self::create_internal_wp_error($e);
         }
         $data = [];
         foreach ($rows as $row) {
             $data[] = $this->prepare_response_for_collection($this->prepare_output($row));
         }
         return rest_ensure_response($data);
     }
     public function get_item_permissions_check($request)
     {
         return $this->is_api_key_valid($request);
     }
     public function get_item($request)
     {
         $marker = $request->get_param('marker');
         $type = $request->get_param('type');
         try {
             $rows = $this->dataRetriever->get_data_rows(1, 1, true, false, $total_rows_count, array( 'marker' => [ 'parameter' => $type === 'public' ? 'by_public' : 'by_private', 'args' => [ $marker ] ] ));
         } catch (Exception $e) {
             return self::create_internal_wp_error($e);
         }
         $data = [];
         foreach ($rows as $row) {
             $data[] = $this->prepare_response_for_collection($this->prepare_output($row));
         }
         return rest_ensure_response($data);
     }
     private function prepare_output($row)
     {
         $postId = WPVGW_Helper::convert_to_int_or_null($row['post_id']);
         $postPermalink = $postId === null ? null : get_permalink($postId);
         if ($postPermalink === false) {
             $postPermalink = null;
         }
         $postCharacterCount = WPVGW_Helper::convert_to_int_or_null($row['e_character_count']);
         $data = array( 'marker' => [ 'id' => intval($row['id']), 'public_marker' => $row['public_marker'], 'private_marker' => $row['private_marker'], 'server' => $row['server'], 'order_date' => $row['order_date'], 'is_marker_disabled' => boolval($row['is_marker_disabled']), 'is_marker_blocked' => boolval($row['is_marker_blocked']), 'is_post_deleted' => boolval($row['is_post_deleted']), 'deleted_post_title' => $row['deleted_post_title'], ], 'post' => [ 'id' => $postId, 'type' => $row['post_type'], 'date' => $row['post_date'], 'status' => $row['post_status'], 'title' => WPVGW_Helper::clean_word_press_text($row['post_title'], true, true, true, true), 'content' => WPVGW_Helper::clean_word_press_text($row['post_content'], true, false, true, true), 'character_count' => $postCharacterCount, 'character_count_sufficient' => $postCharacterCount === null ? null : $this->markersManager->is_character_count_sufficient($postCharacterCount, $this->options->get_vg_wort_minimum_character_count()), 'permalink' => $postPermalink, ], 'post_user' => [ 'id' => WPVGW_Helper::convert_to_int_or_null($row['post_author']), 'display_name' => $row['up_display_name'], 'first_name' => $row['umpf_first_name'], 'last_name' => $row['umpl_last_name'], ], );
         return rest_ensure_response($data);
     }
 } WPVGW_MarkersRestRoute::init_static();
