<!doctype html>
<html lang="en-gb">
<head>
    <meta charset="utf-8"/>
    <meta name="keywords"
          content="virtual airways, virtual airline, virtual airlines, virtual, airline, va, euroharmony, euroharmony va, euroharmony virtual airline, microsoft flight simulator, xplane, x-plane, fs9, fs2004, fsx"/>
    <meta name="description"
          content="Euroharmony Virtual Airline is a mixed community of both professional and casual flight simulation enthusiasts, offering professional virtual airline operations for those that wish to fly in this manner, without imposing restrictions and rules on pilots who prefer to enjoy virtual flying in a more relaxed environment."/>
    <meta name="revisit-after" content="7 Days"/>
    <meta name="Robots" content="index,follow"/>
    <meta name="Googlebot" content="index,follow"/>
    <link rel="icon" type="image/png" href="<?php echo $assets_url; ?>images/favicon.ico"/>
	<?php
	if (!isset($swf)) {
		$swf = '';
	}
	?>
    <title>Euroharmony Virtual Airline - The Wings of Europe</title>


    <script type="text/javascript"
            src="<?php echo $assets_url . 'javascript/functions/jquery-1.10.2.min.js'; ?>"></script>
    <script type="text/javascript" src="<?php echo $assets_url . 'javascript/functions/java_fns.js'; ?>"></script>
    <script type="text/javascript" src="<?php echo $assets_url . 'javascript/functions/sarissa.js'; ?>"></script>
    <script type="text/javascript" src="<?php echo $assets_url . 'javascript/functions/prototype.js'; ?>"></script>
    <script type="text/javascript"
            src="<?php echo $assets_url . 'javascript/functions/scriptaculous.js?load=effects,builder.js'; ?>"></script>
    <script type="text/javascript"
            src="<?php echo $assets_url . 'javascript/functions/lightbox-2.6.min.js'; ?>"></script>
    <script type="text/javascript" src="<?php echo $assets_url . 'javascript/functions/'; ?>swfobject.js"></script>
    <!--
	<script type="text/javascript">
	function wopen(url, name, w, h)
	{
	// Fudge factors for window decoration space.
	w += 32;
	h += 96;
	 var win = window.open(url,
	  name,
	  'width=' + w + ', height=' + h + ', ' +
	  'location=no, menubar=no, ' +
	  'status=no, toolbar=no, scrollbars=no, resizable=no');
	 win.resizeTo(w, h);
	 win.focus();
	}
	</script>
	-->

    <script type="text/javascript">
        assetsUrl = '<?php echo $assets_url; ?>';
        templateUrl = '<?php echo $tmpl_image_url; ?>';
    </script>

	<?php
	if (isset($page_js)) {
		echo $page_js;
	}
	?>

	<?php
	if (isset($javascript_file_array) && is_array($javascript_file_array)) {
		foreach ($javascript_file_array as $row) {
			echo '<script src="' . $assets_url . 'javascript/functions/' . $row . '.js" language="javascript" type="text/javascript"></script>
';
		}
	}

	?>

    <style type="text/css">
<?php $this->load->view('templates/'.$template.'/'.$template.'.css'); ?>
    </style>

    <style type="text/css">
<?php $this->load->view('templates/'.$template.'/lightbox.css'); ?>
    </style>
</head>
<body>
