<?php

namespace App\Repositories;

use App\DTOs\ArticleDTO;
use App\Models\Article;
use Illuminate\Database\Eloquent\Collection;

class ArticleRepository implements ArticleRepositoryInterface
{
    public function save(ArticleDTO $article): void
    {
        Article::updateOrCreate(
            ['url' => $article->url],
            [
                'title' => $article->title,
                'content' => $article->content,
                'author' => $article->author,
                'published_at' => $article->publishedAt,
                'image_url' => $article->imageUrl,
                'source_id' => $article->sourceId,
                'category_id' => $article->categoryId,
            ]
        );
    }

    public function getAll(): Collection
    {
        return Article::all();
    }
}
