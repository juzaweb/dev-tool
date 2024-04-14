<?php

namespace Juzaweb\DevTool\Commands\Theme;

use Illuminate\Support\Facades\File;
use Juzaweb\CMS\Support\HtmlDom;
use Juzaweb\DevTool\Commands\Abstracts\DownloadTemplateCommandAbstract;
use Juzaweb\DevTool\Support\StyleDownloader;

class DownloadStyleCommand extends DownloadTemplateCommandAbstract
{
    protected $name = 'style:download';

    protected string $themeName;

    protected string $url;

    public function handle(): void
    {
        $this->sendAsks();

        $html = $this->curlGet($this->url);

        $domp = str_get_html($html);

        $cssUrls = $this->downloadCss($domp);
        $css = collect($cssUrls)
            ->map(fn ($path, $index) => "\t'{$path}',")
            ->implode("\n");

        $jsUrls = $this->downloadJs($domp);
        $js = collect($jsUrls)
            ->map(fn ($path, $index) => "\t'{$path}',")
            ->implode("\n");

        $mix = "const mix = require('laravel-mix');

mix.styles([
{$css}
], 'themes/{$this->themeName}/assets/public/css/main.min.css');

mix.combine([
{$js}
], 'themes/{$this->themeName}/assets/public/js/main.min.js');";

        File::put("themes/{$this->themeName}/assets/mix.js", $mix);
    }

    protected function sendAsks(): void
    {
        $this->url = $this->ask(
            'Url Template?',
            $this->getDataDefault('url')
        );

        $this->setDataDefault('url', $this->url);

        $this->themeName = $this->ask(
            'Theme Name?',
            $this->getDataDefault('name')
        );

        $this->setDataDefault('name', $this->themeName);
    }

    protected function downloadCss(HtmlDom $domp): array
    {
        $result = [];
        foreach ($domp->find('link[rel="stylesheet"]') as $e) {
            $href = $e->href;
            $href = $this->parseHref($href);

            if ($this->isExcludeDomain($href)) {
                continue;
            }

            $name = explode('?', basename($href))[0];

            $path = "themes/{$this->themeName}/assets/styles/css/{$name}";

            if (StyleDownloader::make()->withOutputStyle($this->output)->setUrl($href)->download(base_path($path))) {
                $result[] = "'{$path}'";

                $this->info("-- Downloaded file {$path}");
            } else {
                $this->warn("Download error: {$href}");
            }
        }

        return $result;
    }

    protected function downloadJs(HtmlDom $domp): array
    {
        $result = [];
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

            $path = "themes/{$this->themeName}/assets/styles/js/{$name}";

            try {
                $this->downloadFile($href, base_path($path));
                $result[] = "'{$path}'";
                $this->info("-- Downloaded file {$path}");
            } catch (\Exception $e) {
                $this->warn("Download error: {$href}");
            }
        }

        return $result;
    }

    protected function parseHref(string $href, ?string $url = null): string
    {
        $url = $url ?? $this->url;

        if (str_starts_with($href, '//')) {
            $href = 'https:'.$href;
        }

        if (!is_url($href)) {
            $baseUrl = explode('/', $url)[0];
            $baseUrl .= '://'.get_domain_by_url($url);

            if (str_starts_with($href, '/')) {
                $href = $baseUrl.trim($href);
            } else {
                $dir = dirname($url);
                $href = "{$dir}/".trim($href);
            }
        }

        return $href;
    }
}
