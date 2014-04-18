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
    var selectedOptions = [], twiceSelected = [];

    for (var i = 0; i < mendatoryArray.length; i++) {
        var flag = 0, find = mendatoryArray[i].substr(0, mendatoryArray[i].indexOf("__"));
        var name = mendatoryArray[i].substr(mendatoryArray[i].indexOf("__") + 2);
        for (var j = 0; j < columnKey.length; j++) {
            var e = document.getElementById(columnKey[j]);
            var idVal = e.options[e.selectedIndex].value, idText = e.options[e.selectedIndex].text;
            if (idVal == '') {
                continue;
            }
            if (!i && $.inArray(idText, selectedOptions) === -1) {
                selectedOptions.push(idText);
            } else if (!i && $.inArray(idText, twiceSelected) === -1){
                twiceSelected.push(idText);
            }
            if (idVal === find){
                flag = 1;
                if (i) break;
            }
        }
        if (flag === 0) {
            alert(name + ' is required. Please use a column in the import file for this field.');
            return false;
        }
    }

    if (twiceSelected.length > 0) {
        var alertMsg = (twiceSelected.length == 1) ? ' column is' : ' columns are';
        alert(twiceSelected + alertMsg + ' mapped twice. Please map each column only once.');
        return false;
    }

    return true;
}