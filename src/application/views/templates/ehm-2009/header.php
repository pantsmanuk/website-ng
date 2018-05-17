<?php
$is_admin = 0;
if ($this->session->userdata('admin_cp') == 1) {
	$is_admin = 1;
}

?>
<div id="content">
    <div id="header">
        <img src="<?php echo $tmpl_image_url; ?>structure/head_top_right_sm.jpg" alt="Euroharmony"/>

        <div id="controls">
            <div id="login">
				<?php
				if ($this->session->userdata('logged_in') == TRUE) {
					echo '<table class="boxed">
            <tr><td><span class="login">[' . $this->session->userdata('rank_short') . '] ' . $this->session->userdata('pilotname') .
						'</span></td><td><a href="' . $base_url . 'auth/logout"><img src="' . $tmpl_image_url . 'buttons/logout.png" alt="logout" /></a></td></tr>';

					echo '<tr><td colspan="2" align="center"><font size="1">';
					echo '<a href="' . $base_url . 'hubs/index/' . $this->session->userdata('hub') . '">HUB</a>';
					echo ' | <a href="' . $base_url . 'dispatch/">DISPATCH</a>';
					echo ' | <a href="' . $base_url . 'profile/">PROFILE</a>';
					//if we're an admin
					if ($is_admin == 1) {
						echo ' | <a href="' . $base_url . 'admincp/">ADMIN</a>';
					}
					echo '</font></td></tr></table>';

				} else {

					$login_username = array('name' => 'username', 'id' => 'username', 'maxlength' => '5', 'size' => '5');
					$login_password = array('name' => 'password', 'id' => 'password', 'maxlength' => '10', 'size' => '10');

					$hidden = array('valid' => 'true');
					echo form_open('auth/login', '', $hidden);

					echo '
            <table class="boxed">
            <tr>
            <td><div align="right" class="login">EHM-</div></td>
            <td><div align="left">' . form_input($login_username) . '</div></td>
            <td><div align="left">' . form_password($login_password) . '</div></td>
            <td><input type="submit" class="form_button" value="Login" /></td>
            </tr>
            </table>';

					echo form_close();
					$hidden = '';
				}
				?>
            </div>


            <div id="menu_primary">
                <div style="float: left;">
                    <center>
                        <img src="<?php echo $tmpl_image_url; ?>buttons/arrivals.png" alt="Menu"
                             style="vertical-align:middle;"/><br/>
                    </center>
                </div>
				<?php $this->load->view('global/menus/menu1'); ?>

				<?php
				/*
				<a href="<?php echo $base_url; ?>">Home</a>
				| <a href="<?php echo $base_url; ?>hubs/">Hubs</a>
				| <a href="<?php echo $base_url; ?>divisions/">Divisions</a>
				| <a href="<?php echo $base_url; ?>fleet/">Fleet</a>
				| <a href="<?php echo $base_url; ?>ranks/">Ranks</a>
				| <a href="<?php echo $base_url; ?>tours/">Tours</a>
				| <a href="<?php echo $base_url; ?>events/">Events</a>
				| <a href="https://www.fly-euroharmony.com/forum/">Community</a>
				*/ ?>
            </div>


            <br/>
            <div class="datestyle"><?php echo date('l jS \of F Y'); ?> <span id="jstime"></span></div>


        </div>


        <div class="clear"><!-- --></div>
    </div>

	<?php

	$minutes_now = gmdate('i', time('now'));

	// Slogan bar
	if ($minutes_now < 20) {
		$slogan = 'Flight Simulator 2004, Flight Simulator X and X-Plane';
	} elseif ($minutes_now < 40) {
		$slogan = '"Your World e-connection"';
	} else {
		$slogan = '"The Wings of Europe"';
	}

	echo '<div id="slogan">';
	echo $slogan;
	echo '</div>';

	?>


    <div id="main">

        <div id="headspacer">&nbsp;</div>


        <div class="clear"><!-- --></div>

		<?php

		//warning bar if email not confirmed
		if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('email_confirmed') == 0) {
			//output email not confirmed warning
			echo '<br />';
			echo '<div class="warning">
		<font size="3">Warning: Your email address is currently unconfirmed.</font> <br />This means that you will not be able to log flight time. 
		Please click the link in the email that was sent to you, or re-enter the address in your profile to have the confirmation email resent.
		</div>';
			echo '<br />';
		}

		//warning bar if admin and flights not approved
		if ($this->session->userdata('logged_in') == 1 && $is_admin == 1) {

			$warning = '';

			//determine if the writable directories are writable
			//aircraft folder
			if (!is_writable($this->config->item('base_path') . 'assets/uploads/')
				|| !is_writable($this->config->item('base_path') . 'assets/uploads/tmp/')
			) {
				$warning .= '<font size="3">Cannot write to ' . $this->config->item('base_path') . 'assets/uploads/ or sub directories</font>';
			} elseif (!is_writable($this->config->item('base_path') . 'assets/uploads/aircraft/')) {
				$warning .= '<font size="3">Cannot write to ' . $this->config->item('base_path') . 'assets/uploads/aircraft or sub directories</font>';
			} elseif (!is_writable($this->config->item('base_path') . 'assets/uploads/awards/')) {
				$warning .= '<font size="3">Cannot write to ' . $this->config->item('base_path') . 'assets/uploads/awards or sub directories</font>';
			} elseif (!is_writable($this->config->item('base_path') . 'assets/uploads/divisions/')) {
				$warning .= '<font size="3">Cannot write to ' . $this->config->item('base_path') . 'assets/uploads/divisions or sub directories</font>';
			} elseif (!is_writable($this->config->item('base_path') . 'assets/uploads/news/')) {
				$warning .= '<font size="3">Cannot write to ' . $this->config->item('base_path') . 'assets/uploads/news or sub directories</font>';
			} elseif (!is_writable($this->config->item('base_path') . 'assets/uploads/tours/')) {
				$warning .= '<font size="3">Cannot write to ' . $this->config->item('base_path') . 'assets/uploads/tours or sub directories</font>';
			}

			//sql query to determine if there are unchecked pireps.
			$query = $this->db->query("	SELECT 	pirep.id AS id,
											pirep.last_updated AS submitdate
													
											FROM pirep
											
											WHERE pirep.checked = '0' 
											OR pirep.checked = '4'
											
											ORDER BY pirep.submitdate ASC
											
											LIMIT 1
										");

			$result = $query->result_array();
			$num_rows = $query->num_rows();

			//if we found any
			if ($num_rows > 0) {

				//calculate the age
				$pirep_time = strtotime($result['0']['submitdate']);
				$now_time = strtotime($data['gmt_mysql_datetime']);

				$difference = $now_time - $pirep_time;

				$time_duration = $this->format_fns->time_duration($difference, 'yMWd');

				if ($warning != '') {

					$warning .= '<br /><br />';

				}

				//output email not confirmed warning
				$warning .= '<font size="3">Warning: There are unchecked PIREPs</font> <br />The oldest was submitted or updated ' . $time_duration . ' ago.';

			}

			if ($warning != '') {

				echo '<br />';
				echo '<div class="warning">';
				echo $warning;
				echo '</div>';
				echo '<br />';

			}

		}

		?>

	

