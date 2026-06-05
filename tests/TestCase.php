<?php

namespace Tests;

use Illuminate\Support\Facades\File;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $compiledPath = storage_path('framework/testing/views')
            .DIRECTORY_SEPARATOR.str_replace('\\', '-', static::class)
            .DIRECTORY_SEPARATOR.str_replace(['\\', '/', ' '], '-', $this->name());

        File::ensureDirectoryExists($compiledPath);

        config(['view.compiled' => $compiledPath]);
    }
}
