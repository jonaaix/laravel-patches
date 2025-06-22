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
    * Resolve application Console Kernel implementation.
    *
    * @param  \Illuminate\Foundation\Application  $app
    * @return void
    */
   protected function resolveApplicationConsoleKernel($app)
   {
      // Use our custom Kernel for all tests. This is the key to solving the issue.
      $app->singleton(
         'Illuminate\Contracts\Console\Kernel',
         'Aaix\LaravelPatches\Tests\Console\Kernel'
      );
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
