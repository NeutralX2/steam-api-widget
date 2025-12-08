<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
?>
<div class="table">
    <div class="row">
		<div class="column" style="padding: 5px;">
			<a href="<?= $profile->getProfileUrl() ?>" target="_blank" >
				<img class="avatar bg <?= $profile->getPersonaState() ?>" src="<?= $profile->getAvatarMedium() ?>" title="<?= $profile->getPersonaName() ?>" />
			</a>
		</div>
		<div class="column" style="padding: 5px;">
			<p>
				<a class="fg <?= $profile->getPersonaState() ?>" href="<?= $profile->getProfileUrl() ?>" target="_blank">
					<?= $profile->getPersonaName() ?> (<?= $profile->getPersonaState() ?>)
				</a>
				<br />
				Since <?= $profile->getTimeCreated('m/d/Y') ?>
			</p>
		</div>
    </div>
</div>
<?php if ($profile->isInGame()) : ?>
	<div class="message">
		<p>I'm currently playing</p>
	</div>
	<div>
		<?php $pgame = $games->getGameByAppId($profile->getGameId()) ?>
		<?php if ($pgame) : ?>
			<a href="<?= $pgame->getLink() ?>" target="_blank" title="<?= $pgame->getName() ?>">
				<img src="<?= $pgame->getHeader() ?>" alt="<?= $pgame->getName() ?>"/>
			</a>
		<?php else : ?>
			<div class="message">
				<p><b><?= $profile->getGameExtraInfo() ?></b></p>
			</div>
		<?php endif; ?>
	</div>
<?php else : ?>
	<div class="message">
		<p>Recently played games:</p>
	</div>
	<div class="table">
		<?php foreach ($games->getRecentPlayedGames($count) as $game) : ?>
			<div class="row">
				<a href="<?= $game->getLink() ?>" target="_blank">
					<div class="column">
						<img src="<?= $game->getImage() ?>" title="<?= $game->getName() ?>" />
					</div>
					<div class="column" style="padding: 0px 5px 0px 5px">
						<p class="fg <?= $profile->getPersonaState() ?>">&ndash;</p>
					</div>
					<div class="column">
						<p class="fg <?= $profile->getPersonaState() ?>"> <?= $game->getName() ?></p>
					</div>
				</a>
			</div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>

