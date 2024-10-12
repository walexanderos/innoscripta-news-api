<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Services\News\GuardianService;
use App\Services\News\NewsApiInterface;
use App\Services\News\NewsApiService;
use App\Services\News\NewYorkTimeService;
use Illuminate\Console\Command;

class FetchNewsArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch news articles and store them in the database';

    /**
     * Execute the console command.
     *
     * @var array<NewsApiInterface>
     */

    protected $newsApiServices;

    public function __construct()
    {
        parent::__construct();

        //$this->newsApiServices[] = new NewsApiService();
        //$this->newsApiServices[] = new GuardianService();
        $this->newsApiServices[] = new NewYorkTimeService();
    }

    public function handle()
    {
        foreach ($this->newsApiServices as $service) {
            $articles = $service->fetchArticles();
            if(count($articles)){
                Article::upsert(
                    $articles,
                    ['title', 'published_at'],
                );
            }
        }

        $this->info('News articles fetched and stored successfully!');
    }
}
