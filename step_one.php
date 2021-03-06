<?php

function step1($messages = null){
    if ($_SERVER['HTTP_HOST'] == 'localhost') {
        $url = "/" . basename(__DIR__) . '/utils_conversion.php?get_sample=download_now';
    } else {
        // TODO: Change This URL
        $url = '/wp-content/plugins/' . basename(__DIR__) . '/utils_conversion.php?get_sample=download_now';
    }
    ?>
    <script type="text/javascript">
        function download_csv(){
            var file_download = $.post( "", {'get_sample': 'download_now'}, function( data ) {
                var iframe = document.getElementById("download-container");
                if (iframe === null)
                {
                    iframe = document.createElement('iframe');
                    iframe.id = "download-container";
                    iframe.style.visibility = 'hidden';
                    document.body.appendChild(iframe);
                }
                iframe.src = "<?php echo $url ?>";
            });
        }
    </script>
<div class="block" style="margin: 10px 20px 25px 0px; padding-bottom: 0px;">
    <div class="block_head">
        <div class="bheadl"></div>
        <div class="bheadr"></div>
        <h2 style="margin: 0;">Coursefinderdemo Course Import System</h2>
    </div>
    <div class="block_content">
        <!--<p class="breadcrumb"><a href="#"><strong>Step One</strong></a> &raquo; <a href="#">Step Two</a></p>-->
        <?php messages($messages); ?>
        <h4>Please <a href="#" onclick="download_csv()">download the sample file</a> to match with your file first.</h4>
        <form id="course_import_step_one" name="course_import_step_one" onsubmit="return validate_form_step_1();"
              enctype="multipart/form-data" method="post" action="">
            <input type="hidden" name="step" value="step1"/>
            <input type="hidden" id="uploaded_file_name" name="uploaded_file_name" value=""/>
            <!--<input type="hidden" id="account_id" name="account_id" value="<?php /*echo sess_getAccountIdFromSession(); */?>"/>-->
            <input type="hidden" id="account_id" name="account_id" value="1"/>

            <h3 style="text-decoration: underline;">Step One</h3>
            <div class="message info"><p>Please select Microsoft Excel or CSV type file only.</p></div>

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