<?php
/**
 * Theme Name:      Epron
 * Theme Author:    Mariusz Rek - Rascals Themes
 * Theme URI:       http://rascalsthemes.com/epron
 * Author URI:      http://rascalsthemes.com
 * File:            single-epron_gallery.php
 * @package epron
 * @since 1.0.0
 */

get_header();

// Get options
$epron_opts = epron_opts();

wp_reset_postdata();

// Copy Query 
$temp_post = $post;
$query_temp = $wp_query;

// Album ID 
$album_id = get_the_id();

// Thumb size 
$thumb_size = 'epron-large-square-thumb';

// Lazy loading 
$lazy = true;

// Images 
$images_ids = get_post_meta(  $wp_query->post->ID, '_gallery_images', true ); 

// Images layout
$images_layout = get_post_meta(  $wp_query->post->ID, '_images_layout', true );
$images_layout = $images_layout === '' ? 'flex-4' : $images_layout;

// Get content layout 
$content_layout = get_post_meta( $wp_query->post->ID, '_content_layout', true );
$content_layout = ( $content_layout !== '' ? $content_layout : 'wide' );

// Hero Layout 
$hero_layout = get_post_meta( $wp_query->post->ID, '_hero_layout', true ); 
$hero_layout = ( ! isset( $hero_layout ) || $hero_layout === '' ) ? 'default' : $hero_layout;

// Background 
$content_bg = get_post_meta( $wp_query->post->ID, '_content_bg', true );

// Set classes and variables 
$sidebar = false;
$content_classes = array(
	'content page-template-simple'
);
$container_classes = array(
	'container'
);
if ( $content_layout === 'narrow' ) {
	array_push( $content_classes, 'page-layout-' . $content_layout );
	$container_classes[] = 'small';
} else if ( $content_layout === 'wide' ) {
	array_push( $content_classes, 'page-layout-' . $content_layout );
	$container_classes[] = 'wide';
} else if ( $content_layout === 'left_sidebar' ) {
	$sidebar = true;
	array_push( $content_classes, 'page-layout-' . $content_layout, 'sidebar-on-left' );
} else if ( $content_layout === 'right_sidebar' ) {
	$sidebar = true;
	array_push( $content_classes, 'page-layout-' . $content_layout, 'sidebar-on-right' );
} 

?>

<?php 
// Default Hero
if ( $hero_layout === 'default' ) {
	get_template_part( 'partials/hero', 'default' );
}
?>

<?php if ( $content_layout === 'page_builder' && class_exists( 'KingComposer' )) : ?>
	<?php $content_classes[] = 'page-builder-top'; ?>
	<div class="content-full">
		<div class="container-full">
			<?php 
			while ( have_posts() ) {
			 	the_post();

		    	// Render content via Page Builder 
		  		epron_get_content();
			} 
			?>
		</div>
	</div>
	<?php rewind_posts(); ?>
	
<?php endif; ?>

<div class="<?php echo esc_attr( implode(' ', $content_classes ) ) ?>" 
	<?php if ( function_exists( 'epron_get_background' ) && epron_get_background( $content_bg ) ) {
		echo 'style="' . epron_get_background( $content_bg ) . '"';
	} ?>
>

	<div class="<?php echo esc_attr( implode(' ', $container_classes ) ) ?>">
		<div class="main">

		<?php 
		// Simple Hero
		if ( $hero_layout === 'simple' ) {
			get_template_part( 'partials/hero', 'simple' );
		}
		?>
			
		<div class="clear"></div>

		<?php if ( $images_ids || $images_ids !== '' ) :

			$ids = explode( '|', $images_ids ); 
           	$gallery_loop_args = array(
				'post_type'      => 'attachment',
				'post_mime_type' => 'image',
				'post__in'       => $ids,
				'orderby'        => 'post__in',
				'post_status'    => 'any',
				'showposts'      => -1
			);

			$wp_query = new WP_Query();
			$wp_query->query( $gallery_loop_args );
			?>
			
			<?php if ( $images_layout === 'masonry' ) : ?>
				<?php 
				// Thumb size 
				$thumb_size = 'large';

				 ?>
				<div class="gallery-images-grid masonry-grid anim-grid" data-anim-effect="amun">
			<?php else : ?>
				<div class="gallery-images-grid flex-grid <?php echo esc_attr( $images_layout ) ?> flex-tablet-2 flex-mobile-2 flex-mobile-portrait-1 flex-gap-medium posts-container anim-grid" data-anim-effect="amun">
			<?php endif; ?>

				<?php if ( have_posts() ) : ?>

					<?php while ( have_posts() ) : the_post(); ?>
						
						<?php 
						$image_att = wp_get_attachment_image_src( get_the_id(), $thumb_size );
						if ( ! $image_att[0] ) { 
							continue;
						}

						// Get image meta 
						$image = get_post_meta( $album_id, '_gallery_images_' . get_the_id(), true );

						// Add default values 
						$defaults = array(
							'title'       => '',
							'custom_link' => '',
							'thumb_icon'  => 'view'
				         );

						if ( isset( $image ) && is_array( $image ) ) {
							$image = array_merge( $defaults, $image );
						} else {
							$image = $defaults;
						}

						// Add image src to array 
						$image['src'] = $image_att[0];
						if ( $image[ 'custom_link' ] !== '' ) {
							$link = $image[ 'custom_link' ];
						} else {
							$link = wp_get_attachment_image_src( get_the_id(), 'full' );
							$link = $link[0];
						}

						?>
						<?php if ( $images_layout === 'masonry' ) : ?>
							<?php $lazy = false; ?>
							<div class="masonry-brick">
								<div class="masonry-content">
						<?php else : ?>
							<div class="flex-item">
								<div class="flex-content">
						<?php endif; ?>
		                        <div <?php post_class( ); ?>>
		                            <a href="<?php echo esc_attr( $link ) ?>" class="<?php if ( $image[ 'custom_link' ] !== '' ) { echo esc_attr( 'iframe-link'); } ?> g-item" title="<?php echo esc_attr( $image['title'] ); ?>">
		                            	<?php echo epron_get_image( false, $thumb_size, '', $lazy, get_the_id() ) ?>
		                            </a>
		                        </div>
	                    	</div>
	                	</div>

					<?php endwhile; // End Loop ?>

				<?php endif; ?>
			</div>
			<div class="clear"></div>
		<?php endif; ?>
		<?php
		   // Get orginal query
			$post     = $temp_post;
			$wp_query = $query_temp;
		?>		
		<?php
		// If comments are open or we have at least one comment, load up the comment template.
		if ( get_theme_mod( 'posts_comments', true ) ) {
			if ( in_array( 'content-full' , $content_classes ) ) {
				echo '<div class="container">';
			}
			if ( comments_open() || get_comments_number() ) {
				$disqus = $epron_opts->get_option( 'disqus_comments' );
				$disqus_shortname = $epron_opts->get_option( 'disqus_shortname' );

				if ( ( $disqus && $disqus === 'on' ) && ( $disqus_shortname && $disqus_shortname !== '' ) ) {
					get_template_part( 'inc/disqus' );
				} else {
					comments_template();
				}
			}
			if ( in_array( 'content-full' , $content_classes ) ) {
				echo '</div>';
			}
		}
		?>
		</div> <!-- .main -->
	
		<?php if ( $sidebar ) : ?>
		<!-- Sidebar -->
	    <div class="sidebar sidebar-block">
	    	<div class="theiaStickySidebar">
				<?php get_sidebar( 'custom' ); ?>
			</div>
	    </div>
	    <!-- /sidebar -->
		<?php endif; ?>

	</div> <!-- .container -->

    <?php 

	// Posts Navigation 
	if ( function_exists( 'epron_custom_post_nav' ) ) {
		echo epron_custom_post_nav();
	} else {
		posts_nav_link( ' &#183; ', esc_html_e( 'previous page', 'epron' ), esc_html_e( 'next page', 'epron' ) );
	}
	
	?>
</div> <!-- .content -->

<?php 
get_footer();