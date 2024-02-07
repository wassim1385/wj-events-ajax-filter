<?php 

if( ! class_exists('WJ_Past_Events_Shortcode')){

    class WJ_Past_Events_Shortcode {
        public function __construct() {

            add_shortcode( 'wj_past_events', array( $this, 'add_shortcode' ) );
        }

        public function add_shortcode(){
            
            ob_start();
            require( WJ_EVENTS_PATH . 'views/wj-past-events_shortcode.php' );
            return ob_get_clean();
        }
    }
}