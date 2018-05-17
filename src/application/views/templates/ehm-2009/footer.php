<div class="clear"></div>

<br/>

</div>
<div class="clear"><!-- --></div>

<div id="footer">


    <center>
        <div style="margin-bottom: 4px;">
            Euroharmony Virtual Airline Established 2001 <?php //echo $version; ?>
			<?php
			$current_year = date('Y');
			echo '<br />Website NG v' . $version . ' powered by CodeIgniter v'.CI_VERSION.'<br/>Copyright &copy; 2009-' . $current_year . ', Euroharmony Development Team.';

			?>
            <br/>
            <br/>
        </div>
    </center>


</div>


</div>


<?php // close off the content div
?>
</div>

<div class="clear"></div>
<script type="text/javascript">
    window.onload = function () {
        updateTime();
		<?php echo $js_loader;?>
    }
</script>