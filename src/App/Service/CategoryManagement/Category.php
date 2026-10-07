<?php

namespace App\App\Service\CategoryManagement;

use App\App\Service\CategoryManagement\ValueObjects\CategoryDescription;
use App\App\Service\CategoryManagement\ValueObjects\CategoryTitle;

class Category
{
    private CategoryTitle $title;
    private CategoryDescription $description;
    private ?int $id;

    public function __construct(
        string $title,
        string $description,
        ?int $id = null,
    ) {
        $this->title = new CategoryTitle(title: $title);
        $this->description = new CategoryDescription(description: $description);
        $this->id = $id;
    }


    public function getTitle(): CategoryTitle
    {
        return $this->title;
    }

    public function getDescription(): CategoryDescription
    {
        return $this->description;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

}
