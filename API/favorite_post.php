<?php
/*
	何するプログラム？：
		web_learning.phpのViewでお気に入りボタンを押されたら、
		POSTでこのファイルにデータが送信される。
		データを受信したら、データベースのお気に入り項目をにALTERする。
		ALTERに成功したら、'OK'.失敗したら、'ERROR'を返信。
		
		受信するデータ=[id,state]
		DB送信データ=[id,favorite='true'or'false']
		返信データ=[OK or ERROR]

*/

	require 'tool.php';

	$db = new DATABASE();
	
	if(!empty($_POST)){
		
		$id = $db->h($_POST['id']);
		$favorite = $db->h($_POST['favorite']);
		
		if( $link = $db->mysqlConnect() ){
			$sql = $db->getUpdateSQLofFavoriteWith($id,$favorite);
			if( $db->mysqlSelect($sql,$link) ){
				// 成功したら
				echo "SQL:OK";
			}else{
				echo "SQL:ERROR";
			}
		}
	}

?>