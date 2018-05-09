// JavaScript Document
window.onload=function(){
	updateTime();
	var url = '<?php echo $base_url."/ajax/pilotnews"; ?>';
	getPilotNews(url);
}
