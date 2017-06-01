<?php
/**
 * Scripts
 *
 * @package     EDD\Quick_View\Scripts
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'EDD_Quick_View_Scripts' ) ) {

    class EDD_Quick_View_Scripts {

        public function __construct() {
            // Register scripts
            add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );

            // Enqueue frontend scripts
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 100 );
        }

        /**
         * Register scripts
         *
         * @since       1.0.0
         * @return      void
         */
        public function register_scripts() {
            // Use minified libraries if SCRIPT_DEBUG is turned off
            $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

            // Stylesheets
            wp_register_style( 'edd-quick-view-css', EDD_QUICK_VIEW_URL . 'assets/css/edd-quick-view' . $suffix . '.css', array( ), EDD_QUICK_VIEW_VER, 'all' );

            // Scripts
            wp_register_script( 'edd-quick-view-js', EDD_QUICK_VIEW_URL . 'assets/js/edd-quick-view' . $suffix . '.js', array( 'jquery', 'jquery-ui-dialog' ), EDD_QUICK_VIEW_VER, true );
        }

        /**
         * Enqueue frontend scripts
         *
         * @since       1.0.0
         * @return      void
         */
        public function enqueue_scripts( $hook ) {
            // Localize scripts
            $script_parameters = array(
                'ajax_url'          => admin_url( 'admin-ajax.php' ),
                'nonce'		        => wp_create_nonce( 'edd_quick_view_nonce' ),
            );

            wp_localize_script( 'edd-quick-view-js', 'edd_quick_view', $script_parameters );

            // Stylesheets
            wp_enqueue_style('edd-quick-view-css');

            // Scripts
            wp_enqueue_script( 'edd-quick-view-js' );
        }

    }

}