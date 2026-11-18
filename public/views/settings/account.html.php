<?php
ob_start();
?>
<h1 class="settings__title">Account settings</h1>
<section class="settings-section" aria-labelledby="password-heading">
    <h2 class="settings-section__title" id="password-heading">Change password</h2>
    <p class="settings__text">Choose a new password for your account.</p>

    <form class="settings-form" data-form="password" method="post" action="/api/settings/password">
        <div class="packs__field">
            <label class="packs__label" for="current-password">Current password</label>
            <input type="password" id="current-password" name="current_password" autocomplete="current-password" required>
        </div>

        <div class="packs__field">
            <label class="packs__label" for="new-password">New password</label>
            <p class="packs__field-description" id="new-password-hint">At least 8 characters</p>
            <input type="password" id="new-password" name="new_password" autocomplete="new-password" required minlength="8" aria-describedby="new-password-hint">
        </div>

        <div class="packs__field">
            <label class="packs__label" for="new-password-confirm">Confirm new password</label>
            <input type="password" id="new-password-confirm" name="new_password_confirm" autocomplete="new-password" required minlength="8">
        </div>

        <button class="button button--primary" type="submit">Change password</button>
        <p class="packs__message" data-status="password" role="status" aria-live="polite"></p>
    </form>
</section>
<script type="module" src="/js/settings/password.js"></script>
<?php
$content = (string) ob_get_clean();
$this->partial('settings-layout', ['section' => 'account', 'content' => $content]);
