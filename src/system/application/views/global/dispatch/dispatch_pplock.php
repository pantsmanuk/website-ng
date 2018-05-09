<center>

<center>
	<strong>Please select a propilot destination below by slecting the lock icon on the respective row.</strong>
</center>
<br>
<?php
echo '<table class="boxed" width="100%">
<tr>
<th>Aircraft</th>
<th>From</th>
<th>To</th>
<th>Distance</th>
<th align="center"><img src="'.$image_url.'icons/application/map.png" alt="Map" /></th>

<th></th>
</tr>';
$i=0;
foreach($airport_data as $row){

	$distance = number_format($this->geocalc_fns->GCDistance($lat1, $lon1, $row->lat, $row->lon),0).' nm';

	if($i%2 != 0){
		$bgcol = 'bgcolor="#f2f2f2"';
	}
	else{
		$bgcol = '';
	}

	echo '<tr '.$bgcol.'>';
	echo '<td align="left" width="150">'.$aircraft.'</td>';	
	echo '<td align="left">'.$start_icao.' '.$start_name.'</td>';
	echo '<td align="left">'.$row->icao.' '.$row->name.'</td>';
	echo '<td>'.$distance.'</td>';
	echo '<td align="center" width="30"><a href="http://www.gcmap.com/map?P='.strtolower($start_icao).'-'.strtolower($row->icao).'&MS=bm&PM=*" 
			rel="lightbox" title="Map of the route '.strtoupper($start_icao).'-'.strtoupper($row->icao).'">
			<img src="'.$image_url.'icons/application/map.png" alt="Map" /></a></td>';
	echo'<td align="center" width="20"><a href="'.$base_url.'dispatch/submit/'
	.$start_icao.'/'
	.$row->icao.'/'
	.$aircraft_id.'/1'
	.'">
				<img src="'.$image_url.'icons/application/lock_add.png" alt="Lock" /></a></td>';
	echo '</tr>';
	$i++;
}
echo '</table>';
?>