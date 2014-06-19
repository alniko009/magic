<?php
/**
 * Frontend options handler
 * @author alex
 */

class ctFrontendOptionsHandler {

	/**
	 * Creates admin handler
	 */
	public function __construct() {
	}

	/**
	 * Inits events
	 */

	public function init() {
		add_action('wp_head', array($this, 'handleFaviconOption'));
		add_action('wp_head', array($this, 'handleAppleTouchOption'));
		add_action('wp_head', array($this, 'handleCustomCss'));
		add_action('wp_head', array($this, 'handleCustomJS'));
		add_action('wp_head', array($this, 'handleGoogleWebfonts'), 1);
		add_action('wp_head', array($this, 'handleGoogleAnalytics'));
	}

	/**
	 * Adds google stylesheet
	 */

	function handleGoogleWebfonts() {
		if ($f = ct_get_option('style_font_style')) {
			$font = explode(':', $f);
			if (isset($font[1])) {
				wp_enqueue_style('google.webfont', 'http://fonts.googleapis.com/css?subset=latin,latin-ext&family=' . str_replace(' ', '+', $font[0]));
			}
		}
	}

	/**
	 * Generates favicon icon
	 */

	function handleFaviconOption() {
		if ($f = ct_get_option('general_favicon')) {
			echo '<link rel="shortcut icon" href="' . esc_url($f) . '" />';
		}
	}

	/**
	 * Generates apple touch icon
	 */

	function handleAppleTouchOption() {
		if ($f = ct_get_option('general_apple_touch_icon')) {
			echo '<link rel="apple-touch-icon" href="' . esc_url($f) . '" />';
		}
	}

	/**
	 * Includes user css + css from general settings
	 */

	function handleCustomCss() {
		ctThemeLoader::getFilesLoader()->includeIt(CT_THEME_SETTINGS_MAIN_DIR . '/custom_style.php');
	}

	/**
	 * Custom JS
	 */

	function handleCustomJS() {
		if ($e = ct_get_option('code_custom_styles_js')) {
			$hasTag = stripos($e, '<script') !== false;
			if (!$hasTag) {
				echo '<script type="text/javascript">';
			}
			echo $e . "\n";

			if (!$hasTag) {
				echo '</script>';
			}
		}
	}

	/**
	 * Adds Google Analytics
	 */

	function handleGoogleAnalytics() {
		if ($num = ct_get_option('code_google_analytics_account')) {
			echo '<script type="text/javascript">';
			echo <<<EOF
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '{$num}']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
EOF;

			echo '</script>';
		}
	}
}