
// background.jsの処理が始まったら読み込みが始まる
// 目的:現在見ているWebページに、ページ登録が完了したことを表現する

var ViewController = function(){
	this.insertNewDiv();
};

ViewController.prototype = {

	insertNewDiv:function(){
		var body = document.body,
			child = body.firstChild,
			newDiv = this.createNewDiv();
		if(body.firstChild.id != 'web_learning'){
			body.insertBefore(newDiv,child);
			setJqueryFadeInOut();
		}else
			alert('二度目のクリックです');
	},
	createNewDiv:function(){
		var div = document.createElement('div');
		div.setAttribute('id','web_learning');
		div.innerHTML = "<h1>ページ登録完了</h1>";
		div.appendChild(this.createViewButton());
		setStyle(div);
		return div;
	},
	createViewButton:function(){
		var a = document.createElement('a');
		a.innerText = '今までのを見る';
		a.setAttribute('href',
			'http://localhost:8888/chrome/system_lab/web_learning.php');
		a.setAttribute('target','_blank');
		a.setAttribute('style','font-size:20px;');
		a.onclick = function(){
			//alert('click');
		}
		return a;
	}
};

function setStyle(div){
	div.style.backgroundColor = '#EEE';
	div.style.margin = '50px';
}

/*
	divがスーッと表示されるようにする
	ブラウザをscrollしたら、スーと消える
	#TODO:JQuery未実装
*/
function setJqueryFadeInOut(){

	$(function(){
		alert(2);		
		$div = $('#web_learning');
		$div.fadeIn("slow");
		$(window).scroll(function(){
			$div.fadeOut('slow');
		});
	});
}

window.PC = new ViewController();
