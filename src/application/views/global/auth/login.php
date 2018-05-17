<?php
$hidden = array('valid' => 'true');
//Start form output
echo form_open('auth/login', '', $hidden);
?>



<?php
if (isset($exception)) {
	echo "<table class=\"block\" width=\"100%\">
<tr>
<th>" . $exception . "</th></tr></table>";
} else {
	echo "<br />";
} ?>
<br/>
<br/>

<div align="center">

    <table class="boxed">
        <tr>
            <th colspan="2">Login</th>

        </tr>
        <tr>

            <td>
                <div align="right">Username: EHM-</div>
            </td>
            <td>
                <div align="left"><?php echo form_input($username); ?></div>
            </td>

        </tr>
        <tr>

            <td>
                <div align="right">Password:</div>
            </td>
            <td>
                <div align="left"><?php echo form_password($password); ?></div>
            </td>
        </tr>
    </table>
    <br/>
    <br/>
    <table border="0" cellpadding="0" align="center" width="100%">
        <tr>
            <td align="center">
                <input type="submit" class="form_button" value="Submit Login"/>
            </td>

        </tr>
    </table>
</div>
<br/>
<br/>
<?php
echo form_close();
?>
