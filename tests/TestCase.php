<?php

namespace Aaix\LaravelPatches\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Aaix\LaravelPatches\PatchServiceProvider;
use Illuminate\Support\Facades\File;

abstract class TestCase extends OrchestraTestCase
{
   /**
    * Setup the test environment.
    */
   protected function setUp(): void
   {
      parent::setUp();

      // Prepare a test patch from a fixture for execution tests.
      $this->setupTestPatch();
   }

   /**
    * Copies a fixture patch into the test application's command directory
    * so it can be discovered by our custom test Kernel.
    */
   protected function setupTestPatch(): void
   {
      $fixturePath = __DIR__ . '/fixtures/MyTestPatch.php';
      $destinationPath = app_path('Console/Patches');

      if (!File::isDirectory($destinationPath)) {
         File::makeDirectory($destinationPath, 0755, true);
      }

      File::copy($fixturePath, $destinationPath . '/MyTestPatch.php');
   }

   /**
    * Get package providers.
    */
   protected function getPackageProviders($app): array
   {
      return [
         PatchServiceProvider::class,
      ];
   }

   /**
    * Define environment setup.
    *
    * @param  \Illuminate\Foundation\Application  $app
    * @return void
    */
   protected function getEnvironmentSetUp($app): void
   {
      // Set up the in-memory SQLite database.
      $app['config']->set('database.default', 'testing');
      $app['config']->set('database.connections.testing', [
         'driver'   => 'sqlite',
         'database' => ':memory:',
         'prefix'   => '',
      ]);

      // Set the default patch path to the test app's directory.
      $app['config']->set('patches.path', 'app/Console/Patches');
   }
}
