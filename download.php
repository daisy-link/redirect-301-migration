<?php

require_once('../wp-load.php');

$args = [
    'post_type'      => ['post', 'works', 'voice'],
    'post_status'    => 'publish',
    'posts_per_page' => -1,
];

$posts = get_posts($args);

// CSV出力
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename=post_list.csv');

$output = fopen('php://output', 'w');

// BOM（Excel対策）
fwrite($output, "\xEF\xBB\xBF");

// ヘッダー
fputcsv($output, ['ID', 'タイトル', 'URL']);

foreach ($posts as $post) {
    $url = get_permalink($post->ID);

    fputcsv($output, [
        $post->ID,
        $post->post_title,
        $url
    ]);
}

fclose($output);
exit;