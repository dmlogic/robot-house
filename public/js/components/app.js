var Robot = function() { }

Robot.dashRendered = false;
Robot.devices = {};
Robot.shorcutsRendered = {};


/**
 * Routing between room and dashboard
 */
Robot.route = function() {
    if(location.hash) {
        Robot.room();
        return;
    } else {
        Robot.dash();
    }
}

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
}

Robot.showMessage = function(msg) {
    alert(msg);
}

/**
 * Display of dash
 */
Robot.dash = function() {

    $("#room").addClass("hidden");
    $("#dash").removeClass("hidden");

    if(this.dashRendered) {
        return;
    }

    this.dashRendered = true;

    function createShortcut(s,wrapper) {
        sName = 'shortcut'+s.id;
        Robot.shorcutsRendered[sName] = new Shortcut(s);
        Robot.shorcutsRendered[sName].render(wrapper);
    }

    var scWrap = $("#dash-shortcuts");
    $.each(Robot.shortcuts.shortcut,function(i,s){ createShortcut(s,scWrap) })

    var rWrap = $("#dash-rooms");
    $.each(Robot.shortcuts.room,function(i,s){ createShortcut(s,rWrap) })

    var hWrap = $("#dash-heating");
    $.each(Robot.shortcuts.heating,function(i,s){ createShortcut(s,hWrap) })

    function renderHeatingStatus() {
        str = '<hr><div class="row">';
        $.each(Robot.rooms.services.devices,function(i,s){
            cls = (s.state == 'Off') ? 'info' : 'danger';
            lbl = (s.state == 'Off') ? 'Off' : 'On';
            str += '<div class="col-xs-6"><p>'+s.name+': <span class="label label-'+cls+'">'+lbl+'</label></p></div>';
        })

        str += '</div>';
        hWrap.append(str);
    }

    var batteryDevices = [];

    function renderBatteries() {
        var bWrap = $("#battery-wrap");
        var collected = [];
        $.each(Robot.rooms,function(i,room) {
            var roomName = room.name;
            for(var devId in room.devices) {
                if(room.devices[devId].is_battery === true && room.devices[devId].battery_level <= 20) {
                    room.devices[devId].room_name = roomName;
                    collected.push([room.devices[devId].battery_level,roomName+': '+room.devices[devId].name]);
                }
            }
        })
        if(!collected.length) {
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

    renderHeatingStatus();
}

Robot.refreshDash = function(data) {
    $("#dash-shortcuts, #dash-rooms, #dash-heating, #battery-wrap").html("");
    Robot.dash();
}

Robot.refreshData = function(data) {
    Robot.rooms = data.rooms;
    Robot.shortcuts = data.shortcuts;
    Robot.dashRendered = false;
}

Robot.refreshRoom = function(data) {
    $("#dash-shortcuts, #dash-rooms, #dash-heating, #battery-wrap, #lights-devices, #climate-devices").html("");
    Robot.room();
}

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
      data: 'scene='+el.data('scene'),
      success: function(resp) {
        Robot.refreshDash(resp);
      },
      error:function(resp) {
        Robot.showMessage("Scene could not be run");
        $(".scene-pending").removeClass('scene-pending');
      }
    }).always(function(){
        Robot.dash();
    });
}

Robot.setDevice = function(id,type,value) {

    var devName = 'device'+room.devices[id].device_id;
    Robot.devices[devName].setPending();

    $.ajax({
      type: "POST",
      url: '/set-device',
      data: {"id":id,"type":type,"value":value}
    }).done(function(resp,stat,xhr) {
        if(xhr.status == 202) {
            // Robot.showMessage("Request for value change to "+value+" is pending");
        }
        Robot.refreshData(resp);
    }).fail(function() {
        Robot.showMessage("Device could not be set");
    }).always(function(){
        Robot.refreshRoom();
    });

}
