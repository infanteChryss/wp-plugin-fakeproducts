<?php
/*
    Plugin Name: WC Fake Products
    Plugin URI: http://chryss-fakeproducts.epizy.com/
    Description: A simple WooCommerce extension plugin for importing fake products.
    Version: 1.0.0
    Author: Chryss Infante
    Author URI: http://chryss.epizy.com/
    Text Domain: wc-fake-products
*/

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! defined( 'WC_FAKE_PRODUCTS_PLUGINPATH' ) ) {
	define('WC_FAKE_PRODUCTS_PLUGINPATH', plugin_dir_path( __FILE__ ));
}

if( ! class_exists('WC_FAKE_PRODUCTS_PLUGINPATH') ) :

    class WC_Fake_Products {

        private $section_id = 'wcfakeproducts';

        /**
        *   Constructor
        */
        public function __construct() {
            // Create the section beneath the products tab
            add_filter('woocommerce_get_sections_products', array( $this, 'wc_fake_products_add_section') );
            // Add section's settings
            add_filter( 'woocommerce_get_settings_products', array( $this, 'wc_fake_products_section_settings'), 10, 2 );
            // Add button type support
            add_action( 'woocommerce_admin_field_button' , array( $this, 'wc_fake_products_admin_field_button') );
            // Add ajax handler
            add_action('admin_enqueue_scripts', array( $this, 'add_ajax_handler') );
            // Add ajax action
            add_action( 'wp_ajax_import_products', array( $this, 'import_products') );
        } 
        
        // Add section
        function wc_fake_products_add_section( $sections ) {
            $sections[ $this->section_id ] = __( 'Fake Products', 'wc-fake-products' );
            return $sections;
        }

        // Custom type - button
        function wc_fake_products_admin_field_button( $value ){
            $option_value = (array) WC_Admin_Settings::get_option( $value['id'] );
            $description = WC_Admin_Settings::get_field_description( $value );  ?>        
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label for="<?=esc_attr( $value['id'] )?>">
                            <?=esc_html( $value['title'] )?>
                        </label>
                        <?=$description['tooltip_html']?>
                    </th>
                    <td class="forminp forminp-<?=sanitize_title( $value['type'] )?>">
                        <button role="button"
                            id   ="<?=esc_attr( $value['id'] )?>"
                            name ="<?=esc_attr( $value['name'] )?>"
                            style="<?=esc_attr( $value['css'] )?>"
                            class="<?=esc_attr( $value['class'] )?>"
                        ><?=esc_attr( $value['name'] )?></button>
                        <?=$description['description']?>
                    </td>
                </tr>
                <!-- hide save changes button -->
                <style>p.submit{display:none;}</style>
            <?php       
        }

        // Section Settings
        function wc_fake_products_section_settings( $settings, $current_section ) {
            // If the current section is 'wcfakeproducts'
            if ( $this->section_id == $current_section ) {
                $section_settings = array();

                // Add Title to the Settings
                $section_settings[] = array(
                    'name' => __( 'Import Fake Products', 'wc-fake-products' ),
                    'type' => 'title',
                    'desc' => __( 'Click import button to import fake products', 'wc-fake-products' ),
                    'id' => $this->section_id
                );
                
                // Add button that triggers import
                $section_settings[] = array(
                    'title' => __( 'Import Products', 'wc-fake-products' ),
                    'desc' => __( 'Note that importing products will take a while, please be patient.', 'wc-fake-products' ),
                    'name' => __( 'Import', 'wc-fake-products' ),
                    'type' => 'button',
                    'class' => 'button-secondary',
                    'id'	=> $this->section_id . '_import_button'
                );

                // end of settings
                $section_settings[] = array (
                    'type' => 'sectionend',
                    'id' => $this->section_id
                );
                return $section_settings;
            
            /**
             * If not, return the standard settings
             **/
            } else {
                return $settings;
            }
        }

        // AJAX handler
        function add_ajax_handler(){
            wp_register_script( 'ajaxHandle',  plugins_url( '/ajax/jquery.ajax.js', __FILE__ ), array('jquery'), false, true );
            wp_enqueue_script( 'ajaxHandle' );
            wp_localize_script( 'ajaxHandle', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'nonce' => wp_create_nonce('ajax-nonce') ) );
        }

        /**
         *  AJAX / Functions
         */

        function import_products() {
            // Check for nonce security      
            if ( ! wp_verify_nonce( $_POST['nonce'], 'ajax-nonce' ) ) {
                die ( 'Code is Poetry.');
            } else {
                require_once( WC_FAKE_PRODUCTS_PLUGINPATH . '/ajax/import-products.php');
                wp_die();
            }
        }

        /**
         * Activation Hook 
         */
        public function activation_hook() {
            // register vendor role with author capabilities
            if( !wp_roles()->is_role( 'vendor' ) ) {
                add_role( 'vendor', 'Vendor', get_role( 'author' )->capabilities );
            }
            // flush
            flush_rewrite_rules();
        }

        /**
         * Uninstall Hook 
         */
        public function uninstall_hook() { 
            // get all vendor ( Set numberposts to -1 to get 'ALL' posts)
            $vendors = get_users( array('role' => 'vendor') );
            foreach ($vendors as $vendor) {
                // delete user
                wp_delete_user($vendor->ID);
            }
            // remove vendor role
            remove_role( 'vendor' );
            // get all post ( Set numberposts to -1 to get 'ALL' posts)
            $all_posts = get_posts( array('post_type' => 'product', 'numberposts' => -1) );
            foreach ($all_posts as $post) {
                // force delete a post
                wp_delete_post( $post->ID, true );
            }
        }
    }

    /** 
     * WC Fake Products initialization
     */
    function wc_fake_products_init( $settings ) {
        new WC_Fake_Products();
    }

    /** 
     * Missing WooCommerce admin notice
     */
    function wc_fake_products_wc_missing_notice() {
        /* translators: %s WooCommerce download URL link. */
        echo '<div class="error"><p><strong>' . sprintf( esc_html__(  'WC Fake Products requires WooCommerce to run. You can %s here.', 'wc-fake-products' ), '<a href="https://wordpress.org/plugins/woocommerce/" target="_blank">Download WooCommerce</a>' ) . '</strong></p></div>';
    }

    if ( ! function_exists( 'is_plugin_active' ) ) {
        require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
    }

    /**
     * Check if WooCommerce is active
     */
    if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) { // WooCommerce Docs: in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )
        // if WC is active, initialize plugin once activated plugins have been loaded
        add_action( 'plugins_loaded', 'wc_fake_products_init' );

            // register hooks
    register_activation_hook( __FILE__, array( 'WC_Fake_Products', 'activation_hook') );
    register_deactivation_hook( __FILE__, array( 'WC_Fake_Products', 'uninstall_hook') ); // since this is a test plugin I will run the uninstall function on plugin deactivation 
    } else { 
        // display admin notice
        add_action( 'admin_notices', 'wc_fake_products_wc_missing_notice' );
    }

endif; // class_exists check