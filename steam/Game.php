<?php

namespace Steam;

/**
 * Class Game
 * @package Steam
 */
class Game
{

	/**
	 * @var \stdClass $game
	 */
	protected $game = null;

	/**
	 * @constructor
	 * @param \stdClass $game
	 */
	public function __construct(\stdClass $game)
	{
		$this->game = $game;
	}

	/**
	 * Unique identifier for the game.
	 *
	 * @return int
	 */
	public function getAppId()
	{
		return isset($this->game->appid) ? $this->game->appid : 0;
	}

	/**
	 * The name of the game.
	 *
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->game->name = $name;
	}

	/**
	 * The name of the game.
	 *
	 * @return string
	 */
	public function getName()
	{
		return isset($this->game->name) ? $this->game->name : "";
	}

	/**
	 * The total number of minutes played in the last two weeks.
	 *
	 * @return int
	 */
	public function getPlayTimeTwoWeeks()
	{
		return isset($this->game->playtime_2weeks) ? $this->game->playtime_2weeks : 0;
	}

	/**
	 * The total number of minutes played "on record",
	 * since steam began tracking total playtime in early 2009.
	 *
	 * @return int
	 */
	public function getPlayTimeForever()
	{
		return isset($this->game->playtime_forever) ? $this->game->playtime_forever : 0;
	}

	/**
	 * these are the filenames of various images for the game.
	 * To construct the URL to the image,
	 * use this format: http://media.steampowered.com/steamcommunity/public/images/apps/{appid}/{hash}.jpg.
	 *
	 * @return string
	 */
	public function getImgIconUrl()
	{
		return isset($this->game->img_icon_url) ? $this->game->img_icon_url : "";
	}

	/**
	 * these are the filenames of various images for the game.
	 * To construct the URL to the image,
	 * use this format: http://media.steampowered.com/steamcommunity/public/images/apps/{appid}/{hash}.jpg.
	 *
	 * @return string
	 */
	public function getImgLogoUrl()
	{
		return isset($this->game->img_logo_url) ? $this->game->img_logo_url : "";
	}

	/**
	 * indicates there is a stats page with achievements or other game stats available for this game.
	 *
	 * @return bool
	 */
	public function hasCommunityVisibleStats()
	{
		return isset($this->game->has_community_visible_stats) ? $this->game->has_community_visible_stats : false;
	}

	/**
	 * @return string
	 */
	public function getLink()
	{
		//return "http://steamcommunity.com/app/{$this->getAppId()}";
		return "https://store.steampowered.com/app/{$this->getAppId()}";
	}

	/**
	 * @return string
	 */
	public function getHeader()
	{
		return "https://cdn.cloudflare.steamstatic.com/steam/apps/{$this->getAppId()}/header.jpg";
	}

	/**
	 * @return string
	 */
	public function getImage()
	{
		return "https://cdn.cloudflare.steamstatic.com/steamcommunity/public/images/apps/{$this->getAppId()}/{$this->getImgIconUrl()}.jpg";
	}
}

