/**
 * Event listeners
 */

// Routing
window.onhashchange = Robot.route;

// Back to Dash
document.getElementById("back").addEventListener("click",function(){ location.hash = ''; });

// Click scene
$(document).on("click","[data-scene]",function(){
    Robot.runScene($(this));
})

// Light on/off
$(document).on("click","[data-relay]",function(){
    id = $(this).data('device-id')
    type = $(this).data('type')
    if($(this).data("relay") == 'on') {
        Robot.setDevice(id,type,1);
    } else {
        Robot.setDevice(id,type,0);
    }
})

// Stat change
$(document).on("change","[data-stat]",function(){
    id = $(this).data('stat')
    Robot.setDevice(id,'stat',$(this).val());
})