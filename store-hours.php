<?php 

// -------- PHP STORE HOURS ---------
// ---------- Version 2.0 -----------
// -------- BY CORY ETZKORN ---------
// -------- coryetzkorn.com ---------


// -------- EDIT FOLLOWING SECTION ONLY ---------

// Set your timezone (codes listed at http://php.net/manual/en/timezones.php)
// Delete the following line if you've already defined a timezone elsewhere.
date_default_timezone_set('America/New_York'); 

// Define daily open hours
// Must be in 24-hour format, separated by dash 
// If closed for the day, set to 00:00-00:00
// If open multiple times in one day, enter time ranges separated by a comma
// If open late (ie. 6pm - 1am), add hours after midnight to the next day (ie. 00:00-1:00)
$hours = array(
    'mon' => array('11:00-20:30'),
    'tue' => array('11:00-16:00', '18:00-20:30'),
    'wed' => array('11:00-20:30'),
    'thu' => array('11:00-20:30'),
    'fri' => array('11:00-20:30'),
    'sat' => array('11:00-20:00'),
    'sun' => array('11:00-20:30')
);

// Optional: add exceptions (great for holidays etc.)
// Works best with format day/month
// Leave array empty if no exceptions
$exceptions = array(
	//'Christmas' => '10/22',
	//'New Years Day' => '1/1'
);

// Place HTML for output below. This is what will show in the browser.
// Optional: use %open% and %closed% to add dynamic times to your open message.
// Warning: %open% and %closed% will NOT work if you have multiple time ranges assigned to a single day.
// Optional: use %day% to make your "closed all day" message more dynamic.
// Optional: use %exception% to make your exception messages dynamic.
$open_now = "<h3>Yes, we're open! Today's hours are %open% until %closed%.</h3>";
$closed_now = "<h3>Sorry, we're closed. Today's hours are %open% until %closed%.</h3>";
$closed_all_day = "<h3>Sorry, we're closed on %day%.</h3>";
$exception = "<h3>Sorry, we're closed for %exception%.</h3>";

// Enter custom time format if using %open% and %closed%
// (options listed here: http://php.net/manual/en/function.date.php)
$time_format = 'g:ia';

// The %day% shortcode is replaced by these days of the week.
// Edit these if you'd like to use a language other than English.
$days = array(
  'mon' => 'Mondays',
  'tue' => 'Tuesdays',
  'wed' => 'Wednesdays',
  'thu' => 'Thursdays',
  'fri' => 'Fridays',
  'sat' => 'Saturdays',
  'sun' => 'Sundays'
);


// -------- END EDITING -------- 

$day = strtolower(date("D"));
$today = strtotime('today midnight');
$now = strtotime(date("G:i"));
$is_open = 0;
$is_exception = false;
$is_closed_all_day = false;

// Check if closed all day
if($hours[$day][0] == '00:00-00:00') {
	$is_closed_all_day = true;
}

// Check if currently open
foreach($hours[$day] as $range) {
	$range = explode("-", $range);
	$start = strtotime($range[0]);
	$end = strtotime($range[1]);
	if (($start <= $now) && ($end >= $now)) {
		$is_open ++;
	}
}

// Check if today is an exception
foreach($exceptions as $ex => $ex_day) {
	$ex_day = strtotime($ex_day);
	if($ex_day === $today) {
		$is_open = 0;
		$is_exception = true;
		$the_exception = $ex;
	}
}

// Output HTML
if($is_exception) {
	$exception = str_replace('%exception%', $the_exception, $exception);
	echo $exception;
} elseif($is_closed_all_day) {
	$closed_all_day = str_replace('%day%', $days[$day], $closed_all_day);
	echo $closed_all_day;
} elseif($is_open > 0) {
	$open_now = str_replace('%open%', date($time_format, $start), $open_now);
	$open_now = str_replace('%closed%', date($time_format, $end), $open_now);
	echo $open_now;
} else {
	$closed_now = str_replace('%open%', date($time_format, $start), $closed_now);
	$closed_now = str_replace('%closed%', date($time_format, $end), $closed_now);
	echo $closed_now;
}

?>
