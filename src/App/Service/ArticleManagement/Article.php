<?php

namespace App\App\Service\ArticleManagement;

use App\App\Service\ArticleManagement\ValueObjects\ArticleContent;
use App\App\Service\ArticleManagement\ValueObjects\ArticleDescription;
use App\App\Service\ArticleManagement\ValueObjects\ArticlePicUri;
use App\App\Service\ArticleManagement\ValueObjects\ArticleTitle;
use App\App\Service\CategoryManagement\Category;

class Article
{
    private ArticleTitle $title;
    private ArticleContent $content;
    private ArticleDescription $description;
    private Category $category;
    private ArticlePicUri $picUri;
    private ?int $id;

    public function __construct(
        string $title,
        string $content,
        string $description,
        string $picUri,
        Category $category,
        ?int $id = null
    ) {
        $this->title = new ArticleTitle($title);
        $this->content = new ArticleContent($content);
        $this->description = new ArticleDescription($description);
        $this->picUri = new ArticlePicUri($picUri);
        $this->category = $category;
        $this->id = $id;
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

    public function getPicUri(): ArticlePicUri
    {
        return $this->picUri;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }


}
