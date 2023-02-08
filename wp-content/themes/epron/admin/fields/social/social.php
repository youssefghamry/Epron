<?php
/**
 * Rascals Theme Panel Class
 *
 * @package     RascalsThemePanel
 * @subpackage  text
 * @author      Mariusz Rek
 * @version     2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'RascalsThemePanel_social' ) ) {

	class RascalsThemePanel_social extends RascalsThemePanel {

		private static $_initialized = false;
		private static $_args;
		private static $_saved_options;
		private static $_option;

		private static $socials = array(
			'' 				 => 'Select Service',
			'twitter'        => 'Twitter',
			'facebook'       => 'Facebook',
			'youtube'        => 'Youtube',
			'instagram'      => 'Instagram',
			'soundcloud'     => 'Soundcloud',
			'mixcloud'       => 'Mixcloud',
			'bandcamp'       => 'Bandcamp',
			'Beatport'       => 'Beatport',
			'spotify'        => 'Spotify',
			'itunes-filled'  => 'iTunes',
			'lastfm'         => 'lastfm',
			'vimeo'          => 'Vimeo',
			'vk'             => 'VK',
			'flickr'         => 'Flickr',
			'snapchat-ghost' => 'Snapchat',
			'dribbble'       => 'Dribbble',
			'deviantart'     => 'Deviantart',
			'github'         => 'Github',
			'blogger'        => 'Blogger',
			'yahoo'          => 'Yahoo',
			'finder'         => 'Finder',
			'skype'          => 'Skype',
			'reddit'         => 'Reddit',
			'linkedin'       => 'Linkedin',
			'amazon'         => 'Amazon',
			'telegram'       => 'Telegram',
			'qq'             => 'QQ',
			'weibo'          => 'Weibo',
			'wechat'         => 'Wechat',
		);


		/**
         * Field Constructor.
         *
         * @since       1.0.0
         * @access      public
         * @return      void
        */
		public function __construct( $option, $args, $saved_options ) {

			// Variables
			self::$_args = $args;
			self::$_saved_options = $saved_options;
			self::$_option = $option;
			
			// Only for first instance
			if ( ! self::$_initialized ) {
	            self::$_initialized = true;
	        }

		}

		/**
         * Field Render Function.
         * Takes the vars and outputs the HTML
         *
         * @since 		1.0.0
         * @access  	public
        */
		public function render() {


			if ( isset( self::$_saved_options[self::$_option['id']] ) ) {
				self::$_option['std'] = self::$_saved_options[self::$_option['id']];
			}
			
			if ( ! isset( self::$_option['classes'] ) ) {
				self::$_option['classes'] = '';
			}

			// Depedency
			if ( isset( self::$_option['dependency']) && is_array( self::$_option['dependency'] ) ) {
				echo '<div class="box-row multi-options social-options clearfix dependent-hidden ' . self::$_option['classes'] . '" data-depedency-el="' . esc_attr( self::$_option['dependency']['element'] ) .'" data-depedency-val="'.esc_attr( implode(',', self::$_option['dependency']['value'] ) ).'" data-id="' . esc_attr( self::$_option['id'] ) . '">';
			} else {
				echo '<div class="box-row multi-options social-options clearfix ' . self::$_option['classes'] . '">';
			}

				// Input Wrap
				echo '<div class="box-row-input">';

					// Label
					echo '<div class="box-tc box-tc-label">';
						if ( isset( self::$_option['name'] ) && ( self::$_option['name'] !== '' ) ) {	
							echo '<label>' . esc_html( self::$_option['name'] ) . '</label>';
						}
					echo '</div>';

					// Input
					echo '<div class="box-tc box-tc-input">';
						if ( isset( self::$_option['sub_name'] ) && ( self::$_option['sub_name'] !== '' ) ) {	
							echo '<div class="sub-name">' . esc_html( self::$_option['sub_name'] ) . '</div>';
						}

						// Fields
						// ---------------------------------------
						
						echo '<div class="multi-container">';
						if ( isset( self::$_option['options'] ) && is_array( self::$_option['options'] ) ) {
							$data = json_decode( self::$_option['std'], true );
							$options = self::$_option['options'];
							// We have saved options
							if ( $data ) {
								$options = array_replace_recursive($options, $data);
							}
							
							foreach ($options as $key => $default) {
								
								if ( $default['name'] === 'service' ) {
									echo '<p>';
									echo '<select size="1" class="multi-option select-input select-service" data-multi-default="' . esc_attr( $default['value'] ) . '">';
										foreach ( self::$socials as $key => $social ) {
											if ( isset( $default['value'] ) && $default['value'] === $key ) $selected = 'selected';
											else $selected = '';
											echo "<option " . esc_attr( $selected ) ." value='" . esc_html( $key ) . "'>" . esc_html( $social ) . "</option>";
											}
									echo '</select>';
									echo '</p>';
								} else {
									echo '<p><input type="text" value="' . esc_attr( $default['value'] ) . '"  data-multi-default="' . esc_attr( $default['value'] ) . '" placeholder="' . esc_attr( $default['label'] ) . '" class="multi-option text-input"/></p>';
								}
							}

						}
						echo '</div>';

						// Hidden fileds store all data
						echo '<input type="hidden" name="' . esc_attr( self::$_option['id'] ) . '" id="' . esc_attr( self::$_option['id'] ) . '" value="' . esc_attr( self::$_option['std'] ) . '" class="multi-data"/>';

						// ----------------------------------------

						// Display help
						if ( isset( self::$_option['desc'] ) && self::$_option['desc'] !== '' ) {
							echo '<div class="help-box">';
							$this->e_esc( self::$_option['desc'] );
							echo '</div>';
						}
					echo '</div>';

				echo '</div>';

				if ( ! isset( self::$_option['separator'] ) || ( self::$_option['separator'] === true ) ) {	
					echo '<div class="box-row-line"></div>';
				}

			
			echo '</div>';


		}

	}
}