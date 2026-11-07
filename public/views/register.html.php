    <main class="auth">
        <section class="auth__panel" aria-labelledby="auth-title">
            <div class="auth__status">
                <span class="auth__status-item auth__status-item--live">
                    <span class="pulse" aria-hidden="true"></span>
                    <span>Live system 0.9.4 // Kick sync active</span>
                </span>
                <span class="auth__status-item">
                    <span class="icon" aria-hidden="true">lock</span>
                    <span>SSL 256-bit secure</span>
                </span>
            </div>

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
                    <a class="auth-form__oauth" href="/auth/kick">
                        <span class="auth-form__kick-mark" aria-hidden="true">K</span>
                        <span>Continue with Kick.com</span>
                        <span class="icon" aria-hidden="true">arrow_forward</span>
                    </a>

                    <p class="auth-form__divider"><span>Or via email</span></p>

                    <div class="auth-form__field">
                        <div class="auth-form__label-row">
                            <label class="auth-form__label" for="reg-username">Username (Kick nickname)</label>
                            <span class="auth-form__hint">Required</span>
                        </div>
                        <div class="auth-form__input-wrap">
                            <span class="icon auth-form__input-icon" aria-hidden="true">person</span>
                            <input class="auth-form__input" type="text" id="reg-username" name="username" placeholder="stream_viewer" required minlength="3" maxlength="30" autocomplete="username">
                        </div>
                    </div>

                    <div class="auth-form__field">
                        <div class="auth-form__label-row">
                            <label class="auth-form__label" for="reg-email">E-mail</label>
                            <span class="auth-form__hint">Required</span>
                        </div>
                        <div class="auth-form__input-wrap">
                            <span class="icon auth-form__input-icon" aria-hidden="true">alternate_email</span>
                            <input class="auth-form__input" type="email" id="reg-email" name="email" placeholder="viewer@example.com" required autocomplete="email">
                        </div>
                    </div>

                    <div class="auth-form__field">
                        <div class="auth-form__label-row">
                            <label class="auth-form__label" for="reg-password">Password</label>
                            <span class="auth-form__hint" id="reg-password-hint">At least 8 characters</span>
                        </div>
                        <div class="auth-form__input-wrap">
                            <span class="icon auth-form__input-icon" aria-hidden="true">key</span>
                            <input class="auth-form__input auth-form__input--trailing" type="password" id="reg-password" name="password" placeholder="CyberSecret123" required minlength="8" autocomplete="new-password" aria-describedby="reg-password-hint">
                            <span class="icon auth-form__input-trailing" aria-hidden="true">visibility</span>
                        </div>
                    </div>

                    <div class="auth-form__field">
                        <div class="auth-form__label-row">
                            <label class="auth-form__label" for="reg-password-repeat">Repeat password</label>
                            <span class="auth-form__hint">Required</span>
                        </div>
                        <div class="auth-form__input-wrap">
                            <span class="icon auth-form__input-icon" aria-hidden="true">key</span>
                            <input class="auth-form__input auth-form__input--trailing" type="password" id="reg-password-repeat" name="password_repeat" placeholder="CyberSecret123" required minlength="8" autocomplete="new-password">
                            <span class="icon auth-form__input-trailing" aria-hidden="true">visibility</span>
                        </div>
                    </div>

                    <button class="button button--primary auth-form__submit" type="submit">
                        <span class="icon" aria-hidden="true">person_add</span>
                        Create account
                    </button>

                    <?php if (!empty($error)): ?>
                        <div class="auth-alert" role="alert">
                            <span class="icon" aria-hidden="true">error</span>
                            <p class="auth-alert__text"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                    <?php endif; ?>
                </form>

                <p class="auth__switch">Already have an account? <a href="/login">Log in</a>.</p>

                <div class="auth-sync">
                    <span class="icon" aria-hidden="true">sync</span>
                    <span>Kick sync active &middot; Live drop ready</span>
                    <span class="badge badge--live">Live drop ready</span>
                </div>
            </div>

            <ul class="auth__meta" role="list">
                <li>TCG verified</li>
                <li>Anti-fraud bot guard</li>
                <li>Kick partner</li>
            </ul>
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
