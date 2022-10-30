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
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrolled_subject_id')->nullable()->constrained();
            $table->foreignId('student_id')->nullable()->constrained();
            $table->foreignId('subject_id')->nullable()->constrained();
            $table->string('academic_year', 9)->nullable();
            $table->foreignId('term_id')->nullable()->constrained();
            $table->decimal('numeric_grade', 3, 2)->nullable();
            $table->string('gpa', 10)->nullable();
            $table->string('remarks', 10)->nullable()->default('PASSED');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');
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
        Schema::dropIfExists('grades');
    }
};
