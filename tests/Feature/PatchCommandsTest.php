<?php

namespace Aaix\LaravelPatches\Tests\Feature;

use Aaix\LaravelPatches\Tests\TestCase;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;

class PatchCommandsTest extends TestCase
{
   use RefreshDatabase;

   protected function tearDown(): void
   {
      $patchPath = app_path('Console/Commands/Patches');
      if (File::isDirectory($patchPath)) {
         File::deleteDirectory($patchPath);
      }
      parent::tearDown();
   }

   #[Test]
   public function it_can_create_a_new_patch_file(): void
   {
      $patchName = 'MyNewGeneratedPatch';

      $this->artisan('make:patch', ['name' => $patchName])
         ->expectsOutputToContain('Patch created successfully')
         ->assertSuccessful();
   }

   #[Test]
   public function status_command_shows_pending_patches_correctly(): void
   {
      $this->artisan('patch:status')
         ->expectsOutputToContain('❌ Pending Patches')
         ->expectsOutputToContain('Patch_2025_01_01_MyTestPatch')
         ->expectsOutputToContain('Patch_2025_01_02_MySecondTestPatch')
         ->assertSuccessful();
   }

   #[Test]
   public function a_patch_can_be_executed_successfully_and_is_logged(): void
   {
      $this->artisan('patch:2025_01_01_my-test-patch')->expectsOutputToContain('Successfully ran and logged')->assertSuccessful();

      $this->assertDatabaseHas('migrations', [
         'migration' => 'my-test-patch-was-here',
      ]);

      $this->assertDatabaseHas('patch_logs', [
         'patch_class' => 'App\\Console\\Commands\\Patches\\Patch_2025_01_01_MyTestPatch',
      ]);
   }

   #[Test]
   public function a_ran_patch_is_not_executed_twice(): void
   {
      $this->artisan('patch:2025_01_01_my-test-patch')->assertSuccessful();

      $this->artisan('patch:2025_01_01_my-test-patch')
         ->expectsConfirmation(
            'Patch <info>App\Console\Commands\Patches\Patch_2025_01_01_MyTestPatch</info> has already been applied 1 time(s). Do you want to run it again?',
            'no'
         )
         ->expectsOutputToContain('Skipping patch')
         ->assertSuccessful();
   }

   #[Test]
   public function it_fails_gracefully_if_migrations_have_not_been_run(): void
   {
      Schema::dropIfExists(config('patches.table', 'patch_logs'));
      $this->artisan('patch:status')->expectsOutputToContain("The patch log table 'patch_logs' does not exist.")->assertFailed();
   }

   #[Test]
   public function it_can_interactively_select_and_run_patches(): void
   {
      if (version_compare(Application::VERSION, '11.0', '<')) {
         $this->markTestSkipped('Laravel Prompts testing is not reliable on this version of Laravel.');
      }

      $this->artisan('patch')
         ->expectsChoice(
            'Which (pending) patches would you like to run?',
            ['Patch_2025_01_02_MySecondTestPatch'],
            ['Patch_2025_01_01_MyTestPatch', 'Patch_2025_01_02_MySecondTestPatch']
         )
         ->expectsOutputToContain('Second test patch executed.')
         ->doesntExpectOutputToContain('Test patch executed.')
         ->assertSuccessful();
   }

   #[Test]
   public function it_can_run_a_specific_patch_non_interactively(): void
   {
      $this->artisan('patch', ['--patch' => ['Patch_2025_01_01_MyTestPatch']])
         ->expectsOutputToContain('Test patch executed.')
         ->doesntExpectOutputToContain('Second test patch executed.')
         ->assertSuccessful();

      $this->assertDatabaseHas('patch_logs', ['patch_class' => 'App\\Console\\Commands\\Patches\\Patch_2025_01_01_MyTestPatch']);
      $this->assertDatabaseMissing('patch_logs', ['patch_class' => 'App\\Console\\Commands\\Patches\\Patch_2025_01_02_MySecondTestPatch']);
   }

   #[Test]
   public function it_can_run_all_pending_patches_non_interactively(): void
   {
      $this->artisan('patch', ['--all' => true])
         ->expectsOutputToContain('Test patch executed.')
         ->expectsOutputToContain('Second test patch executed.')
         ->assertSuccessful();

      $this->assertDatabaseHas('patch_logs', ['patch_class' => 'App\\Console\\Commands\\Patches\\Patch_2025_01_01_MyTestPatch']);
      $this->assertDatabaseHas('patch_logs', ['patch_class' => 'App\\Console\\Commands\\Patches\\Patch_2025_01_02_MySecondTestPatch']);
   }
}
