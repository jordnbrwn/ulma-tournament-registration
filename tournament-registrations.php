<?php 
  
ini_set('display_errors', 'On');
error_reporting(E_ALL);
  
define( 'WP_USE_THEMES', false ); // Don't load theme support functionality
require( './wp-load.php' );
require( './Registrant.php');


$regs = new Registrant(38);
$registrations = $regs->get_registrations();