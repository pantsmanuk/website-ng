<div class="menu_sub">
<?php
echo '<a href="'.$base_url.'dispatch/timetable">Timetable</a> | ';
//echo '<a href="'.$base_url.'dispatch/timetable_map">Map</a> | ';
echo '<a href="'.$base_url.'dispatch/route">Route</a> | ';
echo '<a href="'.$base_url.'dispatch/assign">Assignment</a> | ';
echo '<a href="'.$base_url.'dispatch/charter">Charter</a> |';
echo '<a href="'.$base_url.'tours">Tours</a> |';
echo '<a href="'.$base_url.'events">Events</a> |';
echo '<a href="'.$base_url.'dispatch/propilot">Propilot™</a> ';
?>
</div>
<br /><br />
<?php
$queried = 0;
$unchecked_output = '';
if($num_unchecked > 0){
$unchecked_output .= '<br /><hr /><br /><span style="font-weight:bold;font-size:14px;">Unchecked flights</span><br />';
//$unchecked_output .= '<hr />';
$unchecked_output .= '<span style="color: rgb(0, 0, 0);">Manual submissions have to be approved before showing up in your flight log. Flight Logger can automatically record, submit and approve both normal and propilot flights. <a href="'.$flogger_latest.'">Download Flogger '.$flogger_version.' Now.</a></span><br /><br />';
$unchecked_output .= '<table width="100%" border="0" class="boxed">';
$unchecked_output .= '<tr>';
$unchecked_output .= '<th align="center">Status</th>';
$unchecked_output .= '<th align="center">Aircraft</th>';
$unchecked_output .= '<th align="center">From</th>';
$unchecked_output .= '<th align="center">To</th>';
$unchecked_output .= '<th align="center">Passengers</th>';
$unchecked_output .= '<th align="center">Cargo</th>';
$unchecked_output .= '<th align="center">Submitted</th>';
//echo '<td align="center" width="18">&nbsp;</td>';
$unchecked_output .= '<td align="center" width="18">&nbsp;</td>';
$unchecked_output .= '<td align="center" width="18">&nbsp;</td>';
$unchecked_output .= '<tr>';


$i = 0;
foreach($unchecked_flights as $row){


	if($row->checked == '3'){
		$bgcol = 'bgcolor="#ffffbb"';
	}
	if($i%2 != 0){
		$bgcol = 'bgcolor="#f2f2f2"';
	}
	else{
		$bgcol = '';
	}
	
	$status = 'Unchecked';
	if($row->checked == '3'){
		$bgcol = 'bgcolor="#ffbb99"';
		$status = 'Queried';
	}
	elseif($row->checked == '4'){
		$bgcol = 'bgcolor="#ffffbb"';
		$status = 'Responded';
	}
	
	$unchecked_output .= '<tr '.$bgcol.'>';
	$unchecked_output .= '<td align="center">'.$status.'</td>';
	$unchecked_output .= '<td align="center">'.$row->aircraft.'</td>';
	$unchecked_output .= '<td align="left">'.$row->start_icao.' '.$row->dep_name.'</td>';
	$unchecked_output .= '<td align="left">'.$row->end_icao.' '.$row->arr_name.'</td>';
	$unchecked_output .= '<td align="center">'.number_format($row->passengers,0).'</td>';
	if($row->cargo == 0){
		$unchecked_output .= '<td align="center">-</td>';
	}
	else{
		$unchecked_output .= '<td align="center">'.number_format($row->cargo,0).'lbs</td>';
	}
	$unchecked_output .= '<td align="center">'.date('d/m/Y', strtotime($row->submitdate)).'</td>';
	/*echo '<th align="center"><a href="'.$base_url.'dispatch/pirepedit/'.$row->id.'">
	<img src="'.$image_url.'icons/application/database_edit.png" alt="Delete" /></a></th>';*/
	if($row->checked == '3' || $row->checked == '4'){
		$queried = 1;
		$unchecked_output .= '<td align="center"><a href="'.$base_url.'dispatch/pirepquery/'.$row->id.'">
			<img src="'.$image_url.'icons/application/database_error.png" alt="Query" /></a></td>';
	}
	else{
		$unchecked_output .= '<td align="center">&nbsp;</td>';
	}
	$unchecked_output .= '<td align="center"><a href="'.$base_url.'dispatch/pirepdelete/'.$row->id.'">
	<img src="'.$image_url.'icons/application/database_delete.png" alt="Delete" /></a></td>';
	$unchecked_output .= '<tr>';
	
	
$i++;
}


$unchecked_output .= '</table>';


	$unchecked_output .= '<br /> <br />';
	
	$unchecked_output .= '<table border="0" class="boxed">';
	$unchecked_output .= '<tr>';
	$unchecked_output .= '<td align="center"><img src="'.$image_url.'icons/application/database_error.png" alt="Query" /></td>';
	$unchecked_output .= '<td>View query and respond</td>';
	$unchecked_output .= '</tr>';
	$unchecked_output .= '<tr>';
	$unchecked_output .= '<td align="center"><img src="'.$image_url.'icons/application/database_delete.png" alt="Delete" /></td>';
	$unchecked_output .= '<td>Delete pilot\'s report</td>';
	$unchecked_output .= '</tr>';
	$unchecked_output .= '</table>';
}

if($queried == 1){
echo '<center><div class="warning">Any queried flights must be cleared before further manual submission is permitted. Flight Logger can submit flights at any time - <a href="'.$flogger_latest.'">Download Flogger '.$flogger_version.' Now.</a></div></center><br />';	
}

$i = 0;
$asn = 0;
$pp = 0;
$msn = 0;
$assigned_data = '';
$route_data = array();
$mission_data = '';
$route_output = '';
foreach($assigned_flights as $row){

	

	if($row->group_id == ''){
		if($row->mission_id == ''){
		
			if($asn%2 == 0){
				$bgcol = 'bgcolor="#f2f2f2"';
			}
			else{
				$bgcol = '';
			}
	
			$assigned_data .= '<tr '.$bgcol.'>';
			$assigned_data .= '<td align="center">'.$row->aircraft.'</td>';
			$assigned_data .= '<td align="left">'.$row->start_icao.' '.$row->dep_name.'</td>';
			$assigned_data .= '<td align="left">'.$row->end_icao.' '.$row->arr_name.'</td>';
			$assigned_data .= '<td align="center">'.number_format($row->passengers,0).'</td>';
			if($row->cargo == 0){
				$assigned_data .= '<td align="center">-</td>';
			}
			else{
				$assigned_data .= '<td align="center">'.number_format($row->cargo,0).'lbs</td>';
			}
			
			if($row->dep_time != ''){
				$assigned_data .= '<td align="center">'.substr($row->dep_time,0,5).'z</td>';
			}
			else{
				$assigned_data .= '<td align="center">Unspecified</td>';
			}
			$assigned_data .= '<td align="center" width="75">'.number_format($row->gcd,0).'nm</td>';
			$assigned_data .= '<td align="center" width="15"></td>';
			$assigned_data .= '<td align="center"><a href="http://www.gcmap.com/map?P='.strtolower($row->start_icao).'-'.strtolower($row->end_icao).'&MS=bm&PM=*" 
			rel="lightbox" title="Map of the route '.strtoupper($row->start_icao).'-'.strtoupper($row->end_icao).'">
			<img src="'.$image_url.'icons/application/map.png" alt="Map" /></a></td>';
			$assigned_data .= '<td align="center"><a href="'.$base_url.'dispatch/unassign/'.$row->id.'">
			<img src="'.$image_url.'icons/application/database_delete.png" alt="Delete" /></a></td>';
			if($queried != 1){
				$assigned_data .= '<td align="center"><a href="'.$base_url.'dispatch/pirep/'.$row->id.'">
				<img src="'.$image_url.'icons/application/database_go.png" alt="PIREP" /></a></td>';
			}
			else{
				$assigned_data .= '<td align="center"></td>';
			}
			$assigned_data .= '</tr>';
			
			$asn++;
		}
		else{
		
			if($msn%2 == 0){
				$bgcol = 'bgcolor="#f2f2f2"';
			}
			else{
				$bgcol = '';
			}
		
			$mission_data .= '<tr '.$bgcol.'>';
			$mission_data .= '<td align="center">'.$row->aircraft.'</td>';
			$mission_data .= '<td align="left">'.$row->start_icao.' '.$row->dep_name.'</td>';
			$mission_data .= '<td align="left">'.$row->end_icao.' '.$row->arr_name.'</td>';
			$mission_data .= '<td align="center">'.number_format($row->passengers,0).'</td>';
			if($row->cargo == 0){
				$mission_data .= '<td align="center">-</td>';
			}
			else{
				$mission_data .= '<td align="center">'.number_format($row->cargo,0).'lbs</td>';
			}
			if($row->dep_time != ''){
				$mission_data .= '<td align="center">'.substr($row->dep_time,0,5).'z</td>';
			}
			else{
				$mission_data .= '<td align="center">Unspecified</td>';
			}
			$mission_data .= '<td align="center" width="75">'.number_format($row->gcd,0).'nm</td>';
			$mission_data .= '<td align="center" width="15"></td>';
			$mission_data .= '<td align="center"><a href="http://www.gcmap.com/map?P='.strtolower($row->start_icao).'-'.strtolower($row->end_icao).'&MS=bm&PM=*" 
			rel="lightbox" title="Map of the route '.strtoupper($row->start_icao).'-'.strtoupper($row->end_icao).'">
			<img src="'.$image_url.'icons/application/map.png" alt="Map" /></a></td>';
			$mission_data .= '<td align="center"><a href="'.$base_url.'dispatch/unassign/'.$row->id.'">
			<img src="'.$image_url.'icons/application/database_delete.png" alt="Delete" /></a></td>';
			if($queried != 1){
				$mission_data .= '<td align="center"><a href="'.$base_url.'dispatch/pirep/'.$row->id.'">
				<img src="'.$image_url.'icons/application/database_go.png" alt="PIREP" /></a></td>';
			}
			else{
				$mission_data .= '<td align="center"></td>';	
			}
			$mission_data .= '</tr>';	
			
			$msn++;
		}
	}
	else{

	
		$route_data[$row->group_id]['legs'][$row->group_order]['id'] = $row->id;
		$route_data[$row->group_id]['legs'][$row->group_order]['aircraft'] = $row->aircraft;
		$route_data[$row->group_id]['legs'][$row->group_order]['origin'] = $row->start_icao;
		$route_data[$row->group_id]['legs'][$row->group_order]['origin_name'] = $row->dep_name;
		$route_data[$row->group_id]['legs'][$row->group_order]['destination'] = $row->end_icao;
		$route_data[$row->group_id]['legs'][$row->group_order]['destination_name'] = $row->arr_name;
		$route_data[$row->group_id]['legs'][$row->group_order]['pax'] = $row->passengers;
		$route_data[$row->group_id]['legs'][$row->group_order]['cargo'] = $row->cargo;
		$route_data[$row->group_id]['legs'][$row->group_order]['gcd'] = $row->gcd;
		$route_data[$row->group_id]['legs'][$row->group_order]['order'] = $row->group_order;
		$route_data[$row->group_id]['legs'][$row->group_order]['award_id'] = $row->award_id;
		
		
		
		$route_data[$row->group_id]['tour_id'] = $row->tour_id;
		$route_data[$row->group_id]['tour_name'] = $row->tour_name;
		
		$route_data[$row->group_id]['event_id'] = $row->event_id;
		$route_data[$row->group_id]['event_name'] = $row->event_name;
		
		if(!array_key_exists('route', $route_data[$row->group_id])){
		
			$route_data[$row->group_id]['route'] = $row->start_icao.'-'.$row->end_icao;
		}
		else{
			$route_data[$row->group_id]['route'] .= '-'.$row->end_icao;
		}
		
	}
	


}



$propilot_data = '';
foreach($propilot_flights as $row){

	if($pp%2 == 0){
		$bgcol = 'bgcolor="#f2f2f2"';
	}
	else{
		$bgcol = '';
	}
	
	
	$propilot_data .= '<tr '.$bgcol.'>';
	$propilot_data .= '<td align="center">'.$row->tail_id.' | '.$row->aircraft.'</td>';
	$propilot_data .= '<td align="left">'.$row->start_icao.' '.$row->dep_name.'</td>';
	$propilot_data .= '<td align="left">'.$row->end_icao.' '.$row->arr_name.'</td>';
	$propilot_data .= '<td align="center">'.number_format($row->passengers,0).'</td>';
	if($row->cargo == 0){
		$propilot_data .= '<td align="center">-</td>';
	}
	else{
		$propilot_data .= '<td align="center">'.number_format($row->cargo,0).'lbs</td>';
	}
	$propilot_data .= '<td align="center">'.number_format($row->gcd,0).'nm</td>';
	$propilot_data .= '<td align="center" width="15"></td>';
	$propilot_data .= '<td align="center"><a href="http://www.gcmap.com/map?P='.strtolower($row->start_icao).'-'.strtolower($row->end_icao).'&MS=bm&PM=*" 
	rel="lightbox" title="Map of the route '.strtoupper($row->start_icao).'-'.strtoupper($row->end_icao).'">
	<img src="'.$image_url.'icons/application/map.png" alt="Map" /></a></td>';
	$propilot_data .= '<td align="center"><a href="'.$base_url.'dispatch/unlock/'.$row->id.'">
	<img src="'.$image_url.'icons/application/database_delete.png" alt="Unlock" /></a></td>';
$pp++;
}



if(count($route_data) > 0){
	foreach($route_data as $group_id => $group){
		
		$manual_pirep = 1;
		$tagline = '';
		
		
		if($group['tour_id'] != ''){
			//we are a tour
			$identifier = 'Tour: '.$group['tour_name'];
		}
		elseif($group['event_id'] != '' && $group['event_id'] != '0'){
			//we are an event
			$identifier = 'Propilot Event: '.$group['event_name'];
			$manual_pirep = 0;
			$tagline = '(Flight Logger only)';
		}
		else{
			$identifier = 'Route: '.$group['route'];
		}
		
		//first create the header
		$route_output .= '<tr><td colspan="8" style="color: #000000; background: #d1d9e5; font-weight: bold;">'.$identifier.' '.$tagline.'</td>';
		$route_output .= '<td align="center" style="color: #000000; background: #d1d9e5; font-weight: bold;"><a href="http://www.gcmap.com/map?P='.$group['route'].'&MS=bm&PM=*" 
		rel="lightbox" title="Map of the route '.$group['route'].'">
		<img src="'.$image_url.'icons/application/map.png" alt="Map" /></a></td>';
		$route_output .= '<td align="center" style="color: #000000; background: #d1d9e5; font-weight: bold;"><a href="'.$base_url.'dispatch/killroute/'.$group_id.'">
		<img src="'.$image_url.'icons/application/database_delete.png" alt="Delete" /></a></td>';
		$route_output .= '</tr>';
		
		$i = 1;
		//next, iterate through all the legs
		foreach($group['legs'] as $leg){
			
			$bgcolor = '';
			if($leg['award_id'] != ''){
				$bgcolor = 'style="background: #ffee00;"';	
			}
			
			$route_output .= '<tr '.$bgcolor.'>';
			$route_output .= '<td width="20" align="center">'.$leg['order'].'</td>';
			$route_output .= '<td align="left">'.$leg['aircraft'].'</td>';
			$route_output .= '<td align="left">'.$leg['origin'].' '.$leg['origin_name'].'</td>';
			$route_output .= '<td align="left">'.$leg['destination'].' '.$leg['destination_name'].'</td>';
			$route_output .= '<td align="center">'.number_format($leg['pax'],0).'</td>';
			if($row->cargo == 0){
				$route_output .= '<td align="center">-</td>';
			}
			else{
				$route_output .= '<td align="center">'.number_format($leg['cargo'],0).'lbs</td>';
			}
			$route_output .= '<td align="center">'.number_format($leg['gcd'],0).'nm</td>';
			if($group['tour_id'] == ''){
				$route_output .= '<td align="center" width="15"></td>';
			}
			else{
				$route_output .= '<td align="center" width="15"><a href="'.$base_url.'dispatch/tourcraft/'.$leg['id'].'"><img src="'.$image_url.'icons/application/aircraft.png" alt="Aircraft" /></a></td>';
			}
			$route_output .= '<td align="center"><a href="http://www.gcmap.com/map?P='.strtolower($leg['origin']).'-'.strtolower($leg['destination']).'&MS=bm&PM=*" 
			rel="lightbox" title="Map of the route '.strtoupper($leg['origin']).'-'.strtoupper($leg['destination']).'">
			<img src="'.$image_url.'icons/application/map.png" alt="Map" /></a></td>';
			if($i == 1 && $manual_pirep == 1){
				if($queried != 1){
					$route_output .= '<td align="center"><a href="'.$base_url.'dispatch/pirep/'.$leg['id'].'">
					<img src="'.$image_url.'icons/application/database_go.png" alt="PIREP" /></a></td>';
				}
				else{
					$route_output .= '<td align="center"></td>';
				}
			}
			else{
				$route_output .= '<td>&nbsp;</td>';
			}
			$route_output .= '</tr>';
		$i++;
		}
	}
}


if($pp > 0 || $i > 0 || $msn > 0 || $asn > 0){


	if($propilot_data != ''){
	
		
		echo '<span style="font-weight:bold;font-size:14px;">Propilot locked flight</span><br />';
		//echo '<hr />';
		echo '<table width="100%" border="0" class="boxed"">';
		echo '<tr>';
		echo '<th align="center">Aircraft</td>';
		echo '<th align="center">From</td>';
		echo '<th align="center">To</td>';
		echo '<th align="center" width="30">Pax</td>';
		echo '<th align="center">Cargo</td>';
		echo '<th align="center">Distance</td>';
		echo '<th align="center" width="18">&nbsp;</td>';
		echo '</tr>';
		echo $propilot_data;
		echo '</table>';
	
	}

	
	if($assigned_data != ''){
	
		if($propilot_data != ''){
			echo '<br /><br />';
		}
	
	
		echo '<span style="font-weight:bold;font-size:14px;">Assigned flights</span><br />';
		//echo '<hr />';
		echo '<table width="100%" border="0" class="boxed">';
		echo '<tr>';
		echo '<th align="center">Aircraft</td>';
		echo '<th align="center">From</td>';
		echo '<th align="center">To</td>';
		echo '<th align="center" width="30">Pax</td>';
		echo '<th align="center">Cargo</td>';
		echo '<th align="center">Departure</td>';
		echo '<th align="center">Distance</td>';
		echo '<th align="center" width="18">&nbsp;</td>';
		echo '<th align="center" width="18">&nbsp;</td>';
		echo '<th align="center" width="18">&nbsp;</td>';
		echo '</tr>';
		echo $assigned_data;
		echo '</table>';
	
	}
	
	if($mission_data != ''){
	
		if($assigned_data != '' || $propilot_data != ''){
			echo '<br /><br />';
		}
	
	
		echo '<span style="font-weight:bold;font-size:14px;">Mission flights</span><br />';
		//echo '<hr />';
		echo '<table width="100%" border="0" class="boxed">';
		echo '<tr>';
		echo '<th align="center">Aircraft</td>';
		echo '<th align="center">From</td>';
		echo '<th align="center">To</td>';
		echo '<th align="center" width="30">Pax</td>';
		echo '<th align="center">Cargo</td>';
		echo '<th align="center">Departure</td>';
		echo '<th align="center">Distance</td>';
		echo '<th align="center" width="18">&nbsp;</td>';
		echo '<th align="center" width="18">&nbsp;</td>';
		echo '<th align="center" width="18">&nbsp;</td>';
		echo '</tr>';
		echo $mission_data;
		echo '</table>';
	
	}
	
	
	

	
	//route data
	if($route_output != ''){
		
		if($assigned_data != '' || $propilot_data != '' || $mission_data != ''){
		echo '<br /> <br />';
		}
	
		echo '<span style="font-weight:bold;font-size:14px;">Assigned routes</span><br />';
	
		echo '<table width="100%" border="0" class="boxed">';
		echo '<tr>';
		echo '<th align="center">&nbsp;</td>';
		echo '<th align="left">Aircraft</td>';
		echo '<th align="center">From</td>';
		echo '<th align="center">To</td>';
		echo '<th align="center" width="30">Pax</td>';
		echo '<th align="center">Cargo</td>';
		echo '<th align="center">Distance</td>';
		echo '<th align="center" width="18">&nbsp;</td>';
		echo '<th align="center" width="18">&nbsp;</td>';
		echo '<tr>';
		echo $route_output;
		echo '</table>';
			
		
	}
	


	
	echo '<br /> <br />';
	
	echo '<table border="0" class="boxed" style="margin-left:auto; margin-right:auto;">';
	echo '<tr>';
	echo '<td align="center"><img src="'.$image_url.'icons/application/aircraft.png" alt="Aircraft" /></td>';
	echo '<td><span style="margin-right: 10px;">Change aircraft</span></td>';
	echo '<td align="center"><img src="'.$image_url.'icons/application/map.png" alt="Map" /></td>';
	echo '<td><span style="margin-right: 10px;">View flight map</span></td>';
	echo '<td align="center"><img src="'.$image_url.'icons/application/database_delete.png" alt="Unassign" /></td>';
	echo '<td><span style="margin-right: 10px;">Unassign flight</span></td>';
	echo '<td align="center"><img src="'.$image_url.'icons/application/database_go.png" alt="PIREP" /></td>';
	echo '<td>Submit pilot\'s report</td>';
	echo '</tr>';
	echo '</table>';
	
	
	
}
else{
	echo 'You currently have no flights assigned. Please use the tabs to choose a method of flight selection.<br /><br />You can <ul>
	<li>select a flight from our timetable</li> 
	<li>generate a multi-leg flight (Aeroclub, Eurobusiness and Wild)</li> 
	<li>have a flight assigned to you</li> 
	<li>create your own Aeroclub (GA), Eurobusiness or Wild (Bush) charter flight</li> 
	<li>lock a Propilot™ flight, for more serious simming</li> 
	</ul>';
}

echo $unchecked_output;

?>

<div id="gcmattrib">Maps generated by the <a href="http://www.gcmap.com/">Great Circle Mapper</a>&nbsp;- copyright &#169; <a href="http://www.kls2.com/~karl/">Karl L. Swartz</a>.</div>