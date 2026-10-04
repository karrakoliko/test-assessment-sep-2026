<?php

namespace App\App\Service\CategoryManagement;

use App\App\Service\CategoryManagement\ValueObjects\CategoryDescription;
use App\App\Service\CategoryManagement\ValueObjects\CategoryTitle;

class Category
{
    private CategoryTitle $title;
    private CategoryDescription $description;

    public function __construct(
        string $title,
        string $description
    ) {
        $this->title = new CategoryTitle(title: $title);
        $this->description = new CategoryDescription(description: $description);
    }

    public function getTitle(): CategoryTitle
    {
        return $this->title;
    }

    public function getDescription(): CategoryDescription
    {
        return $this->description;
    }

}
