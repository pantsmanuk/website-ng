<?php
$hidden = array('valid' => 'true');
echo form_open('acp_pilots/edit/' . $pilot_id, '', $hidden);

echo $highlight1 . $error . $highlight2;

echo '<fieldset style="border: 1px dotted rgb(40, 45, 78); padding: 0.6em; margin-top: 0.4em; margin-bottom: 0.4em;">';
echo '<legend>Pilot</legend>';
echo '<label for="fname">Firstname</label>' . form_input($fname) . '<br />';
echo '<label for="sname">Surname</label>' . form_input($sname) . '<br />';
echo '<label for="email">Email</label>' . form_input($emailaddress) . '<br />';

echo '<label for="engstart">DOB</label>'
	. form_dropdown('dobday', $dobday_array, $dobday)
	. form_dropdown('dobmonth', $dobmonth_array, $dobmonth)
	. form_dropdown('dobyear', $dobyear_array, $dobyear) . ' (d/m/y) ';

echo '<label for="country">Country</label>' . form_dropdown('country', $country_array, $country) . '<br />';
echo '<label for="hub">Hub</label>' . form_dropdown('hub', $hub_array, $hub) . '<br />';
echo '<label for="email">Status</label>' . form_dropdown('status', $status_array, $status) . '<br />';
echo '<label for="email">Curr Location</label>' . form_dropdown('curr_location', $location_array, $curr_location) . '<br />';
echo '<label for="email">PP Location</label>' . form_dropdown('pp_location', $location_array, $pp_location) . '<br />';
echo '<br /><center>Changing a pilot\'s locations will not take effect until they log out and back in</center><br />';
echo '</fieldset>';

if ($this->session->userdata('usergroup') == 1) {
	echo '<fieldset style="border: 1px dotted rgb(40, 45, 78); padding: 0.6em; margin-top: 0.4em; margin-bottom: 0.4em;">';
	echo '<legend>Management (Visible to Super-Admins only)</legend>';
	echo '<label for="usergroup">Usergroup</label>' . form_dropdown('usergroup', $usergroup_array, $usergroup) . '<br />';
	echo '<label for="department">Department</label>' . form_dropdown('department', $department_array, $department) . '<br />';
	echo '<label for="title">Pips</label>' . form_dropdown('pips', $pips_array, $pips) . '<br />';
	echo '<label for="title">Title</label>' . form_input($title) . '<br />';
	echo '</fieldset>';
}

echo '<center>' . form_submit('joinsubmit', 'Edit Pilot') . '</center>';
echo form_close();
?>