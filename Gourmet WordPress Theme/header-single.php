<?php

	$front = KHT_BackEnd::db_get( option_page::$prefix . '_' . 'home' );
?>
<!-- Start main-header -->
<header class="main-header shark-bg align-center">
	<div class="logo-container">
		<a href="<?= site_url() ?>" class="logo">
			<img src="<?= $front['general']['logo_top'] ?>" alt="<?= get_bloginfo('name')?>">
		</a>
	</div>
	<!-- /logo-container -->
</header>
<!-- End main-header -->
