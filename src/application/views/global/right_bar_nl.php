<div class="rightbar">
    <center>
    <?php
    if($this->session->userdata('logged_in') != '1'){
    	echo '<a href="'.$base_url.'join/"><img src="'.$tmpl_image_url.'home/join_sm.jpg" alt="Join" /></a><br />';
    }
    else{
    	echo '<br />';
    	$pips_val = '';
		if($this->session->userdata('pips') != ''){
			$pips_val = '_'.$this->session->userdata('pips');
		}
	
    	echo '<img src="'.$image_url.'ranks/'.$this->session->userdata('rank_id').$pips_val.'.png" alt="'.$this->session->userdata('rank_long').'" />';
		echo '<br />';
		echo $this->session->userdata('rank_long');
    }
	?>
    </center>
</div>

<br />

<?php

if(isset($page_title) && $page_title != ''){
	echo '	<div class="divblue" style="height: 25px;">
			<div style="margin-right: 130px">
			<font size="4"><b>'.$page_title.'</b></font>
			</div>
			</div>
';
}
else{
	echo '	<div class="divblue">
			<div style="margin-right: 130px">
			<div id="jspilotnews"> </div>
			</div>
			</div>
';
}
?>

<div class="clear"> </div>
<br />