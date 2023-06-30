<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('survay_question_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\SurvayQuestion::class,'survey_question_id');
            $table->foreignIdFor(\App\Models\SurvayAnswer::class,'survey_answer_id');
            $table->text('answer');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survay_question_answers');
    }
};
