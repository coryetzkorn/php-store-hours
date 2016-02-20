PHP Store Hours
===============

PHP Store Hours is a simple PHP class that outputs content based on time-of-day and-day-of-week. Simply include the script in any PHP page, adjust opening and closing hours for each day of the week and the script will output content based on the time ranges you specify.

### Easily set open hours for each day of the week

~~~ php
// REQUIRED
// Define daily open hours
// Must be in 24-hour format, separated by dash
// If closed for the day, leave blank (ex. sunday) or don't add line
// If open multiple times in one day, enter time ranges separated by a comma
$hours = array(
    'mon' => array('11:00-20:30'),
    'tue' => array('11:00-13:00', '18:00-20:30'),
    'wed' => array('11:00-20:30'),
    'thu' => array('11:00-1:30'), // Open late
    'fri' => array('11:00-20:30'),
    'sat' => array('11:00-20:00'),
    'sun' => array() // Closed all day
);
~~~

### Add exceptions for specific dates / holidays

~~~ php
// OPTIONAL
// Add exceptions (great for holidays etc.)
// MUST be in format month/day[/year] or year-month-day
// Do not include the year if the exception repeats annually
$exceptions = array(
    '2/24'  => array('11:00-18:00'),
    '10/18' => array('11:00-16:00', '18:00-20:30')
);
~~~

### Customize the final output with shortcodes

Choose what you'd like to output if you're currently open, currently closed, or closed all day. Shortcodes add dynamic times to your open or closed message.

~~~ php
// OPTIONAL
// Place HTML for output below. This is what will show in the browser.
// Use {%hours%} shortcode to add dynamic times to your open or closed message.
$template = array(
    'open'           => "<h3>Yes, we're open! Today's hours are {%hours%}.</h3>",
    'closed'         => "<h3>Sorry, we're closed. Today's hours are {%hours%}.</h3>",
    'closed_all_day' => "<h3>Sorry, we're closed today.</h3>",
    'separator'      => " - ",
    'join'           => " and ",
    'format'         => "g:ia", // options listed here: http://php.net/manual/en/function.date.php
    'hours'          => "{%open%}{%separator%}{%closed%}"
);
~~~

### Available Methods

#### render([timestamp = time()])

This is the default method that outputs the templated content. You'll most likely want to use this.

~~~ php
$store_hours = new StoreHours($hours, $exceptions, $template);
$store_hours->render();
~~~

#### hours_overview([groupSameDays = false])

This returns an array with a full list of open hours (for a week without exceptions). Days with same hours will be grouped.

~~~ php
$store_hours = new StoreHours($hours, $exceptions, $template);

echo '<table>';
foreach ($store_hours->hours_overview() as $days => $hours) {
    echo '<tr>';
    echo '<td>' . $days . '</td>';
    echo '<td>' . $hours . '</td>';
    echo '</tr>';
}
echo '</table>';
~~~

#### hours_today([timestamp = time()])

This returns an array of the current day's hours.

~~~ php
$store_hours = new StoreHours($hours, $exceptions, $template);
$store_hours->hours_today();
~~~

#### is_open([timestamp = time()])

This returns true/false depending on if the store is currently open.

~~~ php
$store_hours = new StoreHours($hours, $exceptions, $template);
$store_hours->is_open();
~~~

### Use Cases

#### Multiple stores / sets of hours

If you'd like to show multiple sets of hours on the same page, simply invoke two separate instances of `StoreHours`. Remember to set the timezone before each new instance.

~~~ php
// New York Hours
date_default_timezone_set('America/New_York');
$nyc_store_hours = new StoreHours($nyc_hours, $nyc_exceptions, $nyc_template);
$nyc_store_hours->render();

// Los Angeles Hours
date_default_timezone_set('America/Los_Angeles');
$la_store_hours = new StoreHours($la_hours, $la_exceptions, $la_template);
$la_store_hours->render();
~~~

### Testing

~~~ bash
$ phpunit
~~~

### Troubleshooting

If you're getting errors or if times are not rendering as expected, please double check these items before filing an issue on GitHub:

- Make sure your timezone is configured
- Ensure all exceptions use the month/day format
- Verify that StoreHours.class.php is properly included on the page

Please report any bugs or issues here on GitHub. I'd love to hear your ideas for improving this script or see how you've used it in your latest project.

## Sites using PHP Store Hours

- [Des Plaines Public Library](http://dppl.org/)
- [The Nevada Discovery Museum](http://www.nvdm.org/)
- [Minne's Diner](http://www.minnesdiner.com/)
- Want to showcase your site? Tweet [@coryetzkorn](http://twitter.com/coryetzkorn)
