<?php

/**
 * ----------------------------------
 * PHP STORE HOURS
 * ----------------------------------
 * Version 3.1
 * Written by Cory Etzkorn
 * https://github.com/coryetzkorn/php-store-hours
 *
 * DO NOT MODIFY THIS CLASS FILE
 */
class StoreHours
{
    /**
     *
     * @var array
     */
    private $hours;

    /**
     *
     * @var array
     */
    private $exceptions;

    /**
     *
     * @var array
     */
    private $templates;

    /**
     *
     * @var boolean
     */
    private $yesterdayFlag;

    /**
     *
     * @param array $hours
     * @param array $exceptions
     * @param array $templates
     */
    public function __construct($hours = array(), $exceptions = array(), $templates = array())
    {
        $this->exceptions    = $exceptions;
        $this->templates     = $templates;
        $this->yesterdayFlag = false;

        $weekdayToIndex = array(
            'mon' => 1,
            'tue' => 2,
            'wed' => 3,
            'thu' => 4,
            'fri' => 5,
            'sat' => 6,
            'sun' => 7
        );

        $this->hours = array();

        foreach ($hours as $key => $value) {
            $this->hours[$weekdayToIndex[$key]] = $value;
        }

        // Remove empty elements from values (backwards compatibility)
        foreach ($this->hours as $key => $value) {
            $this->hours[$key] = array_filter($value, function ($element) {
                return (trim($element) !== '');
            });
        }

        // Remove empty elements from values (backwards compatibility)
        foreach ($this->exceptions as $key => $value) {
            $this->exceptions[$key] = array_filter($value, function ($element) {
                return (trim($element) !== '');
            });
        }

        $defaultTemplates = array(
            'open'           => '<h3>Yes, we\'re open! Today\'s hours are {%hours%}.</h3>',
            'closed'         => '<h3>Sorry, we\'re closed. Today\'s hours are {%hours%}.</h3>',
            'closed_all_day' => '<h3>Sorry, we\'re closed.</h3>',
            'separator'      => ' - ',
            'join'           => ' and ',
            'format'         => 'g:ia',
            'hours'          => '{%open%}{%separator%}{%closed%}',

            'overview_separator' => '-',
            'overview_join'      => ', ',
            'overview_format'    => 'g:ia',
            'overview_weekdays'  => array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun')
        );

        $this->templates += $defaultTemplates;
    }

    /**
     *
     * @param string $timestamp
     * @return array Today's hours
     */
    public function hours_today($timestamp = null)
    {
        $timestamp     = (null !== $timestamp) ? $timestamp : time();
        $today         = strtotime(date('Y-m-d', $timestamp) . ' midnight');
        $weekday_short = date('N', $timestamp);

        $hours_today = array();

        if (isset($this->hours[$weekday_short])) {
            $hours_today = $this->hours[$weekday_short];
        }

        foreach ($this->exceptions as $ex_day => $ex_hours) {
            if (strtotime($ex_day) === $today) {
                // Today is an exception, use alternate hours instead
                $hours_today = $ex_hours;
            }
        }

        return $hours_today;
    }

    /**
     *
     * @param string $timestamp
     * @return boolean
     */
    public function is_open($timestamp = null)
    {
        $timestamp = (null !== $timestamp) ? $timestamp : time();

        $is_open = false;
        $this->yesterdayFlag = false;

        // Check whether shop's still open from day before

        $ts_yesterday    = strtotime(date('Y-m-d H:i:s', $timestamp) . ' -1 day');
        $yesterday       = date('Y-m-d', $ts_yesterday);
        $hours_yesterday = $this->hours_today($ts_yesterday);

        foreach ($hours_yesterday as $range) {
            $range = explode('-', $range);
            $start = strtotime($yesterday . ' ' . $range[0]);
            $end   = strtotime($yesterday . ' ' . $range[1]);

            if ($end <= $start) {
                $end = strtotime($yesterday . ' ' . $range[1] . ' +1 day');
            }

            if ($start <= $timestamp && $timestamp <= $end) {
                $is_open = true;
                $this->yesterdayFlag = true;
                break;
            }
        }

        // Check today's hours

        if (!$is_open) {
            $day         = date('Y-m-d', $timestamp);
            $hours_today = $this->hours_today($timestamp);

            foreach ($hours_today as $range) {
                $range = explode('-', $range);
                $start = strtotime($day . ' ' . $range[0]);
                $end   = strtotime($day . ' ' . $range[1]);

                if ($end <= $start) {
                    $end = strtotime($day . ' ' . $range[1] . ' +1 day');
                }

                if ($start <= $timestamp && $timestamp <= $end) {
                    $is_open = true;
                    break;
                }
            }
        }

        return $is_open;
    }

    /**
     * Prep HTML
     *
     * @param string $template_name
     * @param int $timestamp
     */
    private function render_html($template_name, $timestamp)
    {
        $template    = $this->templates;
        $hours_today = $this->hours_today($timestamp);
        $day         = date('Y-m-d', $timestamp);
        $output      = '';

        if (count($hours_today) > 0) {
            $hours_template = '';
            $first = true;

            foreach ($hours_today as $range) {
                $range = explode('-', $range);
                $start = strtotime($day . ' ' . $range[0]);
                $end   = strtotime($day . ' ' . $range[1]);

                if (false === $first) {
                    $hours_template .= $template['join'];
                }

                $hours_template .= $template['hours'];

                $hours_template = str_replace('{%open%}', date($template['format'], $start), $hours_template);
                $hours_template = str_replace('{%closed%}', date($template['format'], $end), $hours_template);
                $hours_template = str_replace('{%separator%}', $template['separator'], $hours_template);

                $first = false;
            }

            $output .= str_replace('{%hours%}', $hours_template, $template[$template_name]);
        } else {
            $output .= $template['closed_all_day'];
        }

        echo $output;
    }

    /**
     * Output HTML
     *
     * @param string $timestamp
     */
    public function render($timestamp = null)
    {
        $timestamp = (null !== $timestamp) ? $timestamp : time();

        if ($this->is_open($timestamp)) {
            // Print yesterday's hours if shop's still open from day before
            if ($this->yesterdayFlag) {
                $timestamp = strtotime(date('Y-m-d H:i:s', $timestamp) . ' -1 day');
            }

            $this->render_html('open', $timestamp);
        } else {
            $this->render_html('closed', $timestamp);
        }
    }

    /**
     *
     * @param array $ranges
     * @return string
     */
    private function hours_overview_format_hours(array $ranges)
    {
        $hoursparts = array();

        foreach ($ranges as $range) {
            $day = '2016-01-01';

            $range = explode('-', $range);
            $start = strtotime($day . ' ' . $range[0]);
            $end   = strtotime($day . ' ' . $range[1]);

            $hoursparts[] = date($this->templates['overview_format'], $start)
                          . $this->templates['overview_separator']
                          . date($this->templates['overview_format'], $end);
        }

        return implode($this->templates['overview_join'], $hoursparts);
    }

    /**
     *
     */
    private function hours_this_week_simple()
    {
        $lookup = array_combine(range(1, 7), $this->templates['overview_weekdays']);

        $ret = array();

        for ($i = 1; $i <= 7; $i++) {
            $hours_str = (isset($this->hours[$i]) && count($this->hours[$i]) > 0)
                    ? $this->hours_overview_format_hours($this->hours[$i])
                    : '-';

            $ret[$lookup[$i]] = $hours_str;
        }

        return $ret;
    }

    /**
     *
     * @return array
     */
    private function hours_this_week_grouped()
    {
        $lookup = array_combine(range(1, 7), $this->templates['overview_weekdays']);

        $blocks = array();

        // Remove empty elements ("closed all day")

        $hours = array_filter($this->hours, function ($element) {
            return (count($element) > 0);
        });

        foreach ($hours as $weekday => $hours2) {
            foreach ($blocks as &$block) {
                if ($block['hours'] === $hours2) {
                    $block['days'][] = $weekday;
                    continue 2;
                }
            }
            unset($block);

            $blocks[] = array('days' => array($weekday), 'hours' => $hours2);
        }

        // Flatten

        $ret = array();

        foreach ($blocks as $block) {
            // Format days

            $keyparts     = array();
            $keys         = $block['days'];
            $buffer       = array();
            $lastIndex    = null;
            $minGroupSize = 3;

            foreach ($keys as $index) {
                if ($lastIndex !== null && $index - 1 !== $lastIndex) {
                    if (count($buffer) >= $minGroupSize) {
                        $keyparts[] = $lookup[$buffer[0]] . '-' . $lookup[$buffer[count($buffer) - 1]];
                    } else {
                        foreach ($buffer as $b) {
                            $keyparts[] = $lookup[$b];
                        }
                    }
                    $buffer = array();
                }

                $buffer[] = $index;

                $lastIndex = $index;
            }
            if (count($buffer) >= $minGroupSize) {
                $keyparts[] = $lookup[$buffer[0]] . '-' . $lookup[$buffer[count($buffer) - 1]];
            } else {
                foreach ($buffer as $b) {
                    $keyparts[] = $lookup[$b];
                }
            }

            // Combine

            $ret[implode(', ', $keyparts)] = $this->hours_overview_format_hours($block['hours']);
        }

        return $ret;
    }

    /**
     *
     * @return array
     */
    public function hours_this_week($groupSameDays = false)
    {
        return (true === $groupSameDays)
                ? $this->hours_this_week_grouped()
                : $this->hours_this_week_simple();
    }
}
