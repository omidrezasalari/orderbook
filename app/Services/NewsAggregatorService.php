<?php

namespace App\Services;

use App\DTOs\ArticleDTO;
use App\Repositories\ArticleRepositoryInterface;

class NewsAggregatorService
{
    public function __construct(
        private readonly ArticleRepositoryInterface $articleRepository,
        private readonly array                      $sources
    ) {}

    public function fetchAndStore(): void
    {
        foreach ($this->sources as $source) {
            $articles = $source->fetch();
            foreach ($articles as $articleData) {
                $this->articleRepository->save(new ArticleDTO(...$articleData));
            }
        }
    }
}

