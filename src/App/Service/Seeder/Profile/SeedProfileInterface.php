<?php

namespace App\App\Service\Seeder\Profile;

interface SeedProfileInterface
{
    public function generate(array $params = []): mixed;
}
