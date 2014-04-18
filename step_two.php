<?php

function step2($messages = array(), $matching = array())
{
    $conversion = new CsvConversion();
    $uploadedFileColumn = $conversion->parse_csv_column();
    if ($conversion->count_rows() > MAX_ALLOWED_ROWS_PER_BATCH) {
        $warning = 'Caution! Your file contains more than ' . MAX_ALLOWED_ROWS_PER_BATCH . ' rows of data. Remember only first 250 rows will be considered as input and the rest will be ignored. To upload more courses, please split your file where each file should not contain more than 250 rows of data. Thanks for your understanding.';
    }
    $csv_column_name = $conversion->parse_csv_column('database_column.csv', true);
    global $required;
    $mendatoryArray = array();
    $file_column_key = '';
    $instruction = "The below mentioned columns must be mapped. Please use a column in the import file for these fields.<br/>";
    $column = '';
    foreach ($csv_column_name as $key2 => $value2) {
        if (in_array($key2, $required)) {
            $mendatoryArray[$key2] = "{$key2}__{$value2}";
            $column .= ($column == '') ? " \"$value2\"" : ", \"$value2\"";
        }
    }
    $instruction .= "$column.";
    $messages['info'][] = 'Please map each column only once.';
    $messages['info'][] = $instruction;
    if (isset($warning)) {
        $messages['warning'][] = $warning;
    }
    ?>
    <div class="block" style="margin: 10px 20px 25px 0px; padding-bottom: 0px;">
        <div class="block_head">
            <div class="bheadl"></div>
            <div class="bheadr"></div>
            <h2 style="margin: 0;">Coursefinderdemo Course Importer Mapping</h2>

            <h2 style="margin: 0; float: right;"><a href="#" onclick="location.reload(true);">Back to Step one</a></h2>
        </div>
        <div class="block_content">
            <h3 style="text-decoration: underline;">Step Two</h3>

            <h2>Map your column names with the appropriate column names of the database file that you
                import.</h2>

            <?php messages($messages); ?>
            <?php if (count($uploadedFileColumn) > 0) { ?>
                <form id="course_import_step_two" name="course_import_step_two"
                      onsubmit="return validate_form_step_2();" method="post" action="">
                    <input type="hidden" name="step" value="step2"/>
                    <input type="hidden" name="column_matching[account_id]" value="1"/><!--For now hardcoded-->

                    <?php foreach ($uploadedFileColumn as $key => $value) {
                        $file_column_key .= ($file_column_key == '') ? $value : ",$value"; ?>
                        <p style="display: inline-block;">
                            <label for="<?php echo $value ?>">Map Field: <?php echo $value ?></label>
                            <select id="<?php echo $value ?>" class="styled"
                                    name="column_matching[<?php echo $value ?>]">
                                <option value="">None</option>
                                <?php foreach ($csv_column_name as $key2 => $value2) { ?>
                                    <option <?php if (isset($matching[$value]) && $matching[$value] == $key2) {
                                        echo 'selected="selected"';
                                    } ?> value="<?php echo $key2 ?>"><?php echo $value2 ?></option>
                                <?php } ?>
                            </select>
                        </p>
                    <?php } ?>
                    <hr/>
                    <p>
                        <input type="hidden" name="columnKey" id="columnKey"
                               value="<?php echo $file_column_key; ?>"/>
                        <input type="hidden" name="mendatoryArray" id="mendatoryArray"
                               value="<?php echo implode(",", $mendatoryArray); ?>"/>
                        <input type="submit" class="submit small" value="Submit"/>
                    </p>
                </form>
            <?php } ?>

        </div>
        <div class="bendl"></div>
        <div class="bendr"></div>
        <div class="clear"></div>
    </div>
<?php
}