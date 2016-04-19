<?php
	
	$general=KHT_BackEnd::db_get(option_page::$prefix.'_'.'general');
?>
<section class="map" id="contact">
	<script>
		var map_latitude='<?= $general['map']['latitude']?>';
		var map_longitude='<?= $general['map']['longitude'] ?>';
		var map_zoom='<?= $general['map']['zoom'] ?>';
	</script>
	<div class="map-container wow fadeInDown">
		<div id="google-map"></div>
		<div id="cd-zoom-in"></div>
		<div id="cd-zoom-out"></div>
	</div>
	<!-- /map-container -->
</section>

<footer class="main-footer dark-bg">
	<div class="container">
		<div class="row">
			<div class="col-md-3 align-center">
				<div class="logo-container wow fadeInLeft">
					<a href="<?= site_url() ?>">
						<img src="<?= $general['general']['logo_footer'] ?>" alt="<?= get_bloginfo('name') ?>">
					</a>
				</div>
				<!-- /logo-container -->
				<div class="socials-container">
					<ul>
						<?= $general['social']['facebook']['enable']?'<li class="wow fadeInLeft"><a href="'.$general['social']['facebook']['url'].'"><i class="fa fa-facebook"></i></a></li>':'' ?>
						<?= $general['social']['twitter']['enable']?'<li class="wow fadeInLeft" data-wow-delay="0.1s"><a href="'.$general['social']['twitter']['url'].'"><i class="fa fa-twitter"></i></a></li>':'' ?>
						<?= $general['social']['skype']['enable']?'<li class="wow fadeInLeft" data-wow-delay="0.2s"><a href="'.$general['social']['skype']['url'].'"><i class="fa fa-skype"></i></a></li>':'' ?>
						<?= $general['social']['google']['enable']?'<li class="wow fadeInLeft" data-wow-delay="0.3s"><a href="'.$general['social']['google']['url'].'"><i class="fa fa-google"></i></a></li>':'' ?>
						<?= $general['social']['linkedin']['enable']?'<li class="wow fadeInLeft" data-wow-delay="0.4s"><a href="'.$general['social']['linkedin']['url'].'"><i class="fa fa-linkedin"></i></a></li>':'' ?>
						<?= $general['social']['instagram']['enable']?'<li class="wow fadeInLeft" data-wow-delay="0.5s"><a href="'.$general['social']['instagram']['url'].'"><i class="fa fa-instagram"></i></a></li>':'' ?>

					</ul>
				</div>
				<!-- /socials-container -->
			</div>
			<!-- /col-md-3 -->
			<div class="col-md-6 wow fadeInDown">
				<div class="contact-form-contaienr">
					<div class="section-title">
						<h1><span><?= $general['feedback']['title']['orange'] ?></span> <?= $general['feedback']['title']['black'] ?></h1>
					</div>
					<form id="contact-form" method="post" action="<?= get_template_directory_uri()?>/php/contact.php">
						<input type="text" id="name" name="name" placeholder="<?= $general['feedback']['placeholder']['name']?$general['feedback']['placeholder']['name']:'Name' ?>*" required>
						<input type="email" id="email" name="email" placeholder="<?= $general['feedback']['placeholder']['email']?$general['feedback']['placeholder']['email']:'Email' ?>*" required>
						<textarea id="message" name="message" rows="6" placeholder="<?= $general['feedback']['placeholder']['message']?$general['feedback']['placeholder']['message']:'Message' ?>" required></textarea>
						<button type="submit"><?= $general['feedback']['submit']?$general['feedback']['submit']:'Send Message' ?></button>
					</form>
					<div id="form-messages"></div>
				</div>
				<!-- /contact-form-container -->
			</div>
			<!-- /col-md-6 -->
			<div class="col-md-3 wow fadeInRight">
				<div class="address-container">
					<address>
						<img src="<?= get_template_directory_uri() ?>/img/template-assets/map-pin.png" alt="Marine Food Address">
						<p>
							<span><?= $general['address']['address']['line1'] ?></span>
							<span><?= $general['address']['address']['line2'] ?></span>
							<span><?= $general['address']['address']['line3'] ?></span>
							<span><?= $general['address']['address']['line4'] ?></span>
						</p>
						<img src="<?= get_template_directory_uri() ?>/img/template-assets/phone-icon.png" alt="Marine Food Address">

						<p>
							<span>Phone: <?= $general['address']['phone'] ?></span>
							<span>Fax: <?= $general['address']['fax'] ?></span>
						</p>
						<img src="<?= get_template_directory_uri() ?>/img/template-assets/mail-icon2.png" alt="Marine Food Address">

						<p>
							<span><?= $general['address']['email'] ?></span>
						</p>
					</address>
				</div>
				<!-- /address-container -->
			</div>
			<!-- /col-md-3 -->
			<div class="copyright col-md-12 wow fadeInUp" data-wow-delay="0.7s">
				<p><?= $general['general']['copyright']?$general['general']['copyright']:'&copy; 2015 The Gourmet. All Rights Reserved' ?></p>
			</div>
			<!-- /copyright -->
		</div>
		<!-- /row -->
	</div>
	<!-- /container -->
</footer>

</div><!-- /wrapper -->
<!-- End wrapper -->

<script src="<?= get_template_directory_uri() ?>/js/vendor/jquery-2.1.3.min.js"></script>
<script src="<?= get_template_directory_uri() ?>/js/imagesloaded.pkgd.min.js"></script>
<script src="<?= get_template_directory_uri() ?>/js/jquery.themepunch.tools.min.js"></script>
<script src="<?= get_template_directory_uri() ?>/js/jquery.themepunch.revolution.min.js"></script>
<script src="<?= get_template_directory_uri() ?>/js/retina.min.js"></script>
<script src="<?= get_template_directory_uri() ?>/js/SmoothScroll.js"></script>
<script src="<?= get_template_directory_uri() ?>/js/owl.carousel.min.js"></script>
<script src="<?= get_template_directory_uri() ?>/js/jquery.mixitup.min.js"></script>
<script src="<?= get_template_directory_uri() ?>/js/jquery.magnific-popup.min.js"></script>
<script src="<?= get_template_directory_uri() ?>/js/jquery.stellar.min.js"></script>
<script src="<?= get_template_directory_uri() ?>/js/jquery.nicescroll.min.js"></script>
<script src="<?= get_template_directory_uri() ?>/js/jquery.nav.js"></script>
<script src="<?= get_template_directory_uri() ?>/js/cd-google-map.js"></script>
<script src="<?= get_template_directory_uri() ?>/js/wow.min.js"></script>
<script src="<?= get_template_directory_uri() ?>/js/tweetie.min.js"></script>
<script src="<?= get_template_directory_uri() ?>/js/jquery.scrollme.min.js"></script>
<script src="<?= get_template_directory_uri() ?>/js/plugins.js"></script>
<script src="<?= get_template_directory_uri() ?>/js/mediaelement-and-player.min.js"></script>
<script src="<?= get_template_directory_uri() ?>/js/main.js"></script>

<!-- WordPress footer files -->
<?php wp_footer() ?>
<!-- /WordPress footer files -->

</body>
</html>
