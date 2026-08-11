<?php
defined('ABSPATH') or die('No script kiddies please!');

/*
  Plugin Name: Steam-Api-Widget-Redux
  Plugin URI: http://neutralx2.com
  Description: A simple WordPress widget for your Steam profile. Modified by NeutralX2.com
  Version: 1.1
  Author: Armin Nowacki & NeutralX2
  License: GPLv2 or later
 */

require __DIR__ . '/steam/Api.php';

use Steam\Api;

/**
 * Class SteamApiWidget
 */

class SteamApiWidget extends WP_Widget
{
	/**
	 * @var array $default_settings
	 */

	private $default_settings = [
		'title' => 'Steam',

		'api_key' => '',
		'steam_id' => '',

		'count' => 7,

		'cache_interval' => 0,
	];

	/**
	 * @constructor
	 */

	public function __construct()
	{
		$this->initPluginConstants();

		$widget_option = [
			'classname' => PLUGIN_SLUG,

			'description' => __(
				'A simple WordPress widget for your steam profile.',
				PLUGIN_LOCALE
			),
		];

		parent::__construct(
			PLUGIN_SLUG,
			__(PLUGIN_NAME, PLUGIN_LOCALE),
			$widget_option
		);

		$this->registerScriptsAndStyles();
	}

	private function initPluginConstants()
	{
		if (!defined('PLUGIN_LOCALE')) {
			define('PLUGIN_LOCALE', 'steam-api-widget-locale');
		}

		if (!defined('PLUGIN_NAME')) {
			define('PLUGIN_NAME', 'steam');
		}

		if (!defined('PLUGIN_SLUG')) {
			define('PLUGIN_SLUG', 'steam-api-widget');
		}

		if (!defined('PLUGIN_VERSION')) {
			$plugin_data = get_file_data(__FILE__, ['Version' => 'Version']);

			define('PLUGIN_VERSION', $plugin_data['Version']);
		}
	}

	private function registerScriptsAndStyles()
	{
		if (!is_admin()) {
			$this->loadFile(
				PLUGIN_NAME,
				'/' . PLUGIN_SLUG . '/assets/css/steam-widget.css'
			);
		}
	}

	/**
	 * @param string $name
	 * @param string $file_path
	 * @param bool $is_script
	 */

	private function loadFile($name, $file_path, $is_script = false)
	{
		$url = WP_PLUGIN_URL . $file_path;

		$file = WP_PLUGIN_DIR . $file_path;

		if (file_exists($file)) {
			if ($is_script) {
				wp_register_script($name, $url, [], PLUGIN_VERSION);

				wp_enqueue_script($name);
			} else {
				wp_register_style($name, $url, [], PLUGIN_VERSION);

				wp_enqueue_style($name);
			}
		}
	}

	/**
	 * @param array $instance
	 */

	public function form($instance)
	{
		$instance = wp_parse_args($instance, $this->default_settings);

		$title = esc_attr($instance['title']);

		$api_key = esc_attr($instance['api_key']);

		$steam_id = esc_attr($instance['steam_id']);

		$count = esc_attr($instance['count']);

		$cache_interval = esc_attr($instance['cache_interval']);

		include WP_PLUGIN_DIR . '/' . PLUGIN_SLUG . '/views/admin.php';
	}

	/**
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */

	public function update($new_instance, $old_instance)
	{
		$instance = $old_instance;

		$instance['title'] = strip_tags($new_instance['title']);

		$instance['api_key'] = strip_tags($new_instance['api_key']);

		$instance['steam_id'] = strip_tags($new_instance['steam_id']);

		$instance['count'] = strip_tags($new_instance['count']);

		$instance['cache_interval'] = strip_tags(
			$new_instance['cache_interval']
		);

		delete_transient($this->id);

		return $instance;
	}

	/**
	 * @param array $args
	 * @param array $instance
	 */

	public function widget($args, $instance)
	{
		extract($args);

		$title = apply_filters('widget_title', $instance['title']);

		$api_key = $instance['api_key'];

		$steam_id = $instance['steam_id'];

		$count = $instance['count'];

		$cache_interval = $instance['cache_interval'];

		echo $before_widget;

		if ($title) {
			echo $before_title . $title . $after_title;
		}

		echo '<div id="Steam-Widget">';

		$data = [];

		$api = new Api($api_key, $steam_id);

		if ($cache_interval > 0) {
			$data = get_transient($this->id);

			if ($data === false) {
				if ($api->getData()) {
					$data['profile'] = $api->getProfile();

					$data['games'] = $api->getGames();

					set_transient($this->id, $data, $cache_interval);
				}
			}
		} else {
			if ($api->getData()) {
				$data['profile'] = $api->getProfile();

				$data['games'] = $api->getGames();
			}
		}

		if ($data) {
			$profile = $data['profile'];

			$games = $data['games'];

			include WP_PLUGIN_DIR . '/' . PLUGIN_SLUG . '/views/widget.php';
		} else {
			echo '<p>Steam servers are currently <br /> unavailable or too busy.</p>';
		}

		echo '</div>';

		echo $after_widget;
	}
}

add_action('widgets_init', function () {
	register_widget('SteamApiWidget');
});
