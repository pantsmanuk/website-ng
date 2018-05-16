<?php
if($current_pilot_id == $this->session->userdata('user_id')){
?>
<span style="float:right;"><a href="<?php echo $base_url.'profile/details/'; ?>">Edit Profile</a></span>
<?php
}
echo '<b><font size="+1">'
.' '.$selected_name.'</font><br />'.'EHM-'.$selected_username.' '.$selected_rank.'</b>';
?>
<hr />

<div class="menu_sub">
<?php
echo '<a href="'.$base_url.'profile/index/'.$current_pilot_id.'">Summary</a> | ';
echo '<a href="'.$base_url.'profile/stats/'.$current_pilot_id.'">Stats</a> | ';
echo '<a href="'.$base_url.'profile/awards/'.$current_pilot_id.'">Awards</a> | ';
echo '<a href="'.$base_url.'profile/flightlog/'.$current_pilot_id.'/e">Flight Log</a> ';
?>
</div>

<br /><br />