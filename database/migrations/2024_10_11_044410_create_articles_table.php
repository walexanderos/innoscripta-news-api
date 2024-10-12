<?php

use App\Enums\NewsSourceEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('category', 100)->nullable();
            $table->text('description')->nullable();
            $table->text('content')->nullable();
            $table->string('author')->nullable();
            $table->string('source');
            $table->text('url')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->index(['title', 'category', 'published_at', 'source'], 'article_filter');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
