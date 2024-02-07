<?php

if( ! class_exists('WJ_Events_Settings') ) {

    Class WJ_Events_Settings {

        public static $options;

        public function __construct() {

            self::$options = get_option( 'wj_events_options' );
            add_action( 'admin_init', array( $this, 'admin_init' ) );
        }

        public function admin_init() {

            register_setting( 'wj_events_group', 'wj_events_options', array( $this, 'wj_events_validate' ) );

            add_settings_section(
                'wj_events_main_section',
                'How does it work?',
                null,
                'wj_events_page1'
            );
            add_settings_field(
                'wj_events_shortcode',
                'Shortcode',
                array( $this, 'wj_events_shortcode_callback' ),
                'wj_events_page1',
                'wj_events_main_section'
            );
            add_settings_section(
                'wj_events_second_section',
                'Other plugin Options',
                null,
                'wj_events_page2'
            );
            add_settings_field(
                'wj_events_filter',
                'Filter Section',
                array( $this, 'wj_events_filter_callback' ),
                'wj_events_page2',
                'wj_events_second_section'
            );
        }
        public function wj_events_shortcode_callback() {
            ?>
            <span>Use the shortcode [wj_events] to display the events in a page/post/widget</span>
            <?php
        }

        public function wj_events_filter_callback() {
            ?>
            <input 
                type="checkbox"
                name="wj_events_options[wj_events_filter]"
                id="wj_events_filter"
                value="1"
                <?php 
                    if( isset( self::$options['wj_events_filter'] ) ){
                        checked( "1", self::$options['wj_events_filter'], true );
                    }    
                ?>
            />
            <label for="wj_events_filter"><?php esc_html_e( 'Whether to display Filter Section or not', 'wj-events' ) ?></label>
            <?php
        }

        public function wj_events_validate( $input ) {

            $new_input = array();
            foreach( $input as $key => $value ) {
                $new_input[$key] = sanitize_text_field( $value );
            }
            return $new_input;
        }
    }
}
