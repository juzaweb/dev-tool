<?php

namespace Juzaweb\DevTool\Commands\Theme;

use Illuminate\Support\Facades\File;
use Juzaweb\DevTool\Commands\Abstracts\DownloadTemplateCommandAbstract;
use Symfony\Component\Console\Input\InputArgument;

class DownloadTemplateCommand extends DownloadTemplateCommandAbstract
{
    protected $name = 'html:download';

    protected array $data;

    public function handle(): void
    {
        $this->sendBaseDataAsks();

        if (filter_var($this->option('styles'), FILTER_VALIDATE_BOOLEAN) === true) {
            $this->call('style:download', [
                '--theme' => $this->data['theme'],
                '--url' => $this->data['url'],
            ]);
        }

        $this->downloadFiles();
    }

    protected function downloadFiles(): void
    {
        $this->downloadTemplateFile('templates/home.twig', $this->data['url']);

        $contents = $this->getFileContent($this->data['url']);

        $dom = str_get_html($contents);

        $urls = [];
        foreach ($dom->find('a') as $e) {
            if (in_array($e->href, ['#', 'javascript:void(0)', 'javascript:', 'javascript:;'])) {
                continue;
            }

            $url = get_full_url($e->href, $this->data['url']);

            if (get_domain_by_url($url) !== get_domain_by_url($this->data['url'])) {
                continue;
            }

            $urls[] = $url;
        }

        $urls = array_unique($urls);

        foreach ($urls as $url) {
            $fileName = basename_without_extension($url);
            $this->downloadTemplateFile("templates/{$fileName}.twig", $url);
        }
    }

    protected function sendBaseDataAsks(): void
    {
        $this->data['url'] = $this->ask(
            'Url Template?',
            $this->getDataDefault('url')
        );

        $this->setDataDefault('url', $this->data['url']);

        $this->data['theme'] = $this->ask(
            'Theme Name?',
            $this->getDataDefault('theme')
        );

        $this->setDataDefault('theme', $this->data['theme']);

        $this->data['container'] = $this->ask(
            'Theme Container?',
            $this->getDataDefault('container', '.container-fluid')
        );

        $this->setDataDefault('container', $this->data['container']);
    }

    protected function downloadTemplateFile(string $file, string $url): void
    {
        $path = "themes/{$this->data['theme']}/views/{$file}";

        $this->info("Downloading file {$path}");

        if (!File::isDirectory(dirname($path))) {
            File::makeDirectory(dirname($path), 0777, true);
        }

        try {
            $contents = $this->getFileContent($url);
        } catch (\Exception $e) {
            $this->error("Cannot get url {$url}: ". $e->getMessage());
            return;
        }

        $content = str_get_html($contents)->find($this->data['container'], 0)?->innertext;

        if (empty($content)) {
            $this->warn("Cannot get content from {$url}");
            return;
        }

        File::put(
            $path,
            "{% extends 'cms::layouts.frontend' %}

{% block content %}
    {$content}
{% endblock %}"
        );
    }

    protected function getOptions(): array
    {
        return [
            ['styles', null, InputArgument::OPTIONAL, 'Download styles', true],
        ];
    }
}
