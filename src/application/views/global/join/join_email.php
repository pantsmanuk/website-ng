<?php
$hub = $this->session->userdata('hub');
?>
<br />
<br />

<div align="left">

Congratulations <?php echo $fname; ?>, your account has been created.
<br /><br />
Your password has been emailed to the account supplied, as well as a confirmation link which needs to be followed in order to verify that you have access to the email account.
<br /><br />
You have been automatically logged in to the site, but in case there is a problem with the email, your credentials to log in are:
<br /><br /><b>
Username: EHM-<?php echo $username; ?></b> (the EHM- section of the callsign is not required for signing in)<br /><b>
Password: <?php echo $password; ?></b>
<br /><br />
You can log in and re-send the confirmation email, or change the email address if necessary. You will not be able to log flight time until this final step is complete.
<br /><br />
You have been assigned to <b><?php echo '['.$hub_icao.'] '.$hub_name; ?> (<?php echo $country; ?>)</b>. Your hub manager is <b><?php echo $hub_captain; ?></b> and they will contact you once you have confirmed your email address. The hub you are assigned to does not affect where you may fly, but your flight hours will apply to this hub's statistics. You may apply for a hub transfer once you have reached Class 3 and flown for the VA for at least a month. You can apply for a hub transfer once each three months thereafter.
<br /><br />
Good luck and welcome to the VA! 

</div>