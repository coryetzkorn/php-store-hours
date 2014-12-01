PHP Store Hours
===============

PHP Store Hours is a simple PHP class that outputs content based on time-of-day and-day-of-week. Simply include the script in any PHP page, adjust opening and closing hours for each day of the week and the script will output content based on the time ranges you specify.

###Easily set open hours for each day of the week
```php
$hours = array(
  'mon' => array('11:00-20:30'),
  'tue' => array('11:00-16:00', '18:00-20:30'),
  'wed' => array('11:00-20:30'),
  'thu' => array('11:00-20:30'),
  'fri' => array('11:00-20:30'),
  'sat' => array('11:00-20:00'),
  'sun' => '',
);
```

###Add exceptions for specific dates / holidays
```php
$exceptions = array(
  '12/1' => array('11:00-16:00', '18:00-20:30'),
  '6/4' => array('11:00-14:00', '18:00-20:30')
);
```

###Customize the final output with shortcodes
Choose what you'd like to output if you're currently open, currently closed, closed for the day, or closed for an exception (such as a holiday). Smart shortcodes allow your message to include dynamic infomation such as the current day's hours or the specific reason you're closed. You can even get creative and output a custom image as shown in the example above.

```php
$template = array(
  'open' => "<h3>Yes, we're open! Today's hours are {%hours%}.</h3>",
  'closed' => "<h3>Sorry, we're closed. Today's hours are {%hours%}.</h3>",
  'closed_all_day' => "<h3>Sorry, we're closed today.</h3>",
  'separator' => " - ",
  'join' => " and ",
  'format' => "g:ia", // options listed here: http://php.net/manual/en/function.date.php
  'hours' => "{%open%}{%separator%}{%closed%}"
);
```

###Available Methods
####$store_hours->render();
This is the default method that outputs the templated content. You'll most likely want to use this.
```php
$store_hours->render();
```

####$store_hours->hours_today();
This returns an array of the current day's hours.
```php
$store_hours->hours_today();
```
####$store_hours->is_open();
This returns true/false depending on if the store is currently open.
```php
$store_hours->hours_today();
```

There's no need to copy/paste the code above... it's all included in the download. Please report any bugs or issues here on GitHub. I'd love to hear your ideas for improving this script or see how you've used it in your latest project.


##Sites using PHP Store Hours
* [Des Plaines Public Library](http://dppl.org/)
* [The Nevada Discovery Museum](http://www.nvdm.org/)
* [Minne's Diner](http://www.minnesdiner.com/)
* Want to showcase your site? Tweet [@coryetzkorn](http://twitter.com/coryetzkorn)