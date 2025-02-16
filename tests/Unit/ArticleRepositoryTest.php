<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Article;
use App\Repositories\ArticleRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\DTOs\ArticleDTO;

class ArticleRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_store_article()
    {
        $repository = new ArticleRepository();

        $articleDTO = new ArticleDTO(
            'Test News',
            'This is a test content.',
            'John Doe',
            '2024-02-15T12:00:00Z',
            'https://example.com/news/1',
            'https://example.com/image.jpg',
            1
        );

        $article = $repository->save($articleDTO);

        $this->assertDatabaseHas('articles', ['title' => 'Test News']);
        $this->assertInstanceOf(Article::class, $article);
    }
}
