<!DOCTYPE html>
<html lang="en" xml:lang="en"><head>
<meta charset="utf-8">

<head>
<title>PHP Store Hours</title>
<style type="text/css">
body {
	font-family: 'Helvetica Neue', arial;
	text-align: center;
}
</style>
</head>

	<body>
	
	<h1>Gadgets Inc.</h1>
	<h2>Store Hours</h2>
	
	<?php

	// REQUIRED
	// Set your default time zone (listed here: http://php.net/manual/en/timezones.php)
	date_default_timezone_set('America/New_York'); 
	// Include the store hours class
	require('StoreHours.class.php');

	// REQUIRED
	// Define daily open hours
  // Must be in 24-hour format, separated by dash 
  // If closed for the day, leave blank (ex. sunday)
  // If open multiple times in one day, enter time ranges separated by a comma
  $hours = array(
	  'mon' => array('11:00-20:30'),
	  'tue' => array('11:00-16:00', '18:00-20:30'),
	  'wed' => array('11:00-20:30'), 
	  'thu' => array('11:00-1:30'), // Open late
	  'fri' => array('11:00-20:30'),
	  'sat' => array('11:00-20:00'),
	  'sun' => '', // Closed all day
	);

  // OPTIONAL
  // Add exceptions (great for holidays etc.)
  // Works best with format day/month
  $exceptions = array(
    '12/1' => array('11:00-16:00', '18:00-20:30'),
    '6/4' => array('11:00-14:00', '18:00-20:30')
  );

  // OPTIONAL
  // Place HTML for output below. This is what will show in the browser.
  // Use {%hours%} shortcode to add dynamic times to your open or closed message.
  $template = array(
    'open' => "<h3>Yes, we're open! Today's hours are {%hours%}.</h3>",
    'closed' => "<h3>Sorry, we're closed. Today's hours are {%hours%}.</h3>",
    'closed_all_day' => "<h3>Sorry, we're closed today.</h3>",
    'separator' => " - ",
    'join' => " and ",
    'format' => "g:ia", // options listed here: http://php.net/manual/en/function.date.php
    'hours' => "{%open%}{%separator%}{%closed%}"
  );

  // Instantiate class and call render method to output content
	$store_hours = new StoreHours($hours, $exceptions, $template);
	$store_hours->render();

	?>
	
	</body>
	
</html>