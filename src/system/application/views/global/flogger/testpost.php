<?php
echo form_open($urlval);

$timestamp = array( 'name' => 'timestamp','id' => 'timestamp','value' => $gmt_mysql_datetime, 'maxlength' => '50','size' => '30');

echo '<fieldset style="border: 1px dotted rgb(40, 45, 78); padding: 0.6em; margin-top: 0.4em; margin-bottom: 0.4em;">';
echo '<legend>Required</legend>'; 
echo '<label for="timestamp">Timestamp</label>'.form_input($timestamp).' eg: 0000-00-00 00:00:00<br />';
echo '</fieldset>';

echo '<center>'.form_submit('submit', 'Submit').'</center>';
echo form_close();

?>
