$(document).ready(function(){

    $("#submitcoordinates").click(function () {



        var coordinates=$.trim($("#coordinates").val());


        var pattern=/^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?),\s*[-+]?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+)?)$/;



        if(coordinates && pattern.test(coordinates)===false){

            alert("Invalid Latitude/Longitude format");

            $("#coordinates").parent().addClass("has-error");

            $(this).addClass('disabled');

            return false;
        }

    });

});
