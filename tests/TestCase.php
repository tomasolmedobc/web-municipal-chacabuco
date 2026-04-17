<?php

namespace Tests;

use Illuminate\Support\Facades\File;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $compiledPath = sys_get_temp_dir()
            .DIRECTORY_SEPARATOR.'chacabuco-testing-views'
            .DIRECTORY_SEPARATOR.str_replace('\\', '-', static::class);

        File::ensureDirectoryExists($compiledPath);

        config(['view.compiled' => $compiledPath]);
    }
}
