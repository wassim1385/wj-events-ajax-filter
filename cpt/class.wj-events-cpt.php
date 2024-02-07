<?php

if( ! class_exists( 'WJ_Events_Post_Type' ) ) {

    class WJ_Events_Post_Type {

        public function __construct() {
            
            add_action( 'init', array( $this, 'create_post_type' ) );
            add_action( 'init', array( $this, 'register_taxonomies' ) );
            add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
            add_action( 'save_post', array( $this, 'save_post' ) );
        }

        public function create_post_type() {

            register_post_type(
                'wj-events',
                array(
                    'label' => 'Event',
                    'description'   => 'Events',
                    'labels' => array(
                        'name'  => 'Events',
                        'singular_name' => 'Event'
                    ),
                    'public'    => true,
                    'supports'  => array( 'title', 'editor', 'thumbnail' ),
                    'hierarchical'  => false,
                    'show_ui'   => true,
                    'rewrite' => [ 'slug' => 'event' ],
                    'show_in_menu'  => false,
                    'menu_position' => 5,
                    'show_in_admin_bar' => true,
                    'show_in_nav_menus' => true,
                    'can_export'    => true,
                    'has_archive'   => true,
                    'exclude_from_search'   => false,
                    'publicly_queryable'    => true,
                    'show_in_rest'  => true,
                    'menu_icon' => 'dashicons-calendar-alt'
                )
            );
        }

        public function register_taxonomies() {

            register_taxonomy(
                'event_cat',
                'wj-events',
                array(
                    'hierarchical' => true,
                    'labels' => array(
                        'name' => 'categories',
                        'singular_name' => 'Category',
                        'menu_name' => 'categories',
                    ),
                'show_ui' => true,
                'show_admin_column' => true,
                )
            );

            register_taxonomy(
                'event_tags',
                'wj-events',
                array(
                    'hierarchical' => false,
                    'labels' => array(
                        'name' => 'Tags',
                        'singular_name' => 'tag',
                        'menu_name' => 'tags',
                    ),
                'show_ui' => true,
                'show_admin_column' => true,
                )
            );
        }

        public function add_meta_boxes( $post ) {

            add_meta_box(
                'wj_events_meta_box',
                'Events Details',
                array( $this, 'add_inner_meta_boxes' ),
                'wj-events',
                'normal',
                'high'
            );
        }

        public function add_inner_meta_boxes( $post ) {

            require_once( WJ_EVENTS_PATH . 'views/wj-events_metaboxes.php' );
        }

        public function save_post( $post_id ) {

            if( isset( $_POST['wj_events_nonce'] ) ){
                if( ! wp_verify_nonce( $_POST['wj_events_nonce'], 'wj_events_nonce' ) ){
                    return;
                }
            }

            if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
                return;
            }

            if( isset( $_POST['post_type'] ) && $_POST['post_type'] === 'wj-events' ){
                if( ! current_user_can( 'edit_page', $post_id ) ){
                    return;
                }elseif( ! current_user_can( 'edit_post', $post_id ) ){
                    return;
                }
            }

            if( isset( $_POST['action'] ) && $_POST['action'] == 'editpost' ) {

                $old_events_date = get_post_meta( $post_id, 'wj_events_date', true );
                $new_events_date = sanitize_text_field( $_POST['wj_events_date'] );

                $old_events_place = get_post_meta( $post_id, 'wj_events_place', true );
                $new_events_place = sanitize_text_field( $_POST['wj_events_place'] );

                $old_events_ticket_url = get_post_meta( $post_id, 'wj_events_ticket_url', true );
                $new_events_ticket_url = esc_url_raw( $_POST['wj_events_ticket_url'] );

                if( empty( $new_events_date ) ) {
                    update_post_meta( $post_id, 'wj_events_date', date("Y/m/d") );
                } else {
                    update_post_meta( $post_id, 'wj_events_date', $new_events_date, $old_events_date );
                }

                if( empty( $new_events_place  ) ) {
                    update_post_meta( $post_id, 'wj_events_place', 'Event Place' );
                } else {
                    update_post_meta( $post_id, 'wj_events_place', $new_events_place, $old_events_place );
                }
                if( empty( $new_events_ticket_url  ) ) {
                    update_post_meta( $post_id, 'wj_events_ticket_url', '#' );
                } else {
                    update_post_meta( $post_id, 'wj_events_ticket_url', $new_events_ticket_url, $old_events_ticket_url );
                }
            }

        }

    }

}