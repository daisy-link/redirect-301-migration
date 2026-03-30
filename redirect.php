<?php

$csvFile = __DIR__ . '/redirect_list.csv';

function getPath($url) {
    $parsed = parse_url($url);
    return $parsed['path'] ?? '';
}

function getHost($url) {
    return parse_url($url, PHP_URL_HOST);
}

/**
 * Rewrite用パス整形
 */
function normalizeRewritePath($path) {
    $path = trim($path, '/');
    return $path;
}

if (!file_exists($csvFile)) {
    die('CSVファイルが見つかりません');
}

if (($handle = fopen($csvFile, 'r')) !== false) {

    $header = fgets($handle);
    $header = preg_replace('/^\xEF\xBB\xBF/', '', $header);

    echo "<pre>";

    echo "RewriteEngine On\n\n";

    while (($row = fgetcsv($handle)) !== false) {

        $newUrl = trim($row[2] ?? '');
        $oldUrl = trim($row[5] ?? '');

        if (!$newUrl || !$oldUrl) continue;

        $newPath = getPath($newUrl);
        $oldPath = getPath($oldUrl);

        if (!$newPath || !$oldPath) continue;

        $newHost = getHost($newUrl);
        $oldHost = getHost($oldUrl);

        $newRewrite = normalizeRewritePath($newPath);
        $oldRewrite = normalizeRewritePath($oldPath);

        // 同じなら不要
        if ($newRewrite === $oldRewrite) continue;

        // 同一ドメイン
        if ($newHost === $oldHost) {

            echo "RewriteRule ^{$oldRewrite}/?$ /{$newRewrite}/ [R=301,L]\n";

        } else {

            echo "RewriteRule ^{$oldRewrite}/?$ {$newUrl} [R=301,L]\n";
        }
    }

    echo "</pre>";

    fclose($handle);
}