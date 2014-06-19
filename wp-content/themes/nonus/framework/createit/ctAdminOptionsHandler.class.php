<?php
/**
 * Admin options handler
 * @author alex
 */

class ctAdminOptionsHandler {

	/**
	 * Creates admin handler
	 */
	public function __construct() {
	}

	/**
	 * Inits events
	 */

	public function init() {
		add_action('login_head', array($this, 'handleLoginLogoOption'));

		add_filter('pre_option_page_on_front', array($this, 'handlePreOptionPageOnFront'));
		add_filter('pre_option_show_on_front', array($this, 'handlePreOptionShowOnFront'));
		add_filter('pre_option_page_for_posts', array($this, 'handlePreOptionPageForPosts'));
		add_filter('pre_option_posts_per_page', array($this, 'handlePreOptionPostsPerPage'));

	}

	/**
	 * Handles login logo options
	 */

	public static function handleLoginLogoOption() {
		if ($logo = esc_url(ct_get_option('general_login_logo'))) {

			echo '<style type="text/css">';
			echo  '.login h1 a { background: url(' . $logo . ') center center no-repeat; }';
			echo '</style>';
		}
	}

	/**
	 * Handles main page setting
	 * @see Settings -> Reading -> Static page -> Front page
	 */

	public static function handlePreOptionPageOnFront() {
		$value = ct_get_option('pages_home_page');

		//WPML
		if (function_exists('icl_object_id')) {
			$value = icl_object_id($value, 'page', true);
		}

		return $value;
	}

	/**
	 * Handles main page setting
	 * @see Settings -> Reading -> Static page -> Posts page
	 */

	public static function handlePreOptionPageForPosts($v) {
		$value = ct_get_option('posts_index_page');

		//WPML
		if (function_exists('icl_object_id')) {
			$value = icl_object_id($value, 'page', true);
		}

		return $value != '' ? $value : $v;
	}


	/**
	 * Handles main page setting
	 * @see Settings -> Reading -> Static page
	 */

	public static function handlePreOptionShowOnFront() {
		$page_on_front = absint(ct_get_option('pages_home_page'));
		$page_for_posts = absint(ct_get_option('posts_index_page'));

		if (!$page_on_front && !$page_for_posts) {
			return 'posts';
		}

		if ($page_on_front === $page_for_posts) {
			return 'posts';
		}

		return 'page';
	}

	/**
	 * @param $v
	 * @return int
	 */
	public static function handlePreOptionPostsPerPage($v) {
		$pp = absint(ct_get_option('posts_index_per_page'));

		if (-1 === $pp || $pp > 0) {
			return $pp;
		} else {
			return $v;
		}
	}


}