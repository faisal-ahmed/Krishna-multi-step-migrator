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
    public $parentCategories;

    public function __construct()
    {
        $this->connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE) or die("Error " . mysqli_error($this->connection));
        $this->EventType = $this->fetch_table('eventtype');
        $this->Location_2 = $this->fetch_table('location_2');
        $this->Location_4 = $this->fetch_table('location_4');
        $this->EventCategory = $this->fetch_table('eventcategory');
        $this->buildParentCat();
    }

    public function getLocationID($location_4_name)
    {
        foreach ($this->Location_4 as $key => $row) {
            if ($row['name'] == $location_4_name) {
                return array($row['id'], $row['location_2']);
            }
        }

        return false;
    }

    public function getRegionName($regionID)
    {
        foreach ($this->Location_2 as $key => $row) {
            if ($row['id'] == $regionID) {
                return $row['name'];
            }
        }

        return false;
    }

    public function buildParentCat()
    {
        $this->parentCategories = array();
        $temp_cat = array();
        foreach ($this->EventCategory as $key => $cat) {
            $temp_cat[$cat['id']] = $cat['category_id'];
            $this->parentCategories[$cat['title']] = array($cat['id']);
        }

        foreach ($this->parentCategories as $title => $cat_id_array) {
            $temp_cat_id = $cat_id_array[0];
            if ($temp_cat_id !== 0) {
                $count = 4;
                while ($temp_cat[$temp_cat_id] && $count--) {
                    $this->parentCategories[$title][] = $temp_cat[$temp_cat_id];
                    $temp_cat_id = $temp_cat[$temp_cat_id];
                }
            }
        }
    }

    public function insert($table_name, $columns, $values)
    {
        $query = "INSERT INTO $table_name (";
        $flag = 0;
        foreach ($columns as $key => $value) {
            $query .= ($flag++) ? ", $value" : $value;
        }
        $query .= ") VALUES (";
        $flag = 0;
        foreach ($values as $key => $value) {
            $query .= ($flag++) ? ", '$value'" : "'$value'";
        }
        $query .= ")";

        print_r($query);
        $stmt = $this->connection->query($query);
        var_dump($stmt);
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

    public function debug($data)
    {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }
}

class db_helper extends db_orm
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertEvents($data_file, $matching)
    {
        global $automatic_values;
        global $default_values;

        foreach ($data_file as $row_key => $each_row) {
            $insert_row = $automatic_values;
            $insert_row['location'] = '';
            $insert_row['address'] = '';
            $insert_row['zip_code'] = '';
            $region = '';
            $city = '';
            foreach ($each_row as $key => $value) {
                if (!isset($matching[$key]) || $matching[$key] == '') continue;
                if ($matching[$key] == 'categories') {
                    $categories = explode(CATEGORY_SEPARATOR, $value);
                    for ($i = 0; $i < count($categories); $i++) {
                        $cat_index = ($i + 1);
                        $temp_db_row_key = "cat_{$cat_index}_id";
                        $tempCategory = trim($categories[$i]);
                        for ($j = 1; $j <= count($this->parentCategories[$tempCategory]); $j++) {
                            $insert_row[$temp_db_row_key] = $this->parentCategories[$tempCategory][$j - 1];
                            $temp_db_row_key = "parcat_{$cat_index}_level{$j}_id";
                        }
                    }
                } else if ($matching[$key] == 'course_duration') {
                    list($insert_row['course_duration_no'], $insert_row['course_duration_hour']) = explode(' ', $value);
                } else if ($matching[$key] == 'location_4') {
                    list($insert_row['location_4'], $insert_row['location_2']) = $this->getLocationID($value);
                    $region = $this->getRegionName($insert_row['location_2']);
                    $city = $value;
                } else if ($matching[$key] == 'start_date_time') {
                    list($insert_row['start_date'], $insert_row['start_time']) = explode(' ', $value);
                    $insert_row['has_start_time'] = 'y';
                } else if ($matching[$key] == 'end_date_time') {
                    list($insert_row['end_date'], $insert_row['end_time']) = explode(' ', $value);
                    $insert_row['has_end_time'] = 'y';
                } else if ($matching[$key] == 'course_free' || $matching[$key] == 'delivery_method' || $matching[$key] == 'private_course') {
                    $insert_row[$matching[$key]] = strtoupper($value);
                } else {
                    $insert_row[$matching[$key]] = $value;
                }
            }
            if (!isset($insert_row['course_type'])) {
                $insert_row['course_type'] = 'null';
            }
            if (!isset($insert_row['start_date'])) {
                $insert_row['start_date'] = $default_values['start_date'];
                $insert_row['start_time'] = $default_values['start_time'];
            }
            if (!isset($insert_row['end_date'])) {
                $insert_row['end_date'] = $default_values['end_date'];
                $insert_row['end_time'] = $default_values['end_time'];
            }
            $insert_row['friendly_url'] = str_replace(" ", "-", strtolower($insert_row['title'])) . "-" . time();
            $insert_row['fulltextsearch_keyword'] = "Title: {$insert_row['title']}, Description: {$insert_row['description']}";
            $insert_row['fulltextsearch_where'] = "Location: {$insert_row['location']}, Address: {$insert_row['address']}, Zip: {$insert_row['zip_code']}, Region: $region, City: $city.";
            //TODO: place the lat/lon here
            $insert_row['latitude'] = 12.35;
            $insert_row['longitude'] = 35.55;
            $this->insert('event', array_keys($insert_row), $insert_row);
            break;
        }
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

        $count = 2;
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

        $count = 2;
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
        $dateTimeFormat = explode(' ', $dateFormat);

        $count = 2;
        foreach ($data as $key => $value) {
            if ($value[$column_name] !== '') {
                $dateTimeValue = explode(' ', $value[$column_name]);
                $flag = 1;
                if (count($dateTimeFormat) === count($dateTimeValue)) {
                    foreach ($dateTimeFormat as $formatKey => $format) {
                        if ($this->validateDate($dateTimeValue[$formatKey], $format)) {
                            $flag = 0;
                            break;
                        }
                    }
                } else {
                    $flag = 0;
                }
                if (!$flag) {
                    $rows .= ($rows == '') ? $count : ", $count";
                }
            }
            $count++;
        }

        return $rows;
    }

    public function emailFilter($data, $column_name)
    {
        $rows = '';

        $count = 2;
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

        $count = 2;
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

        $count = 2;
        foreach ($data as $key => $value) {
            if ($value[$column_name] !== '') {
                $flag = 0;
                foreach ($list as $key2 => $value2) {
                    if (strtoupper($value[$column_name]) == strtoupper($value2)) {
                        $flag = 1;
                        break;
                    }
                }
                if ($flag == 0) {
                    $rows .= ($rows == '') ? $count : ", $count";
                }
            }
            $count++;
        }

        return $rows;
    }

    public function categoryFilter($data, $column_name, $table_data, $table_column_name)
    {
        $rows = '';

        $count = 2;
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

    public function categoryCountFilter($data, $column_name)
    {
        $rows = '';

        $count = 2;
        foreach ($data as $key => $value) {
            $categories = explode(CATEGORY_SEPARATOR, $value[$column_name]);
            if (count($categories) > 5) {
                $rows .= ($rows == '') ? $count : ", $count";
            }
            $count++;
        }

        return $rows;
    }

    function courseDurationFilter($data, $column_name, $text_lists = array())
    {
        $rows = '';

        $count = 2;
        foreach ($data as $key => $value) {
            if ($value[$column_name] === '') {
                $count++;
                continue;
            }
            $duration_array = explode(' ', $value[$column_name]);
            if (count($duration_array) > 1) {
                list($number, $text) = $duration_array;
                if (!in_array($text, $text_lists) || $number < 1 || $number > 31) {
                    $rows .= ($rows == '') ? $count : ", $count";
                }
            } else {
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