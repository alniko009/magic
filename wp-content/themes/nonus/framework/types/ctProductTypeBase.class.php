<?php
require_once 'ctTypeBase.class.php';
/**
 * Product type handler
 * @author hc
 */

class ctProductTypeBase extends ctTypeBase {

	/**
	 * Slug option name
	 */

	const OPTION_SLUG = 'product_index_slug';

	/**
	 * Initializes events
	 * @return mixed|void
	 */

	public function init() {
		add_action('template_redirect', array($this, 'productContextFixer'));

		$this->registerType();
		$this->registerTaxonomies();

		add_action("admin_init", array($this, "addMetaBox"));

		/** @var $NHP_Options NHP_Options */
		global $NHP_Options;
		//add options listener for license
		add_action('nhp-opts-options-validate-' . $NHP_Options->args['opt_name'], array($this, 'handleSlugOptionSaved'));
	}

	/**
	 * Adds meta box
	 */

	public function addMetaBox() {
		add_meta_box("product-meta", __("Product settings", 'ct_theme'), array($this, "productMeta"), "product", "normal", "high");
		add_action('save_post', array($this, 'saveDetails'));
	}

	/**
	 * Fixes proper menu state
	 */

	public function productContextFixer() {
		if (get_query_var('post_type') == 'product') {
			global $wp_query;
			$wp_query->is_home = false;
		}
		if (get_query_var('taxonomy') == 'product_category') {
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
				'name' => _x('Product items', 'post type general name', 'ct_theme'),
				'singular_name' => _x('Product Item', 'post type singular name', 'ct_theme'),
				'add_new' => _x('Add New', 'product', 'ct_theme'),
				'add_new_item' => __('Add New Product Item', 'ct_theme'),
				'edit_item' => __('Edit Product Item', 'ct_theme'),
				'new_item' => __('New Product Item', 'ct_theme'),
				'view_item' => __('View Product Item', 'ct_theme'),
				'search_items' => __('Search Product Items', 'ct_theme'),
				'not_found' => __('No product item found', 'ct_theme'),
				'not_found_in_trash' => __('No product items found in Trash', 'ct_theme'),
				'parent_item_colon' => '',
				'menu_name' => __('Product items', 'ct_theme'),
			),
			'singular_label' => __('product', 'ct_theme'),
			'public' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'show_ui' => true,
			'show_in_menu' => true,
			//'menu_position' => 20,
			'capability_type' => 'post',
			'hierarchical' => false,
			'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'comments', 'page-attributes'),
			'has_archive' => false,
			'rewrite' => array('slug' => $this->getPermalinkSlug(), 'with_front' => true, 'pages' => true, 'feeds' => false),
			'query_var' => false,
			'can_export' => true,
			'show_in_nav_menus' => true,
			'taxonomies' => array('post_tag')
		));

		register_post_type('product', $typeData);
		$this->callHook('post_register_type');
	}

	/**
	 * Returns permalink slug
	 * @return string
	 */

	protected function getPermalinkSlug() {
		// Rewriting Permalink Slug
		$permalink_slug = ct_get_option('product_index_slug', 'product');
		if (empty($permalink_slug)) {
			$permalink_slug = 'product';
		}

		return $permalink_slug;
	}

	/**
	 * Gets hook name
	 * @return string
	 */
	protected function getHookBaseName() {
		return 'ct_product';
	}

	/**
	 * Creates taxonomies
	 */

	protected function registerTaxonomies() {
		$data = $this->callFilter('pre_register_taxonomies', array(
			'hierarchical' => true,
			'labels' => array(
				'name' => _x('Product Categories', 'taxonomy general name', 'ct_theme'),
				'singular_name' => _x('Product Category', 'taxonomy singular name', 'ct_theme'),
				'search_items' => __('Search Categories', 'ct_theme'),
				'popular_items' => __('Popular Categories', 'ct_theme'),
				'all_items' => __('All Categories', 'ct_theme'),
				'parent_item' => null,
				'parent_item_colon' => null,
				'edit_item' => __('Edit Product Category', 'ct_theme'),
				'update_item' => __('Update Product Category', 'ct_theme'),
				'add_new_item' => __('Add New Product Category', 'ct_theme'),
				'new_item_name' => __('New Product Category Name', 'ct_theme'),
				'separate_items_with_commas' => __('Separate Product category with commas', 'ct_theme'),
				'add_or_remove_items' => __('Add or remove product category', 'ct_theme'),
				'choose_from_most_used' => __('Choose from the most used product category', 'ct_theme'),
				'menu_name' => __('Categories', 'ct_theme'),
			),
			'public' => false,
			'show_in_nav_menus' => false,
			'show_ui' => true,
			'show_tagcloud' => false,
			'query_var' => 'product_category',
			'rewrite' => false,

		));
		register_taxonomy('product_category', 'product', $data);
		$this->callHook('post_register_taxonomies');
	}


	/**
	 * Draw s product meta
	 */

	public function productMeta() {
		global $post;
		$custom = get_post_custom($post->ID);
		$price = isset($custom["price"][0]) ? $custom["price"][0] : "";
		$location = isset($custom["location"][0]) ? $custom["location"][0] : "";
		//print_r($custom);exit;
		/*$video = isset($custom["video"][0]) ? $custom["video"][0] : "";
		$displayMethod = isset($custom['display_method'][0]) ? $custom['display_method'][0] : 'image';
		$revolution_slider = isset($custom['revolution_slider'][0]) ? $custom['revolution_slider'][0] : '';

		if ($supportsRevolutionSlider = function_exists('rev_slider_shortcode')) {
			global $wpdb;
			$slides = $wpdb->get_results("SELECT * FROM wp_revslider_sliders");
		}*/
		?>
    <p>
        <label for="price"><?php _e('Price', 'ct_theme')?>: </label>
        <input id="price" class="regular-text" name="price" value="<?php echo $price; ?>"/>
    <p class="howto"><?php _e("Price for the product", 'ct_theme')?></p>
    <p>
        <label for="location"><?php _e('Location', 'ct_theme')?>: </label>
        <input id="location" class="regular-text" name="location" value="<?php echo $location; ?>"/>
    </p>
    <p class="howto"><?php _e("Additional short info", 'ct_theme')?></p>

	<?php /* ?>
    <p>
        <label for="display_method"><?php _e('Show product item as', 'ct_theme')?>: </label>
        <select class="ct-toggler" id="display_method" name="display_method">
            <option data-group=".display" value="image" <?php echo selected('image', $displayMethod)?>><?php _e("Featured image")?></option>
            <option data-group=".display" data-toggle=".ct-toggable.gallery" value="gallery" <?php echo selected('gallery', $displayMethod)?>><?php _e("Gallery")?></option>
            <option data-group=".display" data-toggle=".ct-toggable.video" value="video" <?php echo selected('video', $displayMethod)?>><?php _e("Video")?></option>
			<?php if ($supportsRevolutionSlider): ?>
            <option data-group=".display" data-toggle=".ct-toggable.revolution-slider" value="revolution-slider" <?php echo selected('revolution-slider', $displayMethod)?>><?php _e("Revolution slider gallery")?></option>
			<?php endif;?>
        </select>
    </p>
    <p class="ct-toggable video display">
        <label for="video"><?php _e('Video url', 'ct_theme')?>: </label>
        <input id="video" class="regular-text" name="video" value="<?php echo $video; ?>"/>
    </p>
	<?php if ($supportsRevolutionSlider): ?>
        <p class="ct-toggable revolution-slider display">
            <label for="revolutionSlider"><?php _e('Revolution slider', 'ct_theme')?>: </label>

            <select id="revolutionSlider" name="revolution_slider">
				<?php foreach ($slides as $slide): ?>
                <option <?php echo selected($slide->alias, $revolution_slider)?> value="<?php echo $slide->alias?>"><?php echo $slide->title?></option>
				<?php endforeach;?>
            </select>
        </p>
		<?php endif; ?>
	<?php */?>
	<?php
	}

	public function saveDetails() {
		global $post;

		$fields = array('price', 'location', 'video', 'display_method', 'revolution_slider');
		foreach ($fields as $field) {
			if (isset($_POST[$field])) {
				update_post_meta($post->ID, $field, $_POST[$field]);
			}
		}
	}

	/**
	 * Gets display method for product
	 * @param array $meta - post meta
	 * @return null|string
	 */
	public static function getMethodFromMeta($meta) {
		$method = isset($meta['display_method']) ? $meta['display_method'][0] : null;
		if (!$method) {
			$method = trim($meta['video'][0]) ? 'video' : 'image';
		}
		return $method;
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
