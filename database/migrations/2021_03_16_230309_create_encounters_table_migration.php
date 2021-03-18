<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEncountersTableMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('encounters', function (Blueprint $table) {
            $table->id();
            $table->string('key_no', 20)->index();
            $table->foreignId('patient_id')->constrained();
            $table->json('meta')->nullable();
            $table->dateTime('encountered_at')->index()->nullable();
            $table->dateTime('dismissed_at')->index()->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('encounter_user', function (Blueprint $table) {
            $table->primary(['encounter_id', 'user_id']);
            $table->foreignId('encounter_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->string('status', 30)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('encounter_user');
        Schema::dropIfExists('encounters');
    }
}
