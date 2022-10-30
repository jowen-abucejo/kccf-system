<?php

use Carbon\Carbon;
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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_number', 15)->unique();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('school_setting_id')->constrained();
            $table->foreignId('program_id')->constrained();
            $table->foreignId('level_id')->constrained();
            $table->boolean('regular')->nullable()->default(true);
            $table->foreignId('student_type_id')->constrained();
            $table->dateTime('admission_datetime')->default(Carbon::now('Asia/Manila')->format('Y-m-d H:i:s'));
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->foreignId('deleted_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students');
    }
};
