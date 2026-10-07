<?php

namespace App\App\Service\HomePage\Dto;

use App\App\Service\ArticleManagement\Article;
use App\App\Service\CategoryManagement\Category;

class HomePageCategoryDto
{
    /**
     * @param Category $category
     * @param iterable<Article> $latestArticles
     */
    public function __construct(protected Category $category, protected iterable $latestArticles)
    {
    }

    /** @noinspection PhpUnused */
    public function getCategory(): Category
    {
        return $this->category;
    }

    /** @noinspection PhpUnused */
    public function getLatestArticles(): iterable
    {
        yield from $this->latestArticles;
    }


}
