<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta http-equiv="Content-Language" content="en-gb" />
<meta name="keywords" content="virtual airways, virtual airline, virtual airlines, virtual, airline, va, euroharmony, euroharmony va, euroharmony virtual airline, microsoft flight simulator, xplane, x-plane, fs9, fs2004, fsx" />
<meta name="description" content="Euroharmony Virtual Airline is a mixed community of both professional and casual flight simulation enthusiasts, offering professional virtual airline operations for those that wish to fly in this manner, without imposing restrictions and rules on pilots who prefer to enjoy virtual flying in a more relaxed environment." />
<meta name="copyright" content="Euroharmony Development Team" /> 
<meta name="revisit-after" content="7 Days" />
<meta name="Robots" content="index,follow">
<meta name="Googlebot" content="index,follow">
 
<title>Euroharmony Virtual Airline - Wings of Europe</title>
<style type="text/css">
<!--
td {
	border: 1px dashed #000000;
}
-->
</style>
</head>

<body onload="window.print();">
<table width="850" border="1" bordercolor="3962c1" style="border-collapse: collapse; border: 1px dashed #000000;" cellspacing="0">
  <tr> 
    <td height="23" colspan="4"><font size="5" face="Courier New, Courier, mono"><strong>EuroHarmony 
      Flight Report</strong></font></td>
  </tr>

  <tr> 
    <td width="212"><strong><font size="2" face="Courier New, Courier, mono">Pilot 
      ID</font></strong></td>
    <td width="213"><strong><font size="2" face="Courier New, Courier, mono">Pilot 
      in command</font></strong></td>
    <td width="213"><strong><font size="2" face="Courier New, Courier, mono">Date 
      of flight</font></strong></td>
    <td width="212"><strong><font size="2" face="Courier New, Courier, mono">Type 
      of aircraft</font></strong></td>
  </tr>
  <tr> 
    <td width="212"><font size="2" face="Courier New, Courier, mono"><?php echo $pilot_id; ?></font></td>

    <td width="213"><font size="2" face="Courier New, Courier, mono"><?php echo $pilot_in_command; ?></font></td>
    <td width="213"><font size="2" face="Courier New, Courier, mono"><?php echo $flight_date; ?></font></td>
	    <td width="212"><font size="2" face="Courier New, Courier, mono"><?php echo $aircraft; ?></font></td>
	  </tr>
  <tr> 
    <td width="212" height="23">&nbsp;</td>
    <td width="213">&nbsp;</td>
    <td width="213">&nbsp;</td>

    <td width="212">&nbsp;</td>
  </tr>
  <tr> 
    <td colspan="2" height="23"><strong><font size="2" face="Courier New, Courier, mono">Departure 
      Airport</font></strong></td>
    <td  colspan="2"><strong><font size="2" face="Courier New, Courier, mono">Arrival 
      Airport</font></strong></td>
  </tr>

  <tr> 
    <td colspan="2" height="23"><font size="2" face="Courier New, Courier, mono"><?php echo $origin; ?></font></td>
		
	  	  	    <td colspan="2"><font size="2" face="Courier New, Courier, mono"><?php echo $destination; ?></font></td>
  </tr>
  <tr> 
    <td width="212" height="23">&nbsp;</td>

    <td width="213">&nbsp;</td>
    <td width="213">&nbsp;</td>
    <td width="212">&nbsp;</td>
  </tr>
  <tr> 
  
  	<td width="213"><strong><font size="2" face="Courier New, Courier, mono">Great Circle Distance</font></strong></td>
      <td width="212"><strong><font size="2" face="Courier New, Courier, mono">Online/Offline</font></strong></td>
    <td width="213"><strong><font size="2" face="Courier New, Courier, mono">Passengers</font></strong></td>
      <td width="213"><strong><font size="2" face="Courier New, Courier, mono">Cargo</font></strong></td>
      
  </tr>
  <tr> 
  	<td width="213"><font size="2" face="Courier New, Courier, mono"><?php echo $gcd_nm; ?></font></td>
    <td width="212"><font size="2" face="Courier New, Courier, mono"><?php echo $onoffline; ?></font></td>
    <td width="213"><font size="2" face="Courier New, Courier, mono"><?php echo $passengers; ?></font></td>
    <td width="213"><font size="2" face="Courier New, Courier, mono"><?php echo $cargo; ?></font></td>
    
  </tr>
  <tr> 
    <td width="212" height="23">&nbsp;</td>
    <td width="213">&nbsp;</td>
    <td width="213">&nbsp;</td>

    <td width="212">&nbsp;</td>
  </tr>
  <tr> 
    <td width="212" height="23">&nbsp;</td>
    <td width="213">&nbsp;</td>
    <td width="213">&nbsp;</td>
    <td width="212">&nbsp;</td>
  </tr>
  <tr> 
    <td width="212" height="23"><strong><font size="2" face="Courier New, Courier, mono">Engine 
      Start Time</font></strong></td>

    <td width="213"><strong><font size="2" face="Courier New, Courier, mono">Takeoff 
      Time</font></strong></td>
    <td width="213"><strong><font size="2" face="Courier New, Courier, mono">Landing 
      Time</font></strong></td>
    <td width="212"><strong><font size="2" face="Courier New, Courier, mono">Engine 
      Shutdown Time</font></strong></td>
  </tr>
  <tr> 
    <td width="212" height="23"><font size="2" face="Courier New, Courier, mono"><?php echo $engine_start_time; ?></font></td>
    <td width="213"><font size="2" face="Courier New, Courier, mono"><?php echo $departure_time; ?></font></td>

    <td width="213"><font size="2" face="Courier New, Courier, mono"><?php echo $landing_time; ?></font></td>
    <td width="212"><font size="2" face="Courier New, Courier, mono"><?php echo $engine_stop_time; ?></font></td>
  </tr>
  <tr> 
    <td width="212" height="23">&nbsp;</td>
    <td width="213">&nbsp;</td>
    <td width="213">&nbsp;</td>
    <td width="212">&nbsp;</td>

  </tr>
  <tr> 
    <td width="212" height="23"><strong><font size="2" face="Courier New, Courier, mono">Cruise 
      Altitude</font></strong></td>
    <td width="213"><strong><font size="2" face="Courier New, Courier, mono">Cruise 
      Speed</font></strong></td>
    <td width="213"><strong><font size="2" face="Courier New, Courier, mono">Approach</font></strong></td>
    <td width="212"><strong><font size="2" face="Courier New, Courier, mono">Fuel 
      burnt</font></strong></td>
  </tr>

    <tr> 
    <td width="212" height="23"><font size="2" face="Courier New, Courier, mono"><?php echo $cruisealt; ?></font></td>
    <td width="213"><font size="2" face="Courier New, Courier, mono"><?php echo $cruisespd; ?></font></td>
    <td width="213"><font size="2" face="Courier New, Courier, mono"><?php echo $approach; ?></font></td>
    <td width="212"><font size="2" face="Courier New, Courier, mono"><?php echo $fuelburnt; ?></font></td>
  </tr>

  <tr> 
    <td width="212" height="23">&nbsp;</td>
    <td width="213">&nbsp;</td>
    <td width="213">&nbsp;</td>
    <td width="212">&nbsp;</td>
  </tr>
  <tr> 
    <td width="212" height="23"><font size="2" face="Courier New, Courier, mono"><strong>Comments</strong></font></td>
    <td width="213">&nbsp;</td>

    <td width="213">&nbsp;</td>
    <td width="212">&nbsp;</td>
  </tr>
  <tr> 
      <td height="23" colspan="4"><font size="2" face="Courier New, Courier, mono"><?php echo $comments; ?></font></td>
  </tr>
  </table>
</body>
</html>