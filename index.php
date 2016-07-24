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
        table {
            font-size: small;
            text-align: left;
            margin: 100px auto 0 auto;
            border-collapse: collapse;
        }
        td {
            padding: 2px 8px;
            border: 1px solid #ccc;
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
    require __DIR__ . '/StoreHours.class.php';

    // REQUIRED
    // Define daily open hours
    // Must be in 24-hour format, separated by dash
    // If closed for the day, leave blank (ex. sunday)
    // If open multiple times in one day, enter time ranges separated by a comma
    $hours = array(
        'mon' => array('11:00-20:30'),
        'tue' => array('11:00-13:00', '18:00-20:30'),
        'wed' => array('11:00-13:00', '18:00-20:30'),
        'thu' => array('11:00-1:30'), // Open late
        'fri' => array('11:00-20:30'),
        'sat' => array('11:00-20:00'),
        'sun' => array('11:00-20:00')
    );

    // OPTIONAL
    // Add exceptions (great for holidays etc.)
    // MUST be in a format month/day[/year] or [year-]month-day
    // Do not include the year if the exception repeats annually
    $exceptions = array(
        '2/24'  => array('11:00-18:00'),
        '10/18' => array('11:00-16:00', '18:00-20:30')
    );

    $config = array(
        'separator'      => ' - ',
        'join'           => ' and ',
        'format'         => 'g:ia',
        'overview_weekdays'  => array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun')
    );

    // Instantiate class
    $store_hours = new StoreHours($hours, $exceptions, $config);
    
    // Display open / closed message
    if($store_hours->is_open()) {
        echo "Yes, we're open! Today's hours are " . $store_hours->hours_today() . ".";
    } else {
        echo "Sorry, we're closed. Today's hours are " . $store_hours->hours_today() . ".";
    }

    // Display full list of open hours (for a week without exceptions)
    echo '<table>';
    foreach ($store_hours->hours_this_week() as $days => $hours) {
        echo '<tr>';
        echo '<td>' . $days . '</td>';
        echo '<td>' . $hours . '</td>';
        echo '</tr>';
    }
    echo '</table>';

    // Same list, but group days with identical hours
    echo '<table>';
    foreach ($store_hours->hours_this_week(true) as $days => $hours) {
        echo '<tr>';
        echo '<td>' . $days . '</td>';
        echo '<td>' . $hours . '</td>';
        echo '</tr>';
    }
    echo '</table>';

    ?>

    </body>

</html>
