<?php

namespace Syndicate\Promoter\Prompts;

use Syndicate\Engineer\Contracts\Answer;

readonly class SeoDataAnswer implements Answer
{
    public function __construct(
        public string $title,
        public string $description,
        public string $image_alt,
        public string $slug,
        public ?string $generated_keyword = null,
        public ?int $keyword_score = null,
    ) {
    }

    public function getContent(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'image_alt' => $this->image_alt,
            'slug' => $this->slug,
            'generated_keyword' => $this->generated_keyword,
            'keyword_score' => $this->keyword_score,
        ];
    }
}
