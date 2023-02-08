<?php
/**
 * Theme Name:      Epron
 * Theme Author:    Mariusz Rek - Rascals Themes
 * Theme URI:       http://rascalsthemes.com/epron
 * Author URI:      http://rascalsthemes.com
 * File:            comments.php
 * @package epron
 * @since 1.0.0
 */
?>

<section id="comments" class="comments-section">
    
    <?php
    // protect password protected comments
    if ( post_password_required() ) : ?>

    	<p class="comment-message"><?php esc_html_e( 'This post is password protected. Enter the password to view any comments.', 'epron' ); ?></p>
    	</div></section>
    <?php return; endif; ?>

    <?php if ( have_comments() ) : ?>
        <h4 class="comments-title"><?php esc_html_e( 'Comments.', 'epron' ) ?></h4>

    	<div class="comments-container clearfix">	
    		<ul class="comment-list">
    			<?php 
                    wp_list_comments( array(
                        'type' => 'all',
                        'short_ping' => true,
                        'callback'=> 'epron_comments'
                    ) );
                ?>
    		</ul>		
    		<nav class="comments-navigation" role="navigation">
    		    <div class="nav-prev"><?php previous_comments_link(); ?></div>
    		    <div class="nav-next"><?php next_comments_link(); ?></div>
    		</nav>
    	</div>

    <?php else : // there are no comments so far ?>
    	<?php if ( ! comments_open() ) : ?>
    		<!-- If comments are closed. -->
    		<p class="comment-message"><?php esc_html_e( 'Comments are closed.', 'epron' ); ?></p>
    	<?php endif; ?>
    <?php endif; ?>

    <?php
    $fields = array();
    function custom_fields( $fields ) {
        global $comment_author, $comment_author_email, $comment_author_url;
        $consent = empty( $commenter['comment_author_email'] ) ? '' : 'checked="checked"';
        $fields['author'] = '<div class="flex-col-1-3 first">
                <label for="author">' . esc_html__('Name *', 'epron' ) . '</label>
                <input type="text" name="author" id="author" value="' . esc_attr( $comment_author ) . '" size="22" tabindex="1" required />
                </div>';
        $fields['email'] = '<div class="flex-col-1-3">
                <label for="email">' . esc_html__('Email *', 'epron' ) . '</label>
                <input type="text" name="email" id="email" value="' . esc_attr( $comment_author_email ) . '" size="22" tabindex="2" required />
                </div>';
        $fields['url'] = '<div class="flex-col-1-3 last">
                <label for="url">' . esc_html__('Website URL', 'epron' ) . '</label>
                <input type="text" name="url" id="url" value="' . esc_attr( $comment_author_url ) . '" size="22" tabindex="3"/>
                </div>';
        $fields['cookies'] = '<div class="clear"></div><div class="comment-form-cookies-consent"><input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"' . $consent . ' />' .'<label for="wp-comment-cookies-consent">' . esc_html__( 'Save my name, email, and website in this browser for the next time I comment.', 'epron' ) . '</label></div>';
        return $fields;
    }

    add_filter('comment_form_default_fields', 'custom_fields');

    $form_fields = array(
        'fields' => apply_filters( 'comment_form_default_fields', $fields ),
        'title_reply' => esc_html__('Leave a Reply.', 'epron'),
        'title_reply_to' =>  esc_html__('Leave a Reply.', 'epron'),
        'cancel_reply_link' => esc_html__('(Cancel Reply)', 'epron'),
        'comment_notes_before' => '',
        'label_submit' => esc_html__('Post Comment', 'epron'),
        'comment_notes_after' => '<p class="form-allowed-tags">' . esc_html__('* Your email address will not be published.', 'epron') . '<br/>' . sprintf( esc_html__('You may use these HTML tags and attributes: %s', 'epron'), ' <span>' . esc_html( allowed_tags() ) . '</span>' ) . '</p>',
        'comment_field' => '<div class="comment-field">
                <label for="comment">' . esc_html__('Your Comment (required)', 'epron' ) . '</label>
                <textarea tabindex="4" rows="9" id="comment" name="comment" class="textarea" required></textarea>
                </div>'
    );
    ?>
    <?php comment_form( $form_fields ); ?>
</section>