<?php

$autoloadPath = dirname(__DIR__).'/app/vendor/autoload_runtime.php';

if (!is_file($autoloadPath)) {
    throw new LogicException('Symfony Runtime is missing. Try running "composer require symfony/runtime".');
}

require_once $autoloadPath;

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
    ])
    ->setFinder($finder)
;
