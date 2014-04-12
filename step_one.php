<?php

function step1(){
?>
<div class="block" style="margin: 10px 20px 25px 0px; padding-bottom: 0px;">
    <div class="block_head">
        <div class="bheadl"></div>
        <div class="bheadr"></div>
        <h2 style="margin: 0;">Coursefinderdemo Course Import System</h2>
    </div>
    <div class="block_content">
        <!--<p class="breadcrumb"><a href="#"><strong>Step One</strong></a> &raquo; <a href="#">Step Two</a></p>-->
        <form id="course_import_step_one" name="course_import_step_one" onsubmit="return validate_form_step_1();"
              enctype="multipart/form-data" method="post" action="">
            <input type="hidden" name="step" value="step1"/>
            <input type="hidden" id="uploaded_file_name" name="uploaded_file_name" value=""/>

            <h3 style="text-decoration: underline;">Step One</h3>
            <h4>Few instruction messages goes here</h4>

            <p>
                <label for="file_type">Your file type: </label>
                <select id="file_type" class="styled" name="file_type">
                    <option selected="selected" value="">None</option>
                    <option value="xlsx">Microsoft Excel 2007</option>
                    <option value="xls">Microsoft Excel 2003</option>
                    <option value="csv">CSV File</option>
                </select>
            </p>
            <p class="fileupload">
                <label>Your file: </label><br/>
                <input id="fileupload" type="file"/>
                <span id="uploadmsg">Max size depends on your server uploading configuration.</span>
            </p>
            <hr/>
            <p>
                <input type="submit" class="submit small" value="Submit"/>
            </p>
        </form>
    </div>
    <div class="bendl"></div>
    <div class="bendr"></div>
    <div class="clear"></div>
</div>
<?php
}
?>