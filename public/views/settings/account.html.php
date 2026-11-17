<?php
ob_start();
?>
<h1 class="settings__title">Account settings</h1>
<p class="settings__text">Account settings will appear here.</p>
<?php
$content = (string) ob_get_clean();
$this->partial('settings-layout', ['section' => 'account', 'content' => $content]);
