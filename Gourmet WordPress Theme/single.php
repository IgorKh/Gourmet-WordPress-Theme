<?php
	
	get_header();
	get_template_part( 'header-single' );
?>
	<section class="latest-post blog-single-page">
	<div class="container">
	<div class="row">
	<div class="col-md-10 col-md-offset-1">
	<div class="blog-post-container">
	<div class="blog-post">
	<article>
<?php
	while ( have_posts() ) : the_post();
		?>
		<header>
			<ul class="category">
				<?php
					$link       = get_permalink();
					$categories = get_the_category();
					if ( $categories ) {
						$output = '';
						$count  = count( $categories );
						for ( $i = 0; $i < $count; $i ++ ) {
							$separator = isset( $categories[ $i + 1 ] ) ? ',' : '';
							$output .= '<li><a href="' . get_category_link( $categories[ $i ]->term_id ) . '" title="' . $categories[ $i ]->name . '">' . $categories[ $i ]->cat_name . '</a>' . $separator . '</li>';
							echo trim( $output, $separator );
						}
					}
				?>
			</ul>
			<h1><?= get_the_title() ?></h1>

			<div class="post-meta">
				<span><time><?php the_time( get_option( 'date_format' ) ); ?></time></span>
				<span>by <a href="<?= get_author_posts_url( get_the_author_meta( 'ID' ) ) ?>"><?= get_the_author() ?></a></span>
				<span><a href="<?= $link ?>"><?= get_comments_number( '0' ) ?></a> comments</span>
			</div>
			<!-- /post-meta -->
			<?php
				if ( has_post_thumbnail() ) {
					?>
					<div class="post-image">
						<a href="<?= $link ?>">
							<img src="<?= wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' )[0] ?>" alt="<?= get_the_title() ?>">
						</a>
					</div>
				<?php
				}
			?>
		</header>
		<div class="post-contents">
			<?php
				the_content();
			?>
		</div>
		<!-- /post-contents -->
		<footer>
			<div class="socials-container">
				<ul>
					<li><a href="#comment"><i class="fa fa-comment-o"></i></a></li>
				</ul>
			</div>
			<!-- /socials-container -->
		</footer>
		<div class="author clearfix">
			<div class="author-info">
				<h3 class="author-name"><?= get_the_author_meta( 'first_name' ) ?> <?= get_the_author_meta( 'last_name' ) ?></h3>
				<h4 class="author-job"><?= get_the_author_meta( 'nickname' ) ?></h4>

				<p><?= get_the_author_meta( 'description' ) ?></p>
			</div>
			<!-- /author-info -->
		</div>
		<!-- /author -->
		<?php
		$tags = wp_get_post_tags( get_the_ID() );
		if ( $tags ) {
			$first_tag = $tags[0]->term_id;
			$args      = array(
				'tag__in'          => array( $first_tag ),
				'post__not_in'     => array( get_the_ID() ),
				'posts_per_page'   => 3,
				'caller_get_posts' => 1
			);
			$my_query  = new WP_Query( $args );
			if ( $my_query->have_posts() ) {
				?>
				<div class="related-posts-container">
					<div class="section-title">
						<h3>Related Posts</h3>
					</div>
					<!-- /section-title -->
					<div class="related-posts">
						<div class="row">
							<?php
								while ( $my_query->have_posts() ) : $my_query->the_post(); ?>

									<div class="col-md-4 col-sm-6 col-xs-6">
										<article>
											<h4>
												<a href="<?= get_the_permalink() ?>"><?= get_the_title() ?></a>
											</h4>
											<time><?php the_time( get_option( 'date_format' ) ); ?></time>
										</article>
									</div>
								<?php
								endwhile;
							?>
						</div>
						<!-- /row -->
					</div>
					<!-- /related-posts -->
				</div>
			<?php
			}
			wp_reset_query();
		}

		$postTags = get_the_tags();
		if ( $postTags ) {
			echo '<div class="tags-container">';
			echo '<h6>Tagged With: </h6>';
			echo '<ul>';
			foreach ( $postTags as $tag ) {
				echo '<li> ' . $tag->name . ' </li>';
			}
			echo '</ul>';
			echo '</div>';
		}
		?>
		<div class="comments-container" id="comment">
			<div class="comments-counter">
				<h3>Comments<span> (<?= get_comments_number( '0' ) ?>)</span></h3>
			</div>
			<?php
				echo '<ul class="comment-list">';
				wp_list_comments( array( 'callback' => 'mytheme_comment' ), get_comments( array( 'post_id' => get_the_ID() ) ) );
				echo '</ul>';
			?>
			<div class="comment-form-container">
				<header class="section-title">
					<h3>Leave A reply</h3>
				</header>
				<?php
					$commenter = wp_get_current_commenter();
					$req       = get_option( 'require_name_email' );
					$aria_req  = ( $req ? " aria-required='true'" : '' );
					comment_form( array(
							'fields'               => array(
								'author' => '<div class="col-md-6"><input type="text" id="author" name="author" placeholder="Name' . ( $req ? '*' : '' ) . '" value="' . esc_attr( $commenter['comment_author'] ) . '">',
								'email'  => '<input type="email"  id="email" name="email" placeholder="E-mail' . ( $req ? '*' : '' ) . '" value="' . esc_attr( $commenter['comment_author_email'] ) . '"></div>',
							),
							'id_submit'            => 'submit',
							'class_submit'         => 'custom-button custom-button-style2',
							'title_reply'          => '',
							'title_reply_to'       => '',
							'cancel_reply_link'    => '',
							'label_submit'         => 'Send Message',
							'format'               => 'html5',
							'comment_field'        => '<div class="col-md-6"><textarea id="comment" name="comment" rows="6" placeholder="Message" aria-required="true"></textarea></div>',
							'must_log_in'          => '<p class="must-log-in">' .
							                          sprintf(
								                          __( 'You must be <a href="%s">logged in</a> to post a comment.' ),
								                          wp_login_url( apply_filters( 'the_permalink', get_permalink() ) )
							                          ) . '</p>',
							'logged_in_as'         => '<p class="logged-in-as">' .
							                          sprintf(
								                          __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>' ),
								                          admin_url( 'profile.php' ),
								                          wp_get_current_user()->display_name,
								                          wp_logout_url( apply_filters( 'the_permalink', get_permalink() ) )
							                          ) . '</p>',
							'comment_notes_before' => '',
							'comment_notes_after'  => '',
						)
					);
				?>
			</div>
		</div>
		</article>
		</div>
		<!-- /blog-post -->
		</div>
		<!-- /blog-post-container -->
		</div>
		<!-- /col-md-12 -->
		</div>
		<!-- /row -->
		</div>
		<!-- /container -->
		</section>
		<?php
		$previous = get_previous_post();
		$next     = get_next_post();
		if ( $previous || $next ) {
			echo '<section class="blog-navigation"><div class="container"><div class="row"><div class="col-md-10 col-md-offset-1">';
			echo $previous ? '<a href="' . get_permalink( $previous->ID ) . '" class="blog-post-nav blog-post-prev"><span>Previous Post</span><h5>' . $previous->post_title . '</h5></a>' : '';
			echo $next ? '<a href="' . get_permalink( $next->ID ) . '" class="blog-post-nav blog-post-next"><span>Next Post</span><h5>' . $next->post_title . '</h5></a>' : '';
			echo '</div></div></div></section>';
		}
	endwhile;

	get_footer();
