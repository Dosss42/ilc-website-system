<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    // Runs before RefreshDatabase wipes anything; a cached config makes Laravel ignore phpunit.xml's DB name.
    public function createApplication()
    {
        $app = parent::createApplication();

        $connection = $app['config']->get('database.default');
        $database   = $app['config']->get("database.connections.{$connection}.database");

        if ($database !== 'ilc_system_test') {
            throw new \RuntimeException(
                "STOPPED: tests are pointing at database '{$database}', not 'ilc_system_test'. "
                . 'Run "php artisan config:clear" and try again. No data was touched.'
            );
        }

        return $app;
    }
}
