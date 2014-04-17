<?php

define('DB_HOST', 'localhost');
define('DB_DATABASE', 'importer');
define('DB_USER', 'root');
define('DB_PASSWORD', '');

class db_orm
{
    private $connection;
    public $eventType;
    public $location_2;
    public $location_4;
    public $eventCategory;

    public function __construct()
    {
        $this->connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE) or die("Error " . mysqli_error($this->connection));
        $this->eventType = $this->fetch_table('eventtype');
        $this->location_2 = $this->fetch_table('location_2');
        $this->location_4 = $this->fetch_table('location_4');
        $this->eventCategory = $this->fetch_table('eventcategory');
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

    public function in_db_table($table_data, $table_column_name, $needle) {
        foreach ($table_data as $key => $row) {
            if (isset($row[$table_column_name]) && $row[$table_column_name] === $needle){
                return true;
            }
        }

        return false;
    }

    public function debug($data) {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }
}