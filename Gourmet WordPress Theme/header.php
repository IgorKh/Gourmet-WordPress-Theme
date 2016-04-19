<?php

$general=KHT_BackEnd::db_get(option_page::$prefix.'_'.'general');
?><!DOCTYPE html>
<!--[if lt IE 7]>
<html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>
<html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>
<html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php wp_title('',true,'right'); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#23292c">
	<!-- Android 5.0 Tab Color -->
	<link rel="shortcut icon" href="<?= get_template_directory_uri() ?>/favicon.ico">

	<!-- Web Fonts -->
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,700,300,400' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Great+Vibes' rel='stylesheet' type='text/css'>

	<!-- Icon Fonts -->
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="<?= get_template_directory_uri() ?>/css/fontello.css">

	<!-- Plugins CSS -->
	<link rel="stylesheet" href="<?= get_template_directory_uri() ?>/css/rev-slider-settings.css">
	<link rel="stylesheet" href="<?= get_template_directory_uri() ?>/css/animate.css">
	<link rel="stylesheet" href="<?= get_template_directory_uri() ?>/css/owl.carousel.css">
	<link rel="stylesheet" href="<?= get_template_directory_uri() ?>/css/magnific-popup.css">
	<link rel="stylesheet" href="<?= get_template_directory_uri() ?>/css/mediaelementplayer.css">

	<!-- Template CSS -->
	<link rel="stylesheet" href="<?= get_template_directory_uri() ?>/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?= get_template_directory_uri() ?>/css/reset.css">
	<link rel="stylesheet" href="<?= get_template_directory_uri() ?>/css/main.css">

	<!-- Demo Purpose CSS -->
	<link rel="stylesheet" href="<?= get_template_directory_uri() ?>/css/custom-bg.css">

	<!-- Head JS Libraries -->
	<script src="<?= get_template_directory_uri() ?>/js/vendor/modernizr-2.6.2.min.js"></script>
	<script src="http://maps.google.com/maps/api/js"></script>
	<!-- REQUIRED FOR GOOGLE MAP -->

	<!--Template uri for js-->
	<script>var template_uri="<?= get_template_directory_uri() ?>"</script>
	<!--/Template uri for js-->

	<!-- WordPress Head files -->
	<?php wp_head() ?>
	<!-- /WordPress Head files -->
</head>
<body>
<!--[if lt IE 7]>
<p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade
	your browser</a> to improve your experience.</p>
<![endif]-->

<!-- Start mobile-nav -->
<div class="mobile-nav-container clearfix">
	<div class="main-nav-trigger mobile-nav-trigger">
		<a href="#"></a>
	</div>
</div>
<!-- End mobile-nav -->

<!-- Start main-nav-trigger -->
<div class="main-nav-trigger">
	<a href="#">Menu</a>
</div>
<!-- End main-nav-trigger -->

<!-- Start main-nav -->
<div class="main-nav-container dark">
	<div class="main-nav-inner">
		<div class="logo-container">
			<a href="#">
				<img src="<?= $general['general']['logo_top'] ?>" alt="<?= get_bloginfo('name') ?>">
			</a>
		</div>
		<!-- /logo-container -->
		<nav class="main-nav">
			<?php wp_nav_menu('main_menu') ?>
		</nav>
		<div class="socials-container">
			<ul>
				<?= $general['social']['facebook']['enable']?'<li><a href="'.$general['social']['facebook']['url'].'"><i class="fa fa-facebook"></i></a></li>':'' ?>
				<?= $general['social']['twitter']['enable']?'<li><a href="'.$general['social']['twitter']['url'].'"><i class="fa fa-twitter"></i></a></li>':'' ?>
				<?= $general['social']['skype']['enable']?'<li><a href="'.$general['social']['skype']['url'].'"><i class="fa fa-skype"></i></a></li>':'' ?>
			</ul>
		</div>
		<!-- /socials-container -->
		<div class="copyright">
			<p><?= $general['general']['copyright'] ?></p>
		</div>
		<!-- /copyright -->
	</div>
	<!-- /main-nav-inner -->
</div>
<!-- /main-nav-container -->
<!-- End main-nav -->

<!-- Start wrapper -->
       <div class="wrapper">
