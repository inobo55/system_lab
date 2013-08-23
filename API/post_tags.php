<?php
/*
	Google Wireless Transcoder (GWT)
	にcurlでも何でもいいから、sendして結果のテキストデータを受け取りたい。
*/

require_once "tool.php";

/*
 JavascriptからIDがポストされるので、それを用いてDBを検索。PDFのURLをゲット。
 PDFのURLをもとに、PDFをテキスト化。テキストからキーフレーズ抽出して、キーフレーズをDBに保存。
*/

//POSTされてなければパス
if(empty($_POST) || is_null($_POST)){
	echo "NO POST";
	return;
}

$db = new DATABASE();
$id = $db->h($_POST['id']);
$link = $db->mysqlConnect();
$sql = $db->getSelectSQLWhereId($id);

//idからtagsとpdf_file_table_idを確認する
if($resource = $db->mysqlSelect($sql,$link) ){
	$row = mysql_fetch_array($resource);
	if(empty($row))
		die("NOT MATCH ID");

	$pdf_id = $db->h($row['pdf_file_table_id']);
	$tags = $db->h($row['tags']);
/*	TODO:
	とりあえず、これは置いておく。本番仕様では実装しようかな。無駄な処理をさせないために
	if( !empty($tags) )
		die("TAGS ALREADY EXISTS");
	else if( is_null($pdf_id) || empty($pdf_id) || $pdf_id === "NULL" )
		die("PDF_ID IS NULL. THIS IS NOT PDF");
*/
	if( is_null($pdf_id) || empty($pdf_id) || $pdf_id === "NULL" )
		die("PDF_ID IS NULL. THIS IS NOT PDF");
	//ファイル名をゲットする
	$sql = $db->getSelectPdfFileSQL($pdf_id);
	if( $resource = $db->mysqlSelect($sql,$link) ){
		if( $row = mysql_fetch_array($resource) ){
			$pdf_url = $db->h($row['pdf_src']); 
			$pdfToTexter = new GoogleTransCoder($pdf_url);
			$pdf_text = $pdfToTexter->getText();
			if($pdf_text){
				//キーフレーズ解析
				//echo "YAHOO:";
				//$small_text = mb_substr($pdf_text,0,1000,"UTF-8");
				//TODO:Yahooは1000文字より多いとアウト。
				// ＝＞MecabとTF-IDFを自作で実装するしかないかな。
				//参考：http://okwave.jp/qa/q6186935.html
				//$keyAnalyzer = new KeyPhraseExtract_yahoo($small_text);
				//$keyphrase = $keyAnalyzer->getKeyPhrase();
				echo "Mecab+TFIDF:";
				$analyzed = MeCab::analyze(str_replace(array("\r\n","\r","\n"), '',$pdf_text));
				$tfidf = new TFIDF($analyzed);
				$keyphrase = $tfidf->getKeyphrase();
				var_dump($keyphrase);

				if( $db->updateTagsWith($keyphrase,$id) )
					echo "/OK/";
				else
					echo "/ERROR/".$id."/".$keyphrase;
			}else{
				//OCRによるテキスト化
				echo "OCR:";
			}	
		}else{
			die("NONO");
		}
	}

}

mysql_close($link);

?>
