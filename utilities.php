<?php

date_default_timezone_set('Pacific/Auckland');
$current_time_stamp = time();
$current_time = date("Y-m-d H:i:s", $current_time_stamp);
define('COURSE_DURATION_TEXT', 'day,days,hour,hours,month,months');
define('MAX_ALLOWED_ROWS_PER_BATCH', 250);
define('CATEGORY_SEPARATOR', ';');

global $automatic_values;
global $default_values;
global $required;
global $filters;
global $error_messages;

$automatic_values = array(
    'updated' => $current_time,
    'entered' => $current_time,
    'maptuning_date' => $current_time,
    'listing_id' => 6,
    'importID' => 0,
    'discount_id' => null,
    'seo_title' => null,
    'image_id' => 0,
    'thumb_id' => 0,
    'location_1' => 0,
    'location_3' => 0,
    'location_5' => 0,
    'seo_description' => null,
    'keywords' => null,
    'seo_keywords' => null,
    'maptuning' => null,
    'contact_name' => null,
    'phone' => null,
    'renewal_date' => '0000-00-00',
    'status' => 'A',
    'suspended_sitemgr' => 'n',
    'level' => 10,
    'random_number' => null,
    'video_snippet' => null,
    'recurring' => 'N',
    'day' => 0,
    'dayofweek' => null,
    'week' => null,
    'month' => 0,
    'until_date' => '0000-00-00',
    'repeat_event' => 'N',
    'number_views' => 1,
    'map_zoom' => 0,
    'package_id' => 0,
    'package_price' => 0,
    'custom_id' => NULL,
    'course_duration_day' => 0,
    'course_duration_month' => 0,
    'course_duration_year' => 0,
    'presenter_experience' => null,
    'noschedule' => null,
);

$default_values = array(
    'start_date' => '9999-12-30',
    'end_date' => '9999-12-31',
    'start_time' => '00:00:00',
    'end_time' => '00:00:00',
);

$required = array(
    'title',
    'schedule_description',
    'delivery_method',
    'course_price',
    'email',
    'description',
    'long_description',
    'categories',
);

$filters = array(
    'title' => 'length__250',
    //'location_2' => 'table__Location_2__name', Fetch it from location 4
    'location_4' => 'table__Location_4__name',
    'description' => 'length__500',
    'long_description' => '',
    'start_date_time' => 'datetime__Y-m-d H:i:s',
    //'start_time' => 'datetime__H:i:s',
    'end_date_time' => 'datetime__Y-m-d H:i:s',
    //'end_time' => 'datetime__H:i:s',
    'location' => '',
    'address' => '',
    'zip_code' => 'length__6',
    'url' => '',
    'email' => 'email__FILTER_VALIDATE_EMAIL',
    'categories' => '',
    'course_code' => 'length__32',
    'course_price' => 'length__32',
    'course_free' => 'list__"Y","N"',
    'discounts_available' => 'length__500',
    'course_type' => 'table__EventType__Type',
    'delivery_method' => 'list__"at a venue","online"',
    'private_course' => 'list__"T","F","Yes","No","Y","N","TRUE","FALSE"',
    'pre_requisites' => '',
    'target_audience' => '',
    'programme_structure' => '',
    'outcomes' => '',
    'course_highlights' => '',
    'testimonials' => '',
    'presenter_name' => 'length__100',
    'presenter_qualifications' => 'length__250',
    'presenter_details' => '',
    'schedule_description' => 'length__150',
);

$error_messages = array(
    'required_column' => array(
        'replace' => 'column',
        'message' => '{column} is required. Please use a column in the import file for this field.',
    ),
    'required_cell' => array(
        'replace' => 'column__row',
        'message' => 'Null values are not accepted for the {column}. Please correct the rows numbered {row}.',
    ),
    'course_categories' => array(
        'replace' => 'row',
        'message' => 'Course categories entered does not exist in the system for the rows {row}. Please separate categories by semi-colon(;) only and not any other characters.',
    ),
    'count_categories' => array(
        'replace' => 'row',
        'message' => 'Number of Course Categories entered exceeds the limits for the rows {row}. You can associate at most 5 categories for a single course.',
    ),
    'table' => array(
        'replace' => 'column__row',
        'message' => 'The text entered in import file for the {column} does not match any available options for the field in rows {row}.',
    ),
    'length' => array(
        'replace' => 'column__row',
        'message' => 'Maximum length of text for {column} is exceeded in rows {row}.',
    ),
    'list' => array(
        'replace' => 'column__row__format',
        'message' => 'Values permitted in {column} are {format}. Please correct values in rows {row}.',
    ),
    'datetime' => array(
        'replace' => 'row',
        'message' => 'Invalid date or time format in rows {row}. Supported format is "YYYY-MM-DD HH:MM:SS" for date time.',
    ),
    'email' => array(
        'replace' => 'row',
        'message' => 'Invalid email in rows {row}.',
    ),
    'course_duration' => array(
        'replace' => 'row',
        'message' => 'Invalid value for Course Duration in rows {row}. Valid number is between 1 to 31 and suffix text are one of the day, days, hour, hours, month, months.',
    ),
);