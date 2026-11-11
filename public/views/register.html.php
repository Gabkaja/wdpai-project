    <main class="auth">
        <section class="auth__panel" aria-labelledby="auth-title">
            <div class="auth__body">
                <span class="auth__logo">
                    <span class="icon" aria-hidden="true">playing_cards</span>
                </span>

                <h1 class="auth__title" id="auth-title">StreamRip Access</h1>
                <p class="auth__subtitle">Log in or create an account to collect live TCG drop cards on Kick.</p>

                <nav class="auth__tabs" aria-label="Authentication">
                    <a class="auth__tab" href="/login">
                        <span class="icon" aria-hidden="true">login</span>
                        Log in
                    </a>
                    <a class="auth__tab auth__tab--active" href="/register" aria-current="page">
                        <span class="icon" aria-hidden="true">person_add</span>
                        Create account
                    </a>
                </nav>

                <h2 class="auth__heading">Create account</h2>

                <form class="auth-form" method="post" action="/register">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\App\Session::csrfToken(), ENT_QUOTES, 'UTF-8') ?>">

                    <?php if (!empty($errors['__global'])): ?>
                        <div class="auth-alert" role="alert">
                            <span class="icon" aria-hidden="true">error</span>
                            <p class="auth-alert__text"><?= htmlspecialchars($errors['__global'], ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                    <?php endif; ?>

                    <div class="auth-form__field<?= isset($errors['username']) ? ' auth-form__field--error' : '' ?>">
                        <div class="auth-form__label-row">
                            <label class="auth-form__label" for="reg-username">Username (Kick nickname)</label>
                            <span class="auth-form__hint">Required</span>
                        </div>
                        <div class="auth-form__input-wrap">
                            <span class="icon auth-form__input-icon" aria-hidden="true">person</span>
                            <input class="auth-form__input" type="text" id="reg-username" name="username" placeholder="stream_viewer" required minlength="3" maxlength="50" autocomplete="username" value="<?= isset($old['username']) ? htmlspecialchars($old['username'], ENT_QUOTES, 'UTF-8') : '' ?>" aria-describedby="reg-username-hint<?= isset($errors['username']) ? ' reg-username-error' : '' ?>">
                            <?php if (isset($errors['username'])): ?>
                                <span id="reg-username-error" class="auth-form__error" role="alert"><?= htmlspecialchars($errors['username'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="auth-form__field<?= isset($errors['email']) ? ' auth-form__field--error' : '' ?>">
                        <div class="auth-form__label-row">
                            <label class="auth-form__label" for="reg-email">E-mail</label>
                            <span class="auth-form__hint">Required</span>
                        </div>
                        <div class="auth-form__input-wrap">
                            <span class="icon auth-form__input-icon" aria-hidden="true">alternate_email</span>
                            <input class="auth-form__input" type="email" id="reg-email" name="email" placeholder="viewer@example.com" required autocomplete="email" value="<?= isset($old['email']) ? htmlspecialchars($old['email'], ENT_QUOTES, 'UTF-8') : '' ?>" aria-describedby="reg-email-hint<?= isset($errors['email']) ? ' reg-email-error' : '' ?>">
                            <?php if (isset($errors['email'])): ?>
                                <span id="reg-email-error" class="auth-form__error" role="alert"><?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="auth-form__field<?= isset($errors['password']) ? ' auth-form__field--error' : '' ?>">
                        <div class="auth-form__label-row">
                            <label class="auth-form__label" for="reg-password">Password</label>
                            <span class="auth-form__hint" id="reg-password-hint">At least 8 characters</span>
                        </div>
                        <div class="auth-form__input-wrap">
                            <span class="icon auth-form__input-icon" aria-hidden="true">key</span>
                            <input class="auth-form__input auth-form__input--trailing" type="password" id="reg-password" name="password" placeholder="CyberSecret123" required minlength="8" autocomplete="new-password" aria-describedby="reg-password-hint<?= isset($errors['password']) ? ' reg-password-error' : '' ?>">
                            <?php if (isset($errors['password'])): ?>
                                <span id="reg-password-error" class="auth-form__error" role="alert"><?= htmlspecialchars($errors['password'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                            <span class="icon auth-form__input-trailing" aria-hidden="true">visibility</span>
                        </div>
                    </div>

                    <div class="auth-form__field<?= isset($errors['password_repeat']) ? ' auth-form__field--error' : '' ?>">
                        <div class="auth-form__label-row">
                            <label class="auth-form__label" for="reg-password-repeat">Repeat password</label>
                            <span class="auth-form__hint">Required</span>
                        </div>
                        <div class="auth-form__input-wrap">
                            <span class="icon auth-form__input-icon" aria-hidden="true">key</span>
                            <input class="auth-form__input auth-form__input--trailing" type="password" id="reg-password-repeat" name="password_repeat" placeholder="CyberSecret123" required minlength="8" autocomplete="new-password" aria-describedby="reg-password-repeat-hint<?= isset($errors['password_repeat']) ? ' reg-password-repeat-error' : '' ?>">
                            <?php if (isset($errors['password_repeat'])): ?>
                                <span id="reg-password-repeat-error" class="auth-form__error" role="alert"><?= htmlspecialchars($errors['password_repeat'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                            <span class="icon auth-form__input-trailing" aria-hidden="true">visibility</span>
                        </div>
                    </div>

                    <button class="button button--primary auth-form__submit" type="submit">
                        <span class="icon" aria-hidden="true">person_add</span>
                        Create account
                    </button>

                    <p class="auth__switch">Already have an account? <a href="/login">Log in</a>.</p>
                </form>

            </div>

        </section>

        <aside class="auth-drop" aria-label="Latest top drop">
            <span class="auth-drop__icon">
                <span class="icon" aria-hidden="true">redeem</span>
            </span>
            <div class="auth-drop__content">
                <span class="auth-drop__label">Latest top drop</span>
                <p class="auth-drop__title">Holo Charizard VMAX #074 <span>(Value: $350)</span></p>
            </div>
            <span class="auth-drop__badge">Pull 2m ago</span>
        </aside>
    </main>
