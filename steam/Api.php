<?php

namespace Steam;

require(__DIR__ . '/Games.php');
require(__DIR__ . '/Profile.php');

require(__DIR__ . '/../vendor/Curl/Curl.php');
require(__DIR__ . '/../vendor/Curl/MultiCurl.php');

use Vendor\Curl\MultiCurl;

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
	private $url = array(
		'profile' => 'https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/',
		'games' => 'https://api.steampowered.com/IPlayerService/GetRecentlyPlayedGames/v0001/'
	);

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
	 * @return bool
	 */
	public function getData()
	{
		$multi_curl = new MultiCurl();
		$multi_curl->setOpt(CURLOPT_RETURNTRANSFER, true);
		$multi_curl->setOpt(CURLOPT_FOLLOWLOCATION, false);
		$multi_curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
		$multi_curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);				$multi_curl->setOpt(CURLOPT_TIMEOUT, 3);

		$profile_data = $multi_curl->addGet($this->url['profile'], array(
			'key' => $this->getApiKey(),
			'steamids' => $this->getSteamId(),
			'format' => 'json'
		));
		$game_data = $multi_curl->addGet($this->url['games'], array(
			'key' => $this->getApiKey(),
			'steamid' => $this->getSteamId(),
			'include_played_free_games' => false,
			'include_appinfo' => true,
			'format' => 'json'
		));
		$multi_curl->start();

		if ($profile_data->error || $game_data->error) {
			return false;
		}

		if (!isset($profile_data->response->response->players[0])) {
			return false;
		}

		$this->setProfile(new Profile($profile_data->response->response->players[0]));
		$this->setGames(new Games($game_data->response->response));
		return true;
	}

}
