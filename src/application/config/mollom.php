<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This is the configuration for using Mollom with Codeigniter.
 *
 * Uses (and requires!) the Mollom PHP class from http://mollom.crsolutions.be.
 * The library needs to be named mollom.lib.php & kept in the libraries folder.
 *
 * Most of these entries can be left as they are or left blank. Most map directly to the Mollom PHP class variables.
 *
 * Private & public keys are required, visit http://mollom.com to get yours!
 */

/* the Mollom private key */
$config['mollom_privateKey'] = '8814c5177966610065beac161458e00e';

/* the Mollom public key */
$config['mollom_publicKey'] = '93c29ef997ba15706b2b488c03b8efe1';

/* are reverse proxies allowed? */
$config['mollom_reverseProxy'] = FALSE;

/* an array of allowed reverse proxies if the above is set to true */
$config['mollom_reverseProxyAddresses'] = array();

/* the timeout of connection */
$config['mollom_timeout'] = NULL; // set to a non-zero value to enable

/* how long in minutes to cache the Mollom servers for, leave as 0 to not cache at all */
$config['mollom_cacheServers'] = 30;


/* End of file mollom.php */
/* Location: ./application/config/mollom.php */
