<?php
$hidden['valid'] = 'true';
echo form_open('acp_feature/delete/'.$feature_id.'/', '', $hidden);
?>


<table class="boxed" width="100%">
<tr>
<th>type</th>
<th>url</th>
<th>enabled</th>
<th>order</th>
</tr>
<tr>
<td><?php echo $feature_type; ?></td>
<td><?php echo $feature_uri; ?></td>
<td><?php echo $feature_enabled; ?></td>
<td><?php echo $feature_order; ?></td>
</tr>

</table>

<br /><br />

<center>
<b>Please confirm that you intend to delete this record. This action cannot be undone.</b>
</center>

<br /><br />

<center>
<?php
echo form_submit('Submit', 'Delete');
echo form_close();
?>
</center>