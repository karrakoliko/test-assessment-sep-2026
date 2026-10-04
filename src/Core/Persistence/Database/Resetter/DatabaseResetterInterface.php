<?php

namespace App\Core\Persistence\Database\Resetter;

interface DatabaseResetterInterface
{
    public function reset(): void;

}
