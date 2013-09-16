<?php
/* * * * * 案１
* web_learning.phpの推薦tabをクリックするたびに、$_GETがここに送られる。
* 送られたら、DBにある「favorite=true」かつ「tagsが存在する」、tagsを全て取ってくる。
* 全tagsの中から、ランダムでtagを選択。選択したtagを使ってSearchAPIで検索。
* * * * * 案２
* 予めクローラーしてて、カテゴリキーワードとの適度な一致から推薦するページも決めてる状態。
* 推薦タブを押すたびに、推薦ページが見れる。
* * * * * 案３
* 
* 
* 
* 
*/

//案１で行こうかな

require 'tool.php';



$search_words = array();
$db = new DATABASE();
$link = $db->mysqlConnect();
$sql = $db->getFavTagsSQL();
if($res = $db -> mysqlSelect($sql,$link)){
	while($row = mysql_fetch_array($res)){
		$tag_array = preg_split('/,/',$row['tags']);
		$search_words = array_merge($search_words,$tag_array);
	}
	$w1 = $w2 = NULL; //検索に用いる単語
	do{
		$i1 = rand(0,count($search_words));
		$i2 = rand(0,count($search_words));
		$w1 = $search_words[$i1];
		$w2 = $search_words[$i2];
		echo $w1.' '.$w2;
	}while(strcasecmp($w1,$w2) === 0);

	$search_agent = new SearchAPI($w1.' '.$w2);
	$text = $search_agent->getDatas();
	echo $text;
}





?>