<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guardians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained('student_registrations');
            $table->string('last_name', 50);
            $table->string('first_name', 50);
            $table->string('middle_name', 50)->nullable();
            $table->string('name_suffix', 10)->nullable();
            $table->date('birth_date');
            $table->date('occupation')->nullable();
            $table->json('address')->nullable();
            $table->string('contact_number', 10)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('relationship');
            $table->boolean('is_deceased')->nullable()->default(false);
            $table->boolean('is_guardian')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('guardians');
    }
};
