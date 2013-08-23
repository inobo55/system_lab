<?php
	header("Content-Type: text/html; charset=UTF-8");
/*
	何するページ：
	　1.今まで記録してきたページを見ることができる。
	　2.ページのつながりを作れる。（重ねることで同じ学習に用いたことを表現）
	　3.そのつながりが何の学習に用いたかを入力。
	　4.順番に並べる。その学習ページを見た目的を入力、理解したことも入力。
	　(5).4で可能であれば、プログラムがどのように変わったかも入力したい。
	　6.並べて入力も終えたら、データベースに保存。
	期待すること：
	・（難）システム開発とWeb学習と認知的徒弟制の混合
	・（易）ページ履歴だけでなく、学習履歴も見れる。
	・（易）画像と簡単な内容も表示。
*/
	require 'API/tool.php';
	$view = new VIEW();

?>
<!DOCTYPE html>
<html lang='ja'>
<head>
	<meta charset='UTF-8'>
	<title>今までのWeb学習</title>
	<script src='./Library/jquery-ui-1.10/js/jquery-1.9.1.js'></script>
	<script src='./Library/jquery-ui-1.10/js/jquery-ui-1.10.3.custom.min.js'></script>
	<link rel="stylesheet" type="text/css" href="./Library/jquery-ui-1.10/css/hot-sneaks/jquery-ui-1.10.3.custom.min.css">
	<link rel="stylesheet" type="text/css" href="./css/web_learning.css">
	<script src='./js/web_learning.js'></script>

</head>
<body>
<!--
	プログラムの流れ：
		1.phpでデータベースから全ての登録ページを取得 
		ok
		2.最新のものから順番に並べる。
		ok
		3.2のときに画像／タイトル／内容文章もあるとなおいい
		soso
		4.ページをドラッグできて、どこかのエリアに重ねることで、蓄積。
		 @jqueryUIを使用
		5.4で蓄積したページに「学習名」を入力。決定ボタン。
		6.ページが遷移して、その学習ページが横一列に並んでいる。
		7.学習ページを縦に順番に並べて、そのページを見た目的／理解したことを入力。
		8.並び終えたら、完了ボタン。もとのページに戻る。
-->
	<div id="tabs">
		<ul>
			<li><a id= 'a-tab1' href='#tabs-1'>閲覧履歴</a></li>
			<li><a id= 'a-tab2' href='#tabs-2'>お気に入り</a></li>			
			<li><a id= 'a-tab3' href='#tabs-3'>学習コンテンツを作る</a></li>
			<li><a id= 'a-tab4' href='#tabs-4'>今までの学習コンテンツ</a></li>
		</ul>
		<div id='tabs-1'>
			<div id='history_contents'>
				<?php $view->displayDbData("history"); ?>
			</div><!--/history_contents-->
		</div>
		<div id='tabs-2'>
			<div id='favorite_contents'>
				<!-- web_learning.jsで中身を埋めてる -->
			</div>
		</div>
		<div id='tabs-3'>
			<div id='learning_contents'>
				<?php  $view->displayDbData("learning"); ?>
			</div><!--/learning_contents-->
		</div>
		<div id='tabs-4'>
			<div id='learned_contents'>
				
			</div><!--/learned_contents-->
		</div>		
	</div><!--/tabs-->
	
</body>
</html>

