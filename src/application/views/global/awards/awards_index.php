<?php

//output links
echo '<span style="float: right;"><p>'.$this->pagination->create_links().'</p></span>';
 
//output table
echo '<table class="boxed" width="100%">';
//echo '<tr>';
//echo '<td></td>';
//echo '<td></td>';
//echo '</tr>';

$i = 0;
foreach($awards as $row){


	if(is_numeric($offset) 
	&& $i >= $offset 
	&& $i < ($offset+$limit)
	){
	
		$award_id = $row->id;
	
		//determine if image exists for award
		$image_path = $assets_path.'uploads/awards/'.$award_id;
	
		if (file_exists($image_path.'.png')) {
			$image_url = $assets_url.'uploads/awards/'.$award_id.'.png';
			$image_path = $assets_path.'uploads/awards/'.$award_id.'.png';
		}
		elseif (file_exists($image_path.'.gif')) {
			$image_url = $assets_url.'uploads/awards/'.$award_id.'.gif';
			$image_path = $assets_path.'uploads/awards/'.$award_id.'.gif';
		}
		elseif (file_exists($image_path.'.jpg')) {
			$image_url = $assets_url.'uploads/awards/'.$award_id.'.jpg';
			$image_path = $assets_path.'uploads/awards/'.$award_id.'.jpg';
		}
		else{
			$image_url = $assets_url.'uploads/awards/no-image.jpg'; 
			$image_path = $assets_path.'uploads/awards/no-image.jpg';
		}
	
		
		//recalculate width and height
		
		list($width_orig, $height_orig, $imageType, $imageAttr) = getimagesize($image_path);	
		
		$height_new = 30;
		$width_new = $width_orig / $height_orig * $height_new;
		
		
		echo '<tr valign="middle">';
		echo '<td><img src="'.$image_url .'" width="'.$width_new.'" height="'.$height_new.'"/></td>';
		echo '<td>';
		echo '<b>'.$row->award_name.'</b><br />';
		echo $row->description;
		if($row->automatic == 'Y'){
			echo '<br /><i>This award is automatically bestowed</i>';
		}
		
		echo '</td>';
		echo '</tr>';
	}
$i++;
}
 
echo '</table>';

//output links
echo '<span style="float: right;"><p>'.$this->pagination->create_links().'</p></span>';

?>