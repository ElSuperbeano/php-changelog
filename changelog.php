#!/usr/bin/php
<?php
$basePath = getcwd();

if (file_exists("{$basePath}/vendor/autoload.php")) {
    require_once "{$basePath}/vendor/autoload.php";
}

$definitions = include __DIR__ . '/config/php-changelog.php';

if (file_exists("{$basePath}/config/php-changelog.php")) {
    $definitions = array_replace_recursive($definitions, include "{$basePath}/config/php-changelog.php");
}

// creating lightweight DI container
$container = \PhpChangelog\ContainerBuilder::build($definitions);

$commitParser = $container->get('CommitParser');
$output = $container->get('GitReader')->read();
$messages = [];
foreach ($output as $commit) {
    $messages[] = $commitParser->parse($commit);
}

$container->get('MarkdownWriter')->write($messages);
