<?php

namespace App\Services\Sources;

use App\Contracts\HttpClientInterface;
use App\Models\Constants\NewsAggregatorConstant;
use Illuminate\Support\Collection;

class GuardianService implements NewsSourceInterface
{
    private string $url = NewsAggregatorConstant::GUARDIAN_SOURCE_URL;
    private string $apiKey = NewsAggregatorConstant::GUARDIAN_SOURCE_API_KEY;

    public function __construct(private HttpClientInterface $httpClient) {}

    public function fetch(): Collection
    {
        $response = $this->httpClient->get($this->url, [
            'api-key' => $this->apiKey,
            'show-fields' => 'body,thumbnail',
        ]);

        return collect($response['response']['results'])->map(fn ($article) => [
            'title' => $article['webTitle'],
            'content' => $article['fields']['body'] ?? '',
            'author' => 'Unknown',
            'published_at' => $article['webPublicationDate'],
            'url' => $article['webUrl'],
            'image_url' => $article['fields']['thumbnail'] ?? null,
            'source_id' =>NewsAggregatorConstant::GUARDIAN_SOURCE_ID
        ]);
    }
}
