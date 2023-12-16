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
use Illuminate\Support\Facades\File;

class StyleDownloader
{
    protected Client $client;

    protected string $url;

    protected string $extension;

    public static function make(...$args): static
    {
        return new static(...$args);
    }

    public function download(string $path): bool|string
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
            ->filter(fn ($path) => !str_starts_with($path, 'data:'))
            ->map(
                function ($path) {
                    $url = $this->parseHref($path);
                    dd($path);
                }
            );

        return $fileContents;
    }

    protected function downloadFile(string $url, string $path): void
    {
        $folder = dirname($path);

        if (!is_dir($folder)) {
            File::makeDirectory($folder, 0755, true);
        }

        $this->getHttp()->request(
            'GET',
            $url,
            [
                'sink' => $path,
                'verify' => false
            ]
        );
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
        return $this->client ?? ($this->client = new Client());
    }
}
