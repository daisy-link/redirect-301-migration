## Wordpressの記事リダイレクト設定の出力

既存サイトと新サイトで記事IDが変わってしまう場合、タイトルを利用して、リダイレクト設定を生成します。

### 既存サイトに　download.php を設置して、CSVを出力

- download.php 内に必要な投稿タイプを設定して、アクセスして記事一覧を出力
- post_list.csv が出力

### 新サイトに　make.php・redirect.php　および上記の　post_list.csv を設置して、比較CSVを出力

- make.php にアクセスして既存と新規サイトの記事一覧を出力
- redirect_list.csv　に　新旧の記事一覧が出力されます。

### 新サイトに　redirect_list.csvを設置してredirect.phpにアクセス
- 一般的なリダイレクト設定が表示されます。
