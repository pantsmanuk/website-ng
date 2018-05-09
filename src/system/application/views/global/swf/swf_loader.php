<?php
if(!isset($height) || $height == ''){
	$height = "500";
}
if(!isset($flash_vars) || $flash_vars == ''){
	$flash_vars = "";
}
/*
<object type="application/x-shockwave-flash" data="<?php echo $flash_url.$swf; ?>.swf" width="790" height="500">
	<param name="movie" value="<?php echo $flash_url.$swf; ?>.swf">
    <param value="transparent" name="wmode">
    <param name=FlashVars value="baseUrl=<?php echo $base_url.$flash_vars; ?>">
</object>
*/
?>
<center>
<div id="flashcontent" style="width: 790px; height:500px; background-color:#FFFF99; font-size:16px;"><br /><br /><br /><br /><br /><br />You need Flash v10 or higher to see this content.<br /><br /> <a href="http://get.adobe.com/flashplayer/">Click me to visit the Flash website to install</a>.</div>
<script type="text/javascript">
	var so = new SWFObject('<?php echo $flash_url.$swf; ?>.swf','flashcontent','790','500','10');
	so.addParam('allowfullscreen','true');
	so.addParam('allowscriptaccess','always');
	so.addParam("wmode", "transparent");
	so.addParam('flashvars','baseUrl=<?php echo $base_url.$flash_vars; ?>');
	so.write('flashcontent');
</script>
</center>