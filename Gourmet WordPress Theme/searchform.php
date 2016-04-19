<?php

?>
<form role="search" method="get" id="searchform" class="searchform search-form" action="<?= get_permalink( get_page_by_path( 'blog' ) ) ?>">
	<input type="search" name="s" id="search" placeholder="Search Blog" value="<?= isset($_GET['s']) ?sanitize_text_field($_GET['s']):'' ?>">
	<button type="submit"><i class="fa fa-search"></i></button>
</form>
