<?php
//output current flights


	echo '<h1>Current Flights: '.$num_current_flights.'</h1>';

	//echo '<div id="map_canvas" style="width:100%; height:350px"></div>';
	echo '<div id="flightmapcontainer"></div>';
	
	echo'
	<script>
		
		var table=document.getElementById("currentFlights");
	
		$.ajax({
				type: "POST",
				url: "'.$base_url.'ajax/latestflights/",
				dataType: "xml",
				success: function(xml) {
					$(xml).find(\'flight\').each(function(){
						var pilot = $(this).find(\'pilot\').text();
						var rank = $(this).find(\'rank\').text();
						var aircraft = $(this).find(\'aircraft\').text();
						var bearing = $(this).find(\'bearing\').text();
						var altitude = $(this).find(\'altitude\').text();
						var ias = $(this).find(\'ias\').text();
						var fromicao = $(this).find(\'fromicao\').text();
						var fromlat = $(this).find(\'fromlat\').text();
						var fromlon = $(this).find(\'fromlon\').text();
						var toicao = $(this).find(\'toicao\').text();
						var tolat = $(this).find(\'tolat\').text();
						var tolon = $(this).find(\'tolon\').text();
						var positionlat = $(this).find(\'positionlat\').text();
						var positionlon = $(this).find(\'positionlon\').text();
						var propilot = $(this).find(\'propilot\').text();
						
						var row = table.insertRow(-1);
						var cell1 = row.insertCell(0);
						var cell2 = row.insertCell(1);
						var cell3 = row.insertCell(2);
						var cell4 = row.insertCell(3);
						var cell5 = row.insertCell(4);
						var cell6 = row.insertCell(5);
						var cell7 = row.insertCell(6);
						var cell8 = row.insertCell(7);
						var cell9 = row.insertCell(8);
						
						cell1.innerHTML = "[" + rank + "]";
						cell2.innerHTML = pilot;
						cell2.innerHTML = aircraft;
						cell2.innerHTML = bearing;
						cell2.innerHTML = altitude;
						cell2.innerHTML = ias;
						cell2.innerHTML = fromicao;
						cell2.innerHTML = toicao;
						cell2.innerHTML = propilot;
						alert("Success");
						
						});

					}
				
				
		});
	</script>	
	';
	

	
if($num_current_flights > 0){
	echo '<table width="100%" class="statbox" id="currentFlights">';
	echo '<tr>';
		echo '<th width="35">Rank</th>';
		echo '<th>Pilot</th>';
		echo '<th>Aircraft</th>';
		//echo '<th>lat</th>';
		//echo '<th>lon</th>';
		echo '<th>brg</th>';
		echo '<th>alt</th>';
		echo '<th>ias</th>';
		echo '<th>From</th>';
		echo '<th>To</th>';
		echo '<th>PP</th>';
	echo '</tr>';

	$i = 0;
	foreach($current_flights as $row){
	
		$bg = '';
		$bg_col = '';
		if($i%2 == 0){
			$bg = 'bgcolor="#f2f2f2"';
			$bg_col = '#f2f2f2';
		}
		
		echo "<tr $bg onmouseover=\"this.style.background='#760606';this.style.color='#FFFFFF';this.style.cursor='pointer'\"
        onmouseout=\"this.style.background='$bg_col';this.style.color='#000000';\" onclick=\"centreLoc($row->lat, $row->lon);\">";
        	echo '<td>['.$row->rank.']</td>';
			echo '<td align="left"><div align="left">EHM-'.$row->username.' '.$row->fname.' '.$row->sname.'</div></td>';
			echo '<td>'.$row->name.'</td>';
			//echo '<td>'.$row->lat.'</td>';
			//echo '<td>'.$row->lon.'</td>';
			echo '<td>'.str_pad($row->bearing, 3, "0", STR_PAD_LEFT).'</td>';
			echo '<td>'.$row->altitude.'</td>';
			echo '<td>'.$row->ias.'</td>';
			echo '<td>'.$row->start_icao.'</td>';
			echo '<td>'.$row->end_icao.'</td>';
			echo '<td>'.$row->propilot_flight.'</td>';
		echo '</tr>';
		
		
			
	$i++;
	}
	
	echo '</table>';
	
	echo '<br /><br />';

}

?>


<?php

//output recent flights
if($num_recent_flights > 0){

	echo '<h1>Recent Flights: '.$num_recent_flights.'</h1>';

	echo '<table width="100%" class="statbox">';
	echo '<tr>';
		echo '<th>Pilot</th>';
		echo '<th>Aircraft</th>';
		echo '<th>Pax</th>';
		echo '<th>Cargo</th>';
		echo '<th>From</th>';
		echo '<th>To</th>';
		echo '<th>Arrived</th>';
		echo '<th>Hub</th>';
		echo '<th>PP</th>';
	echo '</tr>';

	$i = 0;
	foreach($recent_flights as $row){
		
		$bg = '';
		if($i%2 == 0){
			$bg = 'bgcolor="#f2f2f2"';
		}
	
		echo "<tr $bg>";
			echo '<td align="left"><div align="left">EHM-'.$row->username.' '.$row->fname.' '.$row->sname.'</div></td>';
			echo '<td>'.$row->name.'</td>';
			echo '<td>'.$row->passengers.'</td>';
			if($row->cargo == 0){
				echo '<td>-</td>';
			}
			else{
				if($row->cargo > 1300){
					echo '<td>'.number_format($this->format_fns->lbs_tonnes($row->cargo),0).'t</td>';
				}
				else{
					echo '<td>'.number_format($row->cargo,0).'lbs</td>';
				}
			}
			echo '<td>'.$row->start_icao.'</td>';
			echo '<td>'.$row->end_icao.'</td>';
			echo '<td>'.gmdate('d/m/Y H:i', strtotime($row->landing_time)).'z</td>';
			echo '<td><a href="'.$base_url.'hubs/index/'.$row->hub_icao.'">'.$row->hub_icao.' '.$row->hub_name.'</a></td>';
			echo '<td>'.$row->propilot_flight.'</td>';
		echo '</tr>';
		//echo 'Recent: '.$i.'/'.$num_recent_flights.'<br />';
		
	$i++;
	}
	
	echo '</table>';

}





?>