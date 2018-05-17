<?php
$hidden = array('valid' => 'true');
echo form_open('acp_pilots/award_edit/' . $pilot_id . '/' . $award_id, '', $hidden);

echo $highlight1 . $error . $highlight2;

echo '<fieldset style="border: 1px dotted rgb(40, 45, 78); padding: 0.6em; margin-top: 0.4em; margin-bottom: 0.4em;">';
echo '<legend>Award</legend>';
echo '<label for="awards_index_id">Award</label>' . form_dropdown('awards_index_id', $awards_array, $awards_index_id) . '<br />';
echo '<label for="notes">Notes</label>' . form_textarea($notes) . '<br />';
echo '</fieldset>';

echo '<center>' . form_submit('submit', 'Submit') . '</center>';
echo form_close();
?>