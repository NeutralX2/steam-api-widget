<?php
defined('ABSPATH') || exit;

/*
  Plugin Name: Steam-Api-Widget-Redux
  Plugin URI: http://neutralx2.com
  Description: A simple WordPress widget for your Steam profile. Modified by NeutralX2.com
  Version: 2.0
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
	const CRON_HOOK_REFRESH_ALL = 'steam_api_widget_refresh_all';
	const CRON_HOOK_REFRESH_INSTANCE = 'steam_api_widget_refresh_instance';
	const CRON_SCHEDULE_ID = 'steam_api_widget_every_minute';

	/**
	 * @var array $default_settings
	 */

	private $default_settings = [
		'title' => 'Steam',

		'api_key' => '',
		'steam_id' => '',

		'count' => 7,

		'cache_interval' => 5,
	];

	/**
	 * @constructor
	 */

	public function __construct()
	{
		self::initPluginConstants();

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

	public static function initPluginConstants()
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

		$instance['cache_interval'] = max(
			1,
			(int) strip_tags($new_instance['cache_interval'])
		);

		delete_transient($this->id);

		$number = (int) $this->number;

		if (!wp_next_scheduled(self::CRON_HOOK_REFRESH_INSTANCE, [$number])) {
			wp_schedule_single_event(
				time(),
				self::CRON_HOOK_REFRESH_INSTANCE,
				[$number]
			);
		}

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

		$count = $instance['count'];

		echo $before_widget;

		if ($title) {
			echo $before_title . $title . $after_title;
		}

		echo '<div id="Steam-Widget">';

		$data = get_transient($this->id);

		if ($data) {
			$profile = $data['profile'];

			$games = $data['games'];

			include WP_PLUGIN_DIR . '/' . PLUGIN_SLUG . '/views/widget.php';
		} elseif ($this->number === -1) {
			echo '<p>Preview is not available in the widget editor. <br /> Check the live page instead.</p>';
		} else {
			echo '<p>Steam servers are currently <br /> unavailable or too busy.</p>';
		}

		echo '</div>';

		echo $after_widget;
	}

	/**
	 * @param array $schedules
	 * @return array
	 */

	public static function addCronSchedule($schedules)
	{
		self::initPluginConstants();

		$schedules[self::CRON_SCHEDULE_ID] = [
			'interval' => MINUTE_IN_SECONDS,
			'display' => __('Every Minute (Steam API Widget)', PLUGIN_LOCALE),
		];

		return $schedules;
	}

	public static function activate()
	{
		if (!wp_next_scheduled(self::CRON_HOOK_REFRESH_ALL)) {
			wp_schedule_event(
				time(),
				self::CRON_SCHEDULE_ID,
				self::CRON_HOOK_REFRESH_ALL
			);
		}
	}

	public static function deactivate()
	{
		wp_clear_scheduled_hook(self::CRON_HOOK_REFRESH_ALL);
	}

	/**
	 * @param string $id
	 * @param string $sidebar_id
	 * @param string $id_base
	 */

	public static function onWidgetDeleted($id, $sidebar_id, $id_base)
	{
		self::initPluginConstants();

		if ($id_base !== PLUGIN_SLUG) {
			return;
		}

		delete_transient($id);
	}

	public static function uninstall()
	{
		self::initPluginConstants();

		$settings = get_option('widget_' . PLUGIN_SLUG);

		if (is_array($settings)) {
			foreach ($settings as $number => $instance) {
				if (!is_numeric($number)) {
					continue;
				}

				delete_transient(PLUGIN_SLUG . '-' . $number);
			}
		}

		delete_option('widget_' . PLUGIN_SLUG);
	}

	public static function refreshAll()
	{
		self::initPluginConstants();

		$settings = get_option('widget_' . PLUGIN_SLUG);

		if (!is_array($settings)) {
			return;
		}

		foreach ($settings as $number => $instance) {
			if (!is_numeric($number)) {
				continue;
			}

			self::refreshInstance((int) $number, $instance);
		}
	}

	/**
	 * @param int $number
	 */

	public static function refreshInstanceByNumber($number)
	{
		self::initPluginConstants();

		$settings = get_option('widget_' . PLUGIN_SLUG);

		if (!is_array($settings) || !isset($settings[$number])) {
			return;
		}

		self::refreshInstance((int) $number, $settings[$number]);
	}

	/**
	 * @param int $number
	 * @return bool
	 */

	private static function isWidgetActive($number)
	{
		$id = PLUGIN_SLUG . '-' . $number;

		$sidebars_widgets = wp_get_sidebars_widgets();

		foreach ($sidebars_widgets as $sidebar_id => $widget_ids) {
			// wp_inactive_widgets is a real widget-ID array like any sidebar,
			// so it can only be excluded by name. Other keys (e.g. array_version)
			// aren't sidebars at all and are excluded by not being arrays.
			if ($sidebar_id === 'wp_inactive_widgets' || !is_array($widget_ids)) {
				continue;
			}

			if (in_array($id, $widget_ids, true)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param int $number
	 * @param array $instance
	 */

	private static function refreshInstance($number, $instance)
	{
		if (!self::isWidgetActive($number)) {
			return;
		}

		$id = PLUGIN_SLUG . '-' . $number;

		$minutes = max(
			1,
			isset($instance['cache_interval'])
				? (int) $instance['cache_interval']
				: 1
		);

		$cached = get_transient($id);

		if (
			$cached &&
			isset($cached['refreshed_at']) &&
			time() - $cached['refreshed_at'] < $minutes * MINUTE_IN_SECONDS
		) {
			return;
		}

		$api_key = isset($instance['api_key']) ? $instance['api_key'] : '';

		$steam_id = isset($instance['steam_id']) ? $instance['steam_id'] : '';

		if (empty($api_key) || empty($steam_id)) {
			return;
		}

		$api = new Api($api_key, $steam_id);

		if ($api->getData()) {
			set_transient(
				$id,
				[
					'profile' => $api->getProfile(),
					'games' => $api->getGames(),
					'refreshed_at' => time(),
				],
				0
			);
		}
	}
}

SteamApiWidget::initPluginConstants();

add_action('widgets_init', function () {
	register_widget('SteamApiWidget');
});

add_filter('cron_schedules', ['SteamApiWidget', 'addCronSchedule']);

add_action(SteamApiWidget::CRON_HOOK_REFRESH_ALL, [
	'SteamApiWidget',
	'refreshAll',
]);

add_action(SteamApiWidget::CRON_HOOK_REFRESH_INSTANCE, [
	'SteamApiWidget',
	'refreshInstanceByNumber',
]);

add_action('delete_widget', ['SteamApiWidget', 'onWidgetDeleted'], 10, 3);

register_activation_hook(__FILE__, ['SteamApiWidget', 'activate']);

register_deactivation_hook(__FILE__, ['SteamApiWidget', 'deactivate']);

register_uninstall_hook(__FILE__, ['SteamApiWidget', 'uninstall']);
