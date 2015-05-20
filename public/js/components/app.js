var Robot = function() { }

Robot.route = function() {
    if(location.hash) {
        Robot.room();
        return;
    } else {
        Robot.dash();
    }
}

Robot.dashRendered = false;
Robot.devices = {};

Robot.room = function() {

    $("#dash").addClass("hidden");
    $("#room").removeClass("hidden");
    var lWrap = $("#lights-devices");
    var cWrap = $("#climate-devices");
    var lights = false;
    var heating = false;

    function clearRoom() {
        $("#lights, #climate").hide();
        lWrap.html("");
        cWrap.html("");
    }

    function drawRoom(room) {
        $("#room-name").text(room.name);
        for(var devId in room.devices) {
            devName = 'device'+room.devices[devId].device_id;

            switch(room.devices[devId].type) {
                case 'dimmer':
                    Robot.devices[devName] = new Dimmer(room.devices[devId]);
                    Robot.devices[devName].render(lWrap);
                    lights = true;
                    break;
                case 'light':
                    Robot.devices[devName] = new Relay(room.devices[devId]);
                    Robot.devices[devName].render(lWrap);
                    lights = true;
                    break;
                case 'rad':
                case 'stat':
                    Robot.devices[devName] = new Thermostat(room.devices[devId]);
                    Robot.devices[devName].render(cWrap);
                    heating = true;
                    break;
            }
        }

        if(lights) {
            $("#lights").show();
        }
        if(heating) {
            $("#climate").show();
        }

    }

    clearRoom();
    name = location.hash.replace('#/room/','');
    room = Robot.rooms[name];
    if(typeof room == "undefined") {
        location.hash = '';
        return;
    }
    drawRoom(room);
}

Robot.dash = function() {

    $("#room").addClass("hidden");
    $("#dash").removeClass("hidden");

    if(this.dashRendered) {
        return;
    }

    this.dashRendered = true;

    var scWrap = $("#dash-shortcuts");

    $.each(Robot.shortcuts.shortcut,function(i,s){
        sc = new Shortcut(s);
        sc.render(scWrap);
    })
    var rWrap = $("#dash-rooms");
    $.each(Robot.shortcuts.room,function(i,s){
        sc = new Shortcut(s);
        sc.render(rWrap);
    })
    var rWrap = $("#dash-heating");
    $.each(Robot.shortcuts.heating,function(i,s){
        sc = new Shortcut(s);
        sc.render(rWrap);
    })

    var batteryDevices = [];

    function renderBatteries() {
        var bWrap = $("#battery-wrap");
        var collected = [];
        $.each(Robot.rooms,function(i,room) {
            var roomName = room.name;
            for(var devId in room.devices) {
                if(room.devices[devId].is_battery === true && room.devices[devId].battery_level < 50) {
                    room.devices[devId].room_name = roomName;
                    collected.push([room.devices[devId].battery_level,roomName+': '+room.devices[devId].name]);
                }
            }
        })
        if(!collected) {
            return false;
        }
        collected.sort(function(a, b) {return a[0] - b[0]});
        for(i = 0 ; i < collected.length ; i++) {
            b = new Battery(collected[i]);
            b.render(bWrap);
        }
        return true;
    }

    if(renderBatteries()) {
        $("#battery-panel").removeClass("hidden");
    }
}

window.onhashchange = Robot.route;
document.getElementById("back").addEventListener("click",function(){ location.hash = ''; });

/*
var myDimmer = new Dimmer(1,50);
    myDimmer.render($(".container"));
    $("#button").on("click",function(){
        // console.log(myDimmer);
        myDimmer.setState(45);
    })
    myDimmer.setState("changed");
    console.log(myDimmer.state);
    */