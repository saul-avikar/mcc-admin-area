<?php
/**
 * Author: Saul Boyd (avikar.io)
 * License: MIT (https://opensource.org/licenses/MIT)
 */

class MCCAdminArea {
	public static function init() {
		// Hooks for directing the post requests
		add_filter( 'generate_rewrite_rules', [get_called_class(), 'rewriteRules'] );
		add_filter( 'query_vars', [get_called_class(), 'queryVars'] );
		add_action( 'template_redirect', [get_called_class(), 'templateRedirect'] );

		// Prevent the failed login redirect
		add_action( 'wp_login_failed', [get_called_class(), 'login_fail'] );
		add_action( 'authenticate', [get_called_class(), 'login_empty'] );

		// Scripts and css
		add_action('wp_enqueue_scripts', [get_called_class(), 'setupScripts']);

		// Menu
		add_filter( 'wp_nav_menu_items', [get_called_class(), 'menu'] );

		// Admin options
		if ( is_admin() ) {
			add_action( 'admin_menu', [get_called_class(), 'adminMenu'] );
			add_action( 'admin_init', [get_called_class(), 'registerSettings'] );
		}
	}

	// Scripts and css__________________________________________________________

	public static function setupScripts() {
		$asset_url = plugin_dir_url(__FILE__) . 'assets/';

		wp_enqueue_script('mcc_admin_area', $asset_url . 'mcc-admin-area.js', ['jquery']);
		wp_enqueue_style('mcc_admin_area', $asset_url . 'mcc-admin-area.css');
	}

	// Login Fail ______________________________________________________________
	public static function login_fail( $username ) {
		$referrer = $_SERVER['HTTP_REFERER'];

		// if there's a valid referrer, and it's not the default log-in screen
		if ( !empty($referrer) && !strstr($referrer,'wp-login') && !strstr($referrer,'wp-admin') ) {
			if ( !strstr($referrer,'?login=failed') ) { // make sure we don’t append twice
                wp_redirect( $referrer . '?login=failed' ); // append some information (login=failed) to the URL for the theme to use
            } else {
                wp_redirect( $referrer );
            }
			exit;
		}
	}

	public static function login_empty( $username ) {
		$referrer = $_SERVER['HTTP_REFERER'];
        if ( strstr( $referrer, get_option( 'mccadminarea_loginPageName' ) ) && $user==null ) { // mylogin is the name of the loginpage.
            if ( !strstr($referrer,'?login=empty') ) { // prevent appending twice
                wp_redirect( $referrer . '?login=empty' );
            } else {
                wp_redirect( $referrer );
            }
        }
	}

	// Menu_____________________________________________________________________

	public static function menu ( $items ) {
		global $current_user;
		get_currentuserinfo();

		$dom = new DOMDocument;
		$dom->loadHTML($items);
		$lis = $dom->getElementsByTagName('li');
		$items_to_remove = [];

		foreach( $lis as $li ) {
			if ( is_user_logged_in() ) {
				if (
					$li->textContent === get_option( 'mccadminarea_loginLabel' ) ||
					$li->textContent === (
						user_can( $current_user, 'mccadminarea_teacher' ) ?
							get_option( 'mccadminarea_studentpostLabel' ) :
							get_option( 'mccadminarea_teacherpostLabel' )
					)
				) {
					$items_to_remove[] = $li;
				}
			} else {
				if (
					$li->textContent === get_option( 'mccadminarea_teacherpostLabel' ) ||
					$li->textContent === get_option( 'mccadminarea_studentpostLabel' ) ||
					$li->textContent === get_option( 'mccadminarea_logoutLabel' )
				) {
					$items_to_remove[] = $li;
				}
			}
		};

		foreach ( $items_to_remove as $item_to_remove ) {
			$item_to_remove->parentNode->removeChild( $item_to_remove );
		}

		$items = $dom->saveXML();

		// DELETE LATER---------------------------------------------------------
		if ( is_user_logged_in() ) {
			global $current_user;
			get_currentuserinfo();

			// Hide the admin bar from students and teachers
			if (
				user_can( $current_user, 'mccadminarea_teacher' ) ||
				user_can( $current_user, 'mccadminarea_student')
			){
				// Disable the admin bar
				show_admin_bar( false );
				// Reset the styles inforced by it
				echo "<style>
					.site-navigation-fixed.navigation-top {
						top: 0 !important;
					}

					html {
						margin: 0 !important;
					}
				</style>";
			}
		}
		// ---------------------------------------------------------------------

		return $items;
	}

	// Settings_________________________________________________________________

	// Add an option to the settings menu for the blockstack options page
	public static function adminMenu(){
		// add_options_page( 'page title', 'menu title', capability, unique_slug, output_function );
		add_options_page(
			'MCCAdminArea Options',
			__('MCCAdminArea', 'mccadminarea'),
			'manage_options',
			__FILE__,
			[get_called_class(), 'optionsForm']
		);
	}

	public static function optionsForm() {
		include( plugin_dir_path( __FILE__ ) . 'templates/options.php' );
	}

	public static function registerSettings() {
		// add_option( 'option_name', 'default value' );
		add_option( 'mccadminarea_loginLabel', 'Login' );
		add_option( 'mccadminarea_loginPageName', 'Login' );
		add_option( 'mccadminarea_logoutLabel', 'Logout' );
		add_option( 'mccadminarea_teacherpostLabel', 'Admin' );
		add_option( 'mccadminarea_studentpostLabel', 'Kids Zone' );
		add_option( 'mccadminarea_studentloginredirect', '/' );
		add_option( 'mccadminarea_teacherloginredirect', '/' );

		register_setting( 'mccadminarea_settings', 'mccadminarea_loginLabel' );
		register_setting( 'mccadminarea_settings', 'mccadminarea_loginPageName' );
		register_setting( 'mccadminarea_settings', 'mccadminarea_logoutLabel' );
		register_setting( 'mccadminarea_settings', 'mccadminarea_teacherpostLabel' );
		register_setting( 'mccadminarea_settings', 'mccadminarea_studentpostLabel' );
		register_setting( 'mccadminarea_settings', 'mccadminarea_studentloginredirect' );
		register_setting( 'mccadminarea_settings', 'mccadminarea_teacherloginredirect' );
	}

	// Redirect_________________________________________________________________
	public static function queryVars( $query_vars ) {
		$query_vars[] = 'mccadminarea_post';

		return $query_vars;
	}


	public static function rewriteRules( $wp_rewrite ) {
		$feed_rules = [
			'mccadminarea_post/?$' => 'index.php?mccadminarea_post=true'
		];

		$wp_rewrite->rules = $feed_rules + $wp_rewrite->rules;
		return $wp_rewrite->rules;
	}

	// Specify template
	public static function templateRedirect() {
		$post = get_query_var( 'mccadminarea_post' );

		global $current_user;
		get_currentuserinfo();

		if ($post) {
			include plugin_dir_path( __FILE__ ) . 'includes/post-creation.php';
			die;
		}
	}

	// Activation an Deactivation_______________________________________________
	public static function activated() {
		// Flush rewrites
		add_filter( 'generate_rewrite_rules', ['MCCAdminArea', 'rewriteRules'] );
		flush_rewrite_rules();

		// Add new roles
		add_role( 'mccadminarea_teacher', __('Teacher'), [
			'read' => true,
			'edit_posts' => true,
			'edit_others_posts' => true,
			'publish_posts' => true
		]);

		add_role( 'mccadminarea_student', __('Student'), [
			'read' => true,
			'edit_posts' => true
		]);

		// Add the new categories
		$cat = wp_create_category( 'School Posts' );
		wp_create_category( 'Kids Zone', $cat );
	}

	public static function deactivated() {
		// Flush rewrites
		flush_rewrite_rules();

		// Remove roles
		if( get_role( 'mccadminarea_teacher' ) ){
			remove_role( 'mccadminarea_teacher' );
		}

		if( get_role( 'mccadminarea_student' ) ){
			remove_role( 'mccadminarea_student' );
		}

		self::removeCat( 'Kids Zone' );
		self::removeCat( 'School Posts' );
	}

	// Helper functions_________________________________________________________
	private static function removeCat( $cat_name ) {
		$term = get_term_by( 'name', $cat_name, 'category' );
		$category = $term->term_id;

		if ( $category ) {
			wp_delete_category( $category );
		}
	}
}
