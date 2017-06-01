<?php
/**
 * Options
 *
 * @package     EDD\Quick_View\Options
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'EDD_Quick_View_Options' ) ) {

    class EDD_Quick_View_Options extends uFramework_Options {

        public function __construct() {
            $this->options_key = 'edd-quick-view';

            add_filter( 'tsunoa_' . $this->options_key . '_settings', array( $this, 'register_settings_url' ) );

            parent::__construct();
        }

        public function register_settings_url( $url ) {
            return 'admin.php?page=' . $this->options_key;
        }

        /**
         * Add the options metabox to the array of metaboxes
         * @since  0.1.0
         */
        public function register_form() {
            // Options page configuration
            $args = array(
                'key'      => $this->options_key,
                'title'    => __( 'EDD Quick View', 'edd-quick-view' ),
                'topmenu'  => 'tsunoa',
                'cols'     => 2,
                'boxes'    => $this->boxes(),
                'tabs'     => $this->tabs(),
                'menuargs' => array(
                    'menu_title' => __( 'EDD Quick View', 'edd-quick-view' ),
                ),
                'savetxt'  => __( 'Save changes' ),
                'admincss' => '.' . $this->options_key . ' #side-sortables{padding-top: 0 !important;}' .
                    '.' . $this->options_key . '.cmo-options-page .columns-2 #postbox-container-1{margin-top: 0 !important;}' .
                    '.' . $this->options_key . '.cmo-options-page .nav-tab-wrapper{display: none;}'
            );

            // Create the options page
            new Cmb2_Metatabs_Options( $args );
        }

        /**
         * Setup form in settings page
         *
         * @return array
         */
        public function boxes() {
            // Holds all CMB2 box objects
            $boxes = array();

            // Default options to all boxes
            $show_on = array(
                'key'   => 'options-page',
                'value' => array( $this->options_key ),
            );

            // General options box
            $cmb = new_cmb2_box( array(
                'id'      => $this->options_key . '-general',
                'title'   => __( 'General options', 'edd-quick-view' ),
                'show_on' => $show_on,
            ) );

            $cmb->add_field( array(
                'name' => __( 'Enable by default', 'edd-quick-view' ),
                'desc' => __( 'Set by default quick_view="yes" to all [downloads] and [edd_downloads] shortcodes (you can override this option setting it to quick_view="no")', 'edd-quick-view' ),
                'id'   => 'enabled_by_default',
                'type' => 'checkbox',
            ) );

            $cmb->add_field( array(
                'name' => __( 'Label', 'edd-quick-view' ),
                'desc' => '',
                'id'   => 'button_label',
                'type' => 'text',
                'default' => __( 'Quick view', 'edd-quick-view' ),
            ) );

            $cmb->object_type( 'options-page' );

            $boxes[] = $cmb;

            // Submit box
            $cmb = new_cmb2_box( array(
                'id'      => $this->options_key . '-submit',
                'title'   => __( 'Save changes', 'edd-ajax-search' ),
                'show_on' => $show_on,
                'context' => 'side',
            ) );

            $cmb->add_field( array(
                'name' => '',
                'desc' => '',
                'id'   => 'submit_box',
                'type' => 'title',
                'render_row_cb' => array( $this, 'submit_box' )
            ) );

            $cmb->object_type( 'options-page' );

            $boxes[] = $cmb;

            // Shortcode box
            $cmb = new_cmb2_box( array(
                'id'      => $this->options_key . '-shortcode',
                'title'   => __( 'Shortcode generator', 'edd-quick-view' ),
                'show_on' => $show_on,
                'context' => 'side',
            ) );

            $cmb->add_field( array(
                'name' => '',
                'desc' => __( 'From this options page you can configure default parameters for EDD Quick View. Also using form bellow you can generate a shortcode to place it in any page.', 'edd-quick-view' ),
                'id'   => 'shortcode_generator',
                'type' => 'title',
                'after' => array( $this, 'shortcode_generator' ),
            ) );

            $cmb->object_type( 'options-page' );

            $boxes[] = $cmb;

            return $boxes;
        }

        /**
         * Settings page tabs
         *
         * @return array
         */
        public function tabs() {
            $tabs = array();

            $tabs[] = array(
                'id'    => 'general',
                'title' => 'General',
                'desc'  => '',
                'boxes' => array(
                    $this->options_key . '-general',
                ),
            );

            return $tabs;
        }

        /**
         * Submit box
         *
         * @param array      $field_args
         * @param CMB2_Field $field
         */
        public function submit_box( $field_args, $field ) {
            ?>
            <p>
                <a href="<?php echo tsunoa_product_docs_url( $this->options_key ); ?>" target="_blank" class="uframework-icon-link"><i class="dashicons dashicons-media-text"></i> <?php _e( 'Documentation' ); ?></a>
                <a href="<?php echo tsunoa_product_url( $this->options_key ); ?>" target="_blank" class="uframework-icon-link"><i class="dashicons dashicons-cart"></i> <?php _e( 'Get support and pro features', 'edd-ajax-search' ); ?></a>
            </p>
            <div class="cmb2-actions">
                <input type="submit" name="submit-cmb" value="<?php _e( 'Save changes' ); ?>" class="button-primary">
            </div>
            <?php
        }

        /**
         * Shortcode generator
         *
         * @param array      $field_args
         * @param CMB2_Field $field
         */
        public function shortcode_generator( $field_args, $field ) {
            ?>
            <div id="edd-quick-view-shortcode-form" class="uframework-shortcode-generator">
                <p>
                    <textarea type="text" id="edd-quick-view-shortcode-input" data-shortcode="downloads" readonly="readonly">[downloads quick_view="yes"]</textarea>
                </p>

                <input type="hidden" id="shortcode_form_quick_view" data-shortcode-attr="quick_view" value="yes">
            </div>
            <?php
        }

    }

}