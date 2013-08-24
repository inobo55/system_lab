<?php
/*
	このプログラムは使わない。
	
	id(weblearningテーブルのID)をPOSTで渡されるので、
	それをもとにtagsの中身が空かチェック。
	pdf_file_table_idの中身が有るかチェック。
	空かつ有るなら、PDFファイルの場所を特定。
	abbyy_php_example.phpに渡す。

*/
	require_once 'tool.php';

	//POSTされてなければパス
	if(empty($_POST) || is_null($_POST)){
		echo "NO POST";
		return;
	}
	//tagsとpdf_file_tale_idの中身チェック 
	$db = new DATABASE();
	$id = $db->h($_POST['id']);
	$link = $db->mysqlConnect();
	$sql = $db->getSelectSQLWhereId($id);
	if($resource = $db->mysqlSelect($sql,$link) ){
		$row = mysql_fetch_array($resource);
		if(empty($row)){
			echo "NOT MATCH ID";
			return;
		}
		$pdf_id = $db->h($row['pdf_file_table_id']);
		$tags = $db->h($row['tags']);
		if( !empty($tags) || is_null($pdf_id) ){
			echo "TAGS EXIST & PDF_ID IS NULL";
			return;
		}
		

		//ファイル名をゲットする
		$sql = $db->getSelectPdfFileSQL($pdf_id);
		if( $resource = $db->mysqlSelect($sql,$link) ){
			if( $row = mysql_fetch_array($resource) ){
				$filename = $db->h($row['pdf_file_name']);
				echo "filename:";
				$ocr = new AbbyyOcrSdk($filename);	
				$file_contents = $ocr->sendRequest();
				echo "AbbyyOcrSDK:";
				$keyAnalyzer = new KeyPhraseExtract_yahoo();
				$keyAnalyzer->InsertKeyPhraseToDbWith($file_contents);
				echo "succeed";
			}
		}
	}


?>