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

    $db = new db_helper();
    $apply_filter = new filter();
    $parser = new CsvConversion();
    $file_data = $parser->get_converted_file_data();
    $csv_column_name = $parser->parse_csv_column('database_column.csv', true);

    //Apply Required Start
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
    //Apply Required End

    //Apply Filters Start
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
            $rows = $apply_filter->tableFilter($file_data, $mappedColumn, $db->{strtolower($filterArray[1])}, $filterArray[2]);
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
    //Apply Filters End

    if ($errorFound) {
        step2($messages, $matching);
//        $db->debug($messages);
    }
}