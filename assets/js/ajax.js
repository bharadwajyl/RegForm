//Variable declaration
var serialize, url, temp;

//Validation
function Validate(type){
    url = "root.php";
    if (type == "2") {type = "otp";}
    if (type == "1"){ if ($("#otp").val().length<=3) {return 1;} } else {$("#"+type+"_btn").html("Sending...").delay(1000).show("fast", "easing");}
    switch(type){
        case "otp":
            serialize =  "&email="+$("#email").val()+"&FormType="+type;
            break;
        case "registration":
            if (document.getElementById('terms').checked === false) { popup("failure: Check our terms & conditions");
            $("#registration_btn").html("Register").delay(1000).show("fast", "easing"); return 1; }
            serialize = new FormData($("#"+type+"_form")[0]);
            serialize.append('FormType', type);
            $.ajax({
                type: "POST",
                url: url,
                data: serialize,
                cache: false,
                contentType: false,
                processData: false,
                success: function(result){
                    $("#registration_btn").html("Register").delay(1000).show("fast", "easing");
                    popup(result);
                }
            });
            break;
        case 1:
            serialize =  "&email="+$("#email").val()+"&otp="+$("#otp").val()+"&FormType=otp_validation";
            break;
        default:
            break;
    }
    
    $.ajax({
        type: "POST",
        url: url,
        data: serialize,
        success: function(result){
            if (type != "1") {$("#"+type+"_btn").hide().html("Send OTP").fadeIn('slow');}
            if (result.match(/otp_btn/g)){ countdown(); }
            if (result.match(/Verified/g)) {
                setTimeout(function() {
                    document.getElementById("otp_section").style.display ="none";      
                 }, 5000);
            }
            result.match(/verify_otp: /g) ?
            $("#otp_section").hide().html(result.replace(/verify_otp:/g," ")).fadeIn('slow') :
            popup(result);
        }
    });
}



function countdown() {
    var seconds = 59;
    function tick() {
    var counter = document.getElementById("counter");
    seconds--;
    $('#otp_btn').html("0:" + (seconds < 10 ? "0" : "") + String(seconds));
        if (seconds > 0) {
            setTimeout(tick, 1000);
        } else {
            $('#otp_btn').html("Resend");
            $('#otp_btn').addClass('btn1').removeClass('btn2'); $("#otp_btn").removeClass("none");
        }
    }
    tick();
}
