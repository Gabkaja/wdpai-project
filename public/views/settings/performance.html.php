<?php
ob_start();
?>
<h1 class="settings__title">Performance settings</h1>
<p class="settings__text">Performance settings will appear here.</p>
<?php
$content = (string) ob_get_clean();
$this->partial('settings-layout', ['section' => 'performance', 'content' => $content]);
