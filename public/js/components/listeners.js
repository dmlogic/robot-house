window.onhashchange = Robot.route;

// Back to Dash
document.getElementById("back").addEventListener("click",function(){ location.hash = ''; });

// Refresh
document.getElementById("refresh").addEventListener("click",Robot.refresh);

// Click scene
$(document).on("click","[data-scene]",function(ev){
    ev.preventDefault();
    Robot.runScene($(this));
});

// Light on/off
$(document).on("click","[data-relay]",function(){
    Robot.setDevice($(this).data('device-id'),$(this).data('type'),$(this).data("relay"));
});

// Stat change
$(document).on("change","[data-stat]",function(){
    Robot.setDevice($(this).data('stat'),'stat',$(this).val());
});

// Boost
$(document).on("submit","#boost-form",function(ev){
    ev.preventDefault();
    console.log("boosting");
    if(!Robot.booster) {
        return;
    }
    Robot.boost();
});