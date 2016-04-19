<?php
	
	get_header();
	$front = KHT_BackEnd::db_get( option_page::$prefix . '_' . 'home' );
?>
	<!-- Start main-header -->
	<header class="main-header slider-on" id="top">
		<!-- main-slider-container -->
		<div class="main-slider-container">
			<?php
				$revSlider = 'putRevSlider';
				if ( is_callable( $revSlider ) ) {
					$revSlider( "home", "homepage" );
				} else {
					echo '<script>console.log("revSlider is not callable")</script>';
				}
			?>
		</div>
		<!-- /main-slider-container -->
	</header>
	<!-- End main-header -->

	<section class="team" id="about">
		<div class="row">
			<div class="col-md-6 wow fadeInLeft">
				<div class="col-md-8 pull-right">
					<header class="section-title wow fadeInLeft">
						<h1>
							<span><?= $front['chefs']['title']['orange'] ?></span> <?= $front['chefs']['title']['black'] ?>
						</h1>
					</header>
					<div class="members-details-container">
						<?php
							foreach ( $front['chefs']['member'] as $member ) {
								?>
								<div class="member">
									<div class="member-info clearfix">
										<figure class="member-thumb">
											<img src="<?= $member['thumb'] ?>" alt="<?= $member['title'] ?>">
										</figure>
										<h3><?= $member['title'] ?></h3>

										<p class="member-post"><?= $member['post'] ?></p>
									</div>
									<!-- /member-info -->
									<div class="member-bio">
										<p class="italic"><?= $member['bio_short'] ?></p>

										<p><?= $member['bio_description'] ?></p>
									</div>
									<!-- /member-bio -->
								</div>
							<?php
							}
						?>
					</div>
					<!-- /members-details-container -->
					<div class="team-carousel-nav"></div>
				</div>
				<!-- /col-md-8 -->
			</div>
			<!-- /col-md-6 -->
			<div class="members-images-container col-md-6 pull-right wow fadeInRight">
				<?php
					foreach ( $front['chefs']['member'] as $member ) {
						?>
						<div class="member">
							<div class="member-image">
								<figure>
									<img src="<?= $member['image'] ?>" alt="<?= $member['title'] ?>">
								</figure>
							</div>
						</div>
					<?php
					}
				?>
			</div>
			<!-- /members-images-con -->
		</div>
		<!-- /row -->
	</section><!-- /team -->

	<section class="gallery dark-bg" id="gallery">
		<div class="container">
			<div class="row">
				<div class="col-md-8 col-md-offset-2 wow fadeInDown">
					<header class="section-title">
						<h1>
							<span><?= $front['gallery']['title']['orange'] ?></span> <?= $front['gallery']['title']['black'] ?>
						</h1>

						<p><?= $front['gallery']['title']['description'] ?></p>
					</header>
				</div>
				<!-- /col-md-8 -->
				<div class="col-md-12 wow fadeInDown">
					<div class="gallery-filter-container">
						<ul class="gallery-filter">
							<li class="filter active" data-filter="all"><span>All</span></li>
							<?php
								foreach ( $front['gallery']['filter'] as $filter ) {
									?>
									<li class="filter" data-filter=".<?= $filter ?>"><span><?= $filter ?></span></li>
								<?php
								}
							?>
						</ul>
					</div>
					<!-- /gellery-filter-container -->
				</div>
				<!-- /col-md-12 -->
			</div>
			<!-- /row -->
		</div>
		<!-- /container -->
		<div class="gallery-items-container wow fadeInDown">
			<ul class="clearfix">
				<?php
					foreach ( $front['gallery']['item'] as $item ) {
						?>
						<li class="overlay-container mix <?= $item['filter'] ?>">
							<img src="<?= $item['image'] ?>" alt="">

							<div class="overlay">
								<div class="overlay-details">
									<h3>Look</h3>
								</div>
								<!-- /overlay-details -->
								<div class="buttons-container">
									<a href="<?= $item['image'] ?>" class="button-zoom popup-trigger"></a>
								</div>
								<!-- /buttons-container -->
							</div>
							<!-- /overlay -->
						</li>
					<?php
					}
				?>
			</ul>
		</div>
		<!-- /gallery-items-container -->
	</section><!-- /gallery -->

	<section class="services" id="services">
		<div class="container">
			<div class="row">
				<div class="col-md-8 col-md-offset-2 wow fadeInDown">
					<header class="section-title">
						<h1>
							<span><?= $front['services']['title']['orange'] ?></span> <?= $front['services']['title']['black'] ?>
						</h1>

						<p><?= $front['services']['description'] ?></p>
					</header>
				</div>
				<!-- /col-md-8 -->
				<div class="col-md-12">
					<div class="row">
						<?php
							$menu = KHT_BackEnd::db_get( option_page::$prefix . '_' . 'menu' );
							foreach ( $menu['services'] as $service ) {
								?>
								<div class="col-md-3 wow fadeInDown">
									<div class="service">
										<figure>
											<img src="<?= $service['thumb'] ?>" alt="<?= $service['title_front'] ?>">
										</figure>
										<h2><?= $service['title_front'] ?></h2>

										<p><?= $service['description_front'] ?></p>
									</div>
									<!-- /service -->
								</div>
							<?php
							}
						?>
						<!-- /col-md-3 -->
					</div>
					<!-- /row -->
				</div>
				<!-- /services-container -->
			</div>
			<!-- /row -->
		</div>
		<!-- /container -->
	</section><!-- /services -->

	<section class="menus dark-bg custom-bg1 parallax" data-stellar-background-ratio="0.5" data-stellar-vertical-offset="-150" id="menu">
		<style>
			.custom-bg1 {
				background-image: url("<?= $front['services']['background'] ?>") !important;
			}
		</style>
		<div class="container">
			<div class="row">
				<div class="menus-container wow fadeInDown">
					<div class="menu-carousel-nav"></div>
					<div class="menu-carousel">
						<?php
							foreach ( $menu['services'] as $service ) {
								?>
								<div class="menu col-md-12">
									<div class="row">
										<div class="col-md-8 col-md-offset-2">
											<header class="section-title">
												<h1>
													<span><?= $service['title']['orange'] ?></span> <?= $service['title']['black'] ?>
												</h1>
											</header>
										</div>
										<!-- /col-md-8 -->
										<div class="col-md-6">
											<?php
												foreach ( $service['food'] as $food ) {
													?>
													<div class="food">
														<h6 class="food-name"><?= $food['title'] ?></h6>

														<div class="food-desc">
															<div class="food-details">
																<span><?= $food['description'] ?></span>
															</div>
															<!-- /food-details -->
															<div class="dots"></div>
															<div class="food-price">
																<span><?= $food['price'] ?></span>
															</div>
															<!-- /food-price -->
														</div>
														<!-- /food-desc -->
													</div>

												<?php
												}
											?>
										</div>
										<!-- /col-md-6 -->
									</div>
									<!-- /row -->
								</div>
							<?php
							}
						?>

					</div>
					<!-- /menu-carousel -->
				</div>
				<!-- /menus-container -->
			</div>
			<!-- /row -->
		</div>
		<!-- /container -->
		<div class="container">
			<div class="row">
				<div class="col-md-12 align-center wow fadeInUp">
					<a href="<?= get_page_link( $front['services']['link'] ) ?>" class="custom-button button-style1"><i class="icon-eye"></i><?= $front['services']['label'] ?>
					</a>
				</div>
			</div>
		</div>
		<!-- /col-md-12 -->
	</section><!-- /menu -->
<?php
	get_footer();
