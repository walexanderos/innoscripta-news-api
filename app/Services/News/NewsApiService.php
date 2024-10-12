<?php
namespace App\Services\News;

use DateTime;
use Illuminate\Support\Facades\Http;

class NewsApiService implements NewsApiInterface
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.newsapi.key');
        $this->baseUrl = config('services.newsapi.base_url');
    }

    public function fetchArticles(): array
    {
        $url = "{$this->baseUrl}/everything";

        $currentDate = new DateTime();
        $fiveMinutesAgo = new DateTime();
        $fiveMinutesAgo->modify('-1 day');

        $sources = $this->getSources();
        $response = Http::withOptions([
            'verify' => false,  // Disables SSL certificate verification
        ])->get($url, [
            'apiKey' => $this->apiKey,
            'from' => $fiveMinutesAgo->format('Y-m-d'),
            'to' => $currentDate->format('Y-m-d'),
            'sources' => implode(', ', $sources)
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return $this->normalizeData($data['articles']);
        }

        return [];
    }

    protected function normalizeData(array $articles): array
    {
        $data = [];
        foreach ($articles as $article) {
            $data[] = [
                'title' => $article['title'],
                'description' => $article['description'],
                'author' => $article['author'],
                'source' => $article['source']['name'],
                'published_at' => date("Y-m-d H:i:s", strtotime($article['publishedAt'])),
                'url' => $article['url'],
                'content' => $article['content'],
            ];
        }
        return $data;
    }

    public function getSources(): array
    {
        $url = "{$this->baseUrl}/top-headlines/sources";

        $response = Http::withOptions([
            'verify' => false,  // Disables SSL certificate verification
        ])->get($url, [
            'apiKey' => $this->apiKey
        ]);


        if ($response->successful()) {
            $data = $response->json();
            if($data['status'] == 'ok' && isset($data['sources'])){
                return array_column($data['sources'], 'id');
            }
        }

        return [];
    }
}
