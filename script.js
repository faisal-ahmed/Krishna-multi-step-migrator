function validate_form_step_1(){
    var uploadedFile = document.getElementById("uploaded_file_name").value;

    if (uploadedFile == '') {
        alert("Please upload your data file first.");
        $('.fileupload .file').addClass('redBorder');
        return false;
    }

    return true;
}

function validate_form_step_2(){
    var columnKey = document.getElementById("columnKey").value.split(",");
    var mendatoryArray = document.getElementById("mendatoryArray").value.split(",");

    for (var i = 0; i < mendatoryArray.length; i++) {
        var flag = 0, find = mendatoryArray[i].substr(0, mendatoryArray[i].indexOf("__"));
        var name = mendatoryArray[i].substr(mendatoryArray[i].indexOf("__") + 2);
        for (var j = 0; j < columnKey.length; j++) {
            var e = document.getElementById(columnKey[j]);
            var idVal = e.options[e.selectedIndex].value;
            if (idVal === find){
                flag = 1;
                break;
            }
        }
        if (flag === 0) {
            alert(name + ' is required. Please use a column in the import file for this field.');
            return false;
        }
    }

    return true;
}