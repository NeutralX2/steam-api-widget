<?php

namespace Steam;

/**
 * Class Profile
 * @package Steam
 */
class Profile
{

	/**
	 * @var \stdClass $profile
	 */
	protected $profile = null;

	/**
	 * @constructor
	 * @param \stdClass $object
	 */
	public function __construct(\stdClass $profile)
	{
		$this->profile = $profile;
	}

	#region Private Profile API-Data
	/**
	 * The player's "Real Name", if they have set it.
	 *
	 * @return string
	 */

	public function getRealName()
	{
		return isset($this->profile->realname) ? $this->profile->realname : "";
	}

	/**
	 * The player's primary group, as configured in their steam Community profile.
	 *
	 * @return int
	 */
	public function getPrimaryClanId()
	{
		return isset($this->profile->primaryclanid) ? $this->profile->primaryclanid : -1;
	}

	/**
	 * The time the player's account was created.
	 *
	 * @param string $format
	 * @return string
	 */
	public function getTimeCreated($format)
	{
		return isset($this->profile->timecreated) ? date($format, $this->profile->timecreated) : "";
	}

	/**
	 * If the user is currently in-game,
	 * this value will be returned and set to the gameid of that game.
	 *
	 * @return int
	 */
	public function getGameId()
	{
		return isset($this->profile->gameid) ? $this->profile->gameid : -1;
	}

	/**
	 * Is player currently playing?
	 * @return bool
	 */
	public function isInGame()
	{
		return isset($this->profile->gameid) ? true : false;
	}

	/**
	 * The ip and port of the game server the user is currently playing on,
	 * if they are playing on-line in a game using steam matchmaking.
	 * Otherwise will be set to "0.0.0.0:0".
	 *
	 * @return string
	 */
	public function getGameServerIp()
	{
		return isset($this->profile->gameserverip) ? $this->profile->gameserverip : "";
	}

	/**
	 * If the user is currently in-game, this will be the name of the game they are playing.
	 * This may be the name of a non-steam game shortcut.
	 * @return string
	 */
	public function getGameExtraInfo()
	{
		return isset($this->profile->gameextrainfo) ? $this->profile->gameextrainfo : "";
	}

	/**
	 * If set on the user's steam Community profile,
	 * The user's country of residence, 2-character ISO country code
	 * @return string
	 */
	public function getCountryCode()
	{
		return isset($this->profile->loccountrycode) ? $this->profile->loccountrycode : "";
	}

	#endregion
	#region Public Profile Api-Data
	/**
	 * 64bit SteamID of the user
	 *
	 * @return string
	 */

	public function getSteamId()
	{
		return isset($this->profile->steamid) ? $this->profile->steamid : "";
	}

	/**
	 * The player's persona name (display name)
	 *
	 * @return string
	 */
	public function getPersonaName()
	{
		return isset($this->profile->personaname) ? $this->profile->personaname : "";
	}

	/**
	 * The full URL of the player's steam Community profile.
	 *
	 * @return string
	 */
	public function getProfileUrl()
	{
		return isset($this->profile->profileurl) ? $this->profile->profileurl : "";
	}

	/**
	 * The full URL of the player's 32x32px avatar.
	 * If the user hasn't configured an avatar, this will be the default ? avatar.
	 *
	 * @return string
	 */
	public function getAvatar()
	{
		return isset($this->profile->avatar) ? $this->profile->avatar : "";
	}

	/**
	 * The full URL of the player's 64x64px avatar.
	 * If the user hasn't configured an avatar, this will be the default ? avatar.
	 *
	 * @return string
	 */
	public function getAvatarMedium()
	{
		return isset($this->profile->avatarmedium) ? $this->profile->avatarmedium : "";
	}

	/**
	 * The full URL of the player's 184x184px avatar.
	 * If the user hasn't configured an avatar, this will be the default ? avatar.
	 *
	 * @return string
	 */
	public function getAvatarFull()
	{
		return isset($this->profile->avatarfull) ? $this->profile->avatarfull : "";
	}

	/**
	 * The user's current status.
	 * 0 - Offline,
	 * 1 - Online,
	 * 2 - Busy,
	 * 3 - Away,
	 * 4 - Snooze,
	 * 5 - looking to trade,
	 * 6 - looking to play.
	 * If the player's profile is private, this will always be "0",
	 * except is the user has set his status to looking to trade or looking to play,
	 * because a bug makes those status appear even if the profile is private.
	 * @return string
	 */
	public function getPersonaState()
	{

		if ($this->isInGame()) {
			return "InGame";
		}

		switch ($this->profile->personastate) {
			case 0:
				return "Offline";
				break;
			case 1:
				return "Online";
				break;
			case 2:
				return "Busy";
				break;
			case 3:
				return "Away";
				break;
			case 4:
				return "Snooze";
				break;
			case 5:
				return "LookingToTrade";
				break;
			case 6:
				return "LookingToPlay";
				break;
			default:
				return "Offline";
				break;
		}
	}

	/**
	 * The last time the user was online.
	 *
	 * @param string $format
	 * @return string
	 */
	public function getLastLogOff($format)
	{
		return isset($this->profile->lastlogoff) ? date($format, $this->profile->lastlogoff) : "";
	}

	#endregion
}
