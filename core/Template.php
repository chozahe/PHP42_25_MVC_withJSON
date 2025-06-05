<?php

declare(strict_types=1);

namespace app\core;

class Template
{
    private static array $blocks = [];
    private static $cache_path = PROJECT_ROOT . "/cache/";
    private static $template_path = PROJECT_ROOT . "/views/";

    private static bool $cache_enabled = false;

    static function View($file, $data = []): void
    {
        $cached_file = self::Cache($file);
        extract($data);
        require $cached_file;
    }

    private static function CompilePHP(string $code): string
    {
        return preg_replace('~\{%\s*(.+?)\s*%}~is', '<?php $1 ?>', $code);
    }

    private static function CompileEchos(string $code): string
    {
        return preg_replace('~\{\{\s*(.+?)\s*}}~is', '<?= $1 ?>', $code);
    }

    private static function CompileEscapedEchos(string $code): string
    {
        return preg_replace('~\{\{\{\s*(.+?)\s*}}}~is', '<?php echo htmlentities($1, ENT_QUOTES, \'UTF-8\') ?>', $code);
    }

    private static function CompileBlock(string $code): string
    {
        preg_match_all('~\{% ?block ?(.*?) ?%}(.*?)\{% ?endblock ?%}~is', $code, $matches, PREG_SET_ORDER);
        foreach ($matches as $value) {
            if (!array_key_exists($value[1], self::$blocks)) {
                self::$blocks[$value[1]] = '';
            }
            if (!str_contains($value[2], '@parent')) {
                self::$blocks[$value[1]] = $value[2];
            } else {
                self::$blocks[$value[1]] = str_replace('@parent', self::$blocks[$value[1]], $value[2]);
            }
            $code = str_replace($value[0], '', $code);
        }
        return $code;
    }

    private static function CompileYield($code): string
    {
        foreach (self::$blocks as $block => $value) {
            $code = preg_replace('~\{% ?yield ?' . $block . ' ?%}~', $value, $code);
        }
        preg_replace('~\{% ?yield ?(.*?) ?%}~i', '', $code);
        return $code;
    }

    private static function CompileCode($code): string
    {
        $code = self::CompileBlock($code);
        $code = self::CompileYield($code);
        $code = self::CompileEscapedEchos($code);
        $code = self::CompileEchos($code);
        $code = self::CompilePHP($code);
        return $code;
    }

    private static function IncludeFiles(string $file): string
    {
        $code = file_get_contents(self::$template_path . $file);
        if (!$code) return "";
        preg_match_all('~\{% ?(extends|include) ?\'?(.*?)\'? ?%}~i', $code, $matches, PREG_SET_ORDER);
        foreach ($matches as $value) {
            $code = str_replace($value[0], self::IncludeFiles($value[2]), $code);
        }
        return preg_replace('~\{% ?(extends|include) ?\'?(.*?)\'? ?%}~i', '', $code);
    }

    private static function Cache(string $file): string
    {
        if (!file_exists(self::$cache_path)) {
            mkdir(self::$cache_path, 0744);
        }
        $cached_file = self::$cache_path . str_replace(['/', '.html'], ['_', ''], $file) . '.php';
        if (!self::$cache_enabled || !file_exists($cached_file) || filemtime($cached_file) < filemtime($file)) {
            $code = self::IncludeFiles($file);
            $code = self::CompileCode($code);
            file_put_contents($cached_file, '<?php class_exists(\'' . __CLASS__ . '\') or exit; ?>' . PHP_EOL . $code);
        }
        return $cached_file;
    }

    public static function ClearCache(): void {
        foreach (glob(self::$cache_path."*") as $file) {
            unlink($file);
        }
    }
    
}