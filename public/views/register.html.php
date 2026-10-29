<?php $this->partial('header', ['pageTitle' => $pageTitle]); ?>

    <main>
        <h1>Create account</h1>

        <form method="post" action="/register">
            <div>
                <label for="reg-username">Username (your Kick nickname)</label>
                <input type="text" id="reg-username" name="username" required minlength="3" maxlength="30" autocomplete="username">
            </div>

            <div>
                <label for="reg-email">E-mail</label>
                <input type="email" id="reg-email" name="email" required autocomplete="email">
            </div>

            <div>
                <label for="reg-password">Password</label>
                <input type="password" id="reg-password" name="password" required minlength="8" autocomplete="new-password" aria-describedby="reg-password-hint">
                <small id="reg-password-hint">At least 8 characters.</small>
            </div>

            <div>
                <label for="reg-password-repeat">Repeat password</label>
                <input type="password" id="reg-password-repeat" name="password_repeat" required minlength="8" autocomplete="new-password">
            </div>

            <button type="submit">Create account</button>
        </form>

        <p>Already have an account? <a href="/login">Log in</a>.</p>
    </main>

<?php $this->partial('footer'); ?>