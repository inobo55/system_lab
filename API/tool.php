<?php

// 参照URL http://liginc.co.jp/programmer/archives/4921
// PDFファイル取得 or 画像取得 or テキストフレーズ取得 に用いる予定

require_once dirname(__FILE__).'/../Library/simple_html_dom.php';
require_once dirname(__FILE__).'/../Library/mecab.php';

//エラー設定
ini_set( 'display_errors', 1 );

class DATABASE{

	private $host = 'localhost';
	private $user = 'root';
	private $pass = 'root';
	private $db_name = 'chrome';
	private $table_name_web = 'weblearning';
	private $table_name_pdf = 'pdf_file_table';

	private function initMysql(){
		//mySQLの前処理
		mb_language("uni");
		mb_internal_encoding("utf-8");
		mb_http_input("auto");
		mb_http_output("utf-8");
	}

	//データベース接続する際はこの関数を使う。返り値$link。
	public function mysqlConnect(){
		$this->initMySQL();
		$link = mysql_connect($this->host, $this->user,$this->pass);

		if(!$link){
			echo "接続に失敗しました。";
			return FALSE;
		}
		mysql_query("SET NAMES utf8",$link);
		mysql_select_db($this->db_name, $link);
		if(!$link){
			echo "DBが見つかりません。";
			return FALSE;
		}
		return $link;
	}

	//mySQLを操作するときはこの関数を使う
	public function mysqlSelect($sql, $link){
		$result = mysql_query($sql, $link);
		return $result;
	}


	// Database table = weblearning
	public function getInsertSQL($url,$title,$favIconUrl,$image){

		$sql = "INSERT INTO `chrome`.`weblearning` (`id`, `url`, `title`, `faviconurl`, `image`, `post_time`) VALUES (NULL, '$url', '$title', '$favIconUrl', '$image', CURRENT_TIMESTAMP);";
		return $sql;

	}

	public function getSelectSQLWhereUrl($url){
		$sql = "SELECT * FROM  `weblearning` WHERE  `url` =  '$url' LIMIT 0 , 1";
		return $sql;
	}
	public function getSelectSQLWhereId($id){
		$sql = "SELECT * FROM  `weblearning` WHERE  `id` =  '$id'";
		return $sql;
	}

	// Database table = weblearning
	public function getSelectSQL(){
		$sql = "SELECT * FROM  `weblearning` ORDER BY  `id` DESC LIMIT 0 , 50";
		return $sql;
	}

	public function getSelectLastSQL(){
		$sql = "SELECT * FROM  `weblearning` ORDER BY  `id` DESC LIMIT 0 , 1";
		return $sql;
	}
	
	public function getSelectAllUrlSQL(){
		$sql = "SELECT DISTINCT `url` FROM  `weblearning`";
		return $sql;
	}

	public function getUpdateSQLofFavoriteWith($id,$favorite){
		$sql = "UPDATE  `chrome`.`weblearning` SET  `favorite` =  '$favorite' WHERE  `weblearning`.`id` = $id;";
		return $sql;
	}

	public function getSelectSQLWhereFavorite(){
		$sql = "SELECT * FROM `weblearning` WHERE `favorite` = 'true';";
		return $sql;
	}

	// Database table = pdf_file_table
	public function getInsertSQL_pdf($pdf_url,$pdf_file_name){

		$sql = "INSERT INTO `chrome`.`pdf_file_table` (`id`, `pdf_src`, `pdf_file_name`, `post_time`) VALUES (NULL, '$pdf_url', '$pdf_file_name', CURRENT_TIMESTAMP);";

		return $sql;
	}

	public function getLatestPdfId(){
		$sql = "SELECT * FROM `chrome`.`pdf_file_table` ORDER BY `id` DESC LIMIT 0,1";
		return $sql;
	}

	public function getSelectPdfFileSQL($pdf_id){
		$sql = "SELECT * FROM `pdf_file_table` WHERE `id` = $pdf_id";
		return $sql;
	}


	public function getUpdatePdfIdSQL($pdf_id,$id){
		$sql = "UPDATE `chrome`.`weblearning` SET `pdf_file_table_id` = '$pdf_id' WHERE `weblearning`.`id` = $id";
		return $sql;
	}

	public function getUpdateTagsSQL($phrase,$id){
		$sql = "UPDATE  `chrome`.`weblearning` SET  `tags` =  '$phrase' WHERE  `weblearning`.`id` =$id";
		return $sql;
	}

	
	public function updateTagsWith($phrase,$id){
		$result = FALSE;
		$sql = $this->getUpdateTagsSQL($phrase,$id);
		if($link = $this->mysqlConnect())
			if( $this->mysqlSelect($sql,$link) )
				$result = TRUE;
			else
				$result = FALSE;
			
		//mysql_close($link);
		return $result;
	}

	// $POSTの中からurl補完
	// http://ci.nii.ac.jp/els/110002937669.pdf?id=ART0003289472&type=pdf&lang=jp&host=cinii&order_no=&ppv_type=0&lang_sw=&no=1374339184&cp=
	// &が区切りで、それ以降の文字が無くなる
	public function compensateUrl($url,$post){
		foreach ($post as $key => $value) {
			if($key ==='title' || $key === 'favIconUrl' || $key === 'image' || $key === 'url') continue;
			$urlData = '&'.$key.'='.$value;
			$url .=  $urlData;
		}
		return $url;
	}

	public function echoVarDump($data){
		echo "<dump>";
		var_dump($data);
		echo "</dump>";	
	}

	public function h($str){
		return htmlspecialchars($str);
	}

}//DATABASE class

/* web_learning.phpなどのViewで扱う関数
*	
*/

class VIEW{

	private function printDataFrom($row){

		$webAPI = "http://capture.heartrails.com/large?";

		print("<div class='content'>
				<div class='header'>
					<span class='id'>".$row['id']."</span>
					<h2>".$row['title']."</h2>
				</div>
				<div class='img'>
					<a href='".$row['url']."' target='blank_'>
						<img src='".$webAPI.$row['url']."'>
					</a>
				</div>
				<div class='favorite'>
					<input type='checkbox' id='".$row['id']."'  data-favorite='".$row['favorite']."' class='check'");
					if($row['favorite'] === 'true')
						 echo(" checked='checked' ");
					print(" /><label for='check'>お気に入り</label>
				</div>
				<div class='footer'>
					<a href='".$row['url']."' target='blank_'>
						<img src='".$row['faviconurl']."'>
						<span class='url'>".$row['url']."</span>
					</a>
					<span class='post_time'>".$row['post_time']."</span>
				</div>
			</div>");

	}

	private function lineUpDataFrom($row){
		//自分でJavacriptのプラグインを作ってみようか
		/*
			どんなプラグイン？
			画面下部でimageが並んだ状態。
			それが左か右にスクロールできる。
			imageを選択したら、その画像が拡大され、タイトルとアイコンが表示される。
			タイトルをクリックしたら、そのページがblankで表示される。
			画面下部にあるimageをドラッグして画面上部にセットできる。
			画面上部の同じところにセットもでき、違うところにセットも可能。
			同じところにセットしたら、画面がソリフィアのように重なる。
		*/
	 	print_r("
	 		<div id='header_learning'>
	 			
	 		</div>
	 		<div id='content_learning'>
	 			<div id='' class=''></div>
	 			<div id='' class=''></div>
	 			<div id='' class=''></div>
	 		</div>
	 		<div id='footer_learning'>
	 			<ul id='ul_img'>
	 				".$this->listImages()."
				</ul>
	 		</div>
	 	");
	}

	private function listImages(){

	}

	//閲覧履歴ページ用のデータ表示or学びを作り中のページ
	public function displayDbData($mode){

		$db = new DATABASE();
		$sql = $db->getSelectSQL();
		if($mode === 'favorite')
			$sql = $db->getSelectSQLWhereFavorite();
		$link = $db->mysqlConnect();
		if( $resource = $db->mysqlSelect($sql,$link) )
			while($row = mysql_fetch_array($resource))
				if($mode === 'history' || $mode === 'favorite')
					$this->printDataFrom($row);
				else if($mode === 'learning')
					$this->lineUpDataFrom($row);
				else
					echo "THE MODE DOES NOT EXSIT";
		else
			echo "ERROR in displayDbData:".var_dump($resource);
	}

	protected function h($str){
		return htmlspecialchars($str);
	}


}//VIEW class

//YahooWebAPIを用いたキーフレーズ解析
//TODO:キーフーレズ解析の関数はまだ未使用。
// abbyy_phpで落としたテキストデータにgetKeyPhraseFromを用いる。
/* それの工程は以下の通り
	1.ブラウザでお気に入りボタンpushでidゲット
	2.idで解析済みかチェック（DBのtagsの中身が空か見る）
	3.idのURLがPDFかどうかチェック（pdfを含むか否か）
	(4).PDFじゃない場合、simple_html_docでテキストデータゲット。6番へ
	4.PDFかつ空なので、pdf_table_idでpath名を獲得して、ファイルの場所ゲット
	（DB連携が前提：まずweblearningテーブルにpdf_table_id項目を追加。pdf_tableへデータ追加の度に、そのidをpdf_table_idにupdateする処理が必要）
	5.abbyy_phpを用いて、PDFをテキストデータに変形
	6.yahoo_apiを用いて、テキストデータからキーフレーズを抽出
	7.idを指定してキーフレーズをtagsにupdate
	8.tagsをもとに、scholar/CiNii/などで検索。論文ゲットだぜ
*/
class KeyPhraseExtract_yahoo{

	private $keyphrase = ""; // comma separated string

	function __construct($total){

		$appid = 'dj0zaiZpPUd3NTBGeTgzMTZHViZkPVlXazlTR05ZWW05eE5tY21jR285TUEtLSZzPWNvbnN1bWVyc2VjcmV0Jng9NWI-';

		if(!isset($total) || $total === "")
			die('NO PARAM');
		echo "TOTAL:".$total;
	  	$sentence = mb_convert_encoding($total,'utf-8', 'auto');
	  	
	  	$responsexml = $this->sendRequest($appid,$sentence);

		if($phrase = $this->analyzeResponse($responsexml) ){
			$this->setKeyPhrase($phrase);
	  		echo "GOOD";
		}else{
	  		echo "NO GOOD";
	  	}
	  	//$this->show_keyphrase($appid,$sentence);
	}
	private function setKeyPhrase($phrases){
		$this->keyphrase = $phrases;
	}

	public function getKeyPhrase(){
		return $this->keyphrase;
	}

	private function sendRequest($appid,$sentence){

		$output = "xml";
		$request = "http://jlp.yahooapis.jp/KeyphraseService/V1/extract?";
		$request .= "appid=".$appid."&sentence=".urlencode($sentence)."&output=".$output;

		$responsexml = simplexml_load_file($request);
		return $responsexml;
	}

	private function analyzeResponse($responsexml){

		$result_num = count($responsexml->Result);
		if($result_num > 0){

			$db = new DATABASE();
			// 配列から文字列に整形して、DBに入る形に変更
			$str_result = $this->formatDataFrom($responsexml->Result,$result_num);
			return $str_result;
		}else{
			return NULL;
		}
	}
	//array(keyphrase,score) => (string)"keyphrase1,keypharese2,...."
	private function formatDataFrom($results,$num){
		$str_result = NULL;
		for($i=0;$i<$num;$i++){
			$result = $results[$i];
			$str_result .= $result->Keyphrase;
			if($i!=$num) $str_result .= ",";
		}
		return $str_result;
	}
/*
	private function show_keyphrase($appid, $sentence){

		$output = "xml";
		$request  = "http://jlp.yahooapis.jp/KeyphraseService/V1/extract?";
		$request .= "appid=".$appid."&sentence=".urlencode($sentence)."&output=".$output;

		$responsexml = simplexml_load_file($request);
		$result_num = count($responsexml->Result);

		if($result_num > 0){
			echo "<table>";
			echo "<tr><td><b>キーフレーズ</b></td><td><b>スコア</b></td></tr>";
			for($i = 0; $i < $result_num; $i++){
				$result = $responsexml->Result[$i];
				echo "<tr><td>".$this->h($result->Keyphrase)."</td><td>".$this->h($result->Score)."</td></tr>";
	    	}
		    echo "</table>";
		}
	}
*/

}// KeyPhraseExtract_yahoo


/*
 HTML解析に使うクラス
 参照URL: http://liginc.co.jp/programmer/archives/4921
*/
class SimpleHtmlDom{

	private $destPdfDir = '../pdf_files/';
	public $destPath = '';

	private function setDestPathWith($fileName){
		$this->destPath = $this->destPdfDir . $fileName;
	}

	private function getDestPath(){
		return $this->destPath;
	}

	// PDFファイルを保存するディレクトリを生成
	private function init(){
		if (!file_exists($this->destPdfDir)) 
			mkdir($this->destPdfDir, 0755, true); 
		
	}
	// URLから余計な文字を取り除くか入れ替えたりしてファイル名を整形。
	private function replaceFileNameFrom($url){
		$fileName = str_replace('https://','',$url);
		$fileName = str_replace('http://','',$fileName);
		$fileName = str_replace('.','_',$fileName);
		$fileName = str_replace('/','-',$fileName);		
		$fileName = str_replace('_pdf','.pdf',$fileName);
		return $fileName;
	}
	//TODO:pdfの判断をURLのみで行っている点が危うい。
	//http://detail.chiebukuro.yahoo.co.jp/qa/question_detail/q1210281517

	private function isItPdf($url){
		//JAIRO is one of monograph search engine sites.
		$JAIRO = '/koara.lib.keio.ac.jp\/xoonips\/modules\/xoonips\/download.php/';

		if(mb_strpos($url,'pdf') || preg_match($JAIRO, $url))
			return TRUE;
		
		return FALSE;
	}

	// webページのURLからPDFを取得してDIRとDBに保存
	public function savePdfWith($url){

		$this->init();
		
		// pdfファイルで在れば、DIRとDBのpdf_file_tableに保存
		if($this->isItPdf($url)){
			
			$fileName = $this->replaceFileNameFrom($url);
			$this->setDestPathWith($fileName);
			
			FileDownloader::saveTargetFile($url, $this->getDestPath());
			$db = new DATABASE();
			$sql = $db->getInsertSQL_pdf($url,$fileName);
			$link = $db->mysqlConnect();
			
			if( $db->mysqlSelect($sql,$link) ){
				return TRUE;
			}else
				return FALSE;
		}else
			return FALSE;
	}

	public function saveImageWith($url){
		/*
		$doc = file_get_html($url);
		$objects_img = $doc->('img');
		*/
	}
	// HTMLから文字を抽出する
	public function saveTextWith($url){
		/*
		$doc = file_get_html($url);
		$objects_h1 = $doc->('h1');
		$objects_h2 = $doc->('h2');
		$objects_p = $doc->('p');
		*/
	}
}

/**
 * File download class.
 * コピペ参照URL: http://liginc.co.jp/programmer/archives/4921
 */
class FileDownloader {

	/**
	 * Save target file.
	 * @param string $srcPath source path
	 * @param string $destPath dest path
	 */
	public static function saveTargetFile($srcPath, $destPath) {
		
		$contents = self::getFileContents($srcPath);
		self::putFileContents($destPath, $contents);
	}

	/**
	 * Get file contents.
	 * @param string $srcPath source file path
	 * @return contents instance
	 */
	private static function getFileContents($srcPath) {
		
		if (!function_exists('file_get_contents')) {
			$fhandle = fopen($srcPath, "");
			$fcontents = fread($fhandle, filesize($srcPath));
			fclose($fhandle);
		} else {
			$fcontents = file_get_contents($srcPath);
		}
		return $fcontents;
	}

	/**
	 * Put file contents.
	 * @param string $destPath destination file path
	 * @param string $contents save file contents
	 */
	private static function putFileContents($destPath, $contents) {

		if (!function_exists('file_put_contents')) {
			$fhandle = fopen($destPath, "");
			$fcontents = fwrite($fhandle, $contents);
			fclose($fhandle);
		} else 
			file_put_contents($destPath, $contents);
	}
}

/*
class AbbyyOcrSdk:
  テキスト抽出するファイルを指定する必要がある。
  このPHPに対して、POSTなりGETなりでファイル名をここに転送する。
　テキスト抽出後には、一旦保存していいし、そのまま解析して重要キーワードだけ抽出して、
　そのPDFのタグとして扱ってもよい。
*/

  // 1. Send image to Cloud OCR SDK using processImage call
  // 2.	Get response as xml
  // 3.	Read taskId from xml

class AbbyyOcrSdk{

  private $fileName;
  private $local_directory;
  private $filePath;

  private $applicationId = 'monograph_text_recognition';
  private $password = 'JjTcZ+NX0fc3GYunuy/bfq8S';

  //PDFファイルが読み込めるか確認
  function __construct($filename){

  	$this->fileName = $filename;
  	$this->local_directory = dirname(__FILE__).'/../pdf_files/';
  	$this->filePath = $this->local_directory . $this->fileName;

    if(!file_exists($this->filePath))
      die('File '.$this->filePath.' not found.');
    if(!is_readable($this->filePath))
      die('Access to file '.$this->filePath.' denied.');
  }


  //curlでWebAPIにリクエストを投げる。PDFの文字列が返り値。
  public function sendRequest(){
      // Send HTTP POST request and ret xml response
      $lang = 'english,japanese';
      $format = 'txt';
      $profile = 'textExtraction';//未実装:TODO
      $method = 'processImage';
      $url = 'http://cloud.ocrsdk.com/'.$method.'?language='.$lang.'&exportFormat='.$format;

      $curlHandle = curl_init();
      curl_setopt($curlHandle, CURLOPT_URL, $url);
      curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1); // return (string)
      curl_setopt($curlHandle, CURLOPT_USERPWD, "$this->applicationId:$this->password");
      curl_setopt($curlHandle, CURLOPT_POST, 1);
      curl_setopt($curlHandle, CURLOPT_USERAGENT, "PHP Cloud OCR SDK Sample");
      $post_array = array(
          "my_file"=>"@".$this->filePath,
      );
      curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $post_array); 
      $response = curl_exec($curlHandle);
      if($response == FALSE) {
        $errorText = curl_error($curlHandle);
        curl_close($curlHandle);
        die($errorText);
      }
      $httpCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
      curl_close($curlHandle);

      $xml = $this->xmlParse($response,$httpCode);
      $arr = $xml->task[0]->attributes();
      $taskStatus = $arr["status"];
      if($taskStatus != "Queued") {
        die("Unexpected task status ".$taskStatus);
      }

      $this->loopGetTaskStatus($arr,$httpCode);
  }
  

  private function xmlParse($response,$httpCode){
      // Parse xml response
      $xml = simplexml_load_string($response);
      if($httpCode != 200) {
        if(property_exists($xml, "message")) {
          die($xml->message);
        }
        die("unexpected response ".$response);
      }
      return $xml;
  }
  

  private function loopGetTaskStatus($arr){

      // Task id
      $taskid = $arr["id"];  
      // 4. Get task information in a loop until task processing finishes
      // 5. If response contains "Completed" staus - extract url with result
      // 6. Download recognition result (text) and display it

      $url = 'http://cloud.ocrsdk.com/getTaskStatus';
      $qry_str = "?taskid=$taskid";

      // Check task status in a loop until it is finished
      // TODO: support states indicating error
      while(true){
        sleep(5);
        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $url.$qry_str);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlHandle, CURLOPT_USERPWD, "$this->applicationId:$this->password");
        curl_setopt($curlHandle, CURLOPT_USERAGENT, "PHP Cloud OCR SDK Sample T");
        $response = curl_exec($curlHandle);
        $httpCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
        curl_close($curlHandle);
      	
        $xml = $this->xmlParse($response,$httpCode);
        $arr = $xml->task[0]->attributes();
        $taskStatus = $arr["status"];
        
        if($taskStatus == "Queued" || $taskStatus == "InProgress") 
          // continue waiting
          continue;
        
        if($taskStatus == "Completed") 
          // exit this loop and proceed to handling the result
          break;
        
        if($taskStatus == "ProcessingFailed") 
          die("Task processing failed: ".$arr["error"]);
        
        die("Unexpected task status ".$taskStatus);
      }
      //$this->deownloadResult($arr);
  }
  
  
  private function downloadResult($arr){

      $url = $arr["resultUrl"];   
      $curlHandle = curl_init();
      curl_setopt($curlHandle, CURLOPT_URL, $url);
      curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
      // Warning! This is for easier out-of-the box usage of the sample only.
      // The URL to the result has https:// prefix, so SSL is required to
      // download from it. For whatever reason PHP runtime fails to perform
      // a request unless SSL certificate verification is off.
      curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);
      $response = curl_exec($curlHandle);
      curl_close($curlHandle);
      
      // Let user donwload text result
//      header('Content-type: text/plain');
//      header('Content-Disposition: attachment; filename="file.txt"');
      $file_contents = file_get_contents($response);

      return $file_contents;
  }

}//AbbyyOcrSdk

/*
	http://www.google.co.jp/gwt/n
*/
class GoogleTransCoder{

	private $base_url = "http://www.google.co.jp/gwt/x?u=";
	private $opt_url = "&noimg=1&btnGo=送信&source=wax&ie=UTF-8&oe=UTF-8";

	private $text;

	function __construct($pdf_url){

		if(preg_match('/^http/',$pdf_url) )
			$url = $this->base_url . $pdf_url . $this->opt_url;
		else
			die('THE URL IS NOT HTTP/HTTPS');

		$result = $this->sendRequest($url);
		$text = $this->getTextFrom($result);
		$this->setText($text);
		//httpsからhttpに直すと、稀にGoogleサービスでのエラーがなくなる
		//https://www.google.co.jp/gwt/x?u=http%3A%2F%2Fwww.jstage.jst.go.jp%2Farticle%2Frika%2F23%2F6%2F23_6_727%2F_pdf&noimg=1&btnGo=%E9%80%81%E4%BF%A1&source=wax&ie=UTF-8&oe=UTF-8
		if(preg_match("/Oops/",$text) && preg_match("/^https/",$pdf_url)){
			$pdf_url = preg_replace("/^https/","http",$pdf_url);
			$this->__construct($pdf_url);
		}

	}

	public function getText(){
		return $this->text;
	}

	private function setText($text){
		$this->text = $text;
	}

	private function sendRequest($url){

		$curlHandler = curl_init($url);
		curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curlHandler, CURLOPT_HEADER, true);
		curl_setopt($curlHandler, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.6; rv:2.0.1) Gecko/20100101 Firefox/4.0.1"); 

		$result = curl_exec($curlHandler);
		if(curl_errno($curlHandler)){
			//エラーならば
		    $info = curl_getinfo($curlHandler);
		    print_r($info);
		}
		curl_close($curlHandler); 

		return $result;
	}

	private function getTextFrom($result){

		$array = explode("<div>",$result);
		if( !empty($array[2]) )
			$array = explode("</div>",$array[2]);
		else{
			echo "Error:getTextFrom:";
			var_dump($array);
		}
		$text = strip_tags($array[0]);
		if(empty($text)) return FALSE;
		$this->stopWords($text);
		return $text;
	}
	private function stopWords($text){
		$text = preg_replace("/\&emsp;/"," ",$text);
	}
}
	/*
* TFIDF
*  1.GWTで１章ずつMecabに渡す。
*  2.Mecabで形態素解析して単語群をTFIDFに渡す。
*  3.TFIDFで重要な単語を上位１０個選定。
*  4.1~3を章が終わるまで繰り返す。（章分けができなければ、文字数で区切るのも手）
*/
class TFIDF{
	//変数：$results： $wordと$tfidfの連想配列
	//関数1：filter($anaylzed)：return $filtered：音数と品詞でフィルタリング
	//関数2：getTfFrom($word,$filtered)：return $tf：１文章中の全単語の中からある単語の頻出度を返す
	//関数3：getIdfFrom($word,$filtered)：return $idf：log(全文章数/ある単語を含む文章数)を返す
	//関数4：addToResults($word,$tfidf)：：変数$resultsに引数を追加
	//関数5：__construct($analyzed)：：関数１ー＞foreach($analyzed as word)ー＞$tfidfー＞addToResults(...)

	private $results = array();
	
	function __construct($analyzed){
		$filtered = self::filter($analyzed);
		foreach($filtered as $word){
			//word[0]:単語、word[1]:品詞、word[9]:発音
			$tf = self::getTfFrom($word[0],$filtered);
			$idf = self::getIdfFrom($word[0],$filtered);
			$tfidf = $tf * $idf;
			self::addToResults($word[0],$tfidf);
		}
		self::sort();
	}

	private function filter($analyzed){
		$filtered = array();
		foreach ($analyzed as $word){
			if(strlen($word[0]) > 1  && strcasecmp($word[1],'名詞')==0){//名詞以外、１文字を消す
				if(strlen($word[7]) > 2 || strcasecmp($word[7],'*')==0){//日本語読み2文字以下を消す
					if(preg_match('/^[!-~]/',$word[0]) && strlen($word[0]) > 2){//半角文字の2文字以下を消す
						array_push($filtered,$word);
					}
				}
			}
		}

		return $filtered;
	}
	private function getTfFrom($word,$filtered){
		$matchCount=$match=0;
		$all_words = self::pickupWordFrom($filtered);
		for($i=0;$i<count($all_words);$i++){
			if(strcasecmp($word,$all_words[$i]) == $match)
				$matchCount++;
		}
		$tf = $matchCount / count($all_words);
		return $tf;
	}
	private function getIdfFrom($word,$filtered){
		//30単語を１文章とする。
		$includeCount = $include = 0;
		$documents = self::splitWords($filtered);
		for($i=0;$i<count($documents);$i++){
			for($j=0;$j<count($documents[$i]);$j++){
				if(strcasecmp($word,$documents[$i][$j][0]) == $include){
					$includeCount++;
					$j = count($documents[$i]);
				}
			}
		}
		$idf = log(count($documents)/$includeCount);
		return $idf;
	}

	//return array{[30words],[30words],...,[30words]}
	private function splitWords($filtered){
		//1文章当たりの単語数を30とする。

		$fil_count = count($filtered);
		$doc_count = $fil_count/30;
		if($fil_count%30 != 0) 
			$doc_count++;

		$array = array();
		$documents_array = array();
		for($j=0;$j<$fil_count;$j++){
			array_push($array,$filtered[$j]);
			if( ($j%30==0 && $j!=0) || $j==$fil_count-1 ){
				array_push($documents_array,$array);
				$array = array();
			}
		}
		
		return $documents_array;
	}

	public function getResults(){
		
		return $this->results;
	}

	public function getKeyphrase(){

		$results = $this->getResults();
		$words = $this->getAllWordsFrom($results);
		$uniq_words = $this->uniqueWords($words);
		return join(',',$uniq_words);
	}

	private function getAllWordsFrom($results){

		if(!is_array($results)) 
			die("NONE");

		$words = array();
		for($i=0;$i<count($results);$i++)
			array_push($words , $results[$i]['word']);
		return $words;
	}

	private function uniqueWords($words){

		for($i=0;$i<count($words);$i++){//起点
			for($j=$i+1;$j<count($words);$j++){//チェック対象
				if(isset($words[$i]) && isset($words[$j])){
					if( strcasecmp($words[$i],$words[$j]) == 0)//一致
						//一致した対象を消去
						unset($words[$j]);
				}
			}
		}
		return $words;
	}

	private function pickupWordFrom($filtered){
		$all_words = array();
		foreach($filtered as $word){
			array_push($all_words,$word[0]);
		}
		return $all_words;
	}

	private function addToResults($word,$tfidf){

		$this->results[] = array('word'=>$word,'tfidf'=>$tfidf);
	}

	private function sort(){
		usort($this->results,function($obj1,$obj2){
			//tfidfの大きい順にソート
			return $obj1['tfidf'] < $obj2['tfidf'];
		});
	}


}//TFIDF

/*
* まず試作でGoogleScholarのURLを取得してみよう。
* 参考:http://d.hatena.ne.jp/Lejay/20080920/1221893780
*
* @param: Search String
* @return: URLs
* 
* 1.検索文字をcurlでbase_urlに投げる[日本語／英語]
* 2.HTML形式で返ってくるので、各URLを抽出
* 3.URLsをreturnする。
*/	
class SearchAPI{
	//__construction()::
	//getJaUrl($q):$url_ja:クエリーからsendするURLを作って返す.日本語設定。
	//getEnUrl($q):$url_en:クエリーからsendするURLを作って返す.英語設定。
	//sendRequest($url):$response:$urlをもとにcurlの設定をし、リクエストを投げる。(他のクラスと被ってる感あり)
	//extractData($response):$data:全てのurlとそれに関するデータを抽出する。

	private $datas;

	private $base_url = "http://scholar.google.co.jp/scholar?";
	private $lang = "hl=";
	private $query = "q=";

	function __construct($q){
		if(!isset($q)) 
			die("NO QUERY");

		$url_ja = $this->getJaUrl($q);
		$url_en = $this->getEnUrl($q);
		$resp_ja = $this->sendRequest($url_ja);
		$resp_en = $this->sendRequest($url_en);
		$data_ja = $this->extractData($resp_ja);
		$data_en = $this->extractData($resp_en);
		$datas = $data_ja . $data_en;
		$this->setData($datas);
	}

	private function getJaUrl($q){

		$query_ja = $this->query . $q;
		$lang_ja = $this->lang . "ja";
		$url_ja = $this->base_url . $lang_ja . $query_ja;
		return $url_ja;
	}

	private function getEnUrl($q){
		//TODO:日ー＞英に翻訳してもいいかも
		$query_en = $this->query . $q;
		$lang_en = $this->lang . "en";
		$url_en = $this->base_url . $lang_en . $query_en;
		return $url_en;
	}

	//TODO:GoogleTransferのsendRequestと全く同じ。extendsすべし。
	private function sendRequest($url){

		$curlHandler = curl_init($url);
		curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curlHandler, CURLOPT_HEADER, true);
		curl_setopt($curlHandler, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.6; rv:2.0.1) Gecko/20100101 Firefox/4.0.1"); 

		$response = curl_exec($curlHandler);
		if(curl_errno($curlHandler)){
			//エラーならば
		    $info = curl_getinfo($curlHandler);
		    print_r($info);
		}
		curl_close($curlHandler); 

		return $response;
	}
	//title,url,content,author,year,などを抽出。
	private function extractData($response){
		//TODO:とりま中身を見る
		$data = $response;
		return $data;
	}

	private function setDatas($datas){

		$this->$datas = $datas;

	}

	public function getDatas(){

		return $this->datas;

	}
}



?>