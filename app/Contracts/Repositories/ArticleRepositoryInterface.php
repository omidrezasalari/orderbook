<?php

namespace App\Contracts\Repositories;

use App\DTOs\ArticleDTO;
use Illuminate\Database\Eloquent\Collection;

interface ArticleRepositoryInterface
{
    public function save(ArticleDTO $article): void;
    public function getAll(): Collection;
}
