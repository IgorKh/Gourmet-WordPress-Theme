<?php
	/*
		Template Name: Menu template
	*/


	get_header();
	get_template_part('header-custom-page');
	$menu=KHT_BackEnd::db_get(option_page::$prefix.'_'.'menu');

		$service='';
	foreach($menu['services'] as $services){

		$service .='<div class="menu col-md-12">';
		$service .='<div class="row">';

		$service .='<div class="col-md-12">';
		$service .= '<header class="section-title">';
		$service .= '<h1><span>'.$services['title']['orange'].'</span>'.$services['title']['black'].'</h1>';
		$service .= '<p>'.$services['title']['description'].'</p>';
		$service .= '</header>';
		$service .='</div>';

		$service.= '<div class="col-md-12">';

		foreach ( $services['food'] as $food) {
			$service .='<div class="food">';
			$service .= '<h6 class="food-name">'.$food['title'].'</h6>';
			$service .= '<div class="food-desc">';
			$service .= '<div class="food-details"><span>'.$food['description'].'</span></div>';
			$service .= '<div class="dots"></div>';
			$service .= '<div class="food-price"><span>'.$food['price'].'</span></div>';
			$service .='</div>';
			$service .='</div>';
		}
		$service .='</div>';

		$service .='</div>';
		$service .='</div>';
	}

	$right_services='';
	foreach($menu['services'] as $services){
		$right_services .= '<li>';
		$right_services .= '<figure><img src="'.$services['thumb'].'" alt="'.$services['title_front'].'"></figure>';
		$right_services .= '<div class="meal-details">';
		$right_services .= '<h3>'.$services['title_front'].'</h3>';
		$right_services .= '<p>'.$services['description_front'].'</p>';
		$right_services .= '</div>';
		$right_services .= '</li>';

	}

?>
	<section class="menus menus-full dark-bg white-rock-bg">
		<div class="row">
			<div class="left-section white-rock-bg">
				<div class="col-md-8 pull-right menus-container">
					<div class="menu-carousel wow fadeInLeft">
						<?= $service ?>
					</div>
					<!-- /menu-carousel -->
					<div class="menu-carousel-nav"></div>
				</div>
				<!-- /menus-container -->
			</div>
			<!-- /left-section -->
			<div class="right-section">
				<div class="col-md-4 menu-meals-container wow fadeInRight">
					<ul class="menu-meals">
						<?= $right_services ?>
					</ul>
				</div>
				<!-- /menu-meals-container -->
			</div>
			<!-- /right-section -->
		</div>
		<!-- /row -->
	</section><!-- /menu-container -->

	<section class="promo-image scrollme parallax custom-bg7" data-stellar-background-ratio="0.5" data-stellar-vertical-offset="-1200">
		<style>
			.custom-bg7{
				background-image: url("<?= $menu['promo']['background']?>");
			}
		</style>
		<div class="container animateme" data-when="exit" data-from="0" data-to="0.8" data-opacity="0" data-translatey="100">
			<div class="row">
				<h1><?= $menu['promo']['title']?></h1>
				<a href="<?= get_page_link($menu['promo']['link']) ?>" class="custom-button button-style1"><i class="icon-eye"></i><?= $menu['promo']['label']?></a>
			</div>
			<!-- /row -->
		</div>
		<!-- /container -->
	</section>

<?php
	get_footer();
