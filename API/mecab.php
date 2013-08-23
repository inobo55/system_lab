<?php



/*
	目標：
	　論文のテキストがポストされるので、
	　それを形態素解析。および、TF-IDFに基づいて単語に重要度を付与。
	　重要語句上位１０単語をcomma separated stringで返す。(重要度が高いものをarrayで返した)
	必要な追加機能：
		ステミング（思われる＝思う｜思った＝思う）
		フィルタリング（余計な単語を消す）：
			以下の特徴を有する単語を除く
			発音が２文字以下のもの（に、その、よう、た、では、する、し、また、etc）
			名刺以外の単語（助詞、動詞、連体詞、助動詞、etc）　
			
	
*/

require_once "tool.php";

if (isset($_POST['text'])) {
	$KEITAISO = 0;
	$analyzed = MeCab::analyze(str_replace(array("\r\n","\r","\n"), '', $_POST['text']));
}

	$text = $_POST['text'];
	$type = $_POST['type'];
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
	"http://www.w3.org/TR/html4/strict.dtd">
<html lang="ja">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8">
	<title>MeCab for PHP5 Sample</title>
</head>
<body>
	<h1>MeCab for PHP5 Sample</h1>
	<form action="./mecab.php" method="post">
		<p>
			<textarea name="text" cols="60" rows="15" value=""><?php echo htmlspecialchars($text); ?></textarea><br>
			<input type="radio" name="type" value="0" id="type_analyze"<?php echo ($type == 0)? ' checked': ''; ?>><label for="type_analyze">解析</label>
			<input type="radio" name="type" value="1" id="type_split"  <?php echo ($type == 1)? ' checked': ''; ?>><label for="type_split">分かち書き</label>
		</p>
		<p><input type="submit" value="send"></p>
	</form>

<?php if (isset($analyzed)): ?>
	<table border="1" cellspacing="2" cellpadding="2">
		<tr>
			<th>表層形[0]</th>
			<th>品詞[1]</th>
			<th>品詞細分類1[2]</th>
			<th>品詞細分類2[3]</th>
			<th>品詞細分類3[4]</th>
			<th>活用形[5]</th>
			<th>活用型[6]</th>
			<th>原形[7]</th>
			<th>読み[8]</th>
			<th>発音[9]</th>
		</tr>
<?php foreach ($analyzed as $word): ?>
		<tr>
<?php foreach ($word as $value): ?>
			<td><?php echo htmlspecialchars($value); ?></td>
<?php endforeach ?>
		</tr>
<?php endforeach ?>
	</table>
<?php endif ?>

<?php if (isset($splited)): ?>
	<p>
		<?php echo nl2br(htmlspecialchars($splited)); ?>
	</p>
<?php endif ?>

</body>
</html>