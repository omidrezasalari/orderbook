<?php

namespace App\DTOs;

class ArticleDTO
{
    public function __construct(
        public string  $title,
        public string  $content,
        public ?string $author,
        public string  $publishedAt,
        public string  $url,
        public ?string $imageUrl,
        public int     $sourceId,
        public ?int    $categoryId
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['title'],
            $data['content'] ?? '',
            $data['author'] ?? 'Unknown',
            $data['published_at'],
            $data['url'],
            $data['image_url'] ?? null,
            $data['source_id'],
            $data['category_id'] ?? null
        );
    }
}
