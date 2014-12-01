<?php 

// ----------------------------------
// PHP STORE HOURS
// ----------------------------------
// Version 2.1.0
// Written by Cory Etzkorn
// https://github.com/coryetzkorn/php-store-hours


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
  'sun' => array('11:00-21:30')
);

// Optional: add exceptions (great for holidays etc.)
// Works best with format day/month
// Leave array empty if no exceptions
$exceptions = array(
	'11/30' => array('11:00-16:00', '21:00-2:30')
	//'New Years Day' => '1/1'
);

// Place HTML for output below. This is what will show in the browser.
// Optional: use %open% and %closed% to add dynamic times to your open message.
// Warning: %open% and %closed% will NOT work if you have multiple time ranges assigned to a single day.
// Optional: use %day% to make your "closed all day" message more dynamic.
// Optional: use %exception% to make your exception messages dynamic.
$template = array(
  'open' => "<h3>Yes, we're open! Today's hours are {%hours%}.</h3>",
  'closed' => "<h3>Sorry, we're closed. Today's hours are {%hours%}.</h3>",
  'separator' => ' - ',
  'join' => ' and ',
  'format' => 'g:ia', // (options listed here: http://php.net/manual/en/function.date.php)
  'hours' => '{%open%}{%separator%}{%closed%}'
);

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
$now = strtotime('today 5pm');
//$now = strtotime(date("G:i"));
$is_open = 0;
$is_exception = false;
$is_closed_all_day = false;

// Check if closed all day
if($hours_today == '00:00-00:00') {
	$is_closed_all_day = true;
}


// Check for exceptions, else use regular hours
if($exceptions) {
  foreach($exceptions as $ex_day => $ex_hours) {
    if(strtotime($ex_day) == $today) {
      // Today is an exception, use alternate hours instead
      $hours_today = $ex_hours;
    } else {
      // Today is not an exception, use regular hours
      $hours_today = $hours[$day];
    }
  }
} else {
  $hours_today = $hours[$day];
}

// Check if currently open
foreach($exceptions as $ex_day => $ex_hours) {
  foreach($hours_today as $range) {
    $range = explode("-", $range);
    $start = strtotime($range[0]);
    $end = strtotime($range[1]);
    // Add one day if the end time is past midnight
    if($end <= $start) {
      $end = strtotime($range[1] . ' + 1 day');
    }
    if (($start <= $now) && ($end >= $now)) {
      $is_open ++;
    }
  }
}

function render_output($template_name) {
  global $template;
  global $hours_today;
  $output = '';
  $index = 0;
  foreach($hours_today as $range) {
    $range = explode("-", $range);
    $start = strtotime($range[0]);
    $end = strtotime($range[1]);
    if($index >= 1) {
      $hours_template .= $template['join'];
    }
    $hours_template .= $template['hours'];
    $hours_template = str_replace('{%open%}', date($template['format'], $start), $hours_template);
    $hours_template = str_replace('{%closed%}', date($template['format'], $end), $hours_template);
    $hours_template = str_replace('{%separator%}', $template['separator'], $hours_template);
    $index ++;
  }
  $output .= str_replace('{%hours%}', $hours_template, $template[$template_name]);
  echo $output;
}

// Output HTML
if($is_open > 0) {
  render_output('open');
} else {
  render_output('closed');
}

?>
