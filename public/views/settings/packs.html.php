<?php
ob_start();
?>
<h1 class="settings__title">Pack settings</h1>
<p class="settings__text">Pack settings will appear here.</p>
<?php
$content = (string) ob_get_clean();
$this->partial('settings-layout', ['section' => 'packs', 'content' => $content]);
