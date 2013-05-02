<?php 

// -------- PHP STORE HOURS ---------
// ---------- Version 1.4 -----------
// -------- BY CORY ETZKORN ---------
// -------- coryetzkorn.com ---------


// -------- EDIT FOLLOWING SECTION ONLY ---------

// Set your timezone (codes listed at http://php.net/manual/en/timezones.php)
// Delete the following line if you've already defined a timezone elsewhere.
date_default_timezone_set('America/Chicago'); 

// Define daily open hours
// Must be in 24-hour format, separated by dash 
// If closed for the day, set to 00:00-00:00
// Midnight is represented by 00:00
// If open multiple times in one day, enter time ranges separated by a comma
// If open late (ie. 6pm - 1am), add hours after midnight to the next day (ie. 00:00-1:00)

$time_range = array(
    'mon' => array('11:00-20:30'),
    'tue' => array('7:00-11:00', '13:00-20:30'),
    'wed' => array('11:00-20:30'),
    'thu' => array('11:00-20:30'),
    'fri' => array('11:00-20:30'),
    'sat' => array('11:00-20:30'),
    'sun' => array('11:00-20:30')
);

// Place HTML for output here. Image paths or plain text (H1, H2, p) are all acceptable.
$open_output = '<img src="images/open_sign.png" alt="Come in, we\'re open!" />';
$closed_output = '<img src="images/closed_sign.png" alt="Sorry, we\'re closed!" />';

// OPTIONAL: Output current day's open hours 
$echo_daily_hours = true; // Switch to FALSE to hide numerical display of current hours
$time_output = 'g:ia'; // Enter custom time output format (options listed here: http://php.net/manual/en/function.date.php)
$time_separator = ' - '; // Choose how to indicate range (i.e XX - XX, XX to XX, XX until XX)

// -------- END EDITING -------- 

// Gets current day of week
$status_today = strtolower(date("D"));
// Gets current time of day in 00:00 format
$current_time = date("G:i");

// Makes current time of day computer-readable
$current_time_x = strtotime($current_time);

// Builds an array, assigning user-defined time ranges to each day of week
$all_days = array("mon" => $time_range['mon'], "tue" => $time_range['tue'], "wed" => $time_range['wed'], "thu" => $time_range['thu'], "fri" => $time_range['fri'], "sat" => $time_range['sat'], "sun" => $time_range['sun']);
foreach ($all_days as &$each_day) {
	foreach ($each_day as &$each_interval) {
		// count($each_day)
		$each_interval = explode("-", $each_interval);
		$each_interval[0] = strtotime($each_interval[0]);
		$each_interval[1] = strtotime($each_interval[1]);
	}
}

// Open / closed logic
echo '<div class="open-closed-sign">';
$output_status = false;

foreach($all_days[$status_today] as $each_interval) {
	if (($each_interval[0] <= $current_time_x) && ($each_interval[1] >= $current_time_x)) {
		// If any interval matches the current time, output should be set to true. Future intervals should not override this setting.
		$output_status = true;
		break;
	}
}

if ($output_status) {
	echo $open_output;
} else {
	echo $closed_output;
}

if ($echo_daily_hours) {
	echo '<br /><span class="time_output">';
	foreach($all_days[$status_today] as $each_interval) {
		echo date($time_output, $each_interval[0]) . $time_separator . date($time_output, $each_interval[1]);
		echo '<br />';
	}
	echo '</span>';
}
echo '</div>';

?>