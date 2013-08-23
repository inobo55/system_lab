
web_learning.php
 => DBにあるデータを出力している。
    VIEWに使っている。
 	 
css
 => 自分で作ったcssファイルを置くところ。

js
 => 自分で作ったjsファイルを置くところ。

 	web_learning.js
 	 => web_learning.php専用のjsファイル
 	 	jquery-uiの機能を使用
 	 	ajaxでAPI経由でDBとやり取り

API
 => 自分で作ったDBとやり取りするプログラムを置くところ。

 	tool.php
 	 => PHPのクラスや関数をまとめたもの。関数は全てここ。

	save_post_data.php
	 => ~/Desktop/practice/javascript/web_learning/*から
	    送られたデータをMAMPデータベースに保存している。

 	check_same_url.php
 	 => 送られたurlがDBに保存済みかチェックする。

 	favorite_post.php
 	 => idとfavorite変数が送られるので、そのIDの状態に変更する。

 	favorite_get.php
 	 => web_learning.phpのfavoriteタブが押されたらajaxで_GETがくるので、
 	 	そしたらデータを表示させる。

Library
 => 外部から取り寄せたプログラムを蓄積してる。 require して使う。

 	simple_html_dom.php
 	 => ページの中身を解析できるプログラム

 	abbyy_php_example.php
 	 => OCR webサービスに pdf投げて、textを受け取るプログラム

pdf_files
 => 取得に成功したPDFの置き場所。

