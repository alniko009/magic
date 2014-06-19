<?php
/**
 * Import revolution slider
 */
class ctRevolutionSliderImporter {

	/**
	 * Get path to exported settings
	 * @author alex
	 * @return array
	 */
	public static function getSliderConfigs() {
		$dir = ctImport::getDemoContentBaseDir(). '/revolution_slider*.txt';
		return glob($dir);
	}

	/**
	 * Import settings
	 * @param null $path
	 */
	public function import($path = null) {
		if (self::isPluginActive()) {
			foreach (self::getSliderConfigs() as $config) {
				$this->importSlider($config);
			}
		}
	}

	/**
	 * Is revolution slider active?
	 * @return bool
	 */

	public static function isPluginActive(){
		return class_exists('GlobalsRevSlider');
	}

	/**
	 * Adds recommendation to user
	 */

	public static function addRecommendation($html){
		if(self::getSliderConfigs()){
			//we have configs
			if(!self::isPluginActive()){
				$html = $html!=''?$html:'';
				$html.='<p>This theme comes with <strong>preconfigured</strong> Revolution Slider gallery. Please <strong>install and activate</strong> Revolution Slider in order to import gallery. If you are not interested in using Revolution Slider please discard this message.</strong></p>';
			}
		}

		return $html;
	}

	/**
	 * Import slider
	 * @param $config
	 * @throws Exception
	 * @return array
	 */
	protected function importSlider($config) {
		global $wpdb;
		//we take name/alias/shortcode from file name
		$name = str_replace(array('revolution_slider_', '.txt'), '', basename($config));

		$slider = new RevSlider();
		try {
			$sliderId = $slider->createSliderFromOptions(array('main' => array('title' => $name, 'alias' => $name), 'params' => array()));
		} catch (Exception $e) {
			//already exists - let's leave it as is
			return;
		}

		//create a slider
		try {
			$slider->initByID($sliderId);

			//get content array
			$content = @file_get_contents($config);
			$arrSlider = @unserialize($content);
			if (empty($arrSlider)) {
				UniteFunctionsRev::throwError("Wrong export slider file format!");
			}

			//update slider params

			$sliderParams = $arrSlider["params"];
			$sliderParams["title"] = $name;
			$sliderParams["alias"] = $name;
			$sliderParams["shortcode"] = '[rev_slider ' . $name . ']';

			if (isset($sliderParams["background_image"])) {
				$sliderParams["background_image"] = UniteFunctionsWPRev::getImageUrlFromPath($sliderParams["background_image"]);
			}

			$json_params = json_encode($sliderParams);
			$arrUpdate = array("params" => $json_params);
			$wpdb->update(GlobalsRevSlider::$table_sliders, $arrUpdate, array("id" => $sliderId));

			//-------- Slides Handle -----------


			//create all slides
			$arrSlides = $arrSlider["slides"];
			foreach ($arrSlides as $slide) {

				$params = $slide["params"];
				$layers = $slide["layers"];

				//convert params images:
				if (isset($params["image"])) {
					$params["image"] = UniteFunctionsWPRev::getImageUrlFromPath($params["image"]);
				}

				//convert layers images:
				foreach ($layers as $key => $layer) {
					if (isset($layer["image_url"])) {
						$layer["image_url"] = UniteFunctionsWPRev::getImageUrlFromPath($layer["image_url"]);
						$layers[$key] = $layer;
					}
				}

				//create new slide
				$arrCreate = array();
				$arrCreate["slider_id"] = $sliderId;
				$arrCreate["slide_order"] = $slide["slide_order"];
				$arrCreate["layers"] = json_encode($layers);
				$arrCreate["params"] = json_encode($params);

				$wpdb->insert(GlobalsRevSlider::$table_slides, $arrCreate);
			}

		} catch (Exception $e) {
			if (WP_DEBUG) {
				throw $e;
			}
		}
	}

}