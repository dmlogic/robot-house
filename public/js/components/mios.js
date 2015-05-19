$.support.cors = true;
var Mios = function() {
    var server = 'http://192.168.1.104:3480/';
    checkLocal = function() {

        var xmlhttp;
        xmlhttp = new XMLHttpRequest();

https://fwd8.mios.com/millers/monkies12/35107098/data_request?id=lu_status2&DeviceNum=18

        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == XMLHttpRequest.DONE ) {
               if(xmlhttp.status == 200){
                   console.log( xmlhttp.responseText );
               }
               else if(xmlhttp.status == 400) {
                   console.log('There was an error 400')
               }
               else {
                    console.log('something else other than 200 was returned')
               }
            }
        }

        xmlhttp.open("GET", "http://192.168.1.104:3480/data_request?id=lu_status2", true);
        xmlhttp.send();
        // $.ajax({
        //     type: 'GET',
        //     // data: ['foo','baa'],
        //     url: "http://192.168.1.104:3480/data_request?id=lu_status2",
        //     // url: "https://sta1.mios.com/locator_json.php",
        //     success: function(data){
        //         console.log("success");
        //         console.log(data);
        //     },
        //     crossDomain: true,
        //     error:function(err,b) {
        //         console.log("error");
        //         console.log(err);
        //     }
        // }).done(function(){
        //     console.log("done");
        // });
    }
    checkLocal();
}
Mios();