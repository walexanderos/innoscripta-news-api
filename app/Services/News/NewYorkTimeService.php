<?php
namespace App\Services\News;

use DateTime;
use Illuminate\Support\Facades\Http;

class NewYorkTimeService implements NewsApiInterface
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.newyorktimes.key');
        $this->baseUrl = config('services.newyorktimes.base_url');
    }

    public function fetchArticles(): array
    {
        $url = "{$this->baseUrl}/articlesearch.json";

        $currentDate = new DateTime();
        $fiveMinutesAgo = new DateTime();
        $fiveMinutesAgo->modify('-1 day');

        $response = Http::withOptions([
            'verify' => false,  // Disables SSL certificate verification
        ])->get($url, [
            'api-key' => $this->apiKey,
            'begin_date' => $fiveMinutesAgo->format('Ymd'),
            'end_date' => $currentDate->format('Ymd')
        ]);
        if ($response->successful()) {
            $data = $response->json();
            return $this->normalizeData($data['response']["docs"]);
        }

        return [];
    }

    protected function normalizeData(array $articles): array
    {
        $data = [];
        foreach ($articles as $article) {
            $data[] = [
                'title' => $article['headline']['main'],
                'description' => $article['abstract'],
                'category' => $article['section_name'],
                'author' => isset($article['byline']['person'][0]) ? ($article['byline']['person'][0]['firstname'] ?? '')  .' '.($article['byline']['person'][0]['lastname'] ?? '') : '',
                'source' => $article['source'],
                'published_at' => date("Y-m-d H:i:s", strtotime($article['pub_date'])),
                'url' => $article['web_url'],
                'content' => $article['lead_paragraph'],
            ];
        }
        return $data;
    }
}
