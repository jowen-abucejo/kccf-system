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
        Schema::create('student_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->foreignId('student_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('school_setting_id')->constrained();
            $table->foreignId('program_id')->constrained();
            $table->foreignId('level_id')->constrained();
            $table->foreignId('student_type_id')->constrained();
            $table->string('last_name', 50);
            $table->string('first_name', 50);
            $table->string('middle_name', 50)->nullable();
            $table->string('name_suffix', 10)->nullable();
            $table->string('sex', 10);
            $table->string('civil_status', 10);
            $table->date('birth_date');
            $table->string('birth_place');
            $table->json('address');
            $table->string('contact_number', 10)->nullable();
            $table->string('email')->unique();
            $table->json('religion');
            $table->json('other_info');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
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
        Schema::dropIfExists('student_registrations');
    }
};
