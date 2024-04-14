<?php
/**
 * JUZAWEB CMS - Laravel CMS for Your Project
 *
 * @package    juzaweb/cms
 * @author     The Anh Dang
 * @link       https://juzaweb.com
 * @license    GNU V2
 */

namespace Juzaweb\DevTool\Support;

use GuzzleHttp\Client;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Facades\File;
use const PHP_URL_PATH;

class StyleDownloader
{
    protected Client $client;

    protected string $url;

    protected string $extension;

    protected OutputStyle $output;

    public static function make(...$args): static
    {
        return new static(...$args);
    }

    public function withOutputStyle(OutputStyle $output): static
    {
        $this->output = $output;

        return $this;
    }

    public function download(string $path): bool
    {
        $this->downloadFile($this->url, $path);

        if ($this->isCss()) {
            $contents = $this->downloadImageCss($path);

            File::put($path, $contents);
        }

        return is_file($path);
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function setExtension(string $extension): static
    {
        $this->extension = $extension;

        return $this;
    }

    public function getExtension(): string
    {
        return $this->extension ?? strtolower(pathinfo(basename($this->url), PATHINFO_EXTENSION));
    }

    public function isCss(): bool
    {
        return $this->getExtension() === 'css';
    }

    public function downloadImageCss(string $filePath): string
    {
        $fileContents = File::get($filePath);

        preg_match_all('/url\((["\']?)(.*?)\1\)/i', $fileContents, $matches);

        collect($matches[2] ?? [])
            ->filter(fn ($path) => !str_starts_with($path, 'data:') && !is_url($path))
            ->map(
                function ($path) use ($filePath) {
                    $url = $this->parseHref($path);
                    $newPath = abs_path(dirname($filePath) .'/'.explode('#', $path)[0]);
                    $this->downloadFile($url, $newPath);
                }
            );

        return $fileContents;
    }

    protected function parseHref(string $href): string
    {
        if (is_url($href)) {
            return $href;
        }

        if (str_starts_with($href, '//')) {
            $href = 'https:'.$href;
        }

        $baseUrl = explode('/', $this->url)[0];
        $baseUrl .= '//'.get_domain_by_url($this->url);

        if (str_starts_with($href, '/')) {
            $href = $baseUrl.trim($href);
        } else {
            $path = parse_url($this->url, PHP_URL_PATH);
            $dir = dirname($path);
            $href = $baseUrl.abs_path("{$dir}/".trim($href));
        }

        return $href;
    }

    protected function downloadFile(string $url, string $path): void
    {
        $folder = dirname($path);

        if (!is_dir($folder)) {
            File::makeDirectory($folder, 0755, true);
        }

        try {
            $this->getHttp()->request(
                'GET',
                $url,
                [
                    'sink' => $path,
                    'verify' => false,
                ]
            );
        } catch (\Exception $e) {
            if (isset($this->output)) {
                report($e);
                $this->output->error("Download error: {$url}");
            } else {
                throw $e;
            }
        }
    }

    protected function getFileContent(string $url): string
    {
        return $this->getHttp()->request(
            'GET',
            $url,
            [
                'verify' => false
            ]
        )->getBody()->getContents();
    }

    protected function getHttp(): Client
    {
        return new Client(
            [
                'timeout' => 10,
                'connect_timeout' => 10,
            ]
        );
    }
}
