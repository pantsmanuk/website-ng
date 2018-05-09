<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * THIS SOFTWARE AND DOCUMENTATION IS PROVIDED "AS IS," AND COPYRIGHT
 * HOLDERS MAKE NO REPRESENTATIONS OR WARRANTIES, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO, WARRANTIES OF MERCHANTABILITY OR
 * FITNESS FOR ANY PARTICULAR PURPOSE OR THAT THE USE OF THE SOFTWARE
 * OR DOCUMENTATION WILL NOT INFRINGE ANY THIRD PARTY PATENTS,
 * COPYRIGHTS, TRADEMARKS OR OTHER RIGHTS.COPYRIGHT HOLDERS WILL NOT
 * BE LIABLE FOR ANY DIRECT, INDIRECT, SPECIAL OR CONSEQUENTIAL
 * DAMAGES ARISING OUT OF ANY USE OF THE SOFTWARE OR DOCUMENTATION.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://gnu.org/licenses/>.
 */
?> 


<table>
<tr valign="top">
<td width="435">

<div class="container">

<table class="statbox">
<tr>
<th colspan="3">Rank</th>
<th>Hours</th>
<th>Class</th>
</tr>
<?php
	
	foreach($rank_array as $row){
	
	if($this->session->userdata('logged_in') == '1' && $row['id'] == $this->session->userdata('rank_id')){
		$bgcol = 'bgcolor="#d1d9e5"';
	}
	else{
		$bgcol = '';
	}
	
	
	echo '<tr '.$bgcol.'>';
		echo '<td width="120"><img src="'.$image_url.'ranks/'.$row['id'].'.png" alt="'.$row['name'].'" /></td>';
		echo '<td width="35">'.$row['rank'].'</td>';
		echo '<td width="150" align="left">'.$row['name'].'</td>';	
		echo '<td width="65">'.$row['hours'].'</td>';	
		echo '<td width="65">'.$row['clss'].'</td>';						
	echo '</tr>';
			
		
	}

?>

</table>
</div>
</td>

<td>

<div class="container">

At Euroharmony, you will be promoted once you reach certain milestones in your career. The table to the left shows how many hours are required for each promotion. 

<br /><br />
Each rank unlocks unique benefits at Euroharmony. Every rank promotion opens up additional aircraft and routes for you to fly, but some also unlock divisions and therefore have a greater impact.

<br /><br />
Euroharmony's relaxed ethos is reflected in the small number of hours required for promotion. In addition to a large number of aircraft that you can fly already as a First Officer, you will find that you move very quickly through the ranks, providing fresh available aircraft from our large and diverse fleet.

</div>
</td>

</tr>
</table>

