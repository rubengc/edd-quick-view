<?php
/**
 * Functions
 *
 * @package     EDD\Quick_View\Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'EDD_Quick_View_Functions' ) ) {

    class EDD_Quick_View_Functions {

        public function __construct() {
            // Easy Digital Downloads [downloads] shortcode hooks
            add_filter( 'shortcode_atts_downloads', array( $this, 'shortcode_atts_downloads' ), 10, 4 );
            add_filter( 'edd_downloads_list_wrapper_class', array( $this, 'edd_downloads_list_wrapper_class' ), 10, 2 );

            // Ajax requests
            add_action( 'wp_ajax_edd_quick_view', array( $this, 'quick_view' ) );
            add_action( 'wp_ajax_nopriv_edd_quick_view', array( $this, 'quick_view' ) );
        }

        // [downloads] custom attributes
        public function shortcode_atts_downloads( $out, $pairs, $atts, $shortcode ) {
            // Default custom attributes
            $custom_pairs = array(
                'quick_view'        => (bool) edd_quick_view()->options->get( 'enabled_by_default', false ) ? 'yes' : 'no',
            );

            foreach ($custom_pairs as $name => $default) {
                if ( array_key_exists( $name, $atts ) )
                    $out[$name] = $atts[$name];
                else
                    $out[$name] = $default;
            }

            return $out;
        }

        // edd_quick_view classes on edd_download_list_wrapper class
        public function edd_downloads_list_wrapper_class( $wrapper_class, $atts ) {
            if( $atts['quick_view'] == 'yes' ) {
                $wrapper_class .= ' edd-quick-view';

                // Hook to add quick view link
                add_action('edd_purchase_link_top', array($this, 'quick_view_link'));
            }

            return $wrapper_class;
        }

        // Quick view link
        public function quick_view_link( $download_id = null ) {
            if( $download_id == null ) {
                $download_id = get_the_ID();
            }

            ?>
            <a href="#" class="edd-quick-view-button" data-download-id="<?php echo $download_id; ?>">
                <span class="edd-quick-view-label"><?php echo edd_quick_view()->options->get( 'button_label', __( 'Quick view', 'edd-quick-view' ) ); ?></span>
                <span class="edd-loading" aria-label="Loading"></span>
            </a>
            <?php
        }

        /**
         * Download quick view
         */
        public function quick_view() {
            if ( ! isset( $_REQUEST['nonce'] ) && ! wp_verify_nonce( $_REQUEST['nonce'], 'edd_quick_view_nonce' ) ) {
                wp_send_json_error( 'invalid_nonce' );
                wp_die();
            }

            $download_id = isset( $_REQUEST['download_id'] ) ? $_REQUEST['download_id'] : null;

            // Download id is required to continue
            if( $download_id == null || $download_id == 0 || empty( $download_id ) ) {
                wp_send_json_error( 'missing_download_id' );
                wp_die();
            }

            global $post;

            $post = get_post( $download_id );

            setup_postdata( $post );

            $response = array(
                'html' => '',
            );

            // Quick view content
            ob_start(); ?>

            <div class="edd_download_image">
                <a href="<?php the_permalink(); ?>">
                    <?php echo get_the_post_thumbnail( get_the_ID(), array( 80, 80 ), array( 'title' => esc_attr( get_the_title() ) ) ); ?>
                </a>
            </div>

            <div class="edd_download_title">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </div>

            <div class="edd_price edd_download_price">
                <?php edd_price( get_the_ID() ); ?>
            </div>

            <?php if ( has_excerpt() ) : ?>
                <div class="edd_download_excerpt">
                    <?php echo wp_trim_words( get_post_field( 'post_excerpt', get_the_ID() ), 30 ); ?>
                </div>
            <?php elseif ( get_the_content() ) : ?>
                <div class="edd_download_excerpt">
                    <?php echo wp_trim_words( get_post_field( 'post_content', get_the_ID() ), 30 ); ?>
                </div>
            <?php endif; ?>

            <?php $response['html'] = ob_get_clean();

            wp_send_json( $response );
            wp_die();
        }

    }

}