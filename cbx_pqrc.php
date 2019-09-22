<?php
	/*
	Plugin Name: Cbx Posts To QrCode
	Plugin URL:
	Description: QrCode for posts. It will display under every single post.
	Version: 1.0
	Author: Imtiaz Epu
	Author URI: https://imtiazepu.com/
	License: GPLv2 or later
	Text Domain: cbx-pqrc
	Domain Path: /languages/
	 */
	
	if ( ! class_exists( 'Cbx_pqrc' ) ) {
		class Cbx_pqrc {
			
			/**
			 * Cbx_pqrc constructor.
			 */
			public function __construct() {
				add_action( 'plugins_loaded', array( $this, 'load_text_domain' ) );
				add_filter( "the_content", array( $this, "cbx_post_to_qrcode" ) );
				add_action( 'admin_init', array( $this, 'cbx_setting_init' ) );
				add_action( 'admin_menu', array( $this, 'cbx_pqrc_option_page' ) );
			}// End method constructor
			
			
			/**
			 * Load text domain
			 */
			public function load_text_domain() {
				
				load_plugin_textdomain( 'cbx-pqrc', false, dirname( __FILE__ ) . "/languages" );
				
			}// End Method load_text_domain
			
			
			/**
			 * Display QRcode
			 *
			 * @param $content
			 *
			 * @return string
			 */
			public function cbx_post_to_qrcode( $content ) {
				$current_post_id    = get_the_ID();
				$current_post_url   = urlencode( get_the_permalink( $current_post_id ) );
				$current_post_title = get_the_title( $current_post_id );
				//$currrent_post_type = get_post_type( $current_post_id );
				
				$width  = get_option( 'pqrc_width' );
				$height = get_option( 'pqrc_width' );
				
				$width     = $width ? $width : 100;
				$height    = $height ? $height : 100;
				$dimension = apply_filters( 'pqrc_qrcode_dimension', "{$width}x{$height}" );
				
				if ( is_singular( 'post' ) ) {
					$img_src = sprintf( 'https://api.qrserver.com/v1/create-qr-code/?size=%s&data=%s', $dimension,
						$current_post_url );
					$content .= sprintf( "<div>QR Code: <img src='%s' alt='%s'></div>", $img_src, $current_post_title );
				}
				
				return $content;
				
				
			}// End Method cbx_post_to_qrcode
			
			
			/**
			 * Setting Initial
			 */
			public function cbx_setting_init() {
				
				add_settings_section( "pqrc_section", __( 'Post to QR code', 'cbx-pqrc' ), 'pqrc_setting_section',
					'postToQrcode' );
				add_settings_field( "pqrc_width", __( 'QR code image width', 'cbx-pqrc' ), 'pqrc_display_field',
					'postToQrcode',
					'pqrc_section', array( 'pqrc_width' ) );
				add_settings_field( "pqrc_height", __( 'QR code image height', 'cbx-pqrc' ), 'pqrc_display_field',
					'postToQrcode',
					'pqrc_section', array( 'pqrc_height' ) );
				
				
				register_setting( 'postToQrcode', "pqrc_width", array( 'sanitize_callback' => "esc_attr" ) );
				register_setting( 'postToQrcode', "pqrc_height", array( 'sanitize_callback' => "esc_attr" ) );
				
				
				/**
				 * Setting Section Callback function
				 */
				function pqrc_setting_section() {
					echo "<p>" . __( "Setting for Post to QR code Plugin" ) . "</p>";
				}//end function pqrc_setting_section
				
				
				/**
				 * Setting Fields Callback function
				 *
				 * @param $arg
				 */
				function pqrc_display_field( $arg ) {
					$option = get_option( $arg[0] );
					printf( "<input type='text' id='%s' name='%s' value='%s'>", $arg[0], $arg[0], $option );
				}// end function pqrc_display_field
				
			}//End Method cbx_setting_init
			
			/**
			 * top level menu initial
			 */
			public function cbx_pqrc_option_page() {
				$main_menu_hook = add_menu_page(
					'Post to QRcode',
					'Post to QRcode',
					'manage_options',
					'postToQrcode',
					array( $this, 'Post_to_Qrcode' ),
					'dashicons-code-standards',
					20
				);
			}//end method cbx_pqrc_option_page
			
			
			/**
			 * Top level menu callback
			 */
			public function Post_to_Qrcode() {
				?>
                <div class="wrap">
                    <form action="options.php" method="post">
						<?php
							settings_fields( 'postToQrcode' );
							do_settings_sections( 'postToQrcode' );
							submit_button( 'Save Settings' );
						?>
                    </form>
                </div>
				<?php
			}//end method Post_to_Qrcode
			
		}// End Class Cbx_pqrc
		
	}//End Pluggable Functions
	
	$cbx_pqrc = new Cbx_pqrc();
