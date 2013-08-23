
//タブの設定
$(function() {
	$( "#tabs" ).tabs();
});

//画像をドラッグする設定
$(function(){
	$img = $('#history_contents').find('.img');
	$img.draggable({revert:'invalid'});
});

//画像を別の画像にドラッグする設定
$(function(){
	$img = $('#learning_contents').find('.img');
	$img.draggable({revert:'invalid'});
	$img.droppable({
	  	avtiveClass:'ui-state-hover',
	  	hoverClass:'ui-state-active',
	  	drop:function(event,ui){
	  		$(this)
	  		.addClass('ui-state-highlight')
	  		.find('#id')
	   		.html('Dropped');
	   		alert('dropped');
	  	}
	});
});

//お気に入りボタンの設定
// TODO: 何故かお気に入りタブのボタンには反応しない！！！
$(function(){
	$button = $('.favorite').find('.check');
	$button.click(function(){
		alert($(this).attr('id'));
		var id = $(this).attr('id');
		var favorite = $(this).attr('data-favorite');
		var post_tag_flag = false;
		if(favorite=='true') 
			favorite = 'false';
		else{ 
			favorite = 'true';
			post_tag_flag = true;
		}

		$.ajax({
			type:"POST",
			url:"./API/favorite_post.php",
			data:{"id":id,"favorite":favorite},
			timeout:10000
		}).done(function(data,status,xhr){
			$button.attr('data-favorite',favorite);
			if(post_tag_flag){
				alert("favorite:"+id);
				postTagsOf(id);
			}
		}).fail(function(xhr,status,error){
			alert(status+': '+error);
		}).always(function(arg1,status,arg2){
			
		});

	});
});
//ボタンIDをどっかのファイルに、tags確認とPDF確認とPDFファイル特定と解析のために渡す
function postTagsOf(btn_id){
	$.ajax({
		type:'POST',
		url:"./API/post_tags.php",
		data:{id:btn_id},
		timeout:10000,
	}).done(function(data,status,xhr){
		alert(data);
	}).fail(function(xhr,status,error){
		alert('NO GOOD:'+error);
	});
}

//お気に入りタブを押した時に、FavoriteDataを取得＆展開
$(function(){

	$tab_favorite = $('#tabs').find('#a-tab2');
	$tab_favorite.click(function(){
		$.ajax({
			type:'GET',
			url:"./API/favorite_get.php",
			data:{"tab":"favorite"}, // 値の中身は使ってない
			timeout:2000,
		}).done(function(data,status,xhr){
			$div = $('#tabs-2').find('#favorite_contents');
			$div.html(data);				
		}).fail(function(xhr,status,error){
			alert(erorr);
		}).always(function(arg1,status2,arg2){

		});
	});
});

