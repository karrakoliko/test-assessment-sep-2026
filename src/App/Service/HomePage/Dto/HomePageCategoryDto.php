<?php

/** @noinspection PhpUnused */

namespace App\App\Service\HomePage\Dto;

use App\App\Service\ArticleManagement\Article;
use App\App\Service\CategoryManagement\Category;
use Uri\Rfc3986\Uri;

class HomePageCategoryDto
{
    /**
     * @param Category $category
     * @param iterable<Article> $articlesLatest
     * @param Uri $linkToAllArticles
     */
    public function __construct(
        protected Category $category,
        protected iterable $articlesLatest,
        protected Uri $linkToAllArticles
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

    public function getLinkToAllArticles(): Uri
    {
        return $this->linkToAllArticles;
    }

}
