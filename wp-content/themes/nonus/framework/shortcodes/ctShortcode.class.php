<?php
/**
 * Main shortcode class
 * @author alex
 */

abstract class ctShortcode {

	/**
	 * Shortcode without content inside
	 */

	const TYPE_SHORTCODE_SELF_CLOSING = 'self-closing';
	/**
	 * Shortcode which wraps selection
	 */
	const TYPE_SHORTCODE_ENCLOSING = 'enclosing';

	/**
	 * Shortocode self_closing and enclosing
	 */
	const TYPE_SHORTCODE_BOTH = 'both';

	/**
	 * Shortcode has config popup
	 */

	const GENERATOR_ACTION_POPUP = 'popup';

	/**
	 * Add shortcode now
	 */

	const GENERATOR_ACTION_INSERT = 'insert';

	/**
	 * @var string
	 */
	protected static $data;

	/**
	 * Callback which is overwritten by current shortcode
	 * @var null
	 */
	protected $overwrittenCallback = null;


	/**
	 * Dynamic javascript files
	 * @var array
	 */

	protected $dynamicJs = array();

	/**
	 * Creates and registers shortcode
	 */
	public function __construct() {
		//register shortcode
		global $shortcode_tags;
		if (isset($shortcode_tags[$this->getShortcodeName()])) {
			$this->overwrittenCallback = $shortcode_tags[$this->getShortcodeName()];
		}
		add_shortcode($this->getShortcodeName(), array($this, 'handleShortcode'));

		//allow to add scripts for increased compatibility
		if (!is_admin()) {
			add_action('init', array($this, 'enqueueHeadScripts'));
		}
		ctShortcodeHandler::register($this);
	}

	/**
	 * Handle shortcode
	 * @param array $atts
	 * @param null $content
	 * @return mixed|void
	 */

	public function handleShortcode($atts, $content = null) {
		$atts = $this->preHandleShortcode($atts, $content);

		$content = $this->handle($atts, $content);
		return $this->postHandleShortcode($atts, $content);
	}

	/**
	 * Allows to filter attributes
	 * @param $atts
	 * @param null $content
	 * @return mixed|void
	 */

	protected function preHandleShortcode($atts, $content = null) {
		$name = $this->getShortcodeName();

		//add all scripts in footer
		add_action('wp_footer', array($this, 'enqueueScripts'));

		//print inline scripts
		add_action('wp_print_footer_scripts', array($this, 'printInlineScripts'));

		$atts = apply_filters('ct_shortcode_pre_handle', $atts, $content);
		return apply_filters($this->getShortcodeName() . '_pre_handle', $atts);
	}

	/**
	 * Prints inline scripts
	 */

	public function printInlineScripts() {
		if ($this->dynamicJs) {
			$this->dynamicJs = array_filter($this->dynamicJs);
			echo '<script type="text/javascript">' . "\n";
			echo implode("\n", $this->dynamicJs);
			echo '</script>' . "\n";
		}
	}

	/**
	 * Handle shortcode after its rendered
	 * @param $atts
	 * @param null $content
	 * @return mixed|void
	 */

	protected function postHandleShortcode($atts, $content = null) {
		$content = apply_filters('ct_shortcode_post_handle', $content, $atts);
		return apply_filters($this->getShortcodeName() . '_post_handle', $content);
	}

	/**
	 * Returns overwritten callback if available
	 * @author alex
	 * @return mixed
	 */

	protected function getOverwrittenCallback() {
		return $this->overwrittenCallback;
	}

	/**
	 * Adds data
	 * @param mixed $key
	 * @param $value
	 */

	protected function setData($key, $value) {
		self::$data[$this->getShortcodeName()][$key] = $value;
	}

	/**
	 * Returns data
	 * @param mixed $id
	 * @param mixed $default
	 * @return mixed
	 */
	protected function getData($id, $default = null) {
		return isset(self::$data[$this->getShortcodeName()]) && array_key_exists($id, self::$data[$this->getShortcodeName()]) ? self::$data[$this->getShortcodeName()][$id] : $default;
	}

	/**
	 * Returns all data
	 * @param array $default
	 * @return array
	 */

	protected function getAllData($default = array()) {
		return array_key_exists($this->getShortcodeName(), self::$data) ? self::$data[$this->getShortcodeName()] : $default;
	}

	/**
	 * Unsets shortcode data
	 * @param string $shortcodeName
	 */

	protected function cleanData($shortcodeName = null) {
		$shortcodeName = $shortcodeName ? $shortcodeName : $this->getShortcodeName();

		if (isset(self::$data[$shortcodeName])) {
			unset(self::$data[$shortcodeName]);
		}
	}

	/**
	 * Returns group name
	 * @return mixed
	 */

	public function getGroupName() {
		$class_info = new ReflectionClass($this);
		return ucfirst(basename(dirname($class_info->getFileName())));
	}

	/**
	 * Returns shortcode label
	 * @return mixed
	 */

	public abstract function getName();

	/**
	 * Returns shortcode name
	 * @return string
	 */

	public abstract function getShortcodeName();

	/**
	 * Handles shortcode
	 * @param $atts
	 * @param null $content
	 * @return mixed
	 */

	public abstract function handle($atts, $content = null);

	/**
	 * Returns config
	 * @return array
	 */

	public abstract function getAttributes();

	/**
	 * Extracts attributes
	 * @param array $atts
	 */
	protected function extractShortcodeAttributes($atts) {
		$values = array_map(create_function('$s', 'return isset($s["default"])?$s["default"]:"";'), $this->getAttributes());
		if (isset($values['content']) && $values['content'] == '') {
			unset($values['content']);
		}
		return $values;
	}

	/**
	 * Returns
	 * @return array
	 */
	public function getShortcodeMenuItem() {
		$action = $this->getAttributes() || $this->getChildShortcodeInfo() ? $this->getGeneratorAction() : self::GENERATOR_ACTION_INSERT;
		$d = array('name' => $this->getName(), 'action' => $action, 'id' => $this->getShortcodeName());

		if ($action == self::GENERATOR_ACTION_INSERT) {
			$code = '[' . $this->getShortcodeName() . ']';
			if ($this->getShortcodeType() !== self::TYPE_SHORTCODE_SELF_CLOSING) {
				$code .= '(*)[/' . $this->getShortcodeName() . ']';
			}

			$d['code'] = $code;
		}

		return $d;
	}

	/**
	 * Returns child shortcode if exists
	 * @return ctShortcode
	 * @throws Exception
	 */
	public function getChildShortcode() {
		//maybe child?
		$childInfo = $this->getChildShortcodeInfo();
		if (isset($childInfo['name']) && ($childInfo['name'])) {
			//find shortcode
			if (!$childShortcode = ctShortcodeHandler::getInstance()->getShortcode($childInfo['name'])) {
				throw new Exception("Cannot find shortcode " . $childInfo['name']);
			}
			return $childShortcode;
		}

		return null;
	}

	/**
	 * Returns shortcode type
	 * @return mixed
	 */

	public function getShortcodeType() {
		return self::TYPE_SHORTCODE_BOTH;
	}

	/**
	 * Returns type
	 * @return mixed
	 */

	public function getGeneratorAction() {
		return self::GENERATOR_ACTION_POPUP;
	}

	/**
	 * Handler for enqueue scripts (in footer)
	 */

	public function enqueueScripts() {
		//do nothing here
	}

	/**
	 * Handler for head enquee scripts
	 */

	public function enqueueHeadScripts() {
		//do nothing here
	}

	/**
	 * Returns child shortcode name
	 * @return string
	 */
	public function getChildShortcodeInfo() {
		//returns definition of child shortcode (name -> shortcode name, min - min qty, max - max qty)
		return array('name' => '', 'min' => 0, 'max' => 0, 'default_qty' => 5);
	}

	/**
	 * Calls preHook
	 * @param $content
	 * @param array $options
	 * @return mixed
	 */

	protected function callPreFilter($content, $options = array()) {
		return apply_filters($this->getShortcodeName() . '_pre', $content, $options);
	}

	/**
	 * Calls post hook
	 * @param string $content
	 * @param array $options
	 * @return mixed
	 */

	protected function callPostFilter($content, $options = array()) {
		return apply_filters($this->getShortcodeName() . '_post', $content, $options);
	}

	/**
	 * Connects pre filter
	 * @param $parentShorcodeName
	 * @param $callback
	 */
	protected function connectPreFilter($parentShorcodeName, $callback) {
		add_filter($parentShorcodeName . '_pre', $callback);
	}

	/**
	 * Connects post filter
	 * @param $parentShortcodeName
	 * @param $callback
	 */

	protected function connectPostFilter($parentShortcodeName, $callback) {
		add_filter($parentShortcodeName . '_post', $callback);
	}

	/**
	 * Allows to embed shortcode inside shortcode
	 * @param string $name
	 * @param array $params
	 * @param null $content
	 */

	protected function embedShortcode($name, $params, $content = null) {
		$html = '[' . $name;
		foreach ($params as $pname => $value) {
			$html .= ' ' . $pname . '="' . esc_attr($value) . '"';
		}

		$html .= ']';

		if ($content != '') {
			$html .= $content;
			$html .= '[/' . $name . ']';
		}

		return $html;
	}

	/**
	 * Returns custom form view
	 * @param array $params
	 * @return string
	 */

	public function getCustomFormView($params = array()) {
		return '';
	}

	/**
	 * Parent shortcode name
	 * @return null
	 */

	public function getParentShortcodeName() {
		return null;
	}

	/**
	 * Add inline javascripts
	 * @param string $script
	 */

	protected function addInlineJS($script) {
		$this->dynamicJs[] = $script;
	}
}