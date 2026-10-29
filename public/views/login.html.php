<?php $this->partial('header', ['pageTitle' => $pageTitle]); ?>

    <main>
        <h1>Log in</h1>

        <form method="post" action="/login">
            <div>
                <label for="login-email">E-mail</label>
                <input type="email" id="login-email" name="email" required autocomplete="email">
            </div>

            <div>
                <label for="login-password">Password</label>
                <input type="password" id="login-password" name="password" required autocomplete="current-password">
            </div>

            <div>
                <input type="checkbox" id="login-remember" name="remember">
                <label for="login-remember">Remember me</label>
            </div>

            <button type="submit">Log in</button>
        </form>

        <p>No account yet? <a href="/register">Create one</a>.</p>
    </main>

<?php $this->partial('footer'); ?>