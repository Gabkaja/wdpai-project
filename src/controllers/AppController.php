<?php

declare(strict_types=1);

namespace App\Controller;

abstract class AppController
{
    protected function render(string $template, array $variables = [], ?string $layout = 'layout'): void
    {
        $templatePath = $this->viewPath($template);

        extract($variables, EXTR_SKIP);

        if ($layout === null) {
            require $templatePath;

            return;
        }

        ob_start();
        require $templatePath;
        $content = (string) ob_get_clean();

        require $this->viewPath($layout);
    }

    protected function partial(string $name, array $variables = []): void
    {
        $partialPath = $this->viewPath('partials/' . $name);

        extract($variables, EXTR_SKIP);
        require $partialPath;
    }

    private function viewPath(string $name): string
    {
        $path = dirname(__DIR__, 2) . '/public/views/' . $name . '.html.php';

        if (!is_file($path)) {
            throw new \RuntimeException('View not found: ' . $name);
        }

        return $path;
    }
}