<?php
ob_start();
?>
<h1 class="settings__title">Privacy settings</h1>
<p class="settings__text">Control who can see the cards in your collection.</p>
<form class="settings-form" data-form="privacy" method="post" action="/api/settings/privacy">
    <fieldset class="privacy-form__fieldset">
        <legend class="privacy-form__legend">Collection visibility</legend>
        <div class="packs__radios">
            <label class="packs__radio">
                <input type="radio" name="collection_visibility" value="public" aria-describedby="collection-visibility-public"<?= $userSettings->getCollectionVisibility() === 'public' ? ' checked' : '' ?>>
                <span class="packs__radio-title">Public</span>
                <span class="packs__radio-desc" id="collection-visibility-public">Anyone can see the cards you own on your public profile.</span>
            </label>
            <label class="packs__radio">
                <input type="radio" name="collection_visibility" value="private" aria-describedby="collection-visibility-private"<?= $userSettings->getCollectionVisibility() === 'private' ? ' checked' : '' ?>>
                <span class="packs__radio-title">Private</span>
                <span class="packs__radio-desc" id="collection-visibility-private">Only you and admins can see your card list. Your rank and stats stay visible on the leaderboard.</span>
            </label>
        </div>
    </fieldset>
    <p class="packs__message" data-status="privacy" role="status" aria-live="polite"></p>
</form>
<p class="privacy-form__profile"><a href="/users/<?= (int) $userId ?>">View my public profile</a></p>
<script type="module" src="/js/settings/privacy.js"></script>
<?php
$content = (string) ob_get_clean();
$this->partial('settings-layout', ['section' => 'privacy', 'content' => $content]);
