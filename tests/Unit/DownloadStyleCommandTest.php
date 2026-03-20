<?php

namespace Juzaweb\DevTool\Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\File;
use Juzaweb\DevTool\Commands\Themes\DownloadStyleCommand;
use Juzaweb\DevTool\Tests\TestCase;
use Juzaweb\Modules\Core\Facades\Theme;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class DownloadStyleCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('themes.path', base_path('themes'));
        $stubsPath = dirname(__DIR__, 2).'/stubs/themes';
        $this->app['config']->set('dev-tool.themes.stubs.path', $stubsPath);

        if (File::isDirectory(base_path('themes'))) {
            File::deleteDirectory(base_path('themes'));
        }

        $this->artisan('theme:make', ['name' => 'test-theme', '--force' => true]);

        $this->app->forgetInstance(\Juzaweb\Modules\Core\Contracts\Theme::class);
        Theme::clearResolvedInstance(\Juzaweb\Modules\Core\Contracts\Theme::class);
    }

    protected function tearDown(): void
    {
        if (File::isDirectory(base_path('themes'))) {
            File::deleteDirectory(base_path('themes'));
        }
        parent::tearDown();
    }

    public function test_download_style()
    {
        $mock = new MockHandler([
            new Response(200, [], '<html><head><link rel="stylesheet" href="http://example.com/style.css"></head><body></body></html>'),
            new Response(200, [], 'body { background: url("logo.png"); }'),
            new Response(200, [], 'binary-data-of-logo'),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $command = $this->getMockBuilder(DownloadStyleCommand::class)
            ->setConstructorArgs([$client])
            ->onlyMethods(['ask'])
            ->getMock();

        $command->method('ask')->willReturn('http://example.com');
        $command->setLaravel($this->app);

        $input = new ArrayInput([
            'theme' => 'test-theme',
        ]);
        $output = new BufferedOutput;

        // Create necessary directory for generateMixFile
        $mixDir = base_path('themes/test-theme/assets');
        if (! File::isDirectory($mixDir)) {
            File::makeDirectory($mixDir, 0755, true);
        }

        $command->run($input, $output);

        $this->assertFileExists(base_path('themes/test-theme/assets/css/style.css'));
        $this->assertFileExists(base_path('themes/test-theme/assets/logo.png'));
    }
}
