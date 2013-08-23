<?php
/*
	web_learning.phpの「web_learning.js」から
	ajaxで送られた$_GETをもとに、
	favoriteDataを出力する。
*/

	require 'tool.php';

	if( !empty($_GET) ){
		$view = new View();
		$view->displayDbData('favorite');
	}
?>