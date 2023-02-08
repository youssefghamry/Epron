<?php
/**
 * Rascals Kirki Installation
 *
 *
 * @author Rascals Themes
 * @category Core
 * @package Epron Toolkit
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed diCustomizer
}

/**
 * This file adds a custom section in the customizer that recommends the installation of the Kirki plugin.
 * It can be used as-is in themes (drop-in).
 *
 * @package kirki-helpers
 */
if ( ! function_exists( 'RascalsCustomizerInstaller' ) ) {
	/**
	 * Registers the section, setting & control for the kirki installer.
	 *
	 * @param object $wp_customize The main customizer object.
	 */
	function RascalsCustomizerInstaller( $wp_customize ) {
		// Early exit if Kirki exists.
		if ( class_exists( 'Kirki' ) ) {
			return;
		}
		if ( class_exists( 'WP_Customize_Section' ) && ! class_exists( 'RascalsCustomizerSection' ) ) {
			/**
			 * Recommend the installation of Kirki using a custom section.
			 *
			 * @see WP_Customize_Section
			 */
			class RascalsCustomizerSection extends WP_Customize_Section {
				/**
				 * Customize section type.
				 *
				 * @access public
				 * @var string
				 */
				public $type = 'rascals_installer';
				/**
				 * The plugin install URL.
				 *
				 * @access private
				 * @var string
				 */
				public $plugin_install_url;
				/**
				 * Render the section.
				 *
				 * @access protected
				 */
				protected function render() {
				
					// Determine if the plugin is not installed, or just inactive.
					$plugins   = get_plugins();
					$installed = false;
					foreach ( $plugins as $plugin ) {
						if ( 'Kirki' === $plugin['Name'] || 'Kirki Toolkit' === $plugin['Name'] ) {
							$installed = true;
						}
					}
					$plugin_install_url = $this->getPluginInstallUrl();
					$classes            = 'cannot-expand accordion-section control-section control-section-themes control-section-' . esc_attr( $this->type );
					?>
					<li id="accordion-section-<?php echo esc_attr( $this->id ); ?>" class="<?php echo esc_attr( $classes ); ?>" style="border-top:none;border-bottom:1px solid #ddd;padding:7px 14px 16px 14px;text-align:right;">
						<?php if ( ! $installed ) : ?>
							<?php $this->installButton(); ?>
						<?php else : ?>
							<?php $this->activateButton(); ?>
						<?php endif; ?>
					
					</li>
					<?php
				}
			
				/**
				 * Adds the install button.
				 *
				 * @since 1.0.0
				 * @return void
				 */
				protected function installButton() {
					?>
					<p style="text-align:left;margin-top:0;">
						<?php esc_html_e( 'Please install the Kirki plugin to take full advantage of this theme\s customizer capabilities', 'epron-toolkit' ); ?>
					</p>
					<a class="install-now button-primary button" data-slug="kirki" href="<?php echo esc_url_raw( $this->getPluginInstallUrl() ); ?>" aria-label="<?php esc_attr_e( 'Install Kirki Toolkit now', 'epron-toolkit' ); ?>" data-name="Kirki Toolkit">
						<?php esc_html_e( 'Install Now', 'epron-toolkit' ); ?>
					</a>
					<?php
				}
				/**
				 * Adds the install button.
				 *
				 * @since 1.0.0
				 * @return void
				 */
				protected function activateButton() {
					?>
					<p style="text-align:left;margin-top:0;">
						<?php esc_html_e( 'You have installed Kirki. Activate it to take advantage of this theme\'s features in the customizer.', 'epron-toolkit' ); ?>
					</p>
					<a class="activate-now button-primary button" data-slug="kirki" href="<?php echo esc_url_raw( self_admin_url( 'plugins.php' ) ); ?>" aria-label="<?php esc_attr_e( 'Activate Kirki Toolkit now', 'epron-toolkit' ); ?>" data-name="Kirki Toolkit">
						<?php esc_html_e( 'Activate Now', 'epron-toolkit' ); ?>
					</a>
					<?php
				}
				
				/**
				 * Get the plugin install URL.
				 *
				 * @access private
				 * @return string
				 */
				private function getPluginInstallUrl() {
					if ( ! $this->plugin_install_url ) {
						// Get the plugin-installation URL.
						$this->plugin_install_url = add_query_arg(
							array(
								'action' => 'install-plugin',
								'plugin' => 'kirki',
							),
							self_admin_url( 'update.php' )
						);
						$this->plugin_install_url = wp_nonce_url( $this->plugin_install_url, 'install-plugin_kirki' );
					}
					return $this->plugin_install_url;
				}
			}
		}
		
		$wp_customize->add_section(
			new RascalsCustomizerSection(
				$wp_customize,
				'rascals_installer',
				array(
					'title'      => '',
					'capability' => 'install_plugins',
					'priority'   => 0,
				)
			)
		);
		$wp_customize->add_setting(
			'rascals_installer_setting',
			array(
				'sanitize_callback' => '__return_true',
			)
		);
		$wp_customize->add_control(
			'rascals_installer_control',
			array(
				'section'  => 'rascals_installer',
				'settings' => 'rascals_installer_setting',
			)
		);
	}
}
add_action( 'customize_register', 'RascalsCustomizerInstaller', 999 );