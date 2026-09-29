<?php

require_once 'vendor/autoload.php';

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true
    ])
    ->setCacheFile(sys_get_temp_dir().'/.php-cs-fixer.cache')
    ->setFinder($finder)
    ;