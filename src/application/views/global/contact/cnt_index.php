<?php  

	$hidden = array('contact_submit' => 'true');


	//Start form output	
	echo form_open('contact/','',$hidden);

	//initialise if required $reqd1
	if(!isset($reqd1) && !isset($reqd2)){ 
		$reqd1 = "";
		$reqd2 = "";
		$data['reqd1'] = '';
		$data['reqd2'] = '';
			}		
?>
<div class="content">





	<div align="center">


<?php if(isset($exception) && $exception != ''){ 
echo $exception; 
}
?>

</div>

<fieldset>
<legend>About You</legend>

<label for="name"><?php echo $reqd1; ?>Name<?php echo $reqd2; ?></label>
<?php echo form_input($name); ?>

<br />

<label for="email"><?php echo $reqd1; ?>Email<?php echo $reqd2; ?></label>
<?php echo form_input($email); ?>

</fieldset>

<fieldset>
<legend>Your Message</legend>
<label for="contact_nature"><?php echo $reqd1; ?>Send to<?php echo $reqd2; ?></label>
<?php echo form_dropdown('contact_nature', $contact_nature_array, $contact_nature); ?>
<br />
<label for="contact_title"><?php echo $reqd1; ?>Title<?php echo $reqd2; ?></label>
<?php echo form_input($contact_title); ?>
<label for="contact_message"><?php echo $reqd1; ?>Message<?php echo $reqd2; ?></label>
<?php echo form_textarea($contact_message); ?>

</fieldset>	






	<div class="buttons" align="center">
	    <button type="submit">
		<img src="<?php echo $image_url; ?>icons/application/accept.png" alt=""/> 
		Send Message
	    </button>
	</div>

</div>
	  <?php echo form_close(); ?>
