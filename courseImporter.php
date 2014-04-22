<?php

require_once('includeLibraryFiles.php');

if (isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) === 'post') {
    extract($_REQUEST);
    if (isset($step) && $step == 'step1') {
        $extention = strtolower(substr($uploaded_file_name, strrpos($uploaded_file_name, '.') + 1));
        $conversion = new CsvConversion();
        $conversion->convert_to_local_file($uploaded_file_name, $extention);
        $newFile = "converted_$uploaded_file_name";
        step2($uploaded_file_name);
    } else if (isset($step) && $step == 'step2') {
        step3($column_matching, $file_name);
    }
} else {
    step1();
}