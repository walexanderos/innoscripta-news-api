<?php
namespace App\Services\News;

interface NewsApiInterface
{
    public function fetchArticles(): array;
}
