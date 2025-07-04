<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      $tableName = config('patches.table', 'patch_logs');

      Schema::create($tableName, function (Blueprint $table) {
         $table->id();
         $table->string('patch_class')->unique();
         $table->timestamp('ran_at')->useCurrent();
      });
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down()
   {
      $tableName = config('patches.table', 'patch_logs');
      Schema::dropIfExists($tableName);
   }
};
