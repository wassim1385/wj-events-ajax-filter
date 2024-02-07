<?php

if( ! class_exists( 'class WJ_Events_Shortcode ' ) ) {

    class WJ_Events_Shortcode {
    
        public function __construct() {

            add_shortcode( 'wj_events', array( $this, 'add_shortcode' ) );
        }

        public function add_shortcode( $atts = array(), $content = null, $tag ='' ) {

            $atts = array_change_key_case( (array) $atts, CASE_LOWER );
            extract( shortcode_atts(
                array(
                    'id' => ''
                ),
                $atts,
                $tag
            ) );

            if( !empty( $id ) ){
                $id = array_map( 'absint', explode( ',', $id ) );
            }
            
            ob_start();
            require( WJ_EVENTS_PATH . 'views/wj-events-shortcode.php' );
            wp_enqueue_style( 'wj-events-front' );
            wp_enqueue_script( 'filter-script' );
            wp_localize_script( 'filter-script', 'VARS', [ 'ajax_url' => admin_url( 'admin-ajax.php' ) ] );
            return ob_get_clean();
        }
    }

}