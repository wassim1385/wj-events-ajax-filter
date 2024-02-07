<?php

/**
*Plugin Name: WJ Events
*Plugin URI: https://wordpress.org/wj-events
*Description: My plugin's description
*Version: 1.0
*Requires at least: 5.6
*Author: Wassim Jelleli
*Author URI: https://www.linkedin.com/in/wassim-jelleli/
*Text Domain: wj-events
*Domain Path: /languages
*/

if ( ! defined ( 'ABSPATH' ) ) {
    exit;
}

if( ! class_exists( 'WJ_Events' ) ) {

    class WJ_Events {

        public function __construct() {

            $this->define_constants();

            require_once( WJ_EVENTS_PATH . 'cpt/class.wj-events-cpt.php' );
            $wj_events_cpt = new WJ_Events_Post_Type();

            require_once( WJ_EVENTS_PATH . 'shortcode/class.wj-events-shortcode.php' );
            $wj_events_shortcode = new WJ_Events_Shortcode();

            require_once( WJ_EVENTS_PATH . 'shortcode/class.wj-past-events-shortcode.php' );
            $wj_pas_events_shortcode = new WJ_Past_Events_Shortcode();

            require_once( WJ_EVENTS_PATH . 'class.wj-events-settings.php' );
            $wj_Settings = new WJ_Events_Settings();

            add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ), 999 );

            add_action( 'admin_menu', array( $this, 'add_menu' ) );

            add_action( 'wp_ajax_filter', array( $this, 'filter_ajax' ) );
            add_action( 'wp_ajax_nopriv_filter', array( $this, 'filter_ajax' ) );

        }

        public function define_constants() {

            define( 'WJ_EVENTS_PATH', plugin_dir_path( __FILE__ ) );
            define( 'WJ_EVENTS_URL', plugin_dir_url( __FILE__ ) );
            define( 'WJ_EVENTS_VERSION', '1.0.0' );
        }

        public function register_scripts() {

            wp_register_script( 'filter-script', WJ_EVENTS_URL . 'assets/js/filter-script.js', array( 'jquery' ), WJ_EVENTS_VERSION, true );
            wp_register_style( 'wj-events-front', WJ_EVENTS_URL . 'assets/css/frontend.css', array(), WJ_EVENTS_VERSION, 'all' );
        }

        public function filter_ajax() {

            $today = date("Y-m-d");
            $args = array(
                'post_type' => 'wj-events',
                'post_status' => 'publish',
                'meta_key' => 'wj_events_date',
                'orderby' => 'meta_value',
                'meta_type' => 'DATE',
                'order' => 'ASC',
                'meta_query' => array(
                    array(
                        'key' => 'wj_events_date',
                        'compare' => '>=',
                        'value' => $today,
                        'type' => 'DATE'
                    )
                )
            );

            $type = $_POST['cat'];
            $tags = $_POST['events-keywords'];

            if( ! empty( $type ) ) {
                $args['tax_query'][] = array(
                    'taxonomy' => 'event_cat',
                    'field' => 'slug',
                    'terms' => array($type)
                );
            }
            if( ! empty( $tags ) ) {
                $args['tax_query'][] = array(
                    'taxonomy' => 'event_tags',
                    'field' => 'slug',
                    'terms' => $tags
                );
            }
            $events = new WP_Query( $args );
            if( $events->have_posts() ) : ?>
                
                    <?php while ($events->have_posts() ) : $events->the_post(); ?>
                        <?php
                        $event_date = get_post_meta( get_the_ID(), 'wj_events_date', true );
                        $event_place = get_post_meta( get_the_ID(), 'wj_events_place', true );
                        $event_ticket_url = get_post_meta( get_the_ID(), 'wj_events_ticket_url', true );
                        ?>
                        <div class="wje-container">
                            <?php
                            if( has_post_thumbnail() ) : ?>
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail( 'medium', array( 'class' => 'img-fluid' ) ); ?>
                                </a>
                            <?php endif; ?>
                            <div class="event-title">
                                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                            </div>
                            <?php

                                $terms = get_the_terms( get_the_ID(), 'event_cat' );
                                echo join( ', ', wp_list_pluck( $terms, 'name') );
                                echo '<br/>';
                                $tags = get_the_terms( get_the_ID(), 'event_tags' );
                                echo join( ', ', wp_list_pluck( $tags, 'name') );
                            ?>
                            <div class="event-description">
                                <div class="event-meta">
                                    <?php echo esc_html( $event_date ) ?> <?php esc_html_e( ' in ', 'wj-events' ); ?>
                                    <b><?php echo esc_html( $event_place ); ?></b> </br>
                                </div>
                                <div class="description">
                                    <?php the_content(); ?>
                                </div>
                                <button><a href="<?php echo esc_url($event_ticket_url) ?>"><?php esc_html_e( 'Book Now', 'wj-events' ) ?></a></button>
                            </div>
                        </div>
                    <?php endwhile ; ?>
                
            <?php endif; wp_die();
        }


        public function add_menu() {
            
            add_menu_page(
                'WJ Events Options',
                'WJ Events',
                'manage_options',
                'wj_events_admin',
                array( $this, 'wj_events_settings_page' ),
                'dashicons-calendar-alt',
                100
            );

            add_submenu_page(
                'wj_events_admin',
                'Manage Events',
                'Manage Events',
                'manage_options',
                'edit.php?post_type=wj-events',
                null,
                null
            );

            add_submenu_page(
                'wj_events_admin',
                'Add New Event',
                'Add New Event',
                'manage_options',
                'post-new.php?post_type=wj-events',
                null,
                null
            );

            add_submenu_page(
                'wj_events_admin',
                'Add Category',
                'Add Category',
                'manage_options',
                'edit-tags.php?taxonomy=event_cat&post_type=wj-events',
                null,
                null
            );

            add_submenu_page(
                'wj_events_admin',
                'Add Tag',
                'Add Tag',
                'manage_options',
                'edit-tags.php?taxonomy=event_tags&post_type=wj-events',
                null,
                null
            );
        }

        public function wj_events_settings_page() {

            if( ! current_user_can( 'manage_options' ) ) {
                return;
            }
            if( isset( $_GET['settings-updated'] ) ) {
                add_settings_error( 'wj_events_options', 'wj_events_message', 'Settings Saved', 'success' );
            }
            settings_errors( 'wj_events_options' );
            require( WJ_EVENTS_PATH . 'views/settings-page.php' );
        }

        public static function activate() {

            update_option( 'rewrite_rules', '' );
            global $post;
            if( $post->post_name !== 'past-events' ) {

                $current_user = wp_get_current_user();
                $page = array(
                    'post_title' => __( 'Past events', 'wj-events' ),
                    'post_name' => 'past-events',
                    'post_status' => 'publish',
                    'post_author' => $current_user->ID,
                    'post_type' => 'page',
                    'post_content' => '<!-- wp:shortcode -->[wj_past_events]<!-- /wp:shortcode -->'
                );
                wp_insert_post( $page );
            }
        }

        public static function deactivate() {

            flush_rewrite_rules();
            unregister_post_type( 'wj-events' );
        }

        public static function uninstall() {

        }
    }
}

if( class_exists( 'WJ_Events' ) ) {

    register_activation_hook( __FILE__, array( 'WJ_Events', 'activate' ) );
    register_deactivation_hook( __FILE__, array( 'WJ_Events', 'deactivate' ) );
    register_uninstall_hook( __FILE__, array( 'WJ_Events', 'uninstall' ) );

    $wj_events = new WJ_Events();
}