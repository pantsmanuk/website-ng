<?php

if(!empty($event_award_return)){

    $remark_feedback = $event_award_return;
    if($event_award_return == 1){
        $remark_feedback = 'Success!';
    }

    echo '<div class="warning">';
    echo '<b>Remark Feedback: '.$remark_feedback.'</b>';
    echo '</div>';

}

echo '<h3>The following pilots have particpated in this event.</h3>';
echo '<div style="font-colour: orange;">Click an Entitled badge to initiate a remark if the corresponding Awarded badge is missing.</div><br /><br />';
echo '<table width="100%" class="boxed">';
echo '<tr>';
echo '<th>Pilot</th>';
echo '<th>Flown</th>';
echo '<th>Last Location</th>';
echo '<th>Entitled</th>';
echo '<th>Awarded</th>';
//echo '<th>RMK</th>';
echo '</tr>';

$i = 0;
foreach($pilot_list as $row){

    //dont output if no pilots
    if(!empty($row['name']) && $row['username'] != 'EHM-'){

        //determine row colour
        $bgstyle = 'style="line-height: 30px; overflow-y:hidden;"';
        if($i%2 == 0){
            $bgstyle = 'style="background: #f2f2f2; line-height: 30px; overflow-y:hidden;"';
        }


        echo '<tr '.$bgstyle.'>';
        echo '<td>'.$row['username'].' '.$row['name'].'</td>';
        echo '<td style="text-align: center;">'.$row['num_flights'].'/'.$event_leg_count.'</td>';
        echo '<td style="text-align: center;">Leg '.$row['last_leg'].' ['.$row['last_location'].']</td>';
        echo '<td style="text-align: center;">';
            foreach($row['awards'] as $award_id){
                if(!empty($award_id)){
                echo '<a href="'.$base_url.'acp_propilot/event_awards_remark/'.$event_id.'/'.$row['user_id'].'/'.$award_id.'">';
                echo '<img src="'.$assets_url.'uploads/awards/'.$award_id.'.png" /> ';
                echo '</a>';
                }
            }
        echo '</td>';
        echo '<td style="text-align: center;">';
            foreach($row['awards_assigned'] as $award_id){
                if(!empty($award_id)){
                echo '<img src="'.$assets_url.'uploads/awards/'.$award_id.'.png" /> ';
                }
            }
        echo '</td>';
        //echo'<td align="center" width="15"><a href="'.$base_url.'acp_propilot/event_awards_remark/'.$event_id.'/'.$row['user_id'].'">
        //    <img src="'.$image_url.'icons/application/arrow_refresh.png" alt="Edit" /></a></td>';
        echo '</tr>';
    $i++;
    }

}

//display if no pilots
if($i < 1){
    echo '<tr><td colspan="5" style="text-align: center;"><br />No Pilots have flown this event yet<br /><br /></td></tr>';
}

echo '</table>';


?>