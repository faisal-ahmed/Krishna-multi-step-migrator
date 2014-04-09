function validate_form_step_1(){
    var uploadedFile = document.getElementById("uploaded_file_name").value;

    if (uploadedFile == '') {
        alert("Please upload your data file first.");
        $('.fileupload .file').addClass('redBorder');
        return false;
    }

    return true;
}