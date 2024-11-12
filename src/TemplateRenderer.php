<?php

namespace App;

use Exception;

/**
 * Class TemplateRenderer
 */
class TemplateRenderer
{
    /**
     * @var string
     */
    private string $templatePath;

    /**
     * @param string $templatePath
     */
    public function __construct(string $templatePath = __DIR__ . '/../templates')
    {
        $this->templatePath = rtrim($templatePath, '/');
    }

    /**
     * @param $template
     * @param array $data
     * @return string
     * @throws Exception
     */
    public function render($template, array $data = []): string
    {
        $file = $this->templatePath . '/' . $template . '.php';


        if (!file_exists($file)) {
            throw new Exception("Template not found: $template");
        }

        extract($data);

        ob_start();
        include $file;
        $content = ob_get_clean();

        if ($content === false) {
            throw new Exception("Error rendering template: $template");
        }

        return $content;
    }
}
