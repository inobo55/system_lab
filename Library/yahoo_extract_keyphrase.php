<?php

/*
　テーブルweb_learningとテーブルpdf_file_tableのurlとpdf_srcを比べて一致したら
　後者のidを前者のpdf_file_table_idに代入する。
　id => pdf_file_table_id 
*/

/*
require_once '../API/tool.php';

$db = new DATABASE();
$link = $db->mysqlConnect();

$sql1 = "SELECT * FROM `weblearning`";
$resource1 = $db->mysqlSelect($sql1,$link);
while($row1 = mysql_fetch_array($resource1)){
	$web_url = $row1['url'];
	$web_id = $row1['id'];
	$sql2 = "SELECT * FROM `pdf_file_table` WHERE `pdf_src` = '$web_url'";
	$resource2 = $db->mysqlSelect($sql2,$link);
	while($row2 = mysql_fetch_array($resource2)){
		$pdf_id = $row2['id'];
		$sql3 = "UPDATE `weblearning` SET `pdf_file_table_id` = $pdf_id WHERE `weblearning`.`id` = $web_id";
		$db->mysqlSelect($sql3,$link);
	}
}
echo "成功したぞ";

*/

?>