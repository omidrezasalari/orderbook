<?php

namespace App\Services\Sources;

use App\Contracts\HttpClientInterface;
use App\Models\Constants\NewsAggregatorConstant;
use Illuminate\Support\Collection;

class NewsAPIService implements NewsSourceInterface
{
    private string $url = NewsAggregatorConstant::NEWS_API_SOURCE_URL;
    private string $apiKey = NewsAggregatorConstant::NEWS_API_SOURCE_API_KEY;

    public function __construct(private HttpClientInterface $httpClient) {}

    public function fetch(): Collection
    {
        $response = $this->httpClient->get($this->url, [
            'apiKey' => $this->apiKey,
            'country' => 'us',
        ]);

        return collect($response['articles'])->map(fn ($article) => [
            'title' => $article['title'],
            'content' => $article['content'] ?? '',
            'author' => $article['author'] ?? 'Unknown',
            'published_at' => $article['publishedAt'],
            'url' => $article['url'],
            'image_url' => $article['urlToImage'] ?? null,
            'source_id' => NewsAggregatorConstant::NEWS_API_SOURCE_ID
        ]);
    }
}
