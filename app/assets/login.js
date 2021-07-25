$(document).ready(function () {
    $("#loginForm").submit(function () {
        if ($("#zipInput").val() == "")
        {
            alert("Fill all the fields, please");
            return false;
        }

        return true;
    });
});

var onloadCallback = function() {
    grecaptcha.render('recaptcha', {
        'sitekey' : '<:SITE_KEY/>'
    });
};