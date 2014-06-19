<?php
/**
 * Facebook feed shortcode
 */
abstract class ctFacebookFeedShortcodeBase extends ctShortcode {

	/**
	 * Returns name
	 * @return string|void
	 */
	public function getName() {
		return 'fb';
	}

	/**
	 * Shortcode name
	 * @return string
	 */
	public function getShortcodeName() {
		return 'fb';
	}

	/**
	 * returns the follow link
	 * @param $pageid
	 * @return string
	 */
	protected function getFollowLink($pageid){
		return "http://www.facebook.com/" . $pageid;
	}

	/**
	 * gets twitter news
	 * @param $user
	 * @param $limit
	 * @return stdClass[]
	 */
	protected function getPosts($attributes) {
		extract($attributes);

		$feed = wp_remote_get('https://graph.facebook.com/' . $pageid . '/feed?access_token=' . $token);
		$xml = wp_remote_retrieve_body($feed);
		$json = json_decode($xml, true);
		$data = array();
		if(isset($json['data'])){
			foreach($json['data'] as $p) {
				if(!in_array($p['type'], array('status', 'photo', 'video')) || $limit<=0) { continue; }
				//split user and post ID (userID_postID)
				$idArray = explode("_", $p['id']);

				$post = array();
				$post['author'] = $p['from'];
				$post['content'] = isset($p['message']) ? $p['message'] : '';

				if($p['type'] == 'photo') {
					$post['image'] = $p['picture'];
				} elseif($p['type'] == 'video') {
					$post['image'] = $p['picture'];
					$post['content'] .= "\n\n {$p['link']}";
				} else {
					$post['image'] = null;
				}

				$post['timestamp'] = strtotime($p['created_time']);
				$post['like_count'] = (isset($p['likes'])) ? $p['likes']['count'] : 0;
				$post['comment_count'] = (isset($p['comments'])) ? $p['comments']['count'] : 0;
				$post['link'] = "http://www.facebook.com/".$pageid."/posts/".$idArray[1];

				if($post['content'] || $post['image']){
					$data[] = $post;
					$limit--;
				}
			}
		}

		return $data;
	}

	/**
	 * counts time ago
	 * @param $time
	 * @return string
	 */
	protected function ago($time) {
		$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
		$lengths = array("60", "60", "24", "7", "4.35", "12", "10");

		$now = time();

		$difference = $now - $time;

		for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths) - 1; $j++) {
			$difference /= $lengths[$j];
		}

		$difference = round($difference);

		if ($difference != 1) {
			$periods[$j] .= "s";
		}

		return $difference . " " . $periods[$j] . ' ' . __('ago', 'ct_theme');
	}

	/**
	 * Returns config
	 * @return null
	 */
	public function getAttributes() {
		return array(
			'pageid' => array('label' => __('page id', 'ct_theme'), 'default' => '', 'type' => 'input', 'help' => __("Page/user id", 'ct_theme')),
			'token' => array('label' => __('token', 'ct_theme'), 'default' => '', 'type' => 'input', 'help' => __("Access token", 'ct_theme')),
			'limit' => array('label' => __('limit', 'ct_theme'), 'default' => '2', 'type' => 'input', 'help' => __("Limit news", 'ct_theme')),
			'button' => array('label' => __("follow us button", 'ct_theme'), 'default' => __('Follow us', 'ct_theme'), 'type' => 'input', 'help' => "Follow us button label. Leave blank to hide it", 'ct_theme'),
			'newwindow' => array('label' => __("new window?", 'ct_theme'), 'default' => 'false', 'type' => 'checkbox', 'help' => "Open in new window follow us button?", 'ct_theme'),
			'img' => array('label' => __('embed images?', 'ct_theme'), 'default' => 'no', 'type' => 'select', 'choices' => array('yes' => __('yes', 'ct_theme'), 'no' => __('no', 'ct_theme')), 'help' => __("Embed images into posts content?", 'ct_theme')),
		);
	}
}