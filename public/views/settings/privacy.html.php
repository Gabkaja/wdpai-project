<?php
ob_start();
?>
<h1 class="settings__title">Privacy settings</h1>
<p class="settings__text">Privacy settings will appear here.</p>
<?php
$content = (string) ob_get_clean();
$this->partial('settings-layout', ['section' => 'privacy', 'content' => $content]);
