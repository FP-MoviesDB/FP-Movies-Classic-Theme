<?php
/*
* -------------------------------------------------------------------------------------
* @author: FP Movies Classic Theme
* @author URI: https://fpmoviesdb.xyz/
* @copyright: (c) | All rights reserved
* -------------------------------------------------------------------------------------
*
* @since 1.0.0
*
*/

if (!defined('ABSPATH')) exit;

function fp_comments_template($comment, $args, $depth)
{
	$tag = 'li';
	$add_below = 'comment';
	$margin_left = $depth > 1 ? ($depth - 1) * 20 . 'px' : '0';  // Indent nested comments
?>
	<<?php echo $tag ?>
		<?php comment_class(empty($args['has_children']) ? '' : 'parent') ?> id="comment-<?php comment_ID() ?>" style="display: flex; justify-content: center; align-items: safe center; margin-left: <?php echo $margin_left; ?>;">
		<div class="comment-avatar" style="margin-right: 10px; border-radius: 50%; overflow: hidden;">
			<?php echo get_avatar($comment, $args['avatar_size']); // Display the avatar/icon 
			?>
		</div>
		<div class="comment-content-wrapper" style="display: flex; flex: 1; justify-content: start; align-items: flex-start; flex-direction: column;">
			<div class="comment-content" style="background-color: inherit; color: #f5f5f5; padding: 15px; border-radius: 5px; width: 100%;">

				<div class="comment-header" style="margin-bottom: 0px; font-weight: 600; display: flex; align-items: center;">
					<span class="comment-author">
						<?php echo ucwords(get_comment_author());
						?>
					</span>
				</div>

				<div class="comment-meta" style="margin-bottom: 5px;">
					<span class="comment-date" style="font-size: 0.8rem; color: grey;">
						<?php printf(__('%1$s at %2$s'), get_comment_date(), get_comment_time()); // Display the comment date 
						?>
					</span>
				</div>

				<div class="comment-text" style="margin-bottom: 5px;">
					<?php comment_text(); // Display the comment text 
					?>
				</div>

				<div class="comment-reply" style="display: flex; justify-content: start; align-items: center; color: grey;">
					<?php comment_reply_link(array_merge($args, array('add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth']))); // Display the reply link 
					?>
				</div>

			</div>
		</div>
	</<?php echo $tag ?>>
<?php
}