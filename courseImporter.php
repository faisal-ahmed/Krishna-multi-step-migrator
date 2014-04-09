<?php

if (isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) === 'post') {
    if (isset($_REQUEST['step']) && $_REQUEST['step'] == 'step1') {
        require_once ('step_two.php');
    }
} else {
    require_once ('step_one.php');
}