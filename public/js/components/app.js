var Robot = function() { };

Robot.devices = {};
Robot.shorcutsRendered = {};
Robot.reloading = null;

/**
 * Routing between room and dashboard
 */
Robot.route = function() {
    if(location.hash) {
        Robot.room();
    } else {
        Robot.dash();
    }

    window.scrollTo(0,0);
};

/**
 * Display of a room
 */
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
                case 'temp':
                    Robot.devices[devName] = new Temperature(room.devices[devId]);
                    Robot.devices[devName].render(cWrap);
                    heating = true;
                    break;
                case 'hvac':
                    Robot.devices[devName] = new Relay(room.devices[devId]);
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
};

Robot.showMessage = function(msg) {
    alert(msg);
};

/**
 * Display of dash
 */
Robot.dash = function() {

    $("#room").addClass("hidden");
    $("#dash").removeClass("hidden");

    var scWrap         = $("#dash-shortcuts");
    var rWrap          = $("#dash-rooms");
    var bWrap          = $("#battery-wrap");
    var batteryDevices = [];

    function createShortcut(s,wrapper) {
        sName = 'shortcut'+s.id;
        Robot.shorcutsRendered[sName] = new Shortcut(s);
        Robot.shorcutsRendered[sName].render(wrapper);
    }

    function clearDash() {
        scWrap.html("");
        rWrap.html("");
        bWrap.html("");
    }

    function drawShortcuts() {
        $.each(Robot.shortcuts.shortcut,function(i,s){ createShortcut(s,scWrap); });
        $.each(Robot.shortcuts.room,function(i,s){ createShortcut(s,rWrap); });
        $.each(Robot.shortcuts.heating,function(i,s){ createShortcut(s,radWrap); });
    }

    function renderBatteries() {
        var collected = [];
        $.each(Robot.rooms,function(i,room) {
            var roomName = room.name;
            for(var devId in room.devices) {
                if(room.devices[devId].is_battery === true && room.devices[devId].battery_level <= 20) {
                    room.devices[devId].room_name = roomName;
                    collected.push([room.devices[devId].battery_level,roomName+': '+room.devices[devId].name]);
                }
            }
        });
        if(!collected.length) {
            return false;
        }
        collected.sort(function(a, b) {return a[0] - b[0];});
        for(i = 0 ; i < collected.length ; i++) {
            b = new Battery(collected[i]);
            b.render(bWrap);
        }
        return true;
    }

    clearDash();
    drawShortcuts();
    if(renderBatteries()) {
        $("#battery-panel").removeClass("hidden");
    }
};

Robot.refreshData = function(data) {
    Robot.rooms = data.rooms;
    Robot.shortcuts = data.shortcuts;
};

Robot.runScene = function(el) {

    var sc = Robot.shorcutsRendered[el.attr("id")];

    function setPending(el) {
        if(el.hasClass('btn-success')) {
            return;
        }
        el.addClass("scene-pending");
    }

    setPending(el);

    $.ajax({
      type: "POST",
      url: '/run-scene',
      data: 'scene='+el.data('scene')
    }).done(function(resp) {
        Robot.refreshData(resp);
    }).fail(function(resp) {
        Robot.showMessage("Scene could not be run");
        $(".scene-pending").removeClass('scene-pending');
    }).always(function(){
        Robot.route();
    });
};

Robot.setDevice = function(id,type,value) {

    var devName = 'device'+room.devices[id].device_id;
    var pending = false;
    Robot.devices[devName].setPending();

    $.ajax({
      type: "POST",
      url: '/set-device',
      data: {"id":id,"type":type,"value":value}
    }).done(function(resp,stat,xhr) {
        setTimeout(Robot.reload, 1000);
    }).fail(function() {
        Robot.showMessage("Device could not be set");
    }).always(function(){
        Robot.route();
        Robot.devices[devName].showPendingMessge();
    });
};

Robot.boost = function() {

    $("#boost").addClass("scene-pending");
    data = $('input[name=Setpoint]').val()+'|'+$('select[name=Duration]').val();

    $.ajax({
      type: "POST",
      url: '/set-device',
      data: {"id":Robot.booster,"type":"boost","value":data}
    }).done(function(resp,stat,xhr) {
        setTimeout(Robot.reload, 1000);
    }).always(function(){
        $("#boost").removeClass("scene-pending");
    });
};

Robot.reload = function(){

    if(Robot.reloading) {
        Robot.reloading.abort();
    }

    Robot.reloading = $.ajax({
                          type: "GET",
                          url: '/refresh'
                        }).done(function(resp) {
                            Robot.reloading = null;
                            Robot.refreshData(resp);
                            Robot.route();
                        });

    return Robot.reloading;
};

Robot.refresh = function(){
    $('.data-refresh').addClass("pending").attr("disabled","disabled");
    Robot.reload().always(function(){
        $('.data-refresh').removeClass("pending").removeAttr("disabled");
    });
};