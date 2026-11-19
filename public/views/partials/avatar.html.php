<?php
$username = (string) ($username ?? '');
$avatarPath = $avatarPath ?? null;
$avatarUrl = $avatarUrl ?? null;
$size = in_array(($size ?? 'md'), ['sm', 'md', 'lg'], true) ? $size : 'md';
$preview = $preview ?? false;
$initial = strtoupper(substr($username, 0, 1));
$pixels = ['sm' => 32, 'md' => 96, 'lg' => 128][$size];

if ($avatarUrl === null && is_string($avatarPath) && $avatarPath !== '') {
    $avatarUrl = '/' . ltrim($avatarPath, '/');
}
?>
<span class="avatar avatar--<?= $size ?>" aria-hidden="true"<?= $preview ? ' data-avatar-preview' : '' ?>>
    <?php if ($avatarUrl !== null): ?>
        <img class="avatar__image" src="<?= htmlspecialchars($avatarUrl, ENT_QUOTES, 'UTF-8') ?>" alt="" width="<?= $pixels ?>" height="<?= $pixels ?>" loading="lazy" decoding="async">
    <?php else: ?>
        <?= htmlspecialchars($initial, ENT_QUOTES, 'UTF-8') ?>
    <?php endif; ?>
</span>
