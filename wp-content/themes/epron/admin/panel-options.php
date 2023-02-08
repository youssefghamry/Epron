<?php
/**
 * Theme Name:      Epron
 * Theme Author:    Mariusz Rek - Rascals Themes
 * Theme URI:       http://rascalsthemes.com/epron
 * Author URI:      http://rascalsthemes.com
 * File:            panel-options.php
 * @package epron
 * @since 1.0.0
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


$ad_desc = esc_html__( 'Paste your custom AD code. Below below are all available codes that can be used to display ads, there are two options:', 'epron' ) .
'<br><br><strong>' . esc_html__( '1. Show the same ad on every device:', 'epron' ) . '</strong><br>
<pre>&#x3C;div class="show-on-all-devices">
    Your AD Code - This ad will show on all devices
&#x3C;/div></pre>
<strong>' . esc_html__( '2. Show different ads depending on the device:', 'epron' ) . '</strong><br>
<pre>&#x3C;div class="show-on-desktop">
    Your AD Code - This ad will show only on desktops
&#x3C;/div>
&#x3C;div class="show-on-tablet">
    Your AD Code - This ad will show only on tablets
&#x3C;/div>
&#x3C;div class="show-on-phone">
    Your AD Code - This ad will show only on phones
&#x3C;/div>
</pre>';


/* Options array */
$epron_main_options = array( 


	/* ==================================================
	  Adwords
	================================================== */
	array( 
		'type' => 'open',
		'tab_name' => esc_html__( 'ADS', 'epron' ),
		'tab_id' => 'ad',
		'icon' => 'bullhorn'
	),

		/* Header
		 -------------------------------- */
		array( 
			'type' => 'sub_open',
			'sub_tab_name' => esc_html__( 'Header', 'epron' ),
			'sub_tab_id' => 'sub-ad-header'
		),

			// AD Code Type
			array(
				'name' => esc_html__( 'AD Code Type', 'epron' ),
				'id' => 'ad_header_type',
				'type' => 'select',
				'std' => '',
				'options' => array( 
					array( 'name' => esc_html__( '- Select -', 'epron' ), 'value' => ''),
					array( 'name' => esc_html__( 'Custom AD Code', 'epron' ), 'value' => 'custom'),
					array( 'name' => esc_html__( 'Google AdSense Code', 'epron' ), 'value' => 'adsense')
				),
				'desc' => esc_html__( 'Select type of AD that you want to show.', 'epron' ),
			),

			// Custom
			array(
				'name' => esc_html__( 'Custom Code', 'epron' ),
				'id' => 'ad_header_custom',
				'type' => 'textarea',
				'tinymce' => 'false',
				'std' => '',
				'height' => '240',
				'dependency' => array(
			        "element" => 'ad_header_type',
			        "value" => array( 'custom' )
			    ),
			    'desc' => $ad_desc
			),

			// AdSense
			array(
				'name' => esc_html__( 'AdSense Code', 'epron' ),
				'id' => 'ad_header_adsense',
				'type' => 'textarea',
				'tinymce' => 'false',
				'std' => '',
				'height' => '120',
				'desc' => esc_html__( 'Paste your AdSense code here. ', 'epron' ),
				'dependency' => array(
			        "element" => 'ad_header_type',
			        "value" => array( 'adsense' )
			    )
			),

			
		array( 
			'type' => 'sub_close'
		),


		/* Footer
		 -------------------------------- */
		array( 
			'type' => 'sub_open',
			'sub_tab_name' => esc_html__( 'Footer', 'epron' ),
			'sub_tab_id' => 'sub-ad-footer'
		),

			// AD Code Type
			array(
				'name' => esc_html__( 'AD Code Type', 'epron' ),
				'id' => 'ad_footer_type',
				'type' => 'select',
				'std' => '',
				'options' => array( 
					array( 'name' => esc_html__( '- Select -', 'epron' ), 'value' => ''),
					array( 'name' => esc_html__( 'Custom AD Code', 'epron' ), 'value' => 'custom'),
					array( 'name' => esc_html__( 'Google AdSense Code', 'epron' ), 'value' => 'adsense')
				),
				'desc' => esc_html__( 'Select type of AD that you want to show.', 'epron' ),
			),

			// Custom
			array(
				'name' => esc_html__( 'Custom Code', 'epron' ),
				'id' => 'ad_footer_custom',
				'type' => 'textarea',
				'tinymce' => 'false',
				'std' => '',
				'height' => '240',
				'dependency' => array(
			        "element" => 'ad_footer_type',
			        "value" => array( 'custom' )
			    ),
			    'desc' => $ad_desc
			),

			// AdSense
			array(
				'name' => esc_html__( 'AdSense Code', 'epron' ),
				'id' => 'ad_footer_adsense',
				'type' => 'textarea',
				'tinymce' => 'false',
				'std' => '',
				'height' => '240',
				'desc' => esc_html__( 'Paste your AdSense code here. ', 'epron' ),
				'dependency' => array(
			        "element" => 'ad_footer_type',
			        "value" => array( 'adsense' )
			    )
			),

			
		array( 
			'type' => 'sub_close'
		),


		/* Article Top
		 -------------------------------- */
		array( 
			'type' => 'sub_open',
			'sub_tab_name' => esc_html__( 'Article Top', 'epron' ),
			'sub_tab_id' => 'sub-ad-article-top'
		),

			// AD Code Type
			array(
				'name' => esc_html__( 'AD Code Type', 'epron' ),
				'id' => 'ad_article_top_type',
				'type' => 'select',
				'std' => '',
				'options' => array( 
					array( 'name' => esc_html__( '- Select -', 'epron' ), 'value' => ''),
					array( 'name' => esc_html__( 'Custom AD Code', 'epron' ), 'value' => 'custom'),
					array( 'name' => esc_html__( 'Google AdSense Code', 'epron' ), 'value' => 'adsense')
				),
				'desc' => esc_html__( 'Select type of AD that you want to show.', 'epron' ),
			),

			// Custom
			array(
				'name' => esc_html__( 'Custom Code', 'epron' ),
				'id' => 'ad_article_top_custom',
				'type' => 'textarea',
				'tinymce' => 'false',
				'std' => '',
				'height' => '240',
				'dependency' => array(
			        "element" => 'ad_article_top_type',
			        "value" => array( 'custom' )
			    ),
			    'desc' => $ad_desc
			),

			// AdSense
			array(
				'name' => esc_html__( 'AdSense Code', 'epron' ),
				'id' => 'ad_article_top_adsense',
				'type' => 'textarea',
				'tinymce' => 'false',
				'std' => '',
				'height' => '240',
				'desc' => esc_html__( 'Paste your AdSense code here. ', 'epron' ),
				'dependency' => array(
			        "element" => 'ad_article_top_type',
			        "value" => array( 'adsense' )
			    )
			),

			
		array( 
			'type' => 'sub_close'
		),


		/* Article Bottom
		 -------------------------------- */
		array( 
			'type' => 'sub_open',
			'sub_tab_name' => esc_html__( 'Article Bottom', 'epron' ),
			'sub_tab_id' => 'sub-ad-article-bottom'
		),

			// AD Code Type
			array(
				'name' => esc_html__( 'AD Code Type', 'epron' ),
				'id' => 'ad_article_bottom_type',
				'type' => 'select',
				'std' => '',
				'options' => array( 
					array( 'name' => esc_html__( '- Select -', 'epron' ), 'value' => ''),
					array( 'name' => esc_html__( 'Custom AD Code', 'epron' ), 'value' => 'custom'),
					array( 'name' => esc_html__( 'Google AdSense Code', 'epron' ), 'value' => 'adsense')
				),
				'desc' => esc_html__( 'Select type of AD that you want to show.', 'epron' ),
			),

			// Custom
			array(
				'name' => esc_html__( 'Custom Code', 'epron' ),
				'id' => 'ad_article_bottom_custom',
				'type' => 'textarea',
				'tinymce' => 'false',
				'std' => '',
				'height' => '240',
				'dependency' => array(
			        "element" => 'ad_article_bottom_type',
			        "value" => array( 'custom' )
			    ),
			    'desc' => $ad_desc
			),

			// AdSense
			array(
				'name' => esc_html__( 'AdSense Code', 'epron' ),
				'id' => 'ad_article_bottom_adsense',
				'type' => 'textarea',
				'tinymce' => 'false',
				'std' => '',
				'height' => '240',
				'desc' => esc_html__( 'Paste your AdSense code here. ', 'epron' ),
				'dependency' => array(
			        "element" => 'ad_article_bottom_type',
			        "value" => array( 'adsense' )
			    )
			),

			
		array( 
			'type' => 'sub_close'
		),


		/* Sidebar
		 -------------------------------- */
		array( 
			'type' => 'sub_open',
			'sub_tab_name' => esc_html__( 'Sidebar', 'epron' ),
			'sub_tab_id' => 'sub-ad-sidebar'
		),

			// AD Code Type
			array(
				'name' => esc_html__( 'AD Code Type', 'epron' ),
				'id' => 'ad_sidebar_type',
				'type' => 'select',
				'std' => '',
				'options' => array( 
					array( 'name' => esc_html__( '- Select -', 'epron' ), 'value' => ''),
					array( 'name' => esc_html__( 'Custom AD Code', 'epron' ), 'value' => 'custom'),
					array( 'name' => esc_html__( 'Google AdSense Code', 'epron' ), 'value' => 'adsense')
				),
				'desc' => esc_html__( 'Select type of AD that you want to show.', 'epron' ),
			),

			// Custom
			array(
				'name' => esc_html__( 'Custom Code', 'epron' ),
				'id' => 'ad_sidebar_custom',
				'type' => 'textarea',
				'tinymce' => 'false',
				'std' => '',
				'height' => '240',
				'dependency' => array(
			        "element" => 'ad_sidebar_type',
			        "value" => array( 'custom' )
			    ),
			    'desc' => $ad_desc
			),

			// AdSense
			array(
				'name' => esc_html__( 'AdSense Code', 'epron' ),
				'id' => 'ad_sidebar_adsense',
				'type' => 'textarea',
				'tinymce' => 'false',
				'std' => '',
				'height' => '240',
				'desc' => esc_html__( 'Paste your AdSense code here. ', 'epron' ),
				'dependency' => array(
			        "element" => 'ad_sidebar_type',
			        "value" => array( 'adsense' )
			    )
			),

			
		array( 
			'type' => 'sub_close'
		),


		/* Tracklist Inline
		 -------------------------------- */
		array( 
			'type' => 'sub_open',
			'sub_tab_name' => esc_html__( 'Tracklist Inline', 'epron' ),
			'sub_tab_id' => 'sub-ad-tracklist'
		),

			// AD Code Type
			array(
				'name' => esc_html__( 'AD Code Type', 'epron' ),
				'id' => 'ad_tracklist_type',
				'type' => 'select',
				'std' => '',
				'options' => array( 
					array( 'name' => esc_html__( '- Select -', 'epron' ), 'value' => '' ),
					array( 'name' => esc_html__( 'Custom AD Code', 'epron' ), 'value' => 'custom' ),
					array( 'name' => esc_html__( 'Google AdSense Code', 'epron' ), 'value' => 'adsense' )
				),
				'desc' => esc_html__( 'Select type of AD that you want to show.', 'epron' ),
			),

			// Custom
			array(
				'name' => esc_html__( 'Custom Code', 'epron' ),
				'id' => 'ad_tracklist_custom',
				'type' => 'textarea',
				'tinymce' => 'false',
				'std' => '',
				'height' => '240',
				'dependency' => array(
			        "element" => 'ad_tracklist_type',
			        "value" => array( 'custom' )
			    ),
			    'desc' => $ad_desc
			),

			// AdSense
			array(
				'name' => esc_html__( 'AdSense Code', 'epron' ),
				'id' => 'ad_tracklist_adsense',
				'type' => 'textarea',
				'tinymce' => 'false',
				'std' => '',
				'height' => '240',
				'desc' => esc_html__( 'Paste your AdSense code here. ', 'epron' ),
				'dependency' => array(
			        "element" => 'ad_tracklist_type',
			        "value" => array( 'adsense' )
			    )
			),

			
		array( 
			'type' => 'sub_close'
		),
		

		/* Custom 1
		 -------------------------------- */
		array( 
			'type' => 'sub_open',
			'sub_tab_name' => esc_html__( 'Custom 1', 'epron' ),
			'sub_tab_id' => 'sub-ad-custom1'
		),

			// AD Code Type
			array(
				'name' => esc_html__( 'AD Code Type', 'epron' ),
				'id' => 'ad_custom1_type',
				'type' => 'select',
				'std' => '',
				'options' => array( 
					array( 'name' => esc_html__( '- Select -', 'epron' ), 'value' => '' ),
					array( 'name' => esc_html__( 'Custom AD Code', 'epron' ), 'value' => 'custom' ),
					array( 'name' => esc_html__( 'Google AdSense Code', 'epron' ), 'value' => 'adsense' )
				),
				'desc' => esc_html__( 'Select type of AD that you want to show.', 'epron' ),
			),

			// Custom
			array(
				'name' => esc_html__( 'Custom Code', 'epron' ),
				'id' => 'ad_custom1_custom',
				'type' => 'textarea',
				'tinymce' => 'false',
				'std' => '',
				'height' => '240',
				'dependency' => array(
			        "element" => 'ad_custom1_type',
			        "value" => array( 'custom' )
			    ),
			    'desc' => $ad_desc
			),

			// AdSense
			array(
				'name' => esc_html__( 'AdSense Code', 'epron' ),
				'id' => 'ad_custom1_adsense',
				'type' => 'textarea',
				'tinymce' => 'false',
				'std' => '',
				'height' => '240',
				'desc' => esc_html__( 'Paste your AdSense code here. ', 'epron' ),
				'dependency' => array(
			        "element" => 'ad_custom1_type',
			        "value" => array( 'adsense' )
			    )
			),

			
		array( 
			'type' => 'sub_close'
		),

		/* Custom 2
		 -------------------------------- */
		array( 
			'type' => 'sub_open',
			'sub_tab_name' => esc_html__( 'Custom 2', 'epron' ),
			'sub_tab_id' => 'sub-ad-custom2'
		),

			// AD Code Type
			array(
				'name' => esc_html__( 'AD Code Type', 'epron' ),
				'id' => 'ad_custom2_type',
				'type' => 'select',
				'std' => '',
				'options' => array( 
					array( 'name' => esc_html__( '- Select -', 'epron' ), 'value' => '' ),
					array( 'name' => esc_html__( 'Custom AD Code', 'epron' ), 'value' => 'custom' ),
					array( 'name' => esc_html__( 'Google AdSense Code', 'epron' ), 'value' => 'adsense' )
				),
				'desc' => esc_html__( 'Select type of AD that you want to show.', 'epron' ),
			),

			// Custom
			array(
				'name' => esc_html__( 'Custom Code', 'epron' ),
				'id' => 'ad_custom2_custom',
				'type' => 'textarea',
				'tinymce' => 'false',
				'std' => '',
				'height' => '240',
				'dependency' => array(
			        "element" => 'ad_custom2_type',
			        "value" => array( 'custom' )
			    ),
			    'desc' => $ad_desc
			),

			// AdSense
			array(
				'name' => esc_html__( 'AdSense Code', 'epron' ),
				'id' => 'ad_custom2_adsense',
				'type' => 'textarea',
				'tinymce' => 'false',
				'std' => '',
				'height' => '240',
				'desc' => esc_html__( 'Paste your AdSense code here. ', 'epron' ),
				'dependency' => array(
			        "element" => 'ad_custom2_type',
			        "value" => array( 'adsense' )
			    )
			),

			
		array( 
			'type' => 'sub_close'
		),

		/* Custom 3
		 -------------------------------- */
		array( 
			'type' => 'sub_open',
			'sub_tab_name' => esc_html__( 'Custom 3', 'epron' ),
			'sub_tab_id' => 'sub-ad-custom3'
		),

			// AD Code Type
			array(
				'name' => esc_html__( 'AD Code Type', 'epron' ),
				'id' => 'ad_custom3_type',
				'type' => 'select',
				'std' => '',
				'options' => array( 
					array( 'name' => esc_html__( '- Select -', 'epron' ), 'value' => '' ),
					array( 'name' => esc_html__( 'Custom AD Code', 'epron' ), 'value' => 'custom' ),
					array( 'name' => esc_html__( 'Google AdSense Code', 'epron' ), 'value' => 'adsense' )
				),
				'desc' => esc_html__( 'Select type of AD that you want to show.', 'epron' ),
			),

			// Custom
			array(
				'name' => esc_html__( 'Custom Code', 'epron' ),
				'id' => 'ad_custom3_custom',
				'type' => 'textarea',
				'tinymce' => 'false',
				'std' => '',
				'height' => '240',
				'dependency' => array(
			        "element" => 'ad_custom3_type',
			        "value" => array( 'custom' )
			    ),
			    'desc' => $ad_desc
			),

			// AdSense
			array(
				'name' => esc_html__( 'AdSense Code', 'epron' ),
				'id' => 'ad_custom3_adsense',
				'type' => 'textarea',
				'tinymce' => 'false',
				'std' => '',
				'height' => '240',
				'desc' => esc_html__( 'Paste your AdSense code here. ', 'epron' ),
				'dependency' => array(
			        "element" => 'ad_custom3_type',
			        "value" => array( 'adsense' )
			    )
			),

			
		array( 
			'type' => 'sub_close'
		),
	
	array( 
		'type' => 'close'
	),


	/* ==================================================
	  Fonts 
	================================================== */
	array( 
		'type' => 'open',
		'tab_name' => esc_html__( 'Fonts', 'epron' ),
		'tab_id' => 'fonts',
		'icon' => 'font'
	),

		/* Google Fonts
		 -------------------------------- */
		array(
			'type' => 'sub_open',
			'sub_tab_name' => esc_html__( 'Google Web Fonts', 'epron' ),
			'sub_tab_id' => 'sub-google-fonts',
		),
			array(
				'name' => esc_html__( 'Google Fonts', 'epron' ),
				'id' => 'use_google_fonts',
				'type' => 'switch_button',
				'plugins' => array( 'switch_button' ),
				'std' => 'on',
				'desc' => esc_html__( 'When this option is enabled, the text elements will be automatically replaced with the Google Web Fonts.', 'epron' ),
			),
			array(
				'name' => esc_html__( 'Google Font Code', 'epron' ),
				'id' => 'google_fonts',
				'type' => 'textarea',
				'std' =>'Open+Sans:300,300i,400,400i,500,500i,700,700i,900,900i&amp;subset=latin-ext',
				'tinymce' => 'false',
				'height' => '50',
				'desc' => esc_html__( 'Add Google Fonts family.', 'epron' ),
				'dependency' => array(
			        "element" => 'use_google_fonts',
			        "value" => array( 'on' )
			    )
			),
		array(
			'type' => 'sub_close'
		),
		

	array( 
		'type' => 'close'
	),


	/* ==================================================
	  Sections 
	================================================== */
	array( 
		'type' => 'open',
		'tab_name' => esc_html__( 'Sections', 'epron' ),
		'tab_id' => 'plugins',
		'icon' => 'th-large'
	),	


		/* Comments
		 -------------------------------- */
		array(
			'type' => 'sub_open',
			'sub_tab_name' => esc_html__( 'Comments', 'epron' ),
			'sub_tab_id' => 'sub-sections-comments'
		),
			// DISQUS 
			array(
				'name' => esc_html__( 'DISQUS Comments', 'epron' ),
				'id' => 'disqus_comments',
				'type' => 'switch_button',
				'plugins' => array( 'switch_button' ),
				'std' => 'off',
				'desc' => esc_html__( 'Enable DISQUS comments. Replace default Wordpress comments.', 'epron' ),
			),

			// Disqus ID
			array(
				'name' => esc_html__( 'DISQUS Shortname', 'epron' ),
				'id' => 'disqus_shortname',
				'type' => 'text',
				'std' => '',
				'desc' => esc_html__( 'Enter DISQUS Website\'s Shortname.', 'epron' ),
				'dependency' => array(
					'element' => 'disqus_comments',
					'value' => array( 'on' )
				)
			),

		array(
			'type' => 'sub_close'
		),


		/* Events
		 -------------------------------- */
		array(
			'type' => 'sub_open',
			'sub_tab_name' => esc_html__( 'Events', 'epron' ),
			'sub_tab_id' => 'sub-sections-events'
		),
			// DISQUS 
			array(
				'name' => esc_html__( 'Events Date Format (List Module)', 'epron' ),
				'id' => 'events_date_format_list',
				'type' => 'text',
				'std' => 'd/m',
				'desc' => esc_html__( 'Enter your custom event date. More information: http://codex.wordpress.org/Formatting_Date_and_Time', 'epron' ),
			),

			// Disqus ID
			array(
				'name' => esc_html__( 'DISQUS Shortname', 'epron' ),
				'id' => 'disqus_shortname',
				'type' => 'text',
				'std' => '',
				'desc' => esc_html__( 'Enter DISQUS Website\'s Shortname.', 'epron' ),
				'dependency' => array(
					'element' => 'disqus_comments',
					'value' => array( 'on' )
				)
			),

		array(
			'type' => 'sub_close'
		),

		/* Permalinks
		 -------------------------------------------------------- */
		array(
			'type' => 'sub_open',
			'sub_tab_name' => esc_html__( 'Permalinks', 'epron' ),
			'sub_tab_id' => 'sub-section-permalinks',
		),	

			// Artists
			array(
				'name' => esc_html__( 'Artist Slug', 'epron' ),
				'id' => 'artists_slug',
				'type' => 'text',
				'std' => 'artist',
				'desc' => esc_html__( 'Enter post slug name. No special characters. No spaces. IMPORTANT: When you change post slug name, you have to go to: WordPress Settings > Permalinks and save settings.', 'epron' )
			),
			array(
				'name' => esc_html__( 'Artists Filter 1 Slug', 'epron' ),
				'id' => 'artists_cat_slug',
				'type' => 'text',
				'std' => 'artist-category',
				'desc' => esc_html__( 'Enter post slug name. No special characters. No spaces. IMPORTANT: When you change post slug name, you have to go to: WordPress Settings > Permalinks and save settings.', 'epron' )
			),
			array(
				'name' => esc_html__( 'Artists Filter 2 Slug', 'epron' ),
				'id' => 'artists_cat_slug2',
				'type' => 'text',
				'std' => 'artist-category-2',
				'desc' => esc_html__( 'Enter post slug name. No special characters. No spaces. IMPORTANT: When you change post slug name, you have to go to: WordPress Settings > Permalinks and save settings.', 'epron' )
			),
		

			// Releases
			array(
				'name' => esc_html__( 'Release Slug', 'epron' ),
				'id' => 'releases_slug',
				'type' => 'text',
				'std' => 'release',
				'desc' => esc_html__( 'Enter post slug name. No special characters. No spaces. IMPORTANT: When you change post slug name, you have to go to: WordPress Settings > Permalinks and save settings.', 'epron' )
			),
			array(
				'name' => esc_html__( 'Releases Filter 1 Slug', 'epron' ),
				'id' => 'releases_cat_slug',
				'type' => 'text',
				'std' => 'release-category',
				'desc' => esc_html__( 'Enter post slug name. No special characters. No spaces. IMPORTANT: When you change post slug name, you have to go to: WordPress Settings > Permalinks and save settings.', 'epron' )
			),
			array(
				'name' => esc_html__( 'Releases Filter 2 Slug', 'epron' ),
				'id' => 'releases_cat_slug2',
				'type' => 'text',
				'std' => 'release-category-2',
				'desc' => esc_html__( 'Enter post slug name. No special characters. No spaces. IMPORTANT: When you change post slug name, you have to go to: WordPress Settings > Permalinks and save settings.', 'epron' )
			),
		
			
			// Events
			array(
				'name' => esc_html__( 'Event Slug', 'epron' ),
				'id' => 'events_slug',
				'type' => 'text',
				'std' => 'event',
				'desc' => esc_html__( 'Enter post slug name. No special characters. No spaces. IMPORTANT: When you change post slug name, you have to go to: WordPress Settings > Permalinks and save settings.', 'epron' )
			),
			array(
				'name' => esc_html__( 'Event Filter 1 Slug', 'epron' ),
				'id' => 'events_cat_slug',
				'type' => 'text',
				'std' => 'event-category',
				'desc' => esc_html__( 'Enter post slug name. No special characters. No spaces. IMPORTANT: When you change post slug name, you have to go to: WordPress Settings > Permalinks and save settings.', 'epron' )
			),
			array(
				'name' => esc_html__( 'Event Filter 3 Slug', 'epron' ),
				'id' => 'events_cat_slug2',
				'type' => 'text',
				'std' => 'event-category-2',
				'desc' => esc_html__( 'Enter post slug name. No special characters. No spaces. IMPORTANT: When you change post slug name, you have to go to: WordPress Settings > Permalinks and save settings.', 'epron' )
			),

			// Gallery
			array(
				'name' => esc_html__( 'Gallery Slug', 'epron' ),
				'id' => 'gallery_slug',
				'type' => 'text',
				'std' => 'gallery',
				'desc' => esc_html__( 'Enter post slug name. No special characters. No spaces. IMPORTANT: When you change post slug name, you have to go to: WordPress Settings > Permalinks and save settings.', 'epron' )
			),
			array(
				'name' => esc_html__( 'Gallery Filter 1 Slug', 'epron' ),
				'id' => 'gallery_cat_slug',
				'type' => 'text',
				'std' => 'gallery-category',
				'desc' => esc_html__( 'Enter post slug name. No special characters. No spaces. IMPORTANT: When you change post slug name, you have to go to: WordPress Settings > Permalinks and save settings.', 'epron' )
			),
			array(
				'name' => esc_html__( 'Gallery Filter 2 Slug', 'epron' ),
				'id' => 'gallery_cat_slug2',
				'type' => 'text',
				'std' => 'gallery-category',
				'desc' => esc_html__( 'Enter post slug name. No special characters. No spaces. IMPORTANT: When you change post slug name, you have to go to: WordPress Settings > Permalinks and save settings.', 'epron' )
			),
			array(
				'name' => esc_html__( 'Gallery Filter 3 Slug', 'epron' ),
				'id' => 'gallery_cat_slug3',
				'type' => 'text',
				'std' => 'gallery-category',
				'desc' => esc_html__( 'Enter post slug name. No special characters. No spaces. IMPORTANT: When you change post slug name, you have to go to: WordPress Settings > Permalinks and save settings.', 'epron' )
			),
			

		array(
			'type' => 'sub_close'
		),
		

	array( 
		'type' => 'close'
	),


	/* ==================================================
	  Sidebars 
	================================================== */
	array(
		'type' => 'open',
		'tab_name' => esc_html__( 'Sidebars', 'epron' ),
		'tab_id' => 'sidebars',
		'icon' => 'bars'
	),
		array(
			'type' => 'sub_open',
			'sub_tab_name' => esc_html__( 'Sidebars', 'epron' ),
			'sub_tab_id' => 'sub-sidebars'
		),
			array(
				'name'       => esc_html__( 'Sidebars', 'epron' ),
				'sortable'   => false,
				'array_name' => 'custom_sidebars',
				'id'         => array(
				 	array( 'name' => 'name', 'id' => 'sidebar', 'label' => 'Name:', 'type' => 'text' )
				 ),
				'type'        => 'sortable_list',
				'button_text' => esc_html__( 'Add Sidebar', 'epron' ),
				'desc'        => esc_html__( 'Add your custom sidebars.', 'epron' )
			),
		array(
			'type' => 'sub_close'
		),
	array(
		'type' => 'close'
	),


	/* ==================================================
	  Advanced 
	================================================== */
	array( 
		'type' => 'open',
		'tab_name' => esc_html__( 'Advanced', 'epron' ),
		'tab_id' => 'advanced',
		'icon' => 'wrench'
	),

		/* Plugins
		 -------------------------------- */
		array(
			'type' => 'sub_open',
			'sub_tab_name' => esc_html__( 'Plugins', 'epron' ),
			'sub_tab_id' => 'sub-plugins'
		),

			// Retina Displays
			array( 
				'name' => esc_html__( 'Retina Displays', 'epron' ),
				'id' => 'retina',
				'type' => 'switch_button',
				'plugins' => array( 'switch_button' ),
				'std' => 'off',
				'desc' => esc_html__( 'To make this work you need to specify the width and the height of the image directly and provide the same image twice the size withe the @2x selector added at the end of the image name. For instance if you want your "logo.png" file to be retina compatible just include it in the markup with specified width and height ( the width and height of the original image in pixels ) and create a "logo@2x.png" file in the same directory that is twice the resolution.', 'epron' ),
			),

			// Lazy Loading
			array( 
				'name' => esc_html__( 'Image Loading (LazyLoad)', 'epron' ),
				'id' => 'lazyload',
				'type' => 'switch_button',
				'plugins' => array( 'switch_button' ),
				'std' => 'on',
				'desc' => esc_html__( 'Disable or enable loading animation effect. The effect animation allows you to animate your theme images as you scroll, from top to the bottom. It applies even on the next and prev operations creating an effect of loading images to the right or to the left.', 'epron' ),
			),

			// Facebook JSSDK
			array( 
				'name' => esc_html__( 'Facebook JSSDK', 'epron' ),
				'id' => 'fbsdk',
				'type' => 'switch_button',
				'plugins' => array( 'switch_button' ),
				'std' => 'on',
				'desc' => esc_html__( 'Connect site with Facebook JS SDK. This is necessary to display widgets from Facebook.', 'epron' ),
			),
		array(
			'type' => 'sub_close'
		),
		

		/* Import/Export
		 -------------------------------- */
		array( 
			'type' => 'sub_open',
			'sub_tab_name' => esc_html__( 'Import/Export', 'epron' ),
			'sub_tab_id' => 'sub-import'
		),
			array( 
				'type' => 'export'
			),
			array( 
				'type' => 'import'
			),
		array( 
			'type' => 'sub_close'
		),

	array( 
		'type' => 'close'
	),


	/* ==================================================
	    Hidden fields
	 ================================================== */
	array( 
		'type' => 'hidden_field',
		'id' => 'theme_name',
		'value' => 'epron'
	),
	
);


/* ==================================================
  Init Panel 
================================================== */

/* Class arguments */
$args = array(
	'menu_name' => esc_html__( 'Theme Panel', 'epron' ), 
	'option_name' => 'epron_panel_opts',
	'menu_icon' => '',
);

/* Add class instance */
$main_panel = new RascalsThemePanel( $args, $epron_main_options );


/* ==================================================
  Get Theme Options 
================================================== */
function epron_opts(){
   global $main_panel;
   return $main_panel;
}