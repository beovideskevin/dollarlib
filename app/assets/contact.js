$(document).ready(function () {
    $("#contactForm").submit(function () {
        if ($("#nameInput").val() == "" || $("#emailInput").val() == "" ||
            $("#subjectInput").val() == "" || $("#messageInput").val() == "")
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