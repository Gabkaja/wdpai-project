<?php
ob_start();
?>
<h1 class="settings__title">Account settings</h1>
<?php $flash = $flash ?? null; ?>
<?php if ($flash !== null): ?>
    <div class="auth-alert<?= $flash['type'] === 'error' ? ' auth-alert--error' : '' ?>" role="alert">
        <span class="icon" aria-hidden="true"><?= $flash['type'] === 'error' ? 'error' : 'check_circle' ?></span>
        <p class="auth-alert__text"><?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?></p>
    </div>
<?php endif; ?>
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

<section class="settings-section" aria-labelledby="kick-heading" data-kick>
    <h2 class="settings-section__title" id="kick-heading">Kick</h2>

    <div class="settings-kick">
        <p class="settings__text" data-kick-status>
            <?php if ($kickAccount !== null): ?>
                Connected as <strong data-kick-username><?= htmlspecialchars($kickAccount['kick_username'], ENT_QUOTES, 'UTF-8') ?></strong>.
            <?php else: ?>
                Not connected.
            <?php endif; ?>
        </p>

        <div class="settings-kick__actions" data-kick-actions>
            <?php if ($kickAccount !== null): ?>
                <button class="button button--secondary" type="button" data-kick-disconnect>Disconnect Kick</button>
            <?php else: ?>
                <a class="button button--primary" href="/profile/kick/connect">Connect Kick</a>
            <?php endif; ?>
        </div>

        <p class="packs__message" data-status="kick" role="status" aria-live="polite"></p>
    </div>
</section>
<script type="module" src="/js/settings/kick.js"></script>
<script type="module" src="/js/settings/password.js"></script>
<?php
$content = (string) ob_get_clean();
$this->partial('settings-layout', ['section' => 'account', 'content' => $content]);
