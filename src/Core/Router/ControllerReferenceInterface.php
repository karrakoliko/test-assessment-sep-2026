<?php

namespace App\Core\Router;

interface ControllerReferenceInterface
{
    public function getClass(): string;

    public function getMethod(): string;

}
