<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\Sources\NewsAPIService;
use App\Contracts\HttpClientInterface;
use Illuminate\Support\Collection;
use Mockery;

class NewsAPIServiceTest extends TestCase
{
    public function test_fetch_news_api_returns_collection()
    {
        $fakeResponse = [
            'articles' => [
                [
                    'title' => 'Test News',
                    'content' => 'This is a test content.',
                    'author' => 'John Doe',
                    'publishedAt' => '2024-02-15T12:00:00Z',
                    'url' => 'https://example.com/news/1',
                    'urlToImage' => 'https://example.com/image.jpg'
                ]
            ]
        ];


        $httpClientMock = Mockery::mock(HttpClientInterface::class);
        $httpClientMock->shouldReceive('get')
            ->once()
            ->andReturn($fakeResponse);


        $newsAPIService = new NewsAPIService($httpClientMock);
        $articles = $newsAPIService->fetch();

        $this->assertInstanceOf(Collection::class, $articles);
        $this->assertCount(1, $articles);
        $this->assertEquals('Test News', $articles[0]['title']);
    }
}
