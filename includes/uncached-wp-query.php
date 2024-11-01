<?php
/**
 * Product: Prosodia VGW OS
 * URL: https://prosodia.de/
 * Author: Dr. Ronny Harbich
 * Copyright: Dr. Ronny Harbich
 * License: GPLv2 or later
 */
 class WPVGW_Uncached_WP_Query
 {
     private $queryParameters;
     private $postQuery;
     private $offset;
     private $postsPerQuery;
     private $currentPost;
     private $hasPost;
     public function __construct(array $query, $offset = 0, $posts_per_query = 200)
     {
         $this->offset = $offset;
         $this->postsPerQuery = $posts_per_query;
         $this->queryParameters = array_merge($query, array( 'posts_per_page' => $this->postsPerQuery, 'cache_results' => false, 'update_post_meta_cache' => false, 'update_post_term_cache' => false, 'no_found_rows' => true, ));
         $this->postQuery = new WP_Query(array_merge($this->queryParameters, array( 'offset' => $this->offset )));
         $this->hasPost = $this->postQuery->have_posts();
     }
     public function has_post()
     {
         return $this->hasPost;
     }
     public function get_post()
     {
         if ($this->currentPost !== null) {
             wp_cache_delete($this->currentPost->ID, 'posts');
             wp_cache_delete($this->currentPost->ID, 'post_meta');
         }
         $post = $this->postQuery->next_post();
         $this->currentPost = $post;
         $this->hasPost = $this->postQuery->have_posts();
         return $post;
     }
 }
