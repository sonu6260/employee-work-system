<?php $pageTitle = $pageTitle ?? 'Employee Work Management System'; ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <?php
        $scriptPath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $projectRoot = '/employee-work-system/';
        $projectPrefix = strpos($scriptPath, $projectRoot) === 0 ? $projectRoot : '/';
        $isNestedPage = strpos(substr($scriptPath, strlen($projectPrefix)), '/') !== false;
        $assetUrl = ($isNestedPage ? '../' : '') . 'assets/style.css';
    ?>
    <link rel="stylesheet" href="<?= htmlspecialchars($assetUrl, ENT_QUOTES, 'UTF-8') ?>">
</head>
<body>
<header class="site-header"><div class="container">
    <a class="brand" href="../index.php">Employee Work System</a>
</div></header>
<main class="container">
