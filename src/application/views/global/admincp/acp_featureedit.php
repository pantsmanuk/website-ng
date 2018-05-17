<?php
$hidden['valid'] = 'true';
echo form_open('acp_feature/edit/' . $feature_id . '/', '', $hidden);
?>

<table class="boxed" width="100%">
    <tr>
        <th>type</th>
        <th>url</th>
        <th>enabled</th>
        <th>order</th>
    </tr>
    <tr>
        <td><?php echo form_dropdown('feature_type', $type_array, $feature_type); ?></td>
        <td><?php echo form_input($feature_uri); ?></td>
        <td><?php echo form_dropdown('feature_enabled', $enabled_array, $feature_enabled); ?></td>
        <td><?php echo form_input($feature_order); ?></td>
    </tr>

</table>

<br/><br/>

<center>
	<?php
	echo form_submit('Submit', 'Save');
	echo form_close();
	?>
</center>