<?php

/**
 * ----------------------------------
 * PHP STORE HOURS
 * ----------------------------------
 * Version 3.0
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
        $this->hours         = $hours;
        $this->exceptions    = $exceptions;
        $this->templates     = $templates;
        $this->yesterdayFlag = false;

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
            'hours'          => '{%open%}{%separator%}{%closed%}'
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
        $weekday_short = strtolower(date('D', $timestamp));

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

            if (($start <= $timestamp) && ($timestamp <= $end)) {
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

                if (($start <= $timestamp) && ($timestamp <= $end)) {
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
}
