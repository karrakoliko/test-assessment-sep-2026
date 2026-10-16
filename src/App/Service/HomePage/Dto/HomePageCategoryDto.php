<?php

/** @noinspection PhpUnused */

namespace App\App\Service\HomePage\Dto;

use App\App\Service\ArticleManagement\Article;
use App\App\Service\CategoryManagement\Category;

class HomePageCategoryDto
{
    /**
     * @param Category $category
     * @param iterable<Article> $articlesLatest
     */
    public function __construct(
        protected Category $category,
        protected iterable $articlesLatest
    ) {
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function getArticlesLatest(): iterable
    {
        yield from $this->articlesLatest;
    }

}
