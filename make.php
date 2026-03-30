<?php

require_once('../wp-load.php');

/**
 * =========================================
 * ① CSV読み込み（旧データ）
 * =========================================
 */
$csvFile = __DIR__ . '/post_list.csv';

$oldData = [];
$usedOld = []; // マッチ済み管理

if (($handle = fopen($csvFile, 'r')) !== false) {

    // BOM除去
    $firstLine = fgets($handle);
    $firstLine = preg_replace('/^\xEF\xBB\xBF/', '', $firstLine);

    do {
        $row = str_getcsv($firstLine);
        if (count($row) >= 3) {

            $title = normalize($row[1]);

            $oldData[$title] = [
                'id'    => $row[0],
                'title' => $row[1],
                'url'   => $row[2],
            ];
        }
    } while ($firstLine = fgets($handle));

    fclose($handle);
}

/**
 * タイトル正規化（重要）
 */
function normalize($str) {
    $str = trim($str);
    $str = mb_convert_kana($str, 's'); // 全角スペース→半角
    return $str;
}

/**
 * =========================================
 * ② WP記事取得
 * =========================================
 */
$args = [
    'post_type'      => ['post', 'works', 'voice'],
    'post_status'    => 'publish',
    'posts_per_page' => -1,
];

$posts = get_posts($args);

/**
 * =========================================
 * ③ CSV出力
 * =========================================
 */
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename=redirect_list.csv');

$output = fopen('php://output', 'w');

// BOM
fwrite($output, "\xEF\xBB\xBF");

// ヘッダー
fputcsv($output, [
    '新ID',
    '新タイトル',
    '新URL',
    '旧ID',
    '旧タイトル',
    '旧URL'
]);

/**
 * =========================================
 * ④ 新 → 旧 マッチング
 * =========================================
 */
foreach ($posts as $post) {

    $newTitle = normalize($post->post_title);
    $newUrl   = get_permalink($post->ID);

    $oldId = '';
    $oldTitle = '';
    $oldUrl = '';

    if (isset($oldData[$newTitle])) {
        $oldId    = $oldData[$newTitle]['id'];
        $oldTitle = $oldData[$newTitle]['title'];
        $oldUrl   = $oldData[$newTitle]['url'];

        $usedOld[$newTitle] = true; // 使用済み
    }

    fputcsv($output, [
        $post->ID,
        $post->post_title,
        $newUrl,
        $oldId,
        $oldTitle,
        $oldUrl
    ]);
}

/**
 * =========================================
 * ⑤ 旧にしかないデータを追加
 * =========================================
 */
foreach ($oldData as $key => $data) {

    if (isset($usedOld[$key])) {
        continue; // マッチ済みはスキップ
    }

    fputcsv($output, [
        '', // 新IDなし
        '', // 新タイトルなし
        '', // 新URLなし
        $data['id'],
        $data['title'],
        $data['url']
    ]);
}

fclose($output);
exit;