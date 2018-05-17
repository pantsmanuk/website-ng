<?php
$this->load->view('global/profile/profile_topbar_edit');
?>


<?php
$hidden = array('valid' => 'true');

//Start form output
echo form_open('profile/credentials', '', $hidden);

if (isset($exception)) {
	echo '<br /><center><div class="exception">' . $exception . '</div></center>';
} else {
	echo "<br />";
}
?>
<br/>

<div align="center">

    <table class="statbox" width="400">
        <tr>
            <th colspan="2">Password Change</th>

        </tr>
        <tr>

            <td width="250">
                <div align="right">Current Password:</div>
            </td>
            <td width="150">
                <div align="left"><?php echo form_password($oldpassword); ?></div>
            </td>

        </tr>
        <tr>
			<?php
			$js = 'onKeyUp="checkPassword(this.value)"';
			?>
            <td width="250">
                <div align="right">New Password:</div>
            </td>
            <td width="150">
                <div align="left"><?php echo form_password($newpassword1, '', $js); ?></div>
            </td>

        </tr>
        <tr>

            <td width="250">
                <div align="right">New Password (again):</div>
            </td>
            <td width="150">
                <div align="left"><?php echo form_password($newpassword2, '', $js); ?></div>
            </td>

        </tr>
        <tr>
            <td colspan="2" align="center">
                <div id="credmatch" style="background: green; font-weight: bold; color: #FFF; line-height: 20px;">New
                    Passwords Match
                </div>
            </td>
        </tr>

        <tr>
            <td width="250">
                <div align="right">Password Strength:</div>
            </td>
            <td width="150">
                <div style="border: 1px solid gray; width: 148;">

                    <div id="progressBar"
                         style="font-size: 1px; height: 20px;
    width: 0px; border: 1px solid white;">

                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <span>To make your password more secure, use a mixture of uppercase, lowercase, numbers and special characters. Ideally your password should be 8 or more characters long.</span>
            </td>
        </tr>

    </table>
    <br/>
    <br/>
    <table border="0" cellpadding="0" align="center" width="770">
        <tr>
            <td align="center">
                <input type="submit" class="form_button" value="Submit"/>
            </td>

        </tr>
    </table>
</div>
