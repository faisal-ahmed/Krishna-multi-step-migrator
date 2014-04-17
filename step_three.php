<?php

function step3($matching)
{
    global $automatic_values;
    global $default_values;
    global $required;
    global $filters;
    global $error_messages;
    $messages = array(
        'error' => array(),
        'warning' => array(),
        'info' => array(),
        'success' => array(),
    );
    $errorFound = 0;

    $db = new db_helper(); // Database Object
    $apply_filter = new filter(); // Filter Object
    $parser = new CsvConversion(); // File Parsing Object
    $file_data = $parser->get_converted_file_data(); // User's File Data
    $csv_column_name = $parser->parse_csv_column('database_column.csv', true); // Database Column Name Visible To User

    /*****************************************************************************/
    /*****************************Validation Start********************************/
    /*****************************************************************************/
    //Required Apply Start
    foreach ($required as $key => $column) {
        if (($mappedColumn = array_search($column, $matching)) === false) {
            $errorFound = 1;
            $errorMessage = $error_messages['required_column']['message'];
            $errorMessage = str_replace('{column}', $csv_column_name[$column], $errorMessage);
            $messages['error'][] = $errorMessage;
            continue;
        }
        $rows = $apply_filter->requiredFilter($file_data, $mappedColumn);
        $errorMessage = '';
        if ($rows !== '') {
            $errorMessage = $error_messages['required_cell']['message'];
            $errorMessage = str_replace('{column}', $csv_column_name[$column], $errorMessage);
            $errorMessage = str_replace('{row}', $rows, $errorMessage);
        }
        if ($errorMessage !== '') {
            $errorFound = 1;
            $messages['error'][] = $errorMessage;
        }
    }
    //Required Apply End

    //Filters Apply Start
    foreach ($filters as $column => $value) {
        if (($mappedColumn = array_search($column, $matching)) === false) {
            continue;
        }
        $errorMessage = '';
        $filterArray = explode('__', $value);
        if ($filterArray[0] == 'length') {
            $rows = $apply_filter->lengthFilter($file_data, $mappedColumn, $filterArray[1]);
            if ($rows !== '') {
                $errorMessage = $error_messages['length']['message'];
                $errorMessage = str_replace('{column}', $csv_column_name[$column], $errorMessage);
                $errorMessage = str_replace('{row}', $rows, $errorMessage);
            }
        } else if ($filterArray[0] == 'table') {
            $rows = $apply_filter->tableFilter($file_data, $mappedColumn, $db->{$filterArray[1]}, $filterArray[2]);
            if ($rows !== '') {
                $errorMessage = $error_messages['table']['message'];
                $errorMessage = str_replace('{column}', $csv_column_name[$column], $errorMessage);
                $errorMessage = str_replace('{row}', $rows, $errorMessage);
            }
        } else if ($filterArray[0] == 'datetime') {
            $rows = $apply_filter->dateFilter($file_data, $mappedColumn, $filterArray[1]);
            if ($rows !== '') {
                $errorMessage = $error_messages['datetime']['message'];
                $errorMessage = str_replace('{row}', $rows, $errorMessage);
            }
        } else if ($filterArray[0] == 'email') {
            $rows = $apply_filter->emailFilter($file_data, $mappedColumn);
            if ($rows !== '') {
                $errorMessage = $error_messages['email']['message'];
                $errorMessage = str_replace('{row}', $rows, $errorMessage);
            }
        } else if ($filterArray[0] == 'list') {
            $rows = $apply_filter->listFilter($file_data, $mappedColumn, explode(",", str_replace('"', '', $filterArray[1])));
            if ($rows !== '') {
                $errorMessage = $error_messages['list']['message'];
                $errorMessage = str_replace('{column}', $csv_column_name[$column], $errorMessage);
                $errorMessage = str_replace('{row}', $rows, $errorMessage);
            }
        }
        if ($errorMessage !== '') {
            $errorFound = 1;
            $messages['error'][] = $errorMessage;
        }
    }
    //Filters Apply End

    //Category Validation Start
    $rows = $apply_filter->categoryFilter($file_data, array_search('categories', $matching), $db->EventCategory, 'title');
    if ($rows !== '') {
        $errorMessage = $error_messages['course_categories']['message'];
        $errorMessage = str_replace('{row}', $rows, $errorMessage);
        $errorFound = 1;
        $messages['error'][] = $errorMessage;
    }
    //Category Validation End

    //Course Duration Validation Start
    if (($mappedColumn = array_search('course_duration', $matching)) !== false) {
        $rows = $apply_filter->courseDurationFilter($file_data, $mappedColumn, explode(',', COURSE_DURATION_TEXT));
        if ($rows !== '') {
            $errorMessage = $error_messages['course_duration']['message'];
            $errorMessage = str_replace('{row}', $rows, $errorMessage);
            $errorFound = 1;
            $messages['error'][] = $errorMessage;
        }
    }
    //Course Duration Validation End

    if ($errorFound) {
        step2($messages, $matching);
//        $db->debug($messages);
    }
    /*****************************************************************************/
    /******************************Validation End*********************************/
    /*****************************************************************************/
}