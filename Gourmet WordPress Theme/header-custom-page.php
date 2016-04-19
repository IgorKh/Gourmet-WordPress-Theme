<?php


	$about=KHT_BackEnd::db_get(option_page::$prefix.'_'.'about');
		$menu=KHT_BackEnd::db_get(option_page::$prefix.'_'.'menu');
			$reserve=KHT_BackEnd::db_get(option_page::$prefix.'_'.'reservation');
	if ( is_page_template( 'template-about.php' ) ) {
		$top_image    = $about['head']['top'];
		$bottom_image = $about['head']['bottom'];
		$custom_bg = $about['head']['background'];
	} elseif ( is_page_template( 'template-menu.php' ) ) {
		$top_image    = $menu['head']['top'];
		$bottom_image = $menu['head']['bottom'];
		$custom_bg    = $menu['head']['background'];
	} elseif ( is_page_template( 'template-reservation.php' ) ) {
		$top_image    = $reserve['head']['top'];
		$bottom_image = $reserve['head']['bottom'];
		$custom_bg    = $reserve['head']['background'];
	} else {
		$top_image    = get_template_directory_uri() . '/img/slider-images/who-we-are.png';
		$bottom_image = get_template_directory_uri() . '/img/slider-images/cooking-since2001.png';
		$custom_bg    = '2';
	}
?>
<!-- Start main-header -->
<header class="main-header" id="top">
	<div class="top-banner-container top-banner-container-style1">
		<div class="top-banner-bg bgbgbg parallax" data-stellar-background-ratio="0.5"></div>
		<style>
			.bgbgbg{
				background-image: url("<?= $custom_bg ?>") !important;
			}
		</style>
		<div class="top-banner">
			<div class="top-image">
				<img src="<?= $top_image ?>" alt="<?= get_bloginfo('name') ?>">
			</div>
			<!-- /top-image -->
			<div class="bottom-image">
				<img src="<?= $bottom_image ?>" alt="<?= get_bloginfo('name') ?>">
			</div>
			<!-- /bottom-image -->
		</div>
		<!-- /top-banner -->
	</div>
	<!-- /top-banner-container -->
</header>
<!-- End main-header -->
