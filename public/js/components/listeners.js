window.onhashchange = Robot.route;

// Back to Dash
document.getElementById("back").addEventListener("click",function(){ location.hash = ''; });

// Click scene
$(document).on("click","[data-scene]",function(){
    Robot.runScene($(this));
})

// Light on/off
$(document).on("click","[data-relay]",function(){
    Robot.setDevice($(this).data('device-id'),$(this).data('type'),$(this).data("relay"));
})

// Stat change
$(document).on("change","[data-stat]",function(){
    Robot.setDevice($(this).data('stat'),'stat',$(this).val());
})