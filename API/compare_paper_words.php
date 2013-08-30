<?php
/*

　２つの論文を形態素解析して、各論文の単語群を比べる。一致した単語を出力。
　入力：２論文のテキスト（空白なし）
　出力：一致単語群。
*/

require_once 'tool.php';


//辞書順でユニークな単語の配列を返す。
function words($pdf_url){
	if(empty($pdf_url) || strlen($pdf_url) == 0) return NULL;
	echo $pdf_url;
	echo "<br>";
	$pdfToTexter = new GoogleTransCoder($pdf_url);
	$pdf_texts = $pdfToTexter->getText();	//確実に全文取れてる。
	$text_array = str_split($pdf_texts, 3000);
	$words = array();
	foreach($text_array as $text){
		$analyzed = MeCab::analyze(str_replace(array("\r\n","\r","\n"), '',$text));
		foreach ($analyzed as $word){
			if(strlen($word[0]) < 4) continue; //3bytes以下は飛ばす
			array_push($words, $word[0]);
		}
	}
	$uniq_array = array_unique($words);
	return $uniq_array;
}
/*
	@param
	 $array_words = [[word,word,..][word,word,...]...[word,..][word,..]]
	@return
	 $checked_words = [[word=>count][word=>count]...]
*/
function compare_same_words($all_words){

	$count = count($all_words);
	$checked_words = array();

	for($i=0;$i<$count;$i++){
		if(hasSameValue($all_words[$i],$checked_words))
			continue;
		$same_count=0;
		for($j=0;$j<$count;$j++){
			if(strcasecmp($all_words[$i],$all_words[$j]) === 0)
				$same_count++;
//			if(strnatcasecmp($all_words[$i],$all_words[$j]) < 0) //通りすぎたら終わり
//				$j = $count;
		}
		array_push($checked_words,array('word'=>$all_words[$i],'count'=>$same_count));
	}
	return $checked_words;
}

function hasSameValue($value,$array){
	foreach ($array as $v ){
		if(strcasecmp($value,$v['word']) === 0)
			return $v['count'];
	}
	return FALSE;
}

function comp_words($words1,$words2=NULL,$words3=NULL){
	$same = array();

	if(empty($words2) || is_null($words2)){
		echo "<br>1url<br>";
		return $words1;
	}

	if(empty($words3) || is_null($words3)){
		echo "<br>2url<br>";
		foreach ($words1 as $word1){
			foreach ($words2 as $word2){
				if(strcasecmp($word1,$word2) == 0)
					array_push($same,$word1);
			}
		}
		return array_unique($same);
	}

	echo "<br>3url<br>";
	foreach ($words1 as $word1){
		foreach ($words2 as $word2){
			if(strcasecmp($word1,$word2) == 0){
				foreach($words3 as $word3){
					if(strcasecmp($word1,$word3) == 0){
						array_push($same,$word1);
					}
				}
			}
		}
	}
	return array_unique($same);
}

function dump($array){
	foreach ($array as $word){
		echo $word;
		echo "<br>";
		echo "\n";
	}
}
/*
・scaffoldingの論文
　＠新たに用意すべき
'http://2008.conf.ai-gakkai.or.jp/program/pdf/100102.pdf';
'http://ci.nii.ac.jp/els/110007641457.pdf?id=ART0009459789&type=pdf&lang=jp&host=cinii&order_no=&ppv_type=0&lang_sw=&no=1377773120&cp=';
'http://ci.nii.ac.jp/els/110003863562.pdf?id=ART0005173246&type=pdf&lang=jp&host=cinii&order_no=&ppv_type=0&lang_sw=&no=1377773686&cp=';
・ナビゲーション支援
'https://www.jstage.jst.go.jp/article/tjsai/17/4/17_4_510/_pdf';
'http://www.jaist.ac.jp/DEWS2003/download/dews2003/8-P/8-P-02.pdf';
'http://www.ymd.nii.ac.jp/lab/publication/paper/1999/JSAI-paper-99-NaviPlan.pdf';
'http://ci.nii.ac.jp/els/10016755708.pdf?id=ART0003224834&type=pdf&lang=jp&host=cinii&order_no=&ppv_type=0&lang_sw=&no=1377780017&cp=';
・リフレクション支援
'https://www.jstage.jst.go.jp/article/tjsai/18/5/18_5_245/_pdf';
'http://wlgate.inf.uec.ac.jp/contents/publication/pdfs/Jsise20060708.pdf';
'http://www.jaist.ac.jp/jsai2006/program/pdf/100128.pdf';
'http://wlgate.inf.uec.ac.jp/contents/publication/pdfs/ALST20070319.pdf';
'https://dspace.wul.waseda.ac.jp/dspace/bitstream/2065/34124/1/WasedaNihongoKyoikuJissenKenkyu_1_Imai.pdf';
'http://www.code.ouj.ac.jp/sympo/2012/report/pdf/report_2012.pdf#page=93';
reflection,リフレクション、見直し、再構成、知識構築、プロセス、過程、
・アノテーション支援
'https://www.jstage.jst.go.jp/article/jsoft/20/4/20_4_487/_pdf';
'http://tk-www.elcom.nitech.ac.jp/~daisuke/pdf/trans/ipsj_2007_48_12.pdf';
'http://ci.nii.ac.jp/els/110002726018.pdf?id=ART0003014502&type=pdf&lang=jp&host=cinii&order_no=&ppv_type=0&lang_sw=&no=1377783076&cp=';
Annotation、アノテーション、
・因子分析
'https://qir.kyushu-u.ac.jp/dspace/bitstream/2324/351/4/KJ00000092998-00001.pdf';
'http://www.ritsumei.ac.jp/acd/ac/itl/outline/kiyo/kiyo11/12_okada.pdf';
'http://ir.kagoshima-u.ac.jp/bitstream/10232/9078/3/%E6%95%99%E8%82%B2%E5%AE%9F%E8%B7%B5%E7%A0%94%E7%A9%B6%E7%B4%80%E8%A6%8119%E5%8F%B7_5Fujita1.pdf';
'http://repository.aichi-edu.ac.jp/dspace/bitstream/10424/2405/1/kenkyo39127140.pdf';
'http://hermes-ir.lib.hit-u.ac.jp/rs/bitstream/10086/8549/1/ryugaku0000600770.pdf';
因子、分析、要因、相関、係数、尺度、調査、回答、対象、結果、分散、比較、測定、評価、段階、結果、解釈、負荷、有意、標準、分類、水準、選択、
・回帰分析
'http://ir.iwate-u.ac.jp/dspace/bitstream/10140/345/1/jcer-v1p131-144.pdf';
'https://www.jstage.jst.go.jp/article/rika/24/3/24_3_463/_pdf';
'http://www.arch.t-kougei.ac.jp/ito/pdf/pr5_2006.pdf';
'http://www.unisys.co.jp/tec_info/tr68/6814.pdf';
'http://sugiyama-www.cs.titech.ac.jp/~sugi/2012/MachineLearningReview-jp.pdf';
回帰、偏差、抽出、
効果、比較、検討、関係、有意、評価、分析、結果、analysis、
・構造分析
'http://ci.nii.ac.jp/els/110003026571.pdf?id=ART0003487307&type=pdf&lang=jp&host=cinii&order_no=&ppv_type=0&lang_sw=&no=1377851978&cp=';
「学習と構造分析」で検索しても関連が深い論文はほとんどない。＝＞構造分析はいらん
・重回帰分析
'http://teapot.lib.ocha.ac.jp/ocha/bitstream/10083/31355/1/%E6%95%99%E8%82%B2%E5%BF%83%E7%90%86%E5%AD%A6%E7%A0%94%E7%A9%B650%282%29%E6%B5%85%E9%87%8E.pdf';
'http://ci.nii.ac.jp/els/110002721598.pdf?id=ART0003008682&type=pdf&lang=jp&host=cinii&order_no=&ppv_type=0&lang_sw=&no=1377852445&cp=';
'http://sucra.saitama-u.ac.jp/modules/xoonips/download.php/BKK0001070.pdf?file_id=21783';
'https://www.jstage.jst.go.jp/article/jcss/16/3/16_3_281/_pdf';
'https://soar-ir.shinshu-u.ac.jp/dspace/bitstream/10091/5004/1/EduResearch_H02-07.pdf';
回帰分析と単語が似ているので、分別できない。
・クラスター分析
'';




*/
set_time_limit(200);

//実験群 回帰分析
$pdf_url1 = 'http://teapot.lib.ocha.ac.jp/ocha/bitstream/10083/31355/1/%E6%95%99%E8%82%B2%E5%BF%83%E7%90%86%E5%AD%A6%E7%A0%94%E7%A9%B650%282%29%E6%B5%85%E9%87%8E.pdf';
$pdf_url2 = 'http://ci.nii.ac.jp/els/110002721598.pdf?id=ART0003008682&type=pdf&lang=jp&host=cinii&order_no=&ppv_type=0&lang_sw=&no=1377852445&cp=';
$pdf_url3 = 'http://sucra.saitama-u.ac.jp/modules/xoonips/download.php/BKK0001070.pdf?file_id=21783';
$pdf_url4 = 'https://www.jstage.jst.go.jp/article/jcss/16/3/16_3_281/_pdf';
$pdf_url5 = 'https://soar-ir.shinshu-u.ac.jp/dspace/bitstream/10091/5004/1/EduResearch_H02-07.pdf';

//統制群 因子分析2+構造分析1+回帰分析2
$pdf_url6 = 'http://hermes-ir.lib.hit-u.ac.jp/rs/bitstream/10086/8549/1/ryugaku0000600770.pdf';
$pdf_url7 = 'http://repository.aichi-edu.ac.jp/dspace/bitstream/10424/2405/1/kenkyo39127140.pdf';
$pdf_url8 = 'http://ci.nii.ac.jp/els/110003026571.pdf?id=ART0003487307&type=pdf&lang=jp&host=cinii&order_no=&ppv_type=0&lang_sw=&no=1377851978&cp=';
$pdf_url9 = 'http://ir.iwate-u.ac.jp/dspace/bitstream/10140/345/1/jcer-v1p131-144.pdf';
$pdf_url10 = 'http://www.unisys.co.jp/tec_info/tr68/6814.pdf';


$array1 = words($pdf_url1);
$array2 = words($pdf_url2);
$array3 = words($pdf_url3);
$array4 = words($pdf_url4);
$array5 = words($pdf_url5);

echo "<br>";

$array6 = words($pdf_url6);
$array7 = words($pdf_url7);
$array8 = words($pdf_url8);
$array9 = words($pdf_url9);
$array10 = words($pdf_url10);

$arrays1 = array();
$arrays1 = array_merge($arrays1,$array1);
$arrays1 = array_merge($arrays1,$array2);
$arrays1 = array_merge($arrays1,$array3);
$arrays1 = array_merge($arrays1,$array4);
$arrays1 = array_merge($arrays1,$array5);

$arrays2 = array();
$arrays2 = array_merge($arrays2,$array6);
$arrays2 = array_merge($arrays2,$array7);
$arrays2 = array_merge($arrays2,$array8);
$arrays2 = array_merge($arrays2,$array9);
$arrays2 = array_merge($arrays2,$array10);


echo "<br>";

$words1 = compare_same_words($arrays1);
$words2 = compare_same_words($arrays2);
usort($words1, function($a, $b) {
	return $a['count'] < $b['count'];
});
usort($words2, function($a, $b) {
	return $a['count'] < $b['count'];
});

foreach ($words1 as $key => $value) {

	if($value['count'] <= 2)
			continue;

	if($word2_count = hasSameValue($value['word'],$words2)){
		if($value['count']-$word2_count >= 3){
			$value['word'] .= ' @@@@ '. ($value['count']-$word2_count) .' @@@@';
				echo "<br>";
				echo $value['word'].' : '.$value['count'];
				echo "\n";
		}else{
//			echo "<br>×";
		}
	}else{

		echo "<br>";
		echo $value['word'].' : '.$value['count'];
		echo "\n";
	}
}

?>