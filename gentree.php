<?php declare(strict_types = 1);

require_once(__DIR__. '/src/GenTree.php');

$inputFile = 'input.csv';
$outputFile = 'output.json';

$app = new GenTree();

if ($app->loadCSV($inputFile))
{
    $app->build(true);
    $app->saveJson($outputFile);
}
