<?php

namespace Juzaweb\DevTool\Tests\Unit;

use Illuminate\Contracts\Console\Kernel;
use Juzaweb\DevTool\Commands\Modules\DumpCommand;
use Juzaweb\DevTool\Tests\TestCase;
use Mockery;

class DumpCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_dumps_specific_module()
    {
        $command = Mockery::mock(DumpCommand::class.'[dump]')->makePartial();
        $command->shouldReceive('dump')->with('Blog')->once();

        $this->app[Kernel::class]->registerCommand($command);

        $this->artisan('module:dump', ['module' => 'Blog'])
            ->assertExitCode(0);
    }

    public function test_it_dumps_all_modules()
    {
        $command = Mockery::mock(DumpCommand::class.'[dumpAll]')->makePartial();
        $command->shouldReceive('dumpAll')->once();

        $this->app[Kernel::class]->registerCommand($command);

        $this->artisan('module:dump')
            ->assertExitCode(0);
    }
}
