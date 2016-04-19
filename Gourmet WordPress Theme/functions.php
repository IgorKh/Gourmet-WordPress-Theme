<?php

	include_once( 'admin/KHT-back-end.php' );

	register_nav_menus( array( 'main_menu' => 'Main menu' ) );

	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'comment-form' ) );


	function gourmet_register_files() {
		wp_enqueue_script( 'gourmet_js', get_template_directory_uri() . '/js/empty.js' );
		wp_localize_script( 'gourmet_js', 'gourmet_ajax', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	}

	add_action( 'wp_enqueue_scripts', 'gourmet_register_files' );

	add_action( 'wp_ajax_gourmet_send', 'gourmet_send' );
	add_action( 'wp_ajax_nopriv_gourmet_send', 'gourmet_send' );

	add_action( 'wp_ajax_gourmet_reserve', 'gourmet_reserve' );
	add_action( 'wp_ajax_nopriv_gourmet_reserve', 'gourmet_reserve' );

	add_action( 'wp_ajax_gourmet_loop', 'gourmet_loop_ajax' );
		add_action( 'wp_ajax_nopriv_gourmet_loop', 'gourmet_loop_ajax' );

	function gourmet_loop_ajax(){
		define('WP_USE_THEMES', false);

		$page_number=isset($_POST['page_number'])&&is_numeric($_POST['page_number'])?intval($_POST['page_number']):0;
		gourmet_loop($page_number);

		wp_reset_query();
		wp_die();
	}


	function gourmet_send() {
		if ( $_SERVER["REQUEST_METHOD"] == "POST" ) {
			// Get the form fields and remove whitespace.
			if ( ! isset( $_POST['form'] ) ) {
				header( "HTTP/1.0 500 Internal Server Error" );
				echo 'No form data';
				wp_die();
			}

			$form    = $_POST['form'];
			$name    = strip_tags( trim( $form["name"] ) );
			$name    = str_replace( array( "\r", "\n" ), array( " ", " " ), $name );
			$email   = filter_var( trim( $form["email"] ), FILTER_SANITIZE_EMAIL );
			$message = trim( $form["message"] );

			// Check that data was sent to the mailer.
			if ( empty( $name ) OR empty( $message ) OR ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
				// Set a 400 (bad request) response code and exit.
				header( "HTTP/1.0 400 Bad Request" );
				echo "Oops! There was a problem with your submission. Please complete the form and try again.";
				exit;
			}

			// Set the recipient email address.
			$recipient = KHT_BackEnd::db_get( option_page::$prefix . '_' . 'general' )['feedback']['email'];
			// Set the email subject.
			$subject = "New email from $name";

			// Build the email content.
			$email_content = "Name: $name\n";
			$email_content .= "Email: $email\n\n";
			$email_content .= "Message:\n$message\n";

			// Build the email headers.
			$email_headers = "From: $name <$email>";

			// Send the email.
			if ( wp_mail( $recipient, $subject, $email_content, $email_headers ) ) {
				// Set a 200 (okay) response code.
				header( "HTTP/1.0 200 OK" );
				echo "Thank You! Your message has been sent.";
			} else {
				// Set a 500 (internal server error) response code.
				header( "HTTP/1.0 500 Internal Server Error" );
				echo "Oops! Something went wrong and we couldn't send your message.";
			}


		} else {
			// Not a POST request, set a 403 (forbidden) response code.
			header( "HTTP/1.0 403 Forbidden" );
			echo "There was a problem with your submission, please try again.";
		}
		wp_die();
	}

	function gourmet_reserve() {
		if ( $_SERVER["REQUEST_METHOD"] == "POST" ) {
			// Get the form fields and remove whitespace.
			if ( ! isset( $_POST['form'] ) ) {
				header( "HTTP/1.0 500 Internal Server Error" );
				echo 'No form data';
				wp_die();
			}

			$form = $_POST['form'];
			// Set the recipient email address.
			$recipient = KHT_BackEnd::db_get( option_page::$prefix . '_' . 'general' )['feedback']['email'];
			// Set the email subject.
			$subject = "New email from site:" . site_url();

			// Build the email content.
			$email_content = "";

			foreach ( $form as $field ) {
				$email_content .= '<p>' . $field . '</p>';
			}


			// Send the email.
			if ( wp_mail( $recipient, $subject, $email_content, 'Content-type: text/html' ) ) {
				// Set a 200 (okay) response code.
				header( "HTTP/1.0 200 OK" );
				echo "Thank You! Your message has been sent.";
			} else {
				// Set a 500 (internal server error) response code.
				header( "HTTP/1.0 500 Internal Server Error" );
				echo "Oops! Something went wrong and we couldn't send your message.";
			}


		} else {
			// Not a POST request, set a 403 (forbidden) response code.
			header( "HTTP/1.0 403 Forbidden" );
			echo "There was a problem with your submission, please try again.";
		}
		wp_die();
	}

	function gourmet_wp_title( $title, $sep ) {
		if ( is_feed() ) {
			return $title;
		}

		global $page, $paged;

		// Add the blog name
		$title .= get_bloginfo( 'name', 'display' );

		// Add the blog description for the home/front page.
		$site_description = get_bloginfo( 'description', 'display' );
		if ( $site_description && ( is_home() || is_front_page() ) ) {
			$title .= " $sep $site_description";
		}

		// Add a page number if necessary:
		if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() ) {
			$title .= " $sep " . sprintf( __( 'Page %s', '_s' ), max( $paged, $page ) );
		}

		return $title;
	}

	add_filter( 'wp_title', 'gourmet_wp_title', 10, 2 );

	function mytheme_comment( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		extract( $args, EXTR_SKIP );

		if ( 'div' == $args['style'] ) {
			$add_below = 'comment';
		} else {
			$add_below = 'div-comment';
		}
		?>

	<li <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ) ?> id="comment-<?php comment_ID() ?>">
		<div id="div-comment-<?php comment_ID() ?>" class="comment-body">
			<figure class="comment-author-avatar">
				<?php
					if ( $args['avatar_size'] != 0 ) {
						echo get_avatar( $comment, $args['avatar_size'] );
					}
				?>
			</figure>
			<div class="comment-contents">
				<div class="comment-header clearfix">
					<h4 class="comment-author"><?php printf( __( '%s <span class="says"> says</span>' ), get_comment_author_link() ); ?></h4>
					<time><?php printf( __( '%1$s at %2$s' ), get_comment_date(), get_comment_time() ); ?>
					</time>
					<?php comment_reply_link( array_merge( $args, array(
						'add_below'  => $add_below,
						'depth'      => $depth,
						'reply_text' => '<i class="fa fa-comment"></i>Reply',
						'max_depth'  => $args['max_depth']
					) ) ); ?>
				</div>
				<!-- /comment-header -->
				<div class="comment-text">
					<p><?php comment_text(); ?></p>
					<?php if ( $comment->comment_approved == '0' ) : ?>
						<br/>
						<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.' ); ?></em>
						<br/>
					<?php endif; ?>
				</div>
				<!-- /comment-text -->
			</div>
			<!-- /comment-contents -->
		</div>
	<?php
	}

	function gourmet_loop($page=false,$posts=false,$search=false,$author=false,$category=false) {

		$posts_per_page=$posts===false?get_option('posts_per_page'):$posts;
		$args = array(
			'post_type' => 'post',
			'posts_per_page'=>$posts_per_page
		);
		if ( $page !== false && is_numeric( intval( $page ) ) ) {

			$args['paged']=$page==0?1:($page+1);
		}
		if ( $author !== false ) {
			$args['author'] = $author;
		}
		if ( $category !== false ) {
			$args['cat'] = $category;
		}
		if ( $search !== false ) {
			$args['s'] = $search;
		}
		$query= new WP_Query($args);

		if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post();
			$link       = get_permalink();
			$categories = get_the_category();
			?>
			<article>
				<header>
					<ul class="category">
						<?php
							if ( $categories ) {
								$output = '';
								$count  = count( $categories );
								for ( $i = 0; $i < $count; $i ++ ) {
									$separator = isset( $categories[ $i + 1 ] ) ? ',' : '';
									$output .= '<li><a href="' . get_category_link( $categories[ $i ]->term_id ) . '" title="' . $categories[ $i ]->name . '">' . $categories[ $i ]->cat_name . '</a>' . $separator . '</li>';
								}
								echo trim( $output, $separator );
							}
						?>
					</ul>
					<h1><a href="<?= $link ?>"><?= get_the_title() ?></a></h1>

					<div class="post-meta">
						<span><time><?php the_time( get_option( 'date_format' ) ); ?></time></span>
						<span>by <a href="<?= get_author_posts_url( get_the_author_meta( 'ID' ) ) ?>"><?= get_the_author() ?></a></span>
						<span><a href="<?= $link ?>"><?= get_comments_number( '0' ) ?></a> comments</span>
					</div>
					<!-- /post-meta -->
					<?php
						if ( has_post_thumbnail() ) {
							?>
							<div class="post-image">
								<a href="<?= $link ?>">
									<img src="<?= wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' )[0] ?>" alt="<?= get_the_title() ?>">
								</a>
							</div>
						<?php
						}
					?>
				</header>
				<div class="post-contents">
					<?php the_content( '<br><a href="' . get_permalink( get_the_ID() ) . '" class="read-more-button">Continue Reading ...</a>' ); ?>
				</div>
				<!-- /post-contents -->
				<footer>
					<div class="socials-container">
						<ul>
							<li><a href="<?= $link ?>/#comment"><i class="fa fa-comment-o"></i></a>
							</li>
						</ul>
					</div>
					<!-- /socials-container -->
				</footer>
			</article>
		<?php endwhile;
		else : ?>
			<p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
		<?php endif;
	}


	//Build Option pages
	class option_page {
		private $root = array(
			'page_title'     => 'Template setup',
			'sub_page_title' => 'General settings',
			'menu_title'     => 'Template',
			'sub_menu_title' => 'General',
			'capability'     => 'manage_options',
			'menu_slug'      => 'gourmet',
			'function'       => 'general',
			'icon_url'       => '/img/config_icon.png',
			'position'       => 3,
		);
		private $all = array(
			'home'        => 'Home',
			'about'       => 'About',
			'blog'        => 'Blog',
			'menu'        => 'Menu',
			'reservation' => 'Reservation'
		);

		static $prefix = 'gourmet';
		private $backEnd = 'KHT_BackEnd';

		function __construct() {
			add_action( 'admin_menu', [ $this, 'register' ] );
		}

		function register() {
			$root = $this->root;
			$all  = $this->all;

			add_menu_page( $root['page_title'], $root['menu_title'], $root['capability'], $root['menu_slug'], [
				$this,
				$root['function']
			], get_template_directory_uri() . $root['icon_url'], $root['position'] );
			add_submenu_page( $root['menu_slug'], $root['sub_page_title'], $root['sub_menu_title'], $root['capability'], $root['menu_slug'] );

			foreach ( $all as $name => $page ) {
				add_submenu_page(
					$root['menu_slug'],
					$page,
					$page,
					$root['capability'],
					$root['menu_slug'] . '/' . $name,
					[ $this, $name ]
				);
			}
		}

		function general() {
			new $this->backEnd(
				array(
					'title'    => 'General settings',
					'general'  => array(
						'title'  => 'General',
						'fields' => array(
							'logo_top'    => array(
								'title' => 'Logo in header',
								'type'  => 'image',
								'size'  => 6
							),
							'logo_footer' => array(
								'title' => 'Logo in footer',
								'type'  => 'image',
								'size'  => 6
							),
							'copyright'   => array(
								'title' => 'Copyright',
								'type'  => 'textarea',
							),
						),
					),
					'social'   => array(
						'title'  => 'Social',
						'fields' => array(
							'facebook'  => array(
								'title'  => 'Facebook',
								'fields' => array(
									'url'    => array(
										'title' => 'Full url',
										'size'  => 10
									),
									'enable' => array(
										'title' => 'Enable?',
										'type'  => 'checkbox',
										'size'  => 2
									)
								)
							),
							'twitter'   => array(
								'title'  => 'Twitter',
								'fields' => array(
									'url'    => array(
										'title' => 'Full url',
										'size'  => 10
									),
									'enable' => array(
										'title' => 'Enable?',
										'type'  => 'checkbox',
										'size'  => 2
									)
								)
							),
							'skype'     => array(
								'title'  => 'Skype',
								'fields' => array(
									'url'    => array(
										'title' => 'Full url',
										'size'  => 10
									),
									'enable' => array(
										'title' => 'Enable?',
										'type'  => 'checkbox',
										'size'  => 2
									)
								)
							),
							'google'    => array(
								'title'  => 'Google+',
								'fields' => array(
									'url'    => array(
										'title' => 'Full url',
										'size'  => 10
									),
									'enable' => array(
										'title' => 'Enable?',
										'type'  => 'checkbox',
										'size'  => 2
									)
								)
							),
							'linkedin'  => array(
								'title'  => 'LinkedIn',
								'fields' => array(
									'url'    => array(
										'title' => 'Full url',
										'size'  => 10
									),
									'enable' => array(
										'title' => 'Enable?',
										'type'  => 'checkbox',
										'size'  => 2
									)
								)
							),
							'instagram' => array(
								'title'  => 'Instagram',
								'fields' => array(
									'url'    => array(
										'title' => 'Full url',
										'size'  => 10
									),
									'enable' => array(
										'title' => 'Enable?',
										'type'  => 'checkbox',
										'size'  => 2
									)
								)
							),
						),
					),
					'clients'  => array(
						'title'  => 'Clients gallery',
						'fields' => array(
							'image' => array(
								'title'              => 'Client #',
								'max'                => 100,
								'text_add_button'    => 'Add image',
								'text_remove_button' => 'Remove image',
								'type'               => 'image',
								'size'               => 4
							),
						),
					),
					'address'  => array(
						'title'  => 'Address setup (for front side)',
						'fields' => array(
							'address' => array(
								'title'  => 'Address',
								'fields' => array(
									'line1' => array(
										'title' => 'line 1',
										'size'  => 3
									),
									'line2' => array(
										'title' => 'line 2',
										'size'  => 3
									),
									'line3' => array(
										'title' => 'line 3',
										'size'  => 3
									),
									'line4' => array(
										'title' => 'line 4',
										'size'  => 3
									),
								)
							),
							'phone'   => array(
								'title' => 'Phone',
								'size'  => 4
							),
							'fax'     => array(
								'title' => 'Fax',
								'size'  => 4
							),
							'email'   => array(
								'title' => 'Email',
								'size'  => 4
							),
						)
					),
					'map'      => array(
						'title'  => 'Google map setup',
						'fields' => array(
							'latitude'  => array(
								'title' => 'Latitude',
								'size'  => 4
							),
							'longitude' => array(
								'title' => 'Longitude',
								'size'  => 4
							),
							'zoom'      => array(
								'title' => 'Map zoom',
								'size'  => 4
							),
						)
					),
					'feedback' => array(
						'title'  => 'Feedback setup',
						'fields' => array(
							'title'       => array(
								'title'  => 'Title of form',
								'fields' => array(
									'orange' => array(
										'title' => 'Orange',
										'size'  => 6
									),
									'black'  => array(
										'title' => 'Black',
										'size'  => 6
									)
								)
							),
							'placeholder' => array(
								'title'  => 'Placeholders',
								'fields' => array(
									'name'    => array(
										'title' => 'Name',
										'size'  => 4
									),
									'email'   => array(
										'title' => 'Email',
										'size'  => 4
									),
									'message' => array(
										'title' => 'Message',
										'size'  => 4
									)
								),
							),
							'email'       => array(
								'title' => 'Email to send to',
							),
							'submit'      => array(
								'title' => 'Send button label',
							),
						),
					),
				),
				self::$prefix . '_' . 'general',
				get_template_directory_uri() . '/'
			);
		}

		function home() {
			new $this->backEnd(
				array(
					'title'    => 'Home page',
					'chefs'    => array(
						'title'  => 'About Chefs',
						'fields' => array(
							'title'  => array(
								'title'  => 'Title',
								'fields' => array(
									'orange' => array(
										'title' => 'Orange',
										'size'  => 6
									),
									'black'  => array(
										'title' => 'Black',
										'size'  => 6
									)
								),
							),
							'member' => array(
								'title'              => 'Member #',
								'max'                => 15,
								'text_add_button'    => 'Add member',
								'text_remove_button' => 'Remove member',
								'fields'             => array(
									'thumb'           => array(
										'title' => 'Thumb',
										'type'  => 'image',
										'size'  => 6
									),
									'image'           => array(
										'title' => 'Image',
										'type'  => 'image',
										'size'  => 6
									),
									'title'           => array(
										'title' => 'Title',
										'size'  => 6
									),
									'post'            => array(
										'title' => 'Post',
										'size'  => 6
									),
									'bio_short'       => array(
										'title' => 'Bio short',
										'type'  => 'textarea',
										'size'  => 6
									),
									'bio_description' => array(
										'title' => 'Bio description',
										'type'  => 'textarea',
										'size'  => 6
									),
								),
							),
						),
					),
					'gallery'  => array(
						'title'  => 'Gallery',
						'fields' => array(
							'title'  => array(
								'title'  => 'Title',
								'fields' => array(
									'orange'      => array(
										'title' => 'Orange',
										'size'  => 4
									),
									'black'       => array(
										'title' => 'Black',
										'size'  => 4
									),
									'description' => array(
										'title' => 'Description',
										'size'  => 4,
										'type'  => 'textarea'
									),
								)
							),
							'filter' => array(
								'title'              => 'Filter #',
								'max'                => 10,
								'text_add_button'    => 'Add filter',
								'text_remove_button' => 'Remove filter',
								'size'               => 3
							),
							'item'   => array(
								'title'              => 'Item #',
								'max'                => 100,
								'text_add_button'    => 'Add Item',
								'text_remove_button' => 'Remove Item',
								'fields'             => array(
									'image'  => array(
										'title' => 'Image',
										'type'  => 'image',
										'size'  => 3
									),
									'filter' => array(
										'title' => 'Filters separated by a space from the list above',
										'type'  => 'textarea',
										'size'  => 9
									)
								),
							)
						)
					),
					'services' => array(
						'title'  => 'Services (other details on Menu page)',
						'fields' => array(
							'title'       => array(
								'title'  => 'Title',
								'fields' => array(
									'orange' => array(
										'title' => 'Orange',
										'size'  => 6
									),
									'black'  => array(
										'title' => 'Black',
										'size'  => 6
									),
								)
							),
							'description' => array(
								'title' => 'Description',
								'type'  => 'textarea'
							),
							'background'  => array(
								'title' => 'Background image',
								'type'  => 'image',
							),
							'link'        => array(
								'title' => 'Link to page',
								'type'  => 'wpPages_dropDown',
								'size'  => 2
							),
							'label'       => array(
								'title' => 'Button label',
								'size'  => 10
							)
						)
					)
				),
				self::$prefix . '_' . 'home',
				get_template_directory_uri() . '/'
			);
		}

		function blog() {
			new $this->backEnd(
				array(
					'title' => 'Blog',
					'head'  => array(
						'title'  => 'Header images',
						'fields' => array(
							'background' => array(
								'title' => 'Background image',
								'type'  => 'image',
								'size'  => 4
							),
							'top'        => array(
								'title' => 'Top image',
								'type'  => 'image',
								'size'  => 4
							),
							'bottom'     => array(
								'title' => 'Bottom image',
								'type'  => 'image',
								'size'  => 4
							),
						),
					),
				),
				self::$prefix . '_' . 'blog',
				get_template_directory_uri() . '/'
			);
		}

		function about() {
			new $this->backEnd(
				array(
					'title'   => 'About us page',
					'head'    => array(
						'title'  => 'Header images',
						'fields' => array(
							'background' => array(
								'title' => 'Background image',
								'type'  => 'image',
								'size'  => 4
							),
							'top'        => array(
								'title' => 'Top image',
								'type'  => 'image',
								'size'  => 4
							),
							'bottom'     => array(
								'title' => 'Bottom image',
								'type'  => 'image',
								'size'  => 4
							),
						),
					),
					'text'    => array(
						'title'  => 'Description area',
						'fields' => array(
							'title' => array(
								'title'  => 'Title',
								'fields' => array(
									'orange'      => array(
										'title' => 'Orange',
										'size'  => 6
									),
									'white'       => array(
										'title' => 'White',
										'size'  => 6
									),
									'description' => array(
										'title' => 'description',
										'type'  => 'textarea'
									)
								)
							),
							'left'  => array(
								'title'  => 'Left block',
								'fields' => array(
									'title' => array(
										'title' => 'First letter',
										'size'  => 1
									),
									'text'  => array(
										'title' => 'Text',
										'type'  => 'textarea',
										'size'  => 11
									)
								)
							),
							'right' => array(
								'title'  => 'Right block',
								'fields' => array(
									'title' => array(
										'title' => 'First letter',
										'size'  => 1
									),
									'text'  => array(
										'title' => 'Text',
										'type'  => 'textarea',
										'size'  => 11
									)
								)
							),
						)
					),
					'gallery' => array(
						'title'  => 'Gallery',
						'fields' => array(
							'image' => array(
								'title'              => 'Image #',
								'max'                => 100,
								'text_add_button'    => 'Add image',
								'text_remove_button' => 'Remove image',
								'fields'             => array(
									'image'       => array(
										'type' => 'image',
									),
									'title'       => array(
										'title' => 'Title on hover',
										'size'  => 6
									),
									'description' => array(
										'title' => 'Description on hover',
										'size'  => 6
									),
								)
							),
						),
					),
					'promo'   => array(
						'title'  => 'Promo text',
						'fields' => array(
							'orange' => array(
								'title' => 'Black',
								'size'  => 6
							),
							'white'  => array(
								'title' => 'Orange',
								'size'  => 6
							),
						)
					),
					'blog'    => array(
						'title'  => 'Blog',
						'fields' => array(
							'orange'      => array(
								'title' => 'Orange',
								'size'  => 6
							),
							'black'       => array(
								'title' => 'Black',
								'size'  => 6
							),
							'description' => array(
								'title' => 'Description',
								'type'  => 'textarea'
							)
						),
					),
					'review'  => array(
						'title'  => 'Review',
						'fields' => array(
							'title'  => array(
								'title'  => 'Title',
								'fields' => array(
									'orange' => array(
										'title' => 'Orange',
										'size'  => 6
									),
									'white'  => array(
										'title' => 'White',
										'size'  => 6
									),
								)
							),
							'review' => array(
								'title'              => 'Review #',
								'max'                => 10,
								'text_add_button'    => 'Add review',
								'text_remove_button' => 'Remove review',
								'fields'             => array(
									'text'        => array(
										'title' => 'Text',
										'type'  => 'textarea'
									),
									'name'        => array(
										'title' => 'Name',
										'size'  => 6
									),
									'description' => array(
										'title' => 'Description',
										'size'  => 6
									),
								)
							)
						)
					),
				),
				self::$prefix . '_' . 'about',
				get_template_directory_uri() . '/'
			);
		}

		function menu() {
			new $this->backEnd(
				array(
					'title'    => 'Menu page',
					'head'     => array(
						'title'  => 'Header images',
						'fields' => array(
							'background' => array(
								'title' => 'Background image',
								'type'  => 'image',
								'size'  => 4
							),
							'top'        => array(
								'title' => 'Top image',
								'type'  => 'image',
								'size'  => 4
							),
							'bottom'     => array(
								'title' => 'Bottom image',
								'type'  => 'image',
								'size'  => 4
							),
						),
					),
					'services' => array(
						'title'              => 'Service #',
						'max'                => 10,
						'text_add_button'    => 'Add service',
						'text_remove_button' => 'Remove service',
						'fields'             => array(
							'title'             => array(
								'title'  => 'Title',
								'fields' => array(
									'orange'      => array(
										'title' => 'Orange',
										'size'  => 6
									),
									'black'       => array(
										'title' => 'Black',
										'size'  => 6
									),
									'description' => array(
										'title' => 'Description',
										'type'  => 'textarea'
									),
								)
							),
							'title_front'       => array(
								'title' => 'Title (for front page)',
							),
							'thumb'             => array(
								'title' => 'Thumb (for front page)',
								'type'  => 'image',
							),
							'description_front' => array(
								'title' => 'Description (for front page)',
								'type'  => 'textarea',
							),
							'food'              => array(
								'title'              => 'Food #',
								'max'                => 100,
								'text_add_button'    => 'Add food',
								'text_remove_button' => 'Remove food',
								'fields'             => array(
									'title'       => array(
										'title' => 'Title',
										'size'  => 5
									),
									'description' => array(
										'title' => 'Description',
										'size'  => 5
									),
									'price'       => array(
										'title' => 'Price',
										'size'  => 2
									)
								)
							),
						)
					),
					'promo'    => array(
						'title'  => 'Promo section',
						'fields' => array(
							'background' => array(
								'title' => 'Background image',
								'type'  => 'image',
							),
							'title'      => array(
								'title' => 'Promo text',
								'type'  => 'textarea',
								'size'  => 4
							),
							'link'       => array(
								'title' => 'Link to page',
								'type'  => 'wpPages_dropDown',
								'size'  => 4
							),
							'label'      => array(
								'title' => 'Button label',
								'size'  => 4
							)
						)
					)
				),
				self::$prefix . '_' . 'menu',
				get_template_directory_uri() . '/'
			);
		}

		function reservation() {
			new $this->backEnd(
				array(
					'title'       => 'Reservation page',
					'head'        => array(
						'title'  => 'Header images',
						'fields' => array(
							'background' => array(
								'title' => 'Background image',
								'type'  => 'image',
								'size'  => 4
							),
							'top'        => array(
								'title' => 'Top image',
								'type'  => 'image',
								'size'  => 4
							),
							'bottom'     => array(
								'title' => 'Bottom image',
								'type'  => 'image',
								'size'  => 4
							),
						),
					),
					'text'        => array(
						'title'  => 'Description area',
						'fields' => array(
							'title'        => array(
								'title'  => 'Title',
								'fields' => array(
									'orange' => array(
										'title' => 'Orange',
										'size'  => 6
									),
									'white'  => array(
										'title' => 'White',
										'size'  => 6
									),
								)
							),
							'top_left'     => array(
								'title'  => 'Top left block',
								'fields' => array(
									'title' => array(
										'title' => 'Title',
										'type'  => 'textarea',
										'size'  => 6
									),
									'text'  => array(
										'title' => 'Text',
										'type'  => 'textarea',
										'size'  => 6
									)
								)
							),
							'top_right'    => array(
								'title'  => 'Top right block',
								'fields' => array(
									'title' => array(
										'title' => 'Title',
										'type'  => 'textarea',
										'size'  => 6
									),
									'text'  => array(
										'title' => 'Text',
										'type'  => 'textarea',
										'size'  => 6
									)
								)
							),
							'bottom_left'  => array(
								'title'  => 'Bottom left block',
								'size'   => 6,
								'fields' => array(
									'text' => array(
										'title' => 'Text',
										'type'  => 'textarea',
									)
								)
							),
							'bottom_right' => array(
								'title'  => 'Bottom right block',
								'size'   => 6,
								'fields' => array(
									'text' => array(
										'title' => 'Text',
										'type'  => 'textarea',
									)
								)
							)
						)
					),
					'online_form' => array(
						'title'  => 'Online form',
						'fields' => array(
							'title'       => array(
								'title'  => 'Title',
								'fields' => array(
									'orange' => array(
										'title' => 'Orange',
										'size'  => 6
									),
									'black'  => array(
										'title' => 'Black',
										'size'  => 6
									),
								)
							),
							'placeholder' => array(
								'title'  => 'Placeholders',
								'fields' => array(
									'first_name' => array(
										'title' => 'First name',
										'size'  => 3
									),
									'last_name'  => array(
										'title' => 'Last name',
										'size'  => 3
									),
									'phone'      => array(
										'title' => 'Phone',
										'size'  => 2
									),
									'email'      => array(
										'title' => 'Email',
										'size'  => 2
									),
									'guests'     => array(
										'title' => 'Guests',
										'size'  => 2
									),
									'date'       => array(
										'title'  => 'Date of reservation',
										'fields' => array(
											'title'       => array(
												'title' => 'Title',
												'size'  => 4
											),
											'description' => array(
												'title' => 'Title description',
												'size'  => 5
											),
											'day'         => array(
												'title' => 'Day',
												'size'  => 1
											),
											'month'       => array(
												'title' => 'Month',
												'size'  => 1
											),
											'year'        => array(
												'title' => 'Year',
												'size'  => 1
											)
										)
									),
									'time'       => array(
										'title'  => 'Time of reservation',
										'fields' => array(
											'title'  => array(
												'title' => 'Title',
												'size'  => 4
											),
											'hour'   => array(
												'title' => 'Hour',
												'size'  => 2
											),
											'minute' => array(
												'title' => 'Minute',
												'size'  => 2
											),
											'am'     => array(
												'title' => 'AM',
												'size'  => 2
											),
											'pm'     => array(
												'title' => 'PM',
												'size'  => 2
											)
										)
									),
									'occasion'   => array(
										'title' => 'Occasion',
										'size'  => 4
									),
									'comment'    => array(
										'title' => 'Comment',
										'size'  => 4
									),
									'label'      => array(
										'title' => 'Button label',
										'size'  => 4
									)
								)
							),
						),
					),
					'phone'       => array(
						'title'       => 'Phone',
						'fields'      => array(
							'title'  => array(
								'title'  => 'Title',
								'fields' => array(
									'orange' => array(
										'title' => 'Orange',
										'size'  => 6
									),
									'black'  => array(
										'title' => 'Black',
										'size'  => 6
									),
								)
							),
							'number' => array(
								'title'  => 'Number',
								'fields' => array(
									'orange' => array(
										'title' => 'Code',
										'size'  => 6
									),
									'black'  => array(
										'title' => 'Number',
										'size'  => 6
									),
								)
							),
							'description' => array(
								'title' => 'Description',
								'type'  => 'textarea'
							)
						),
					),
				),
				self::$prefix . '_' . 'reservation',
				get_template_directory_uri() . '/'
			);
		}
	}

	new option_page();
