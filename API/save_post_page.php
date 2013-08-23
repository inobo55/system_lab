<?php
/*
	ブラウザのボタンを押したら、開いたページのデータをこのファイルに送信される。

	~/Desktop/practice/javascript/chrome/webLearning/...から
	POSTされたデータを登録する.
	登録データ　[id=>NULL,url,title,favIconUrl,image(src?)]
	上記のデータをデータベースにINSERTする.

*/
	require_once "tool.php";

	$db = new DATABASE();

	header("Content-Type: application/xml; charset=UTF-8");
 	echo '<?xml version="1.0" encoding="UTF-8"?>';

 	echo "<return>";
	if(!empty($_POST)){
		if( $link = $db->mysqlConnect() ){

			//初期値を代入
			$title = $db->h($_POST['title']);
			$favIconUrl = $db->h($_POST['favIconUrl']);
			$image = $db->h($_POST['image']);
			$url = $db->h($_POST['url']);
			$url = $db->compensateUrl($url,$_POST);
			$sql = $db->getInsertSQL($url,$title,$favIconUrl,$image);
			
			if( $db->mysqlSelect($sql,$link) ){

				// 先ほど保存したURLにPDFが存在すれば、DIRとDBへの保存処理を行う
				$sql_lastOne = $db->getSelectLastSQL();
				echo "<result>SQL成功!</result>";
				
				if( $resource = $db->mysqlSelect($sql_lastOne,$link) ){
					echo "<lastOne>SQL成功!</lastOne>";
					$pdfSaver = new SimpleHtmlDom();
					$row = mysql_fetch_array($resource);
					if( $pdfSaver->savePdfWith($row['url']) ){
						echo "<pdf>PDF保存成功</pdf>";
						$pdf_id = $row['pdf_file_table_id'];
						if( is_null($pdf_id) ){
							//pdf_file_table最新のIDゲット
							$pdf_id = mysql_insert_id(); 
							echo "pdf_id:".$pdf_id;
							$sql = $db->getUpdatePdfIdSQL($pdf_id,$row['id']);
							if( $db->mysqlSelect($sql,$link) ){
								echo "<pdf_id>SQL成功!</pdf_id>";
							}else
								echo "<pdf_id>SQL失敗!</pdf_id>";
						}else
							echo "<error>PDF_ID保存済み!?</error>";
					}else
						echo "<pdf>PDF保存しない</pdf>";
				}else
					echo "<lastOne>SQL失敗!</lastOne>";				
			}else
				echo "<result>SQL失敗!</result>";			
		}else
			echo "<result>DBConnect失敗!</result>";
		
		$db->echoVarDump($_POST);
	}else{
		echo "POSTは空だよん";
	}
	echo "</return>";
?>