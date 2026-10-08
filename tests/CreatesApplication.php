<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

trait CreatesApplication
{
    public function createApplication(): Application
    {
        $app = require __DIR__ . '/../bootstrap/app.php';

        $kernel = $app->make(Kernel::class);
        $kernel->bootstrap();

        return $app;
    }
}
