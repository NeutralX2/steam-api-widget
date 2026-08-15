<?php

namespace Steam;

defined('ABSPATH') || exit;

require __DIR__ . '/Game.php';

/**
 * Class Games
 * @package Steam
 */

class Games
{
	/**
	 * @var array $games
	 */

	protected $games = [];

	/**
	 * @constructor
	 * @param \stdClass $object
	 */

	public function __construct(\stdClass $object)
	{
		if (
			isset($object->games) &&
			(is_array($object->games) || is_object($object->games))
		) {
			foreach ($object->games as $game) {
				$this->games[] = new Game($game);
			}
		}
	}

	/**
	 * the total number of games the user owns
	 * (including free games they've played, if include_played_free_games was passed).
	 *
	 * @return int
	 */

	public function getGameCount()
	{
		return count($this->games);
	}

	/**
	 * the total number of not played games.
	 *
	 * @return int
	 */

	public function getGamesNotPlayedCount()
	{
		$count = 0;

		foreach ($this->games as $game) {
			if ($game->getPlayTimeForever() <= 0) {
				$count++;
			}
		}

		return $count;
	}

	/**
	 * the total number of recent played games.
	 *
	 * @return int
	 */

	public function getRecentPlayedGamesCount()
	{
		$count = 0;

		foreach ($this->games as $game) {
			if ($game->getPlayTimeForever() <= 0) {
				continue;
			}

			if ($game->getPlayTimeTwoWeeks() <= 0) {
				continue;
			}

			$count++;
		}

		return $count;
	}

	/**
	 *
	 * @param int $app_id
	 * @return Game|null
	 */

	public function getGameByAppId($app_id)
	{
		foreach ($this->games as $game) {
			if ($game->getAppId() == $app_id) {
				return $game;
			}
		}

		return null;
	}

	/**
	 * @param $max
	 * @return array
	 */

	public function getRecentPlayedGames($max)
	{
		$recent_played_games = [];

		$count = 0;

		foreach ($this->games as $game) {
			if ($game->getPlayTimeForever() <= 0) {
				continue;
			}

			if ($game->getPlayTimeTwoWeeks() <= 0) {
				continue;
			}

			$name = $game->getName();

			if (strlen($name) > 32) {
				$game->setName(substr($name, 0, 29) . '...');
			}

			$recent_played_games[] = $game;

			$count++;
		}

		usort($recent_played_games, [$this, 'playTimeTwoWeeksSort']);

		if ($max > $count || $max < 0) {
			$max = $count;
		}

		return array_slice($recent_played_games, 0, $max);
	}

	/**
	 * @return float
	 */

	public function getGamesNotPlayedPercentage()
	{
		if ($this->getGameCount()) {
			return round(
				($this->getGamesNotPlayedCount() / $this->getGameCount()) * 100
			);
		}

		return 0.0;
	}

	/**
	 * @param Game $a
	 * @param Game $b
	 * @return int
	 */

	private function playTimeTwoWeeksSort(Game $a, Game $b)
	{
		if ($a->getPlayTimeTwoWeeks() == $b->getPlayTimeTwoWeeks()) {
			return 0;
		}

		return $a->getPlayTimeTwoWeeks() > $b->getPlayTimeTwoWeeks() ? -1 : 1;
	}
}
