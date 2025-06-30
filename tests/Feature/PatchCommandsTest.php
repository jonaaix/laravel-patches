<?php

namespace Aaix\LaravelPatches\Tests\Feature;

use Aaix\LaravelPatches\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;

class PatchCommandsTest extends TestCase
{
   use RefreshDatabase;

   #[Test]
   public function it_can_create_a_new_patch_file(): void
   {
      // Arrange
      $patchName = 'MyNewGeneratedPatch';
      $patchFileName = 'Patch_' . now()->format('Y_m_d') . "_{$patchName}.php";
      $patchPath = app_path("Console/Patches/{$patchFileName}");

      // Act
      $this->artisan('make:patch', ['name' => $patchName])
         ->expectsOutputToContain('Patch created successfully')
         ->assertSuccessful();

      // Assert
      $this->assertTrue(File::exists($patchPath));

      // Cleanup
      File::delete($patchPath);
   }

   #[Test]
   public function status_command_shows_pending_patches_correctly(): void
   {
      // The fixture 'MyTestPatch.php' is automatically loaded by our custom Kernel.
      // The RefreshDatabase trait runs the necessary migrations.

      $this->artisan('patch:status')
         ->expectsOutputToContain('❌ Pending Patches')
         ->expectsOutputToContain('MyTestPatch')
         ->assertSuccessful();
   }

   #[Test]
   public function a_patch_can_be_executed_successfully_and_is_logged(): void
   {
      // 1. Act
      $this->artisan('patch:my-test-patch')->expectsOutputToContain('Successfully ran and logged')->assertSuccessful();

      // 2. Assert database state is correct
      $this->assertDatabaseHas('migrations', [
         'migration' => 'my-test-patch-was-here',
      ]);

      $this->assertDatabaseHas('patch_logs', [
         'patch_class' => 'App\\Console\\Patches\\MyTestPatch',
      ]);

      // 3. Assert the final status command output is correct
      $this->artisan('patch:status')
         ->expectsOutputToContain('✅ Ran Patches')
         ->expectsOutputToContain('MyTestPatch') // The patch appears in the "Ran" list
         ->expectsOutputToContain('❌ Pending Patches') // The "Pending" header is always shown
         ->expectsOutputToContain('None') // The key: "None" should be listed under "Pending"
         ->assertSuccessful();
   }

   #[Test]
   public function a_ran_patch_is_not_executed_twice(): void
   {
      // Act
      $this->artisan('patch:my-test-patch')->assertSuccessful();
      $this->assertCount(1, DB::table('patch_logs')->get());

      // In a non-interactive test, confirm() returns false, so the patch is skipped.
      $this->artisan('patch:my-test-patch')
         ->expectsOutputToContain('Skipping patch')
         ->assertSuccessful();

      // Assert that the run_count was NOT incremented because we skipped.
      $this->assertCount(1, DB::table('patch_logs')->get());
      $this->assertDatabaseHas('patch_logs', ['run_count' => 1]);
   }

   #[Test]
   public function it_fails_gracefully_if_migrations_have_not_been_run(): void
   {
      // Arrange
      Schema::dropIfExists(config('patches.table', 'patch_logs'));

      // Act & Assert
      $this->artisan('patch:status')->expectsOutputToContain("The patch log table 'patch_logs' does not exist.")->assertFailed();
   }
}
