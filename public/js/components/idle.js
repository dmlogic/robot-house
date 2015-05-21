(function() {
    var checkIdle = function(){
        idleTime = idleTime + 1;
        if(idleTime > 10) {
            Robot.reload();
            idleTime = 0;
        }
    };
    var idleTime = 0;
    var idleInterval = setInterval(checkIdle, 1000);

    document.addEventListener("mousemove", function(){idleTime = 0;});
    document.addEventListener("keypress",  function(){idleTime = 0;});
    document.addEventListener("touchend",  function(){idleTime = 0;});
})();