<?php

namespace Tests\Unit\Services;

use App\Models\Article;
use App\Services\ArticleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ArticleServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $articleService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->articleService = new ArticleService();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_cache_and_retrieve_articles()
    {
        Cache::shouldReceive('remember')
            ->once()
            ->andReturn(Article::factory()->count(10)->make());

        $articles = $this->articleService->getArticles([]);

        $this->assertCount(10, $articles);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_save_articles()
    {
        $articleData = [
            'title' => 'Test Article',
            'description' => 'This is a test description',
            'source' => 'Test',
            'category' => 'Test',
            'author' => 'Jamiu Jimoh',
            'published_at' => now()->format('Y-m-d H:i:s'),
        ];

        $this->articleService->saveArticles([$articleData]);

        $this->assertDatabaseHas('articles', ['title' => 'Test Article']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_query_articles_by_keyword()
    {
        $art= Article::factory()->create(['title' => 'Innoscripta Tips']);

        $filters = ['keyword' => 'Innoscripta'];
        $articles = $this->articleService->getArticles($filters);

        $this->assertCount(1, $articles);
        $this->assertEquals('Innoscripta Tips', $articles->first()->title);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_clear_article_cache()
    {
        $filters = ['keyword' => 'example'];
        Cache::shouldReceive('forget')->once();

        $this->articleService->clearArticleCache($filters);
    }
}
