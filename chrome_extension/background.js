/*
	アイコンクリックされたら、
	現在表示しているタブの
		詳細情報（URL,title,etc）や
		キャプチャー画像を
	取得および表示
*/

/*
	現在表示中のURLが、以前登録したURLなのかチェック
	tabを取得。urlを取得。phpでDBcheck。trueならアイコン変更。falseならば無処理。
	参照URL:
	http://www.inashiro.com/2011/06/13/dev-chrome-extension-get-current-tab-url
*/

chrome.tabs.onActivated.addListener(function(activeInfo){
	chrome.tabs.query({active: true , currentWindow: true}, function(tabs){
		var tab = tabs[0];
		console.log(tab.url);
		window.PC = new CheckUrlController(tab.url);
	});
});

var CheckUrlController = function(taburl){
	this.url = taburl;
	this.address_url = "http://localhost:8888/chrome/system_lab/API/check_same_url.php";
	this.sendRequest_();
};

CheckUrlController.prototype = {
	url:null,
	address_url:null,
	sendRequest_:function(){
		var req = new XMLHttpRequest();
		var array = { url: this.url };
		var post_data = createPostData(array);
		console.log(post_data);
		req.open("POST",this.address_url,true);
		req.setRequestHeader("Content-Type" , "application/x-www-form-urlencoded");
		req.send(post_data);
		req.onload = function(){
			//ある文字が含まれているか確認
			if(req.responseText.indexOf('registered') != -1){
				// アイコンを変更する処理
				changeIcon(true);
				console.log("REGISTERED:"+req.responseText);
			}
			else if(req.responseText.indexOf('new') != -1){
				// もとのアイコンに変更する処理
				changeIcon(false);
				console.log("OK ICON:"+req.responseText);
			}
			else　if(req.responseText.indexOf('ERROR') != -1)
				//POSTエラー
				console.log("ERROR in CheckUrlController:"+req.responseText);
			else
				console.log("?:"+req.responseText);
		}
	}
};

function changeIcon(mode){
	if(mode)
		chrome.browserAction.setIcon({path : 'registered.png'});
	else
		chrome.browserAction.setIcon({path : 'web.png'});
}


//アイコンクリックしたら処理する
chrome.browserAction.onClicked.addListener(function(tab){
	chrome.tabs.captureVisibleTab(null, function(dataUrl){
		var img = document.getElementById('img');
		img.src = dataUrl;
		window.PC = new BackgroundController(tab,dataUrl);
	});
});

var BackgroundController = function(tab,dataUrl){
	this.url = tab.url;
	this.title = tab.title;
	this.favIconUrl = tab.favIconUrl;
	this.image = dataUrl;
	this.address_url = "http://localhost:8888/chrome/system_lab/API/save_post_page.php";

	this.sendRequest_();
};

BackgroundController.prototype = {

	url:null,
	title:null,
	favIconUrl:null,
	image:null,
	address_url:null,

	sendRequest_:function(){	
		var req = new XMLHttpRequest();
		var array = { 
			url: this.url , 
			title: this.title, 
			favIconUrl:this.favIconUrl,
			image:this.image
			};
		var post_data = createPostData(array);

		req.open("POST", this.address_url, true);
		req.setRequestHeader("Content-Type" , "application/x-www-form-urlencoded");
		req.send(post_data);

		//PHPからの返答があったらonload.
		req.onload = function(){
			alert(req.responseText);
			chrome.tabs.executeScript(null, {
				"code": "alert('onload:');"
			},callbackPopupJs);
		}
	}
};

//"url='http:'&title='タイトル'"&...
function createPostData(array){

	var keyValue = [];
	
	for (var key in array) 
		keyValue.push(key+"="+array[key]);

	return keyValue.join('&');
}


function callbackPopupJs(){
	chrome.tabs.executeScript(null,{file:"popup.js"});
}
