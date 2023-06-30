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
        Schema::create('survay_questions', function (Blueprint $table) {
            $table->id();
            $table->string('type',45);
            $table->string('question',2000);
            $table->longText('description')->nullable();
            $table->longText('date')->nullable();
            $table->foreignIdFor(\App\Models\Survay::class,'survey_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survay_questions');
    }
};
