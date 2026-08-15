<?php

namespace Steam;

defined('ABSPATH') || exit;

require __DIR__ . '/Games.php';
require __DIR__ . '/Profile.php';

/**
 * Class Api
 * @package Steam
 */
class Api
{
	/**
	 * @var string $api_key
	 */
	private $api_key = '';

	/**
	 * @var string $steam_id
	 */
	private $steam_id = '';

	/**
	 * @var array $url
	 */
	private $url = [
		'profile' =>
			'https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/',
		'games' =>
			'https://api.steampowered.com/IPlayerService/GetRecentlyPlayedGames/v0001/',
	];

	/**
	 * @var Profile $profile
	 */
	private $profile = null;

	/**
	 * @var Games $games
	 */
	private $games = null;

	/**
	 * @param string $api_key
	 * @param string $steam_id
	 */
	public function __construct($api_key, $steam_id)
	{
		$this->setApiKey($api_key);
		$this->setSteamId($steam_id);
	}

	/**
	 * @param string $api_key
	 */
	private function setApiKey($api_key)
	{
		$this->api_key = $api_key;
	}

	/**
	 * @return string
	 */
	private function getApiKey()
	{
		return $this->api_key;
	}

	/**
	 * @param string $steam_id
	 */
	private function setSteamId($steam_id)
	{
		$this->steam_id = $steam_id;
	}

	/**
	 * @return string
	 */
	private function getSteamId()
	{
		return $this->steam_id;
	}

	/**
	 * @param Profile $profile
	 */
	private function setProfile(Profile $profile)
	{
		$this->profile = $profile;
	}

	/**
	 * @return Profile
	 */
	public function getProfile()
	{
		return $this->profile;
	}

	/**
	 * @param Games $games
	 */
	private function setGames(Games $games)
	{
		$this->games = $games;
	}

	/**
	 * @return Games
	 */
	public function getGames()
	{
		return $this->games;
	}

	/**
	 * Fetches and JSON-decodes a Steam Web API endpoint via the WordPress HTTP API.
	 *
	 * @param string $url
	 * @param array $args
	 * @return \stdClass|null
	 */
	private function fetch($url, $args)
	{
		$response = wp_remote_get(add_query_arg($args, $url), ['timeout' => 3]);

		if (
			is_wp_error($response) ||
			wp_remote_retrieve_response_code($response) !== 200
		) {
			return null;
		}

		$body = json_decode(wp_remote_retrieve_body($response));
		return $body ? $body : null;
	}

	/**
	 * @return bool
	 */
	public function getData()
	{
		$profile_data = $this->fetch($this->url['profile'], [
			'key' => $this->getApiKey(),
			'steamids' => $this->getSteamId(),
			'format' => 'json',
		]);
		$game_data = $this->fetch($this->url['games'], [
			'key' => $this->getApiKey(),
			'steamid' => $this->getSteamId(),
			'include_played_free_games' => 0,
			'include_appinfo' => 1,
			'format' => 'json',
		]);

		if (!$profile_data || !$game_data) {
			return false;
		}

		if (!isset($profile_data->response->players[0])) {
			return false;
		}

		$this->setProfile(new Profile($profile_data->response->players[0]));
		$this->setGames(new Games($game_data->response));
		return true;
	}
}
