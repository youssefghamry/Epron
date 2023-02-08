<?php

/**
 * Rascals Theme Panel Class
 *
 * @package     RascalsThemePanel
 * @subpackage  sortable_list
 * @author      Mariusz Rek
 * @version     1.1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'RascalsThemePanel_sortable_list' ) ) {

	class RascalsThemePanel_sortable_list extends RascalsThemePanel {

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
			
			if ( ! isset( self::$_option['classes'] ) ) {
				self::$_option['classes'] = '';
			}

			// Depedency
			if ( isset( self::$_option['dependency']) && is_array( self::$_option['dependency'] ) ) {
				echo '<div class="box-row clearfix sortable-list-wrap dependent-hidden ' . self::$_option['classes'] . '" data-depedency-el="' . esc_attr( self::$_option['dependency']['element'] ) .'" data-depedency-val="'.esc_attr( implode(',', self::$_option['dependency']['value'] ) ).'" data-id="' . esc_attr( self::$_option['array_name'] ) . '">';
			} else {
				echo '<div class="box-row sortable-list-wrap clearfix ' . self::$_option['classes'] . '">';
			}
			
			if ( isset(self::$_option['button_text']) && self::$_option['button_text'] !== '' ) {
			    $button_text = self::$_option['button_text'];
			 } else {
			    $button_text = esc_html__( 'Add New Item', 'epron' );
			}
			
			if ( isset( self::$_option['name'] ) && ( self::$_option['name'] !== '' ) ) {	
				echo '<label>' . esc_html( self::$_option['name'] ) . '</label>';
			}

			echo '<div class="clear"></div>';
			
			/* Hidden items */
			echo '<div class="new-item" style="display:none">';
			echo '<ul>';
			echo '<li>';
			echo '<span class="delete-item"><i class="fa fa-times"></i></span>';
			echo '<span class="drag-item"><i class="fa fa-arrows-alt"></i></span>';
			echo '<div class="content">';
			echo '<input type="hidden" value="" name="' . self::$_option['array_name'] . '_hidden[]"/>';
			foreach ( self::$_option['id'] as $count => $item ) {
			    if ( isset( $item['type'] ) ) {
			    	if ( $item['type'] === 'text' ) {
						echo '<input placeholder="' . esc_html( $item['label'] ) . '" type="text" value="" name="' . esc_attr( $item['id'] ) . '[]"/>';

					// Social
			    	} else if ( $item['type'] === 'social'  ) {

			    		echo '<select size="1" class="multi-option select-input select-service" name="' . esc_attr( $item['id'] ) . '[]">';
							foreach ( self::$socials as $key => $social ) {
								echo "<option value='" . esc_html( $key ) . "'>" . esc_html( $social ) . "</option>";
							}
						echo '</select>';

			    	}
			    }
			    
			}
			echo '</div>';
			echo '</li>';
			echo '</ul>';
			echo '</div>';
			
			if ( self::$_option['sortable'] == true ) 
				$sort = 'sortable';
			else 
				$sort = '';

			if ( isset( self::$_saved_options[self::$_option['array_name']] ) && is_array( self::$_saved_options[self::$_option['array_name']] ) )
			  $list_class = self::$_option['array_name'];
			else 
			  $list_class = '';

			echo '<ul class="sortable-list ' . esc_attr( $list_class ) . ' ' . esc_attr( $sort ) .'">';
				
			if ( isset( self::$_saved_options[self::$_option['array_name']] ) && is_array( self::$_saved_options[self::$_option['array_name']] ) ) {
				foreach ( self::$_saved_options[self::$_option['array_name']] as $items ) {
					echo '<li>';
					echo '<span class="delete-item"><i class="fa fa-times"></i></span>';
					echo '<span class="drag-item"><i class="fa fa-arrows-alt"></i></span>';
					echo '<div class="content">';
					echo '<input type="hidden" value="" name="' . esc_attr( self::$_option['array_name'] ) . '_hidden[]"/>';
					foreach ( self::$_option['id'] as $count => $item ) {

						if ( isset( $items[$item['name']] ) ) {
							$val = $items[$item['name']];
						} else {
							$val = '';
						}

						if ( isset( $item['type'] ) ) {

							// Text
							if ( $item['type'] === 'text' ) {
								echo '<input placeholder="' . esc_html( $item['label'] ) . '" type="text" class="input" value="' . esc_attr( $val ) . '" name="' . esc_attr($item['id']) . '[]"/>';

							// Social
							} else if ( $item['type'] === 'social' ) {
								echo '<select size="1" class="multi-option select-input select-service" name="' . esc_attr( $item['id'] ) . '[]">';
									foreach ( self::$socials as $key => $social ) {
										if ( $val === $key ) $selected = 'selected';
										else $selected = '';
										echo "<option " . esc_attr( $selected ) ." value='" . esc_html( $key ) . "'>" . esc_html( $social ) . "</option>";
									}
								echo '</select>';
			    		
			    			}

						}
					
					}
					echo '</div>';
					echo '</li>';
				}
			}
			echo '</ul>';
	        echo '<div class="clear"></div>';
			echo '<button class="_button add-new-item"><i class="icon fa-plus"></i>' . esc_html( $button_text ) . '</button>';
			echo '<div class="help-box">';
			$this->e_esc( self::$_option['desc'] );
			echo '</div>';
			echo '<div class="box-row-line"></div>';
			echo '</div>';

		}

	}
}