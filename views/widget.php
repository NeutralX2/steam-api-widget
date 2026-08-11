<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
$fallback_icon = esc_js( esc_url( WP_PLUGIN_URL . '/' . PLUGIN_SLUG . '/assets/img/no-icon.svg' ) );
?>
<div class="table">
    <div class="row">
		<div class="column" style="padding: 5px;">
			<a href="<?= esc_url($profile->getProfileUrl()) ?>" target="_blank" >
				<img class="avatar bg <?= esc_attr($profile->getPersonaState()) ?>" src="<?= esc_url($profile->getAvatarMedium()) ?>" title="<?= esc_attr($profile->getPersonaName()) ?>" />
			</a>
		</div>
		<div class="column" style="padding: 5px;">
			<p>
				<a class="fg <?= esc_attr($profile->getPersonaState()) ?>" href="<?= esc_url($profile->getProfileUrl()) ?>" target="_blank">
					<?= esc_html($profile->getPersonaName()) ?> (<?= esc_html($profile->getPersonaState()) ?>)
				</a>
				<br />
				Since <?= esc_html($profile->getTimeCreated('m/d/Y')) ?>
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
			<a href="<?= esc_url($pgame->getLink()) ?>" target="_blank" title="<?= esc_attr($pgame->getName()) ?>">
				<img src="<?= esc_url($pgame->getHeader()) ?>" alt="<?= esc_attr($pgame->getName()) ?>"/>
			</a>
		<?php else : ?>
			<div class="message">
				<p><b><?= esc_html($profile->getGameExtraInfo()) ?></b></p>
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
				<a href="<?= esc_url($game->getLink()) ?>" target="_blank">
					<div class="column">
						<img class="icon" src="<?= esc_url($game->getImage()) ?>" title="<?= esc_attr($game->getName()) ?>" onerror="this.onerror=null;this.src='<?= $fallback_icon ?>';" />
					</div>
					<div class="column" style="padding: 0px 5px 0px 5px">
						<p class="fg <?= esc_attr($profile->getPersonaState()) ?>">&ndash;</p>
					</div>
					<div class="column">
						<p class="fg <?= esc_attr($profile->getPersonaState()) ?>"> <?= esc_html($game->getName()) ?></p>
					</div>
				</a>
			</div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
