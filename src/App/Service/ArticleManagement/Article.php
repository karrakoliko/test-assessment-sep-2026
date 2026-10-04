<?php

namespace App\App\Service\ArticleManagement;

use App\App\Service\ArticleManagement\ValueObjects\ArticleContent;
use App\App\Service\ArticleManagement\ValueObjects\ArticleDescription;
use App\App\Service\ArticleManagement\ValueObjects\ArticlePicUrl;
use App\App\Service\ArticleManagement\ValueObjects\ArticleTitle;
use App\App\Service\CategoryManagement\Category;

class Article
{
    private ArticleTitle $title;
    private ArticleContent $content;
    private ArticleDescription $description;
    private Category $category;
    private ArticlePicUrl $picUrl;

    public function __construct(
        string $title,
        string $content,
        string $description,
        string $picUrl,
        Category $category
    ) {
        $this->title = new ArticleTitle($title);
        $this->content = new ArticleContent($content);
        $this->description = new ArticleDescription($description);
        $this->picUrl = new ArticlePicUrl($picUrl);
        $this->category = $category;
    }

    public function getTitle(): ArticleTitle
    {
        return $this->title;
    }

    public function getContent(): ArticleContent
    {
        return $this->content;
    }

    public function getDescription(): ArticleDescription
    {
        return $this->description;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function getPicUrl(): ArticlePicUrl
    {
        return $this->picUrl;
    }


}
