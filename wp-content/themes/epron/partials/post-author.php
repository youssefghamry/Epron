<?php
/**
 * Theme Name:      Epron
 * Theme Author:    Mariusz Rek - Rascals Themes
 * Theme URI:       http://rascalsthemes.com/epron
 * Author URI:      http://rascalsthemes.com
 * File:            post-author.php
 * @package epron
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get options
$epron_opts = epron_opts();

$author_id = get_the_author_meta( 'ID' );
if ( get_theme_mod( 'show_author_box', '0' ) === true ) : ?>
<div class="clear"></div>
<div class="author-block">
	<a href="<?php echo esc_url( get_author_posts_url( $author_id, get_the_author_meta( 'user_nicename' ) ) ) ?>">
		<?php echo get_avatar( get_the_author_meta( 'email', $author_id ), '96' ); ?>
	</a>
	<div class="author-desc">
		<div class="author-name">
			<a href="<?php echo esc_url( get_author_posts_url( $author_id, get_the_author_meta( 'user_nicename' ) ) ) ?>">
				<?php echo get_the_author_meta( 'display_name', $author_id ) ?>
			</a>
		</div>
		<div class="desc">
			<?php echo get_the_author_meta( 'description', $author_id ); ?>
		</div>
	</div>
</div>
<?php endif; ?>