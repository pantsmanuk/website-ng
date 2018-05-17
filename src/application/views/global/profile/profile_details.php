<?php
$this->load->view('global/profile/profile_topbar_edit');
?>


<?php
$hidden = array('valid' => 'true');

//Start form output
echo form_open('profile/details', '', $hidden);

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
            <th colspan="2">Edit Profile Details</th>

        </tr>


        <tr>
            <td width="250">
                <div align="right">Country:</div>
            </td>
            <td width="150">
                <div align="left"><?php echo form_dropdown('country', $country_array, $country); ?></div>
            </td>
        </tr>

        <tr>
            <td width="250">
                <div align="right">Main Flight Simulator:</div>
            </td>
            <td width="150">
                <div align="left"><?php echo form_dropdown('flight_sim', $flightsim_array, $flightsim); ?></div>
            </td>
        </tr>

        <tr>
            <td width="250">
                <div align="right">IVAO ID:</div>
            </td>
            <td width="150">
                <div align="left"><?php echo form_input($ivaoid); ?></div>
            </td>
        </tr>

        <tr>
            <td width="250">
                <div align="right">VATSIM ID:</div>
            </td>
            <td width="150">
                <div align="left"><?php echo form_input($vatsimid); ?></div>
            </td>
        </tr>

        <tr>
            <td width="250">
                <div align="right">Receive Email Notifications:</div>
            </td>
            <td width="150">
                <div align="left"><?php echo form_checkbox($bulk_email); ?></div>
            </td>
        </tr>
        <tr>
            <td width="250" colspan="2">
                <div>Email notifications include notification of tours and propilot events as well as major news such as
                    the launch of a new hub or division.
                </div>
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
