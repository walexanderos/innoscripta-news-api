<?php
namespace App\Services\News;

use DateTime;
use Illuminate\Support\Facades\Http;

class GuardianService implements NewsApiInterface
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.theguardian.key');
        $this->baseUrl = config('services.theguardian.base_url');
    }

    public function fetchArticles(): array
    {
        $url = "{$this->baseUrl}/search";

        $currentDate = new DateTime();
        $fiveMinutesAgo = new DateTime();
        $fiveMinutesAgo->modify('-1 day');

        $response = Http::withOptions([
            'verify' => false,  // Disables SSL certificate verification
        ])->get($url, [
            'api-key' => $this->apiKey,
            'from-date' => $fiveMinutesAgo->format('Y-m-d'),
            'to-date' => $currentDate->format('Y-m-d'),
            'show-fields' => 'body',
            'format' => 'json'
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return $this->normalizeData($data['response']["results"]);
        }

        return [];
    }

    protected function normalizeData(array $articles): array
    {
        $data = [];
        foreach ($articles as $article) {
            $data[] = [
                'title' => $article['webTitle'],
                'description' => $article['webTitle'],
                'category' => $article['sectionName'],
                'source' => "The Guardian",
                'published_at' => date("Y-m-d H:i:s", strtotime($article['webPublicationDate'])),
                'url' => $article['webUrl'],
                'content' => $article['fields']['body'],
            ];
        }
        return $data;
    }
}
