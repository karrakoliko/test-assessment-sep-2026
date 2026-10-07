<?php

namespace App\App\Service\Seeder;

use App\App\Service\CategoryManagement\Category;

interface SeederInterface
{
    public function seedCategories(int $n): void;
    public function seedArticles(int $n, Category $category): void;

}
