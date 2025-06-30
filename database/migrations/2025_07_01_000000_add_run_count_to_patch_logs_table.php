<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
   /**
    * Add the run_count column to patch_logs.
    *
    * @return void
    */
   public function up(): void
   {
      Schema::table(config('patches.table', 'patch_logs'), function (Blueprint $table) {
         // unsigned integer with default = 1
         $table->unsignedInteger('run_count')->default(1)->after('ran_at');
      });
   }

   /**
    * Remove the run_count column.
    *
    * @return void
    */
   public function down(): void
   {
      Schema::table(config('patches.table', 'patch_logs'), function (Blueprint $table) {
         $table->dropColumn('run_count');
      });
   }
};
