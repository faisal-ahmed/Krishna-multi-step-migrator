<?php
$uploaddir = dirname(__FILE__).'/uploads/';
$id = $_REQUEST['user_id'];
$file_name = $id . "_" . time() . "_". substr(basename($_FILES['userfile']['name']), -5);
//$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
$uploadfile = $uploaddir . $file_name;

if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
    echo $file_name . "__File Uploaded!";
} else {
    echo $file_name . "__There was an error uploading your file, please try again.";
}
?>