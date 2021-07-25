$(document).ready(function () {
    $("#cardForm").submit(function () {
        if ($("#nameInput").val() == "" || $("#cardInput").val() == "" || 
            $("#cvvInput").val() == "" || $("#myInput").val() == "")
        {
            alert("Fill all the fields, please");
            return false;
        }

        return true;
    });
});