<?php
echo '<h1>EHM-'.$this->session->userdata('username').' '.$this->session->userdata('fname').' '.$this->session->userdata('sname').'</h1>';

echo '<br /><br />';

echo 'Setting yourself to deadhead allows you to move location in Propilot without having to fly a leg. Setting a deadhead location will create a mission to alert other pilots to your need.<br /><br />Deadheading requires another pilot to fly from your current location to another location with you on board. You can select indirect or direct. Indirect flights will pick you up and drop you even if the destination is not where you are headed. This will continue until you reach your destination or clear your deadhead request. Direct deadheading will only pick you up if another pilot is flying direct to your destination.';

echo '<br /><br /><br />';

echo '<center>';

echo 'You are currently located at <b>'.$pp_location.' - '.$pp_location_name.'</b> in '.$pp_location_country.'<br />';

if($curr_destination == ''){
	
	echo '<br /><br />You are not currently flagged for deadheading';

}
else{

	if($curr_direct == '1'){
		$direct = 'directly';
	}
	else{
		$direct = 'indirectly';
	}
	
	if(array_key_exists($curr_destination, $airfield_array)){
		echo '<br /><br />You are currently flagged for deadheading '.$direct.' to '.$airfield_array[$curr_destination];
	}
	else{
		echo '<br /><br />You are currently flagged for deadheading '.$direct.' to '.$curr_destination;
	}


echo '<br /><br />';

$hidden = array('valid' => 'true', 'clear' => '1');
echo form_open('dispatch/deadhead/','',$hidden);
echo '<center>'.form_submit('submit', 'Clear Deadhead').'</center>';
echo form_close();
}

echo '</center>';

echo '<br /><br />';

$hidden = array('valid' => 'true');
echo form_open('dispatch/deadhead/','',$hidden);

echo '<fieldset style="border: 1px dotted rgb(40, 45, 78); padding: 0.6em; margin-top: 0.4em; margin-bottom: 0.4em;">';
echo '<legend>Select Destination</legend>'; 
//output dropdown
echo '<label for="destination">Pick Destination</label>'.form_dropdown('destination', $airfield_array, $destination).'<br />';

echo '<center><b>or</b></center>';

echo '<label for="destination_usr">Enter Destination</label>'.form_input($destination_usr).'<br />';
echo '<label for="direct">Direct</label>'.form_dropdown('direct', $direct_array, $direct).' Direct will only deadhead on an aircraft flying direct to your destination<br />';
echo '</fieldset>';
echo '<br />';
echo '<center>'.form_submit('submit', 'Set Deadhead').'</center>';
echo form_close();


if(!empty($error)){
echo '<span style="color: red;"><br /><b>'.$error.'</b></span>';
}

?>