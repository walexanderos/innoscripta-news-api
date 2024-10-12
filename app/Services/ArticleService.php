<?php
namespace App\Services;

use App\Models\Article;
use Illuminate\Support\Facades\Cache;

class ArticleService
{
    protected $ttl = 10 * 60; // 10min

    public function getArticles(array $filters)
    {
        $cacheKey = 'articles';
        if (count($filters) > 0) {
            $cacheKey .= '_filters_'. json_encode($filters);
        }

        $cacheKey .= '_page_' . (!empty($filters['page']) ? $filters['page']: 1);

        return Cache::remember($cacheKey, $this->ttl, function () use ($filters) {
            return $this->queryArticles($filters);
        });
    }

    public function queryArticles(array $filters){
        $query = Article::query();

        if (!empty($filters['keyword'])) {
            $query->where(function($query) use ($filters){
                $query->where('title', 'like', '%' . $filters['keyword'] . '%')
                ->orWhere('description', 'like', '%' . $filters['keyword'] . '%')
                ->orWhere('content', 'like', '%' . $filters['keyword'] . '%');
            });
        }

        if (!empty($filters['categories'])) {
            $query->where('category', $filters['categories']);
        }

        if (!empty($filters['authors'])) {
            $query->where('category', $filters['authors']);
        }

        if (!empty($filters['sources'])) {
            $query->where('source', $filters['sources']);
        }

        if (!empty($filters['date'])) {
            $query->whereDate('published_at', $filters['date']);
        }

        return $query->paginate(10);
    }

    public function getArticleById($id)
    {
        return Article::find($id);
    }

    public function saveArticles(array $articles)
    {
        foreach ($articles as $article) {
            Article::updateOrCreate(
                ['title' => $article['title'], 'published_at' => $article['published_at']],
                $article
            );
        }
    }

    public function clearArticleCache(array $filters)
    {
        $cacheKey = 'articles_' . json_encode($filters);
        Cache::forget($cacheKey); // Clear cache for the specific filters
    }
}
