<?php

if (isset($_REQUEST['get_sample']) && $_REQUEST['get_sample'] === 'download_now') {
    $parser = new CsvConversion();
    $parser->download_file();
    die;
}

require_once dirname(__FILE__) . '/PHPExcel.php';

class CsvConversion
{
    public function __construct()
    {
    }

    private function create_array_2_csv_local_file(array $csv_array, $fileName = null)
    {
        if ($fileName === null) {
            $fileName = 'convertedFile.csv';
        }
        if (count($csv_array) == 0) {
            return null;
        }

        $count = 1;
        $csv = '';
        $csv_handler = fopen(dirname(__FILE__) . '/uploads/' . $fileName, 'w');
        foreach ($csv_array as $key => $value) {
            foreach ($value as $key1 => $value1) {
                $value1 = '"' . htmlentities($value1) . '"';
                if (!$key1) $csv .= $value1;
                else $csv .= ",$value1";
            }
            $csv .= "\n";
            if ($count++ >= MAX_ALLOWED_ROWS_PER_BATCH){
                break;
            }
        }

        fwrite($csv_handler, $csv);
        fclose($csv_handler);
    }

    public function download_file()
    {
        ob_start();
        $fileName = 'sample.csv';
        if (file_exists(dirname(__FILE__) . '/static/' . $fileName)) {
            header('Content-Description: File Transfer');
            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");
            header("Content-type: text/csv");
            header('Content-Disposition: attachment; filename=' . date("Y-m-d_H.i.s_") . $fileName);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize(dirname(__FILE__) . '/static/' . $fileName));
            ob_clean();
            flush();
            readfile(dirname(__FILE__) . '/static/' . $fileName);
        }
        ob_end_flush();
        die;
    }

    public function convert_excel_to_csv($extention, $filename)
    {
        if ($extention === 'xls') $objReader = new PHPExcel_Reader_Excel5();
        else if ($extention === 'xlsx') $objReader = new PHPExcel_Reader_Excel2007();

        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load(dirname(__FILE__) . "/uploads/$filename");
        $rowIterator = $objPHPExcel->getActiveSheet()->getRowIterator();

        $csvArray = array();
        foreach ($rowIterator as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $rowIndex = $row->getRowIndex() - 1;
            $csvArray[$rowIndex] = array();

            foreach ($cellIterator as $cell) {
                $csvArray[$rowIndex][] = htmlentities($cell->getCalculatedValue());
            }
        }

        $this->create_array_2_csv_local_file($csvArray);
    }

    public function convert_to_local_file($filename, $extention)
    {
        if ($extention !== 'csv') {
            $this->convert_excel_to_csv($extention, $filename);
        } else {
            $csv_aray = $this->parse_file_to_array($filename);
            $this->create_array_2_csv_local_file($csv_aray);
        }
    }

    public function parse_csv_column($filename = null, $split = false)
    {
        $dir = 'uploads';
        if ($split) {
            $dir = 'static';
        }
        if ($filename == null) {
            $fp = fopen(dirname(__FILE__) . "/$dir/convertedFile.csv", 'r') or die("can't open file");
        } else {
            $fp = fopen(dirname(__FILE__) . "/$dir/$filename", 'r') or die("can't open file");
        }
        $return = array();

        while ($csv_line = fgetcsv($fp)) {
            for ($i = 0, $j = count($csv_line); $i < $j; $i++) {
                $temp_string = html_entity_decode(trim($csv_line[$i]));
                if ($split === true) {
                    list($key, $value) = explode(' - ', $temp_string);
                    $return[$key] = ucwords($value);
                } else {
                    $return[$temp_string] = $temp_string;
                }
            }
            break;
        }

        fclose($fp);

        return $return;
    }

    public function count_rows($filename = null)
    {
        if ($filename == null) {
            $fp = fopen(dirname(__FILE__) . '/uploads/convertedFile.csv', 'r') or die("can't open file");
        } else {
            $fp = fopen(dirname(__FILE__) . '/uploads/' . $filename, 'r') or die("can't open file");
        }
        $count = -1;

        while ($csv_line = fgetcsv($fp)) {
            $count++;
        }

        fclose($fp);
        return $count;
    }

    public function get_converted_file_data()
    {
        $fp = fopen(dirname(__FILE__) . '/uploads/convertedFile.csv', 'r') or die("can't open file");
        $return = array();

        $column = array();
        $count = 0;
        while ($csv_line = fgetcsv($fp)) {
            if ($count++ == 0){
                for ($i = 0, $j = count($csv_line); $i < $j; $i++) {
                    $column[] = html_entity_decode(trim($csv_line[$i]));
                }
                continue;
            }
            $temp = array();
            for ($i = 0, $j = count($csv_line); $i < $j; $i++) {
                $temp[$column[$i]] = html_entity_decode(trim($csv_line[$i]));
            }
            $return[] = $temp;
        }

        fclose($fp);

        return $return;
    }

    public function getDataOfRows($rows)
    {
        $fp = fopen(dirname(__FILE__) . '/uploads/convertedFile.csv', 'r') or die("can't open file");
        $return = array();

        $count = 0;
        while ($csv_line = fgetcsv($fp)) {
            if (in_array($count, $rows)) {
                $tempArray = array();
                for ($i = 0, $j = count($csv_line); $i < $j; $i++) {
                    $temp_string = html_entity_decode(trim($csv_line[$i]));
                    $tempArray[] = $temp_string;
                }
                $return[] = $tempArray;
            }
            $count++;
        }

        fclose($fp);
        return $return;
    }

    private function parse_file_to_array($filename = null)
    {
        if ($filename == null) {
            $fp = fopen(dirname(__FILE__) . '/uploads/convertedFile.csv', 'r') or die("can't open file");
        } else {
            $fp = fopen(dirname(__FILE__) . '/uploads/' . $filename, 'r') or die("can't open file");
        }
        $return = array();

        while ($csv_line = fgetcsv($fp)) {
            $temp = array();
            for ($i = 0, $j = count($csv_line); $i < $j; $i++) {
                $temp[] = html_entity_decode(trim($csv_line[$i]));
            }
            $return[] = $temp;
        }

        fclose($fp);

        return $return;
    }
}

?>