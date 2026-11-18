<?php
ob_start();
?>
<h1 class="settings__title">Pack opening settings</h1>
<form class="settings-form packs__form" data-form="settings" method="post" action="/api/settings/packs">
    <div class="packs__field packs__field--radios">
        <span class="packs__label">Reveal mode</span>
        <p class="packs__field-description">How cards are revealed after opening.</p>
        <div class="packs__radios" role="radiogroup" aria-label="Reveal mode">
            <label class="packs__radio">
                <input type="radio" name="reveal_mode" value="auto"<?= $userSettings->getRevealMode() === 'auto' ? ' checked' : '' ?>>
                <span class="packs__radio-title">Auto</span>
                <span class="packs__radio-desc">Cards reveal one by one</span>
            </label>
            <label class="packs__radio">
                <input type="radio" name="reveal_mode" value="manual"<?= $userSettings->getRevealMode() === 'manual' ? ' checked' : '' ?>>
                <span class="packs__radio-title">Manual</span>
                <span class="packs__radio-desc">You click each card, in any order</span>
            </label>
        </div>
    </div>

    <div class="packs__field packs__field--radios">
        <span class="packs__label">Multi-open mode</span>
        <p class="packs__field-description">What happens when you open several packs.</p>
        <div class="packs__radios" role="radiogroup" aria-label="Multi-open mode">
            <label class="packs__radio">
                <input type="radio" name="multi_open_mode" value="queue"<?= $userSettings->getMultiOpenMode() === 'queue' ? ' checked' : '' ?>>
                <span class="packs__radio-title">Queue</span>
                <span class="packs__radio-desc">Openings reveal one after another</span>
            </label>
            <label class="packs__radio">
                <input type="radio" name="multi_open_mode" value="all_at_once"<?= $userSettings->getMultiOpenMode() === 'all_at_once' ? ' checked' : '' ?>>
                <span class="packs__radio-title">All at once</span>
                <span class="packs__radio-desc">All cards reveal at once</span>
            </label>
        </div>
    </div>

    <div class="packs__field">
        <label class="packs__label" for="packs-settings-sound">Sound</label>
        <p class="packs__field-description">Play sounds while revealing cards.</p>
        <input class="packs__switch" id="packs-settings-sound" name="sound_enabled" type="checkbox"<?= $userSettings->isSoundEnabled() ? ' checked' : '' ?>>
    </div>

    <div class="packs__field">
        <label class="packs__label" for="packs-settings-volume">Volume</label>
        <p class="packs__field-description">Reveal sound volume.</p>
        <div class="packs__range">
            <input class="packs__range-input" id="packs-settings-volume" name="sound_volume" type="range" min="0" max="100" value="<?= (int) $userSettings->getSoundVolume() ?>">
            <output class="packs__range-output" for="packs-settings-volume"><?= (int) $userSettings->getSoundVolume() ?></output>
        </div>
    </div>

    <button class="button button--primary" type="submit">Save settings</button>
    <p class="packs__message" data-status="settings" role="status" aria-live="polite"></p>
</form>
<script type="module" src="/js/settings/pack-settings.js"></script>
<?php
$content = (string) ob_get_clean();
$this->partial('settings-layout', ['section' => 'packs', 'content' => $content]);
