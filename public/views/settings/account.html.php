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
<?php $avatarUrl = $avatarUrl ?? null; ?>
<section class="settings-section" aria-labelledby="avatar-heading" data-avatar data-initial="<?= htmlspecialchars(strtoupper(substr($currentUser->getUsername(), 0, 1)), ENT_QUOTES, 'UTF-8') ?>">
    <h2 class="settings-section__title" id="avatar-heading">Avatar</h2>

    <div class="settings-avatar">
        <span class="settings-avatar__preview" data-avatar-preview>
            <?php if ($avatarUrl !== null): ?>
                <img class="settings-avatar__image" src="<?= htmlspecialchars($avatarUrl, ENT_QUOTES, 'UTF-8') ?>" alt="" width="96" height="96">
            <?php else: ?>
                <?= htmlspecialchars(strtoupper(substr($currentUser->getUsername(), 0, 1)), ENT_QUOTES, 'UTF-8') ?>
            <?php endif; ?>
        </span>

        <div class="settings-avatar__controls">
            <label class="packs__label" for="avatar-file">Avatar</label>
            <p class="packs__field-description" id="avatar-hint">JPG, PNG or WebP, up to 2 MB</p>
            <input class="settings-avatar__file" type="file" id="avatar-file" name="avatar" accept="image/jpeg,image/png,image/webp" aria-describedby="avatar-hint">

            <div class="settings-avatar__actions">
                <button class="button button--primary" type="button" data-avatar-upload>Upload</button>
                <button class="button button--secondary" type="button" data-avatar-remove<?= $avatarUrl === null ? ' hidden' : '' ?>>Remove</button>
            </div>

            <p class="packs__message" data-status="avatar" role="status" aria-live="polite"></p>
        </div>
    </div>
</section>

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
<script type="module" src="/js/settings/avatar.js"></script>
<script type="module" src="/js/settings/kick.js"></script>
<script type="module" src="/js/settings/password.js"></script>
<?php
$content = (string) ob_get_clean();
$this->partial('settings-layout', ['section' => 'account', 'content' => $content]);
