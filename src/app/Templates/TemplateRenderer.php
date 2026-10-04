<?php

namespace App\Templates;

use RuntimeException;
use Throwable;

class TemplateRenderer
{
    /** @var array<string, callable> Helpers available in every template */
    private array $helpers = [];

    /** @var array<string, mixed> Variables shared with every template */
    private array $shared = [];
    private string $baseDir;

    /**
     * @param string $baseDirName Root directory that contains the templates
     */
    public function __construct(string $baseDirName)
    {
        $this->baseDir = __DIR__ . '/' . rtrim($baseDirName, '/\\');

        // Default helpers
        $this->helpers['e']      = fn(?string $text): string => htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8');
        $this->helpers['slug']   = fn(string $title): string => rawurlencode(str_replace(' ', '_', $title));
        $this->helpers['number'] = fn(int|float $n): string => number_format($n);
        $this->helpers['url']    = fn(array $query): string => http_build_query($query);
    }

    /**
     * Register (or override) a helper available as $name inside templates.
     */
    public function addHelper(string $name, callable $helper): static
    {
        $this->helpers[$name] = $helper;
        return $this;
    }

    /**
     * Share a variable with every rendered template.
     */
    public function share(string $name, mixed $value): static
    {
        $this->shared[$name] = $value;
        return $this;
    }

    /**
     * Render a template and return the output as a string.
     *
     * @param string $template Path relative to the base dir (with or without ".php")
     * @param array  $vars     Variables exposed to the template
     */
    public function render(string $template, array $vars = []): string
    {
        $file = $this->resolve($template);

        // Priority: template vars > shared vars > helpers
        $data = $vars + $this->shared + $this->helpers;

        return $this->include($file, $data);
    }

    /**
     * Render a template and print it directly.
     */
    public function display(string $template, array $vars = []): void
    {
        echo $this->render($template, $vars);
    }

    /**
     * Check whether a template exists.
     */
    public function exists(string $template): bool
    {
        return is_file($this->path($template));
    }

    // ---------------------------------------------------------------
    // Internals
    // ---------------------------------------------------------------

    private function path(string $template): string
    {
        if (!str_ends_with($template, '.php')) {
            $template .= '.php';
        }

        return $this->baseDir . '/' . ltrim($template, '/\\');
    }

    /**
     * Build the full path and make sure it stays inside the base dir.
     */
    private function resolve(string $template): string
    {
        $file = $this->path($template);
        $real = realpath($file);
        $base = realpath($this->baseDir);

        if ($real === false || !is_file($real)) {
            throw new RuntimeException("Template not found: $file");
        }

        // Block path traversal (e.g. "../../config.php")
        if ($base === false || !str_starts_with($real, $base . DIRECTORY_SEPARATOR)) {
            throw new RuntimeException("Template is outside the templates directory: $template");
        }

        return $real;
    }

    /**
     * Include the file in an isolated scope and capture its output.
     * Templates can render partials via $this->render() thanks to the bound closure.
     */
    private function include(string $file, array $data): string
    {
        $level = ob_get_level();

        $run = function (string $__file, array $__data): void {
            extract($__data, EXTR_SKIP);
            include $__file;
        };

        // Bind to $this so templates can call $this->render('partials/row', [...])
        $run = \Closure::bind($run, $this, self::class);

        ob_start();
        try {
            $run($file, $data);
            return (string)ob_get_clean();
        } catch (Throwable $ex) {
            // Clean any buffers opened by the failed template
            while (ob_get_level() > $level) {
                ob_end_clean();
            }
            throw $ex;
        }
    }
}
