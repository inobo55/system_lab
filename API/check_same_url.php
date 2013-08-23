<?php
/*
　タブで開いたページのURLが以前DBに登録したものかを調べる。
　登録してあったら、 文字'registered_url'を出力。
　未登録ならば、文字'not_registered'を出力。
*/

	require 'tool.php';

	$db = new DATABASE();

	header("Content-Type: application/xml; charset=UTF-8");
 	echo '<?xml version="1.0" encoding="UTF-8"?>';
 	if( empty($_POST) ){
		echo "POST ERROR";
		return;
	}

 	echo "<return>";
	
	if( $link = $db->mysqlConnect() ){
		$url = $db->h( $_POST['url'] );
		$sql = $db->getSelectSQLWhereUrl($url);
		if($resource = $db->mysqlSelect($sql,$link) ){
			if( $row = mysql_fetch_array($resource) )
				echo "registered";
			else
				echo  "new url";
		}else
			echo "SQL:ERROR"; 
	}else
		echo "ERROR NOT CONNECTED DATABASE";
	
	echo "</return>";
	
?>