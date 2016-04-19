<?php


	$clients=KHT_BackEnd::db_get(option_page::$prefix.'_'.'general')['clients']['image'];

?>
<section class="clients white-rock-bg">
	<div class="container">
		<div class="clients-carousel grayscale-image row">
			<?php
			foreach($clients as $client){
				?>
				<div class="client-logo-container wow fadeInUp">
					<figure class="client-logo">
						<img src="<?= $client ?>" alt="<?= get_bloginfo('name') ?>">
					</figure>
				</div>
			<?php
			}
			?>
		</div>
		<!-- /row -->
	</div>
	<!-- /contianer -->
</section>
