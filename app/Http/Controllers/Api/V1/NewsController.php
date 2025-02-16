<?php

namespace App\Http\Controllers\Api\V1;

use App\Repositories\ArticleRepositoryInterface;
use Illuminate\Http\JsonResponse;

class NewsController extends Controller
{
    public function __construct(
        private readonly ArticleRepositoryInterface $articleRepository
    ){}

    public function index(): JsonResponse
    {
        return response()->json($this->articleRepository->getAll());
    }
}
