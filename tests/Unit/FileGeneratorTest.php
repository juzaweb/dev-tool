<?php

namespace Juzaweb\DevTool\Tests\Unit;

use Illuminate\Filesystem\Filesystem;
use Juzaweb\DevTool\Generators\FileGenerator;
use Juzaweb\DevTool\Tests\TestCase;
use Juzaweb\Modules\Core\Modules\Exceptions\FileAlreadyExistException;
use Mockery;

class FileGeneratorTest extends TestCase
{
    public function test_it_generates_file_when_not_exists()
    {
        $path = '/fake/path/file.txt';
        $contents = 'fake contents';

        $filesystem = Mockery::mock(Filesystem::class);
        $filesystem->shouldReceive('exists')->once()->with($path)->andReturn(false);
        $filesystem->shouldReceive('put')->once()->with($path, $contents)->andReturn(13);

        $generator = new FileGenerator($path, $contents, $filesystem);

        $this->assertEquals(13, $generator->generate());
    }

    public function test_it_generates_file_when_exists_and_overwrite_is_true()
    {
        $path = '/fake/path/file.txt';
        $contents = 'fake contents';

        $filesystem = Mockery::mock(Filesystem::class);
        $filesystem->shouldReceive('exists')->once()->with($path)->andReturn(true);
        $filesystem->shouldReceive('put')->once()->with($path, $contents)->andReturn(13);

        $generator = new FileGenerator($path, $contents, $filesystem);
        $generator->withFileOverwrite(true);

        $this->assertEquals(13, $generator->generate());
    }

    public function test_it_throws_exception_when_file_exists_and_overwrite_is_false()
    {
        $path = '/fake/path/file.txt';
        $contents = 'fake contents';

        $filesystem = Mockery::mock(Filesystem::class);
        $filesystem->shouldReceive('exists')->once()->with($path)->andReturn(true);
        $filesystem->shouldReceive('put')->never();

        $generator = new FileGenerator($path, $contents, $filesystem);
        $generator->withFileOverwrite(false);

        $this->expectException(FileAlreadyExistException::class);
        $this->expectExceptionMessage('File already exists!');

        $generator->generate();
    }

    public function test_it_throws_exception_when_file_exists_and_overwrite_is_not_set()
    {
        $path = '/fake/path/file.txt';
        $contents = 'fake contents';

        $filesystem = Mockery::mock(Filesystem::class);
        $filesystem->shouldReceive('exists')->once()->with($path)->andReturn(true);
        $filesystem->shouldReceive('put')->never();

        $generator = new FileGenerator($path, $contents, $filesystem);

        $this->expectException(FileAlreadyExistException::class);
        $this->expectExceptionMessage('File already exists!');

        $generator->generate();
    }
}
