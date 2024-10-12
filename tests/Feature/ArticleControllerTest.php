<?php

namespace Tests\Feature\Controllers;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleControllerTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_list_articles()
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        Article::factory()->count(5)->create();

        $response = $this->getJson('/api/articles', [
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => ['data', 'current_page'],
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_filter_articles_by_keyword()
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        Article::factory()->create(['title' => 'Innoscripta Guide']);

        $response = $this->getJson('/api/articles?keyword=Innoscripta', [
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Innoscripta Guide']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_article_not_found()
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->getJson('/api/articles/999', [
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Article not found',
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_show_a_single_article()
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;
        $article = Article::factory()->create();

        $response = $this->getJson('/api/articles/' . $article->id, [
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => $article->title]);
    }
}
