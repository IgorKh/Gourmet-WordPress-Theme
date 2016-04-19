<?php
	/*
		Template Name: Reservation template
	*/

	get_header();
	get_template_part( 'header-custom-page' );
	$reserve=KHT_BackEnd::db_get(option_page::$prefix.'_'.'reservation');
	$general=KHT_BackEnd::db_get(option_page::$prefix.'_'.'general')['address']['address'];
?>
	<section class="reservation-terms dark-bg">
		<div class="container">
			<div class="row">
				<header class="col-md-12 section-title wow fadeInDown">
					<h1><span><?= $reserve['text']['title']['orange'] ?></span> <?= $reserve['text']['title']['white'] ?></h1>
				</header>
				<div class="col-md-6 wow fadeInLeft">
					<div class="term">
						<p>
							<span><?= $reserve['text']['top_left']['title'] ?></span>
							<?= $reserve['text']['top_left']['text'] ?>
						</p>

						<p><?= $reserve['text']['bottom_left']['text'] ?></p>
					</div>
					<!-- /term -->
				</div>
				<!-- /col-md-6 -->
				<div class="col-md-6 wow fadeInRight">
					<div class="term">
						<p>
							<span><?= $reserve['text']['top_right']['title'] ?></span>
							<?= $reserve['text']['top_right']['text'] ?>
						</p>

						<p><?= $reserve['text']['bottom_right']['text'] ?></p>
					</div>
					<!-- /term -->
				</div>
				<!-- /col-md-6 -->
			</div>
			<!-- /row -->
		</div>
		<!-- /container -->
	</section>

	<section class="reservation">
		<div class="container">
			<div class="row">
				<div class="col-md-6 wow fadeInLeft">
					<header class="section-title">
						<h2><span><?= $reserve['online_form']['title']['orange'] ?></span> <?= $reserve['online_form']['title']['black'] ?></h2>
					</header>
					<form action="#" class="reservation-form">
						<div class="row">
							<div class="input-container col-md-6">
								<input type="text" name="reservation-fname" id="reservation-fname" placeholder="<?= $reserve['online_form']['placeholder']['first_name']?$reserve['online_form']['placeholder']['first_name']:'First Name' ?>*" required>
							</div>
							<!-- /input-container -->
							<div class="input-container col-md-6">
								<input type="text" name="reservation-lname" id="reservation-lname" placeholder="<?= $reserve['online_form']['placeholder']['last_name']?$reserve['online_form']['placeholder']['last_name']:'Last Name' ?>*" required>
							</div>
							<!-- /input-container -->
							<div class="input-container col-md-6">
								<input type="text" name="reservation-phone" id="reservation-phone" placeholder="<?= $reserve['online_form']['placeholder']['phone']?$reserve['online_form']['placeholder']['phone']:'Phone' ?>*" required>
							</div>
							<!-- /input-container -->
							<div class="input-container col-md-6">
								<input type="text" name="reservation-email" id="reservation-email" placeholder="<?= $reserve['online_form']['placeholder']['email']?$reserve['online_form']['placeholder']['email']:'Email' ?>*" required>
							</div>
							<!-- /input-container -->
							<div class="input-container col-md-12">
								<input type="text" name="reservation-guests-mumber" id="reservation-guests-mumber" placeholder="<?= $reserve['online_form']['placeholder']['guests']?$reserve['online_form']['placeholder']['guests']:'Number Of Guests' ?>*" required>
							</div>
							<!-- /input-container -->
							<div class="input-container reservation-date col-md-12">
								<p><?= $reserve['online_form']['placeholder']['date']['title']?$reserve['online_form']['placeholder']['date']['title']:'Date of Reservation' ?> <span><?= $reserve['online_form']['placeholder']['date']['description'] ?></span> *</p>
								<input type="text" name="reservation-date-day" id="reservation-date-day" placeholder="<?= $reserve['online_form']['placeholder']['date']['day']?$reserve['online_form']['placeholder']['date']['day']:'DD' ?>" required>
								<span class="seprator">/</span>
								<input type="text" name="reservation-date-month" id="reservation-date-month" placeholder="<?= $reserve['online_form']['placeholder']['date']['month']?$reserve['online_form']['placeholder']['date']['month']:'MM' ?>" required>
								<span class="seprator">/</span>
								<input type="text" name="reservation-date-year" id="reservation-date-year" placeholder="<?= $reserve['online_form']['placeholder']['date']['year']?$reserve['online_form']['placeholder']['date']['year']:'YYYY' ?>" required>
							</div>
							<!-- /input-container -->
							<div class="input-container reservation-time col-md-12">
								<p><?= $reserve['online_form']['placeholder']['time']['title']?$reserve['online_form']['placeholder']['time']['title']:'Time of Reservation' ?>*</p>
								<input type="text" name="reservation-time-hour" id="reservation-time-hour" placeholder="<?= $reserve['online_form']['placeholder']['time']['hour']?$reserve['online_form']['placeholder']['time']['hour']:'HH' ?>" required>
								<span class="seprator">:</span>
								<input type="text" name="reservation-time-minute" id="reservation-time-minute" placeholder="<?= $reserve['online_form']['placeholder']['time']['minute']?$reserve['online_form']['placeholder']['time']['minute']:'MM' ?>" required>
								<span class="seprator">:</span>
								<select name="reservation-time-ampm" id="reservation-time-ampm">
									<option value="am"><?= $reserve['online_form']['placeholder']['time']['am']?$reserve['online_form']['placeholder']['time']['am']:'AM' ?></option>
									<option value="pm"><?= $reserve['online_form']['placeholder']['time']['pm']?$reserve['online_form']['placeholder']['time']['pm']:'PM' ?></option>
								</select>
							</div>
							<!-- /input-container -->
							<div class="input-container col-md-12">
								<input type="text" name="reservation-accasion" id="reservation-accasion" placeholder="<?= $reserve['online_form']['placeholder']['occasion']?$reserve['online_form']['placeholder']['occasion']:'What is accasion?' ?>">
							</div>
							<!-- /input-container -->
							<div class="input-container col-md-12">
								<textarea name="reservation-comment" id="reservation-comment" placeholder="<?= $reserve['online_form']['placeholder']['comment']?$reserve['online_form']['placeholder']['comment']:'Comment' ?>" rows="5"></textarea>
							</div>
							<!-- /input-container -->
							<div class="input-container col-md-12">
								<button type="submit" class="custom-button button-style1"><i class="icon-eye"></i>
									<?= $reserve['online_form']['placeholder']['label']?$reserve['online_form']['placeholder']['label']:'Find Table' ?>
								</button>
							</div>
							<!-- /input-container -->
						</div>
						<!-- /row -->
					</form>
					<div id="reservation-form-messages"></div>
				</div>
				<!-- /col-md-6 -->
				<div class="col-md-6 wow fadeInRight">
					<div class="reservation-by-phone">
						<header class="section-title">
							<h2><span><?= $reserve['phone']['title']['orange'] ?></span> <?= $reserve['phone']['title']['black'] ?></h2>
						</header>
						<div class="contact-info">
							<figure>
								<img src="<?= get_template_directory_uri() ?>/img/template-assets/icon-phone.png" alt="Marine Food Calling Info">
							</figure>
							<div class="info-container">
								<h3 class="phone-number">(<?= $reserve['phone']['number']['orange'] ?>) <span><?= $reserve['phone']['number']['black'] ?></span></h3>

								<p class="call-time"><?= $reserve['phone']['description'] ?></p>
							</div>
							<!-- /info-container -->
						</div>
						<!-- /call-info -->
						<address class="contact-info">
							<figure>
								<img src="<?= get_template_directory_uri() ?>/img/template-assets/icon-map-pin.png" alt="Marine Food Calling Info">
							</figure>
							<div class="info-container">
								<p><?= $general['line1'] ?></p>

								<p><?= $general['line2'] ?></p>

								<p><?= $general['line3'] ?></p>

								<p><?= $general['line4'] ?></p>
							</div>
							<!-- /info-container -->
						</address>
					</div>
					<!-- /reservation-by-phone -->
				</div>
				<!-- /col-md-6 -->
			</div>
			<!-- /row -->
		</div>
		<!-- /container -->
	</section>
<?php
	get_footer();
