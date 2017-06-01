<?php
/**
 * Plugin Name:     EDD Quick View
 * Plugin URI:      https://tsunoa.com/plugins/edd-quick-view
 * Description:     Product quick view for Easy Digital Downloads.
 * Version:         1.0.0
 * Author:          Tsunoa
 * Author URI:      https://tsunoa.com
 * Text Domain:     edd-quick-view
 *
 * @package         EDD\Quick_View
 * @author          Tsunoa
 * @copyright       Copyright (c) Tsunoa
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'EDD_Quick_View' ) ) {

    /**
     * Main EDD_Quick_View class
     *
     * @since       1.0.0
     */
    class EDD_Quick_View {

        /**
         * @var         EDD_Quick_View $instance The one true EDD_Quick_View
         * @since       1.0.0
         */
        private static $instance;

        /**
         * @var         EDD_Quick_View_Functions EDD Quick View functions
         * @since       1.0.0
         */
        protected $functions;

        /**
         * @var         EDD_Quick_View_Options EDD Quick View options
         * @since       1.0.0
         */
        public $options;

        /**
         * @var         EDD_Quick_View_Scripts EDD Quick View scripts
         * @since       1.0.0
         */
        protected $scripts;

        /**
         * Get active instance
         *
         * @access      public
         * @since       1.0.0
         * @return      object self::$instance The one true EDD_Quick_View
         */
        public static function instance() {
            if( !self::$instance ) {
                self::$instance = new EDD_Quick_View();
                self::$instance->setup_constants();
                self::$instance->includes();
                self::$instance->load_textdomain();
            }

            return self::$instance;
        }

        /**
         * Setup plugin constants
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function setup_constants() {
            // Plugin version
            define( 'EDD_QUICK_VIEW_VER', '1.0.0' );

            // Plugin path
            define( 'EDD_QUICK_VIEW_DIR', plugin_dir_path( __FILE__ ) );

            // Plugin URL
            define( 'EDD_QUICK_VIEW_URL', plugin_dir_url( __FILE__ ) );
        }

        /**
         * Include necessary files
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function includes() {
            require_once EDD_QUICK_VIEW_DIR . 'uFramework/uFramework.php';

            // Include scripts
            require_once EDD_QUICK_VIEW_DIR . 'includes/functions.php';
            require_once EDD_QUICK_VIEW_DIR . 'includes/options.php';
            require_once EDD_QUICK_VIEW_DIR . 'includes/scripts.php';


            $this->functions = new EDD_Quick_View_Functions();
            $this->options = new EDD_Quick_View_Options();
            $this->scripts = new EDD_Quick_View_Scripts();
        }

        /**
         * Internationalization
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
        public function load_textdomain() {
            // Set filter for language directory
            $lang_dir = EDD_QUICK_VIEW_DIR . '/languages/';
            $lang_dir = apply_filters( 'edd_quick_view_languages_directory', $lang_dir );

            // Traditional WordPress plugin locale filter
            $locale = apply_filters( 'plugin_locale', get_locale(), 'edd-quick-view' );
            $mofile = sprintf( '%1$s-%2$s.mo', 'edd-quick-view', $locale );

            // Setup paths to current locale file
            $mofile_local   = $lang_dir . $mofile;
            $mofile_global  = WP_LANG_DIR . '/edd-quick-view/' . $mofile;

            if( file_exists( $mofile_global ) ) {
                // Look in global /wp-content/languages/edd-quick-view/ folder
                load_textdomain( 'edd-quick-view', $mofile_global );
            } elseif( file_exists( $mofile_local ) ) {
                // Look in local /wp-content/plugins/edd-quick-view/languages/ folder
                load_textdomain( 'edd-quick-view', $mofile_local );
            } else {
                // Load the default language files
                load_plugin_textdomain( 'edd-quick-view', false, $lang_dir );
            }
        }
    }
}


/**
 * The main function responsible for returning the one true EDD_Quick_View
 * instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \EDD_Quick_View The one true EDD_Quick_View
 */
function edd_quick_view() {
    return EDD_Quick_View::instance();
}
add_action( 'plugins_loaded', 'edd_quick_view' );


/**
 * EDD_Quick_View activation
 *
 * @since       1.0.0
 * @return      void
 */
function edd_quick_view_activation() {
    // Default option => value
    $options = array(
        'enabled_by_default' => 'on',
        'mode' => 'dialog',
        'pagination' => 'on',
        'download_thumbnail' => 'on',
        'download_title' => 'on',
        'download_author' => 'on',
        'download_categories' => 'on',
        'download_price' => 'on',
        'download_tags' => 'on',
        'download_excerpt' => 'on',
        'download_purchase' => 'on',
        'position' => array(
            'my_horizontal' => 'center',
            'at_horizontal' => 'center',
            'collision_horizontal' => 'flip',
        ),
    );

    $opts = array();

    foreach($options as $option => $value) {
        $opts[$option] = $value;
    }

    add_option( 'edd-quick-view', $options );
}
register_activation_hook( __FILE__, 'edd_quick_view_activation' );