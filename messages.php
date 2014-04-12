<?php

function messages($messages)
{
    if (isset($messages['error'])) {
        foreach ($messages['error'] as $key => $value) {
            ?>
            <div class="message errormsg"><p><?php echo $value ?></p></div><?php
        }
    }
    if (isset($messages['warning'])) {
        foreach ($messages['warning'] as $key => $value) {
            ?>
            <div class="message warning"><p><?php echo $value ?></p></div><?php
        }
    }
    if (isset($messages['info'])) {
        foreach ($messages['info'] as $key => $value) {
            ?>
            <div class="message info"><p><?php echo $value ?></p></div><?php
        }
    }
    if (isset($messages['success'])) {
        foreach ($messages['success'] as $key => $value) {
            ?>
            <div class="message success"><p><?php echo $value ?></p></div><?php
        }
    }
}

?>