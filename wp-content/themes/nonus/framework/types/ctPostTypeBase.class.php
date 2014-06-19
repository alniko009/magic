<?php
require_once 'ctTypeBase.class.php';
/**
 * Post type handler
 * @author hc
 */

class ctPostTypeBase extends ctTypeBase {

	/**
	 * Slug option name
	 */

	const OPTION_SLUG = 'post_index_slug';

	/**
	 * Initializes events
	 * @return mixed|void
	 */

	public function init() {
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
		wp_register_script('ct.post',CT_THEME_ADMIN_ASSETS_URI.'/js/post.js',array('jquery'));
		wp_enqueue_script('ct.post');

		add_meta_box("post-gallery-meta", __("Gallery format settings", 'ct_theme'), array($this, "postGalleryMeta"), "post", "normal", "high");
		add_meta_box("post-link-meta", __("Link format settings", 'ct_theme'), array($this, "postLinkMeta"), "post", "normal", "high");
		add_meta_box("post-quote-meta", __("Quote format settings", 'ct_theme'), array($this, "postQuoteMeta"), "post", "normal", "high");
		add_meta_box("post-video-meta", __("Video format settings", 'ct_theme'), array($this, "postVideoMeta"), "post", "normal", "high");
		add_meta_box("post-audio-meta", __("Audio format settings", 'ct_theme'), array($this, "postAudioMeta"), "post", "normal", "high");
		add_action('save_post', array($this, 'saveDetails'));
	}

	/**
	 * Gets hook name
	 * @return string
	 */
	protected function getHookBaseName() {
		return 'ct_post';
	}

	/**
	 * Returns permalink slug
	 * @return string
	 */

	protected function getPermalinkSlug() {
		// Rewriting Permalink Slug
		$permalink_slug = ct_get_option('post_index_slug', 'post');
		if (empty($permalink_slug)) {
			$permalink_slug = 'post';
		}

		return $permalink_slug;
	}

	/**
	 * Draws post gallery format settings
	 */

	public function postGalleryMeta() {
		global $post;
		$custom = get_post_custom($post->ID);
		$displayMethod = isset($custom['display_method'][0]) ? $custom['display_method'][0] : 'image';
		$revolution_slider = isset($custom['revolution_slider'][0]) ? $custom['revolution_slider'][0] : '';

		if ($supportsRevolutionSlider = function_exists('rev_slider_shortcode')) {
			global $wpdb;
			$slides = $wpdb->get_results("SELECT * FROM ".GlobalsRevSlider::$table_sliders);
		}
		?>
    <p>
        <label for="display_method"><?php _e('Show portfolio item as', 'ct_theme')?>: </label>
        <select class="ct-toggler" id="display_method" name="display_method">
            <option data-group=".display" data-toggle=".ct-toggable.gallery" value="gallery" <?php echo selected('gallery', $displayMethod)?>><?php _e("Gallery", 'ct_theme')?></option>
			<?php if ($supportsRevolutionSlider): ?>
            <option data-group=".display" data-toggle=".ct-toggable.revolution-slider" value="revolution-slider" <?php echo selected('revolution-slider', $displayMethod)?>><?php _e("Revolution slider gallery", 'ct_theme')?></option>
			<?php endif;?>
        </select>
    </p>
    <p class="howto"><?php _e("You can use the post images gallery. If the Revolution Slider module is installed, you can also use it to create your custom gallery.", 'ct_theme')?></p>
	<?php if ($supportsRevolutionSlider): ?>
        <p class="ct-toggable revolution-slider display">
            <label for="revolutionSlider"><?php _e('Revolution slider', 'ct_theme')?>: </label>

            <select id="revolutionSlider" name="revolution_slider">
				<?php foreach ($slides as $slide): ?>
                <option <?php echo selected($slide->alias, $revolution_slider)?> value="<?php echo $slide->alias?>"><?php echo $slide->title?></option>
				<?php endforeach;?>
            </select>
        </p>
        <p class="howto ct-toggable revolution-slider display"><?php _e("Choose your revolution slider gallery", 'ct_theme')?></p>
		<?php endif; ?>
	<?php
	}

	/**
	 * Draws post link format settings
	 */

	public function postLinkMeta() {
		global $post;
		$custom = get_post_custom($post->ID);
		$link = isset($custom["link"][0]) ? $custom["link"][0] : "";
		?>
    <p>
        <label for="link"><?php _e('Link', 'ct_theme')?>: </label>
        <input type="text" id="link" class="regular-text" name="link" value="<?php echo $link; ?>"/>
    </p>
    <p class="howto"><?php _e("Input your link. Example: http://www.google.com", 'ct_theme')?></p>
	<?php
	}

	/**
	 * Draws post quote format settings
	 */

	public function postQuoteMeta() {
		global $post;
		$custom = get_post_custom($post->ID);
		$quote = isset($custom["quote"][0]) ? $custom["quote"][0] : "";
		?>
    <p>
        <label for="quote"><?php _e('Quote', 'ct_theme')?>: </label>
        <textarea id="quote" class="regular-text" name="quote" cols="100" rows="10"><?php echo $quote; ?></textarea>
    </p>
    <p class="howto"><?php _e("Quote content", 'ct_theme')?></p>
	<?php
	}

	/**
	 * Draws post video format settings
	 */

	public function postVideoMeta() {
		global $post;
		$custom = get_post_custom($post->ID);
		$videoM4V = isset($custom["videoM4V"][0]) ? $custom["videoM4V"][0] : "";
		$videoOGV = isset($custom["videoOGV"][0]) ? $custom["videoOGV"][0] : "";
		$videoDirect = isset($custom["videoDirect"][0]) ? $custom["videoDirect"][0] : "";
		$videoCode = isset($custom["videoCode"][0]) ? $custom["videoCode"][0] : "";
		?>
	<p>
        <label for="videoM4V"><?php _e('M4V File URL', 'ct_theme')?>: </label>
        <input type="text" id="videoM4V" class="regular-text" name="videoM4V" value="<?php echo $videoM4V; ?>"/>
    </p>
    <p class="howto"><?php _e("The URL to the .m4v video file", 'ct_theme')?></p>
	<p>
        <label for="videoOGV"><?php _e('OGV File URL', 'ct_theme')?>: </label>
        <input type="text" id="videoOGV" class="regular-text" name="videoOGV" value="<?php echo $videoOGV; ?>"/>
    </p>
    <p class="howto"><?php _e("The URL to the .ogv video file", 'ct_theme')?></p>
    <p>
        <label for="videoDirect"><?php _e('Direct video URL', 'ct_theme')?>: </label>
        <input type="text" id="videoDirect" class="regular-text" name="videoDirect" value="<?php echo $videoDirect; ?>"/>
    </p>
    <p class="howto"><?php _e("Direct movie link to embed movie from popular services like Youtube, Vimeo, Dailymotion", 'ct_theme')?></p>
    <p>
        <label for="videoCode"><?php _e('Embedded Code', 'ct_theme')?>: </label>
        <textarea id="videoCode" class="regular-text" name="videoCode" cols="100" rows="10"><?php echo $videoCode; ?></textarea>
    </p>
    <p class="howto"><?php _e("You can use any custom embed code.", 'ct_theme')?></p>
	<?php
	}

	/**
	 * Draws post video format settings
	 */

	public function postAudioMeta() {
		global $post;
		$custom = get_post_custom($post->ID);
		$audioMP3 = isset($custom["audioMP3"][0]) ? $custom["audioMP3"][0] : "";
		$audioOGA = isset($custom["audioOGA"][0]) ? $custom["audioOGA"][0] : "";
		$audioPoster = isset($custom["audioPoster"][0]) ? $custom["audioPoster"][0] : "";
		$audioPosterHeight = isset($custom["audioPosterHeight"][0]) ? $custom["audioPosterHeight"][0] : "";
		?>

    <p>
        <label for="audioMP3"><?php _e('MP3 File URL', 'ct_theme')?>: </label>
        <input type="text" id="audioMP3" class="regular-text" name="audioMP3" value="<?php echo $audioMP3; ?>"/>
    </p>
    <p class="howto"><?php _e("The URL to the .mp3 audio file", 'ct_theme')?></p>
    <p>
        <label for="audioOGA"><?php _e('OGA File URL', 'ct_theme')?>: </label>
        <input type="text" id="audioOGA" class="regular-text" name="audioOGA" value="<?php echo $audioOGA; ?>"/>
    </p>
    <p class="howto"><?php _e("The URL to the .oga, .ogg audio file", 'ct_theme')?></p>
    <p>
        <label for="audioPoster"><?php _e('Poster URL', 'ct_theme')?>: </label>
        <input type="text" id="audioPoster" class="regular-text" name="audioPoster" value="<?php echo $audioPoster; ?>"/>
    </p>
    <p class="howto"><?php _e("The URL to the poster file", 'ct_theme')?></p>
    <p>
        <label for="audioPosterHeight"><?php _e('Poster height', 'ct_theme')?>: </label>
        <input type="text" id="audioPosterHeight" class="regular-text" name="audioPosterHeight" value="<?php echo $audioPosterHeight; ?>"/>
    </p>
    <p class="howto"><?php _e("The height of the poster", 'ct_theme')?></p>
	<?php
	}

	public function saveDetails() {
		global $post;

		$fields = array('link', 'quote', 'videoM4V', 'videoOGV', 'videoDirect', 'videoCode', 'audioMP3', 'audioOGA', 'audioPoster', 'audioPosterHeight');
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

	/**
	 * Gets display method for portfolio
	 * @param array $meta - post meta
	 * @return null|string
	 */
	public static function getMethodFromMeta($meta) {
		$method = isset($meta['display_method']) ? $meta['display_method'][0] : null;
		if (!$method) {
			$method = '';
		}
		return $method ? $method : 'gallery';
	}
}
