<?php
if (isset($currentUser) && $currentUser !== null): ?>
    <div class="user-menu">
        <button type="button" class="user-menu__toggle" aria-haspopup="true" aria-expanded="false" aria-controls="user-menu-panel">
            <span class="user-menu__avatar" aria-hidden="true"><?= htmlspecialchars(strtoupper(substr($currentUser->getUsername(), 0, 1))) ?></span>
            <span class="user-menu__name"><?= htmlspecialchars($currentUser->getUsername()) ?></span>
        </button>
        <ul class="user-menu__panel" id="user-menu-panel" role="menu">
            <li class="user-menu__item"><a class="user-menu__link" href="/profile" role="menuitem">Profile</a></li>
            <li class="user-menu__item"><a class="user-menu__link" href="/settings/account" role="menuitem">Settings</a></li>
            <?php if ($currentUser->getRole() === 'admin'): ?>
                <li class="user-menu__item"><a class="user-menu__link" href="/admin" role="menuitem">Admin</a></li>
            <?php endif; ?>
            <li class="user-menu__item">
                <form class="user-menu__logout" method="post" action="/logout">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\App\Session::csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
                    <button class="user-menu__link user-menu__link--logout" type="submit" role="menuitem">Logout</button>
                </form>
            </li>
        </ul>
    </div>
<?php else: ?>
    <a class="button button--primary header-tools__login" href="/login">
        <span class="icon" aria-hidden="true">login</span>
        Log in
    </a>
    <a class="button button--primary header-tools__register" href="/register">
        <span class="icon" aria-hidden="true">person_add</span>
        Register
    </a>
<?php endif; ?>
