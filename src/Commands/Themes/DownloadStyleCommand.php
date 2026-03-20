<?php

/**
 * JUZAWEB CMS - Laravel CMS for Your Project
 *
 * @author     The Anh Dang
 *
 * @link       https://cms.juzaweb.com
 */

namespace Juzaweb\DevTool\Commands\Themes;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Juzaweb\HtmlDom\HtmlDom;
use Juzaweb\Modules\Core\Themes\Theme;
use Symfony\Component\Console\Input\InputArgument;

class DownloadStyleCommand extends DownloadCommand
{
    protected $name = 'theme:download-style';

    protected array $data;

    protected ?Theme $theme;

    public function handle(): void
    {
        $this->theme = \Juzaweb\Modules\Core\Facades\Theme::find($this->argument('theme'));
        if ($this->theme === null) {
            $this->error("Theme {$this->argument('theme')} not found!");

            return;
        }

        $this->sendAsks();

        $html = $this->curlGet($this->data['url']);

        $domp = str_get_html($html);

        $css = $this->downloadCss($domp);

        $js = $this->downloadJs($domp);

        $this->generateMixFile($css, $js);
    }

    protected function generateMixFile(array $css, array $js): void
    {
        $mixOutput = 'themes/'.$this->theme->name().'/assets';

        $cssList = array_map(
            fn ($item) => "basePath + '/css/".basename(trim($item, "'"))."'",
            $css
        );

        $jsList = array_map(
            fn ($item) => "basePath + '/js/".basename(trim($item, "'"))."'",
            $js
        );

        $mix = "let mix = require('laravel-mix');
let path = require('path');

require('laravel-mix-merge-manifest');

mix.disableNotifications();
mix.version();

mix.options({
    postCss: [
        require('postcss-discard-comments') ({removeAll: true})
    ],
    terser: {extractComments: false}
});

const basePath = path.relative(process.cwd(), __dirname);
const publishPath = basePath + '/public';
mix.setPublicPath(publishPath);

mix.styles([
    ".implode(",\n    ", $cssList)."
], publishPath + '/css/main.min.css');

mix.combine([
    ".implode(",\n    ", $jsList)."
], publishPath + '/js/main.min.js');";

        File::put(base_path("{$mixOutput}/webpack.mix.js"), $mix);

        $this->info("-- Generated {$mixOutput}/webpack.mix.js");
    }

    protected function sendAsks(): void
    {
        $this->data['url'] = $this->ask(
            'Url Template?',
            $this->getDataDefault('url')
        );

        $this->setDataDefault('url', $this->data['url']);
    }

    protected function downloadCss(HtmlDom $domp): array
    {
        $result = [];
        $output = "themes/{$this->theme->name()}/assets";

        foreach ($domp->find('link[rel="stylesheet"]') as $e) {
            $href = $e->href;
            $href = $this->parseHref($href);

            if ($this->isExcludeDomain($href)) {
                continue;
            }

            $name = explode('?', basename($href))[0];

            $path = "{$output}/css/{$name}";

            try {
                $content = $this->getFileContent($href);
                $content = $this->replaceContentCss($content);
                File::put(base_path($path), $content);

                $result[] = "'{$path}'";

                $this->downloadAssetsFromCssWithContent($content, $href);

                $this->info("-- Downloaded file {$path}");
            } catch (\Throwable $e) {
                $this->warn("Failed to download file: {$href}");
            }
        }

        return $result;
    }

    protected function downloadJs(HtmlDom $domp): array
    {
        $result = [];
        $output = "themes/{$this->theme->name()}/assets";

        foreach ($domp->find('script') as $e) {
            $href = $e->src;
            if (empty($href)) {
                continue;
            }

            $href = $this->parseHref($href);

            $this->info("-- Download file {$href}");

            if ($this->isExcludeDomain($href)) {
                continue;
            }

            $name = explode('?', basename($href))[0];

            $path = "{$output}/js/{$name}";

            try {
                $content = $this->getFileContent($href);
                File::put(base_path($path), $content);
                $result[] = "'{$path}'";
                $this->info("-- Downloaded file {$path}");
            } catch (\Throwable $e) {
                $this->warn("Download error: {$href}");
            }
        }

        return $result;
    }

    protected function downloadAssetsFromCssWithContent(string $content, string $cssUrl): void
    {
        $output = "themes/{$this->theme->name()}/assets";
        preg_match_all('/url\([\'"]?(.*?)[\'"]?\)/i', $content, $matches);

        foreach ($matches[1] as $assetUrl) {
            $assetUrl = trim($assetUrl, "\"'");
            if (is_url($assetUrl) && $this->isExcludeDomain($assetUrl)) {
                $this->warn("Skip {$assetUrl}");

                continue;
            }

            if (str_starts_with($assetUrl, 'data:')) {
                continue;
            }

            $parsedUrl = get_full_url($assetUrl, $cssUrl);
            if ($this->isExcludeDomain($parsedUrl)) {
                continue;
            }

            $urlPath = parse_url($assetUrl, PHP_URL_PATH);
            if (! $urlPath) {
                continue;
            }

            $savePath = $output.abs_path($urlPath);

            try {
                $this->downloadFile($parsedUrl, base_path($savePath));
                $this->info("-- Downloaded asset {$savePath}");
            } catch (\Throwable $e) {
                $this->warn("Failed to download asset: {$parsedUrl}");
            }
        }
    }

    protected function downloadAssetsFromCss(array $cssFiles, string $cssUrl): void
    {
        foreach ($cssFiles as $cssPath) {
            $fullPath = base_path(trim($cssPath, "'"));

            if (! File::exists($fullPath)) {
                $this->warn("File {$fullPath} don't exists.");

                continue;
            }

            $content = File::get($fullPath);
            $this->downloadAssetsFromCssWithContent($content, $cssUrl);
        }
    }

    protected function parseHref(string $href): string
    {
        if (str_starts_with($href, '//')) {
            $href = 'https:'.$href;
        }

        if (! is_url($href)) {
            $baseUrl = explode('/', $this->data['url'])[0];
            $baseUrl .= '://'.get_domain_by_url($this->data['url']);

            if (str_starts_with($href, '/')) {
                $href = $baseUrl.trim($href);
            } else {
                $dir = dirname($this->data['url']);
                $href = "{$dir}/".trim($href);
            }
        }

        return $href;
    }

    protected function replaceContentCss(string $content): string
    {
        $content = Str::replace('/*!', '/*', $content);

        $content = Str::replace('../../', '../', $content);

        return $content;
    }

    protected function getArguments(): array
    {
        return [
            ['theme', InputArgument::REQUIRED, 'Theme name'],
        ];
    }
}
