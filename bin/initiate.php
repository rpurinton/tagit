#!/usr/bin/php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use RPurinton\Tagit\VersionManager;

$versionManager = new VersionManager();
$newVersion = $versionManager->updateComposerJson();
$versionManager->gitOperations($newVersion);

echo "Version updated to $newVersion and changes pushed to GitHub.\n";
