<?php

define('DB_HOST', 'localhost');
define('DB_DATABASE', 'importer');
define('DB_USER', 'root');
define('DB_PASSWORD', '');

class db_orm
{
    private $connection;
    public $EventType;
    public $Location_2;
    public $Location_4;
    public $EventCategory;

    public function __construct()
    {
        $this->connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE) or die("Error " . mysqli_error($this->connection));
        $this->EventType = $this->fetch_table('eventtype');
        $this->Location_2 = $this->fetch_table('location_2');
        $this->Location_4 = $this->fetch_table('location_4');
        $this->EventCategory = $this->fetch_table('eventcategory');
    }

    public function fetch_table($table)
    {
        $return = array();
        $query = "SELECT * FROM $table";
        $result = $this->connection->query($query);
        while ($row = mysqli_fetch_assoc($result)) {
            $return[] = $row;
        }

        return $return;
    }
}

class db_helper extends db_orm
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getTableData($tableName)
    {
        if (isset($this->$tableName)) return $this->$tableName;
        return false;
    }

    public function debug($data)
    {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }
}

class filter
{
    public function __construct()
    {
    }

    public function requiredFilter($data, $column_name)
    {
        $rows = '';

        $count = 1;
        foreach ($data as $key => $value) {
            if ($value[$column_name] === '') {
                $rows .= ($rows == '') ? $count : ", $count";
            }
            $count++;
        }

        return $rows;
    }

    public function lengthFilter($data, $column_name, $length)
    {
        $rows = '';

        $count = 1;
        foreach ($data as $key => $value) {
            if (strlen($value[$column_name]) > $length) {
                $rows .= ($rows == '') ? $count : ", $count";
            }
            $count++;
        }

        return $rows;
    }

    public function dateFilter($data, $column_name, $dateFormat)
    {
        $rows = '';

        $count = 1;
        foreach ($data as $key => $value) {
            if ($value[$column_name] !== '' && !$this->validateDate($value[$column_name], $dateFormat)) {
                $rows .= ($rows == '') ? $count : ", $count";
            }
            $count++;
        }

        return $rows;
    }

    public function emailFilter($data, $column_name)
    {
        $rows = '';

        $count = 1;
        foreach ($data as $key => $value) {
            if ($value[$column_name] !== '' && !filter_var($value[$column_name], FILTER_VALIDATE_EMAIL)) {
                $rows .= ($rows == '') ? $count : ", $count";
            }
            $count++;
        }

        return $rows;
    }

    public function tableFilter($data, $column_name, $table_data, $table_column_name)
    {
        $rows = '';

        $count = 1;
        foreach ($data as $key => $value) {
            $flag = 0;
            foreach ($table_data as $key2 => $table_row) {
                if ($value[$column_name] !== '' && strtolower($table_row[$table_column_name]) === strtolower($value[$column_name])) {
                    $flag = 1;
                    break;
                }
            }
            if (!$flag) {
                $rows .= ($rows == '') ? $count : ", $count";
            }
            $count++;
        }

        return $rows;
    }

    public function listFilter($data, $column_name, $list = array())
    {
        $rows = '';

        $count = 1;
        foreach ($data as $key => $value) {
            if ($value[$column_name] !== '' && !in_array($value[$column_name], $list)) {
                $rows .= ($rows == '') ? $count : ", $count";
            }
            $count++;
        }

        return $rows;
    }

    public function categoryFilter($data, $column_name, $table_data, $table_column_name)
    {
        $rows = '';

        $count = 1;
        foreach ($data as $key => $value) {
            $categories = explode(CATEGORY_SEPARATOR, $value[$column_name]);
            foreach ($categories as $cat_key => $category) {
                $flag = 0;
                foreach ($table_data as $key2 => $table_row) {
                    if (strtolower($table_row[$table_column_name]) === strtolower(trim($category))) {
                        $flag = 1;
                        break;
                    }
                }
                if (!$flag) {
                    $rows .= ($rows == '') ? $count : ", $count";
                    break;
                }
            }
            $count++;
        }

        return $rows;
    }

    function courseDurationFilter($data, $column_name, $text_lists = array()) {
        $rows = '';

        $count = 1;
        foreach ($data as $key => $value) {
            if ($value[$column_name] === '' ) {
                $count++;
                continue;
            }
            list($number, $text) = explode(' ', $value[$column_name]);
            if (!in_array($text, $text_lists) || $number < 1 || $number > 31) {
                $rows .= ($rows == '') ? $count : ", $count";
            }
            $count++;
        }

        return $rows;
    }

    function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
}