<span style="float:right;"><a href="<?php echo $base_url.'profile/'; ?>">View Profile</a></span>
<?php
echo '<b><font size="+1">'
.' '.$this->session->userdata('fname')
.' '.$this->session->userdata('sname')
.'</font><br />'
.'EHM-'.$this->session->userdata('username').' '
.$this->session->userdata('rank_long')
.'</b>';
?>
<hr />

<div class="menu_sub">
<?php
echo '<a href="'.$base_url.'profile/credentials">Password</a> | ';
echo '<a href="'.$base_url.'profile/email">Email</a> | ';
echo '<a href="'.$base_url.'profile/hub">Hub Transfer</a> | ';
echo '<a href="'.$base_url.'profile/details">Details</a>';
?>
</div>

<br /><br />