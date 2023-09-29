<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__.'/src')
    ->in(__DIR__.'/tests')
;

$config = (new PhpCsFixer\Config())
    ->setRules([
        '@PhpCsFixer' => true,
        '@PSR2' => true,
        'php_unit_internal_class' => false,
        'php_unit_test_class_requires_covers' => false,
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => false,
        ],
    ])
    ->setUsingCache(true)
    ->setFinder($finder)
;

return $config;
