<?php
require_once 'ctTypeBase.class.php';
/**
 * FAQ type handler
 * @author hc
 */

class ctFaqTypeBase extends ctTypeBase {

	/**
	 * Slug option name
	 */

	const OPTION_SLUG = 'faq_index_slug';

	/**
	 * Initializes events
	 * @return mixed|void
	 */

	public function init() {
		add_action('template_redirect', array($this, 'faqContextFixer'));

		$this->registerType();
		$this->registerTaxonomies();

		/** @var $NHP_Options NHP_Options */
		global $NHP_Options;
		//add options listener for license
		add_action('nhp-opts-options-validate-' . $NHP_Options->args['opt_name'], array($this, 'handleSlugOptionSaved'));
	}


	/**
	 * Fixes proper menu state
	 */

	public function faqContextFixer() {
		if (get_query_var('post_type') == 'faq') {
			global $wp_query;
			$wp_query->is_home = false;
		}
		if (get_query_var('taxonomy') == 'faq_category') {
			global $wp_query;
			$wp_query->is_404 = true;
			$wp_query->is_tax = false;
			$wp_query->is_archive = false;
		}
	}

	/**
	 * Register type
	 */

	protected function registerType() {
		$typeData = $this->callFilter('pre_register_type', array(
			'labels' => array(
				'name' => _x('Faq items', 'post type general name', 'ct_theme'),
				'singular_name' => _x('Faq Item', 'post type singular name', 'ct_theme'),
				'add_new' => _x('Add New', 'faq', 'ct_theme'),
				'add_new_item' => __('Add New Faq Item', 'ct_theme'),
				'edit_item' => __('Edit Faq Item', 'ct_theme'),
				'new_item' => __('New Faq Item', 'ct_theme'),
				'view_item' => __('View Faq Item', 'ct_theme'),
				'search_items' => __('Search Faq Items', 'ct_theme'),
				'not_found' => __('No faq item found', 'ct_theme'),
				'not_found_in_trash' => __('No faq items found in Trash', 'ct_theme'),
				'parent_item_colon' => '',
				'menu_name' => __('FAQ items', 'ct_theme'),
			),
			'singular_label' => __('faq', 'ct_theme'),
			'public' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'show_ui' => true,
			'show_in_menu' => true,
			//'menu_position' => 20,
			'capability_type' => 'post',
			'hierarchical' => false,
			'supports' => array('title', 'editor', 'page-attributes'),
			'has_archive' => false,
			'rewrite' => array('slug' => $this->getPermalinkSlug(), 'with_front' => true, 'pages' => true, 'feeds' => false),
			'query_var' => false,
			'can_export' => true,
			'show_in_nav_menus' => true,
			'taxonomies' => array()
		));

		register_post_type('faq', $typeData);
		$this->callHook('post_register_type');
	}

	/**
	 * Returns permalink slug
	 * @return string
	 */

	protected function getPermalinkSlug() {
		// Rewriting Permalink Slug
		$permalink_slug = ct_get_option('faq_index_slug', 'faq');
		if (empty($permalink_slug)) {
			$permalink_slug = 'faq';
		}

		return $permalink_slug;
	}

	/**
	 * Gets hook name
	 * @return string
	 */
	protected function getHookBaseName() {
		return 'ct_faq';
	}

	/**
	 * Creates taxonomies
	 */

	protected function registerTaxonomies() {
		$data = $this->callFilter('pre_register_taxonomies', array(
			'hierarchical' => true,
			'labels' => array(
				'name' => _x('Faq Categories', 'taxonomy general name', 'ct_theme'),
				'singular_name' => _x('Faq Category', 'taxonomy singular name', 'ct_theme'),
				'search_items' => __('Search Categories', 'ct_theme'),
				'popular_items' => __('Popular Categories', 'ct_theme'),
				'all_items' => __('All Categories', 'ct_theme'),
				'parent_item' => null,
				'parent_item_colon' => null,
				'edit_item' => __('Edit Faq Category', 'ct_theme'),
				'update_item' => __('Update Faq Category', 'ct_theme'),
				'add_new_item' => __('Add New Faq Category', 'ct_theme'),
				'new_item_name' => __('New Faq Category Name', 'ct_theme'),
				'separate_items_with_commas' => __('Separate Faq category with commas', 'ct_theme'),
				'add_or_remove_items' => __('Add or remove faq category', 'ct_theme'),
				'choose_from_most_used' => __('Choose from the most used faq category', 'ct_theme'),
				'menu_name' => __('Categories', 'ct_theme'),
			),
			'public' => false,
			'show_in_nav_menus' => false,
			'show_ui' => true,
			'show_tagcloud' => false,
			'query_var' => false,
			'rewrite' => false,

		));
		register_taxonomy('faq_category', 'faq', $data);
		$this->callHook('post_register_taxonomies');
	}

	public function saveDetails() {
		global $post;

		$fields = array();
		foreach ($fields as $field) {
			if (isset($_POST[$field])) {
				update_post_meta($post->ID, $field, $_POST[$field]);
			}
		}
	}

	/**
	 * Handles rebuild
	 */

	public function handleSlugOptionSaved($newValues) {
		$currentSlug = $this->getPermalinkSlug();
		//rebuild rewrite if new slug
		if (isset($newValues[self::OPTION_SLUG]) && ($currentSlug != $newValues[self::OPTION_SLUG])) {
			$this->callHook('pre_slug_option_saved', array('current_slug' => $currentSlug, 'new_slug' => $newValues[self::OPTION_SLUG]));

			//clean rewrite to refresh it
			delete_option('rewrite_rules');
		}
	}
}
