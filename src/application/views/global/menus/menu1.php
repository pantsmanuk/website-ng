<ul class="menu5">
    <li class="home"><a href="<?php echo $base_url; ?>">Home</a></li>
    <li class="products"><a class="drop" href="#nogo">Airline<!--[if IE 7]><!--></a><!--<![endif]-->
        <!--[if lte IE 6]>
        <table>
            <tr>
                <td><![endif]-->
        <ul>

            <li><a href="<?php echo $base_url; ?>information/index/history">Airline History</a></li>
            <li><a href="<?php echo $base_url; ?>information/pilots/">Pilots</a></li>
            <li><a href="<?php echo $base_url; ?>information/management/">Management</a></li>
            <li><a href="<?php echo $base_url; ?>information/discounts/">Discounts</a></li>
            <li><a href="<?php echo $base_url; ?>information/latest_flights/">Latest flights</a></li>
            <li><a href="<?php echo $base_url; ?>information/online/">Online Networks</a></li>
            <li><a href="<?php echo $base_url; ?>information/index/faq/">FAQ</a></li>
            <!-- <li><a href="https://www.fly-euroharmony.com/forum/index.php?action=treasury">Support Us</a></li> -->
        </ul>
        <!--[if lte IE 6]></td></tr></table></a><![endif]-->
    </li>
    <li class="services"><a class="drop" href="#nogo">Careers<!--[if IE 7]><!--></a><!--<![endif]-->
        <!--[if lte IE 6]>
        <table>
            <tr>
                <td><![endif]-->
        <ul>

            <li><a href="<?php echo $base_url; ?>information/index/new">New to VAs?</a></li>
            <li><a href="<?php echo $base_url; ?>information/index/whyehm/">Why Euroharmony?</a></li>
			<?php
			if ($this->session->userdata('logged_in') != 1) {
				?>
                <li><a href="<?php echo $base_url; ?>join/">Join us</a></li>
				<?php
			}
			?>
            <li><a href="<?php echo $base_url; ?>ranks/">Ranks</a></li>
            <li><a href="<?php echo $base_url; ?>awards/">Awards</a></li>

        </ul>
        <!--[if lte IE 6]></td></tr></table></a><![endif]-->
    </li>

    <li class="operations"><a class="drop" href="#nogo">Operations<!--[if IE 7]><!--></a><!--<![endif]-->
        <!--[if lte IE 6]>
        <table>
            <tr>
                <td><![endif]-->
        <ul>

            <li class="fleet"><a href="<?php echo $base_url; ?>fleet/">Fleet</a></li>
            <li class="hubs"><a href="<?php echo $base_url; ?>hubs/">Hubs<!--[if IE 7]><!--></a><!--<![endif]-->
                <!--[if lte IE 6]>
                <table>
                    <tr>
                        <td><![endif]-->
                <ul>

                    <li><a href="<?php echo $base_url; ?>hubs/index/EGLL">EGLL Heathrow</a></li>
                    <li><a href="<?php echo $base_url; ?>hubs/index/EHAM">EHAM Amsterdam</a></li>
                    <li><a href="<?php echo $base_url; ?>hubs/index/ESSA">ESSA Arlanda</a></li>
                    <li><a href="<?php echo $base_url; ?>hubs/index/LGAV">LGAV Athens</a></li>
                    <li><a href="<?php echo $base_url; ?>hubs/index/LPPT">LPPT Lisbon</a></li>
                    <li><a href="<?php echo $base_url; ?>hubs/index/LSZH">LSZH Zurich</a></li>
                    <li><a href="<?php echo $base_url; ?>hubs/index/KATL">KATL Atlanta</a></li>
                    <li><a href="<?php echo $base_url; ?>hubs/index/WSSS">WSSS Singapore</a></li>
                    <li><a href="<?php echo $base_url; ?>hubs/index/CYCG">CYCG Castlegar</a></li>

                </ul>
                <!--[if lte IE 6]></td></tr></table></a><![endif]-->
            </li>


            <li class="divisions"><a href="<?php echo $base_url; ?>divisions/">Divisions<!--[if IE 7]><!--></a>
                <!--<![endif]-->
                <!--[if lte IE 6]>
                <table>
                    <tr>
                        <td><![endif]-->
                <ul>

                    <li><a href="<?php echo $base_url; ?>divisions/index/">Main Division</a></li>
                    <li><a href="<?php echo $base_url; ?>divisions/index/2">Eurobusiness</a></li>
                    <li><a href="<?php echo $base_url; ?>divisions/index/3">Eurocargo</a></li>
                    <li><a href="<?php echo $base_url; ?>divisions/index/4">Euroholidays</a></li>
                    <li><a href="<?php echo $base_url; ?>divisions/index/5">Aeroclub</a></li>
                    <li><a href="<?php echo $base_url; ?>divisions/index/8">Wild</a></li>
                    <li><a href="<?php echo $base_url; ?>divisions/index/9">Hopper</a></li>

                </ul>
                <!--[if lte IE 6]></td></tr></table></a><![endif]-->
            </li>

            <li class="missions"><a href="<?php echo $base_url; ?>missions/">Missions<!--[if IE 7]><!--></a>
                <!--<![endif]-->
                <!--[if lte IE 6]>
                <table>
                    <tr>
                        <td><![endif]-->
                <ul>

                    <li><a href="<?php echo $base_url; ?>missions/index/2">Eurobusiness</a></li>
                    <li><a href="<?php echo $base_url; ?>missions/index/3">Eurocargo</a></li>
                    <li><a href="<?php echo $base_url; ?>missions/index/4">Euroholidays</a></li>
                    <li><a href="<?php echo $base_url; ?>missions/index/8">Wild</a></li>
                    <li><a href="<?php echo $base_url; ?>missions/index/9">Hopper</a></li>

                </ul>
                <!--[if lte IE 6]></td></tr></table></a><![endif]-->
            </li>


            <li><a href="<?php echo $base_url; ?>tours/">Tours</a></li>
            <li><a href="<?php echo $base_url; ?>information/propilot/">Propilot</a></li>
            <li><a href="<?php echo $base_url; ?>events/">Events</a></li>


        </ul>
        <!--[if lte IE 6]></td></tr></table></a><![endif]-->
    </li>


    <li class="community"><a href="https://www.fly-euroharmony.com/forum/">Community</a></li>
    <li class="contact"><a href="<?php echo $base_url; ?>contact/">Contact Us</a></li>
</ul>
