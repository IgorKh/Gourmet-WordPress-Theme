<?php
	
	get_header();
	$blog=KHT_BackEnd::db_get(option_page::$prefix.'_'.'blog');
?>
	<!-- Start main-header -->
	<header class="main-header" id="top">
		<div class="top-banner-container top-banner-container-style2">
			<div class="top-banner-bg custom-bg4 parallax" data-stellar-background-ratio="0.5"></div>
			<style>
				.custom-bg4{
					background-image: url("<?= $blog['head']['background'] ?>") !important;
				}
			</style>
			<div class="top-banner">
				<div class="top-image">
					<img src="<?= $blog['head']['top'] ?>" alt="<?= get_bloginfo('name')?>">
				</div>
				<!-- /top-image -->
				<div class="bottom-image">
					<img src="<?= $blog['head']['bottom'] ?>" alt="<?= get_bloginfo('name')?>">
				</div>
				<!-- /bottom-image -->
			</div>
			<!-- /top-banner -->
			<div class="header-bottom-bar">
				<div class="container">
					<div class="row">
						<div class="col-md-9">
							<ul class="category-filter blog-category-filter">
								<li class="<?= ! is_category() && ! is_author() && ! is_search() ? 'active' : '' ?>">
									<a href="<?= get_permalink( get_page_by_path( 'blog' ) ) ?>"><span>All</span></li>
								<?php
									$categories = get_categories();
									$current    = is_category() ? single_cat_title( '', false ) : false;
									foreach ( $categories as $category ) {
										$name  = $category->name;
										$class = $current !== false && $current === $name ? 'active' : '';
										$slug  = $category->category_nicename;
										echo '<li class="' . $class . '"><a href="' . get_category_link( $category->cat_ID ) . '"><span>' . $name . '</span></a></li>';
									}
								?>
							</ul>
						</div>
						<!-- col-md-9 -->
						<div class="col-md-3">
							<?php get_search_form() ?>
						</div>
						<!-- /col-md-3 -->
					</div>
					<!-- /row -->
				</div>
				<!-- /container -->
			</div>
			<!-- /header-bottom-bar -->
		</div>
		<!-- /top-banner-container -->
	</header>
	<!-- End main-header -->
<?php
	echo '<h3 style="text-align: center;margin: 10px 0">';
	if ( is_category() ) {
		echo single_cat_title( 'Category: ' );
	} elseif ( is_author() ) {
		echo 'Author: ' . get_the_author();
	}else{
		echo isset($_GET['s'])?'You searched for: '.sanitize_text_field($_GET['s']):'';
	}

	echo '</h3>';
?>
	<section class="latest-post blog-page">
		<div class="container">
			<div class="row">
				<div class="col-md-10 col-md-offset-1">
					<div class="blog-post-container">
						<div class="blog-post" id="blog_posts" data-page="blog" data-page-number="1">
							<?php gourmet_loop(false,false,(isset($_GET['s'])?$_GET['s']:false),(is_author()?get_the_author_meta('ID'):false),(is_category()?get_the_category()[0]->cat_ID:false))?>
						</div>
						<!-- /blog-post -->
						<div class="load-more align-center">
							<a href="#" id="load_more_blog" class="custom-button button-style2 load-more-button"><i class="icon-eye"></i>Load
								More</a>
						</div>
						<!-- /load-more -->
					</div>
					<!-- /blog-post-container -->
				</div>
				<!-- /col-md-12 -->
			</div>
			<!-- /row -->
		</div>
		<!-- /container -->
	</section>
	<section class="clients white-rock-bg">
		<?php
			get_template_part( 'footer-blog' );
		?>
	</section>
<?php
	get_footer();
