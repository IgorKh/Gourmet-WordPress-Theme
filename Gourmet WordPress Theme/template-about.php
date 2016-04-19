<?php
	/*
	Template Name: About template
	*/

	get_header();
	get_template_part( 'header-custom-page' );

	$about=KHT_BackEnd::db_get(option_page::$prefix.'_'.'about');

	$item='';
	foreach($about['gallery']['image'] as $image){
		$item .='<li class="overlay-container">';


		$item .= '<img src="'.$image['image'].'" alt="'.$image['title'].'">';

		$item .= '<div class="overlay">';

		$item .= '<div class="overlay-details">';
		$item .= '<h3>'.$image['title'].'</h3>';
		$item .= '<p>'.$image['description'].'</p>';
		$item .= '</div>';

		$item .='<div class="buttons-container"><a href="#" class="button-link"></a><a href="#" class="button-zoom"></a></div>';

		$item .= '</div>';


		$item .='</li>';
	}


	$review ='';
	foreach($about['review']['review'] as $item){
		$review.='<div class="testimonial">';

		$review.='<blockquote>';
		$review .= '<p>'.$item['text'].'</p>';
		$review.='</blockquote>';

		$review .= '<p class="customer-name">'.$item['name'].'</p>';
		$review .= '<p class="customer-job">'.$item['description'].'</p>';

		$review .= '</div>';
	}
?>
	<section class="about dark-bg">
		<div class="container">
			<div class="row">
				<header class="section-title col-md-6 col-md-offset-3 wow fadeInDown">
					<h1><span><?= $about['text']['title']['orange'] ?></span> <?= $about['text']['title']['white'] ?></h1>
					<p><?= $about['text']['title']['description'] ?></p>
				</header>
				<div class="about-container">
					<div class="row">
						<div class="col-md-6 wow fadeInLeft">
							<p>
								<span class="dropcap"><?= $about['text']['left']['title'] ?></span>
								<?= $about['text']['left']['text'] ?>
							</p>
						</div>
						<!-- /col-md-6 -->
						<div class="col-md-6 wow fadeInRight">
							<p>
								<span class="dropcap"><?= $about['text']['right']['title'] ?></span>
								<?= $about['text']['right']['text'] ?>
							</p>
						</div>
						<!-- /col-md-6 -->
					</div>
					<!-- /row -->
				</div>
				<!-- /about-container -->
			</div>
			<!-- /row -->
		</div>
		<!-- /contianer -->
		<div class="members-carousel wow fadeInDown">
			<div class="members-carousel-nav"></div>
			<ul class="clearfix">
			<?= $item ?>
			</ul>
		</div>
		<!-- /members-carousel -->
		<div class="promo wow fadeInUp">
			<p><?= $about['promo']['orange'] ?> <span><?= $about['promo']['white'] ?> </span></p>
		</div>
		<!-- /promo -->
	</section>
<?php
?>
	<section class="latest-post">
		<div class="container">
			<div class="row">
				<header class="section-title col-md-6 col-md-offset-3 wow fadeInDown">
					<h1><span><?= $about['blog']['orange'] ?></span> <?= $about['blog']['black'] ?></h1>
					<p><?= $about['blog']['description'] ?></p>
				</header>
				<div class="col-md-12 wow fadeInUp">
					<div class="blog-post-container">
						<div class="blog-post" id="blog_posts" data-page="about" data-page-number="0">
						<?php gourmet_loop(false,1) ?>
						</div>
						<!-- /blog-post -->
						<div class="load-more align-center">
							<a href="#" class="custom-button button-style2 load-more-button" id="load_more_blog"><i class="icon-eye"></i>Load
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

	<section class="testimonials dark-bg custom-bg3 parallax" data-stellar-background-ratio="0.5" data-stellar-vertical-offset="-100">
		<div class="container">
			<div class="row">
				<header class="section-title wow fadeInUp">
					<h1><span><?= $about['review']['title']['orange'] ?></span> <?= $about['review']['title']['white'] ?></h1>
				</header>
				<div class="testimonial-container col-md-10 col-md-offset-1 wow fadeInUp">
					<div class="testimonial-carousel">
						<?= $review ?>
					</div>
					<!-- /testimonial-carousel -->
					<div class="testimonial-carousel-nav"></div>
				</div>
				<!-- /testimonial-container -->
			</div>
			<!-- /row -->
		</div>
		<!-- /container -->
	</section>

	<section class="clients">
		<?php
			get_template_part('footer-blog');
		?>
		<!-- /contianer -->
	</section>
<?php
	get_footer();
