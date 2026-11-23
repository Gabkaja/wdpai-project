<?php
ob_start();
?>
<h1 class="settings__title">Performance settings</h1>
<p class="settings__text">Reduce visual effects on slower devices.</p>
<form class="settings-form" data-form="performance" method="post" action="/api/settings/performance">
    <fieldset class="performance-form__fieldset">
        <legend class="performance-form__legend">Performance</legend>
        <div class="performance-switch">
            <input class="performance-switch__input" id="settings-lite-mode" name="lite_mode" type="checkbox" role="switch" aria-describedby="settings-lite-mode-desc"<?= $userSettings->isLiteMode() ? ' checked' : '' ?>>
            <div class="performance-switch__text">
                <label class="performance-switch__title" for="settings-lite-mode">Lite mode</label>
                <p class="performance-switch__desc" id="settings-lite-mode-desc">Turns off glows, blur and animations, opens packs without animation and loads 12 items per page instead of 24. Useful on slower devices.</p>
            </div>
        </div>
    </fieldset>
    <p class="packs__message" data-status="performance" role="status" aria-live="polite"></p>
</form>
<script type="module" src="/js/settings/performance.js"></script>
<?php
$content = (string) ob_get_clean();
$this->partial('settings-layout', ['section' => 'performance', 'content' => $content]);
