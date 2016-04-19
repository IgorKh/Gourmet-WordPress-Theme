<?php

	get_header();
	get_template_part( 'header-single' );
	while ( have_posts() ) :
		the_post();
		?>
		<section class="latest-post blog-single-page">
			<div class="container">
				<div class="row">
					<div class="col-md-10 col-md-offset-1">
						<div class="blog-post-container">
							<div class="blog-post">
								<article>
									<header>
										<h1><?= get_the_title() ?></h1>
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
										<?php the_content(); ?>
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
	endwhile;

	get_footer();
