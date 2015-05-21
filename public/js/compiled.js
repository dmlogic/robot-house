var Device = function(values) {
    this.setValues(values);
};

Device.prototype.setValues = function(values) {
    this.id = values.id;
    this.state = values.state;
};

Device.prototype.setPending = function(values) {
};

Device.prototype.showPendingMessge = function(values) {
    if(typeof this.battery !== "undefined") {
        msg = 'Waiting for device to wake up to apply change';
    } else {
        msg = 'Device change in progress';
    }
    str = '<div class="alert alert-success alert-dismissible" role="alert">'+
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
            msg+
          '</div>';
    $("#device-wrap"+this.id).append(str);
    setTimeout(function(){
        $(".alert").fadeOut( "slow", function() {
            $(this).remove();
        });
    },2000);
};
var Dimmer = function(values) {
    Device.call(this,values);
};

Dimmer.prototype = Object.create(Device.prototype);
Dimmer.prototype.constructor = Dimmer;

Dimmer.prototype.render = function(appendTo) {
    onClass = (this.status == 'on') ? 'success' : 'default';
    offClass = (this.status == 'on') ? 'default' : 'danger';
    str = '<div class="well device-wrap" data-device-type="dimmer" id="device-wrap'+this.id+'">'+
            '<h3 class="device-title">'+this.label+'</h3>'+
            '<p>'+
            '<input id="device'+this.id+'" name="device'+this.id+'" data-slider-id="device'+this.id+'" type="text" data-slider-min="0" data-slider-max="100" data-slider-step="5" data-slider-value="'+this.level+'"/>'+
            '</p>'+
            '<p>'+
            '<button class="btn btn-'+onClass+'" data-light="on" data-device-id="'+this.id+'">On</button>'+
            '<button class="btn btn-'+offClass+'" data-light="off" data-device-id="'+this.id+'">Off</button>'+
            '</p>'+
        '</div>';
    appendTo.append(str);
    this.sliderObject = $("#device"+this.id).slider();
    var resId = this.id;
    this.sliderObject.on("slideStop",function(res){
        Robot.setDevice(resId,'dimmer',res.value);
    });
};

Dimmer.prototype.setPending = function(values) {
    $('[data-device-id="'+this.id+'"]').attr("disabled","disabled");
    this.sliderObject.slider("disable");
    $('#device-wrap'+this.id).addClass("pending");
};

Dimmer.prototype.setValues = function(values) {
    this.id = values.device_id;
    this.label = values.name;
    this.level = values.state;
    this.status = 'off';
    if(values.state > 0) {
        this.status = 'on';
    }
};
var Relay = function(values) {
    Device.call(this,values);
};

Relay.prototype = Object.create(Device.prototype);
Relay.prototype.constructor = Relay;

Relay.prototype.render = function(appendTo) {
    onClass = (this.status == 'on') ? 'success' : 'default';
    offClass = (this.status == 'on') ? 'default' : 'danger';
    str = '<div class="well device-wrap" data-device-type="'+this.type+'" id="device-wrap'+this.id+'">'+
            '<h3 class="device-title">'+this.label+'</h3>'+
            '<button class="btn btn-'+onClass+'" data-relay="1" data-type="'+this.type+'" data-device-id="'+this.id+'">On</button>'+
            '<button class="btn btn-'+offClass+'" data-relay="0" data-type="'+this.type+'" data-device-id="'+this.id+'">Off</button>'+
            '</p>'+
        '</div>';
    appendTo.append(str);
};

Relay.prototype.setPending = function(values) {
    $('[data-device-id="'+this.id+'"]').attr("disabled","disabled");
    $('#device-wrap'+this.id).addClass("pending");
};

Relay.prototype.setValues = function(values) {
    this.id = values.device_id;
    this.label = values.name;
    this.type = values.type;
    if(this.type == 'hvac') {
        this.status = (values.state == 'Off') ? 'off' : 'on';
    } else {
        this.status = (values.state > 0) ? 'on' : 'off';
    }
};
var Battery = function(values) {
    Device.call(this,values);
};

Battery.prototype = Object.create(Device.prototype);
Battery.prototype.constructor = Battery;

Battery.prototype.render = function(appendTo) {
    str = '<p>'+this.desc+'</p>'+this.markup();
    appendTo.append(str);
};

Battery.prototype.markup = function() {
    return '<div class="progress">'+
            '<div class="progress-bar progress-bar-striped progress-bar-'+this.bar_class+'" style="width: '+this.level+'%;">'+this.level+'%</div>'+
        '</div>';
};

Battery.prototype.setValues = function(values) {
    this.desc = values[1];
    this.level = values[0];
    if(this.level <=10) {
        this.bar_class = 'danger';
    } else if(this.level <= 20) {
        this.bar_class =  'warning';
    } else if(this.level <= 50) {
        this.bar_class =  'default';
    } else {
        this.bar_class =  'success';
    }
};
var Thermostat = function(values) {
    Device.call(this,values);
};

Thermostat.prototype = Object.create(Device.prototype);
Thermostat.prototype.constructor = Thermostat;

Thermostat.prototype.render = function(appendTo) {

    bat = new Battery([this.battery,this.label]);

    str =  '<div class="well device-wrap" data-type="stat" id="device-wrap'+this.id+'">'+
                '<h3 class="device-title">'+this.label+'</h3>'+
                '<div class="row">'+
                    '<div class="col-xs-8">'+
                        '<input id="device'+this.id+'" name="device'+this.id+'" data-slider-id="device'+this.id+'" type="text" data-slider-min="5" data-slider-max="25" data-slider-step="1"  data-slider-value="'+this.level+'"/>'+
                    '</div>'+
                 this.currentTemp()+
                '</div>'+
                '<div class="row">'+
                    '<div class="col-xs-8">'+
                        '<input type="number" class="form-control" min="5" max="25" data-stat="'+this.id+'" value="'+this.level+'">'+
                    '</div>'+
                    '<div class="col-xs-4">'+
                        '<p>Battery</p>'+
                        bat.markup()+
                    '</div>'+
                '</div>'+
            '</div>';

    if(this.type == 'rad') {
        appendTo.append(str);
    } else {
        appendTo.prepend(str);
    }

    this.sliderObject = $("#device"+this.id).slider();
    var resId = this.id;
    this.sliderObject.on("slideStop",function(res){
        Robot.setDevice(resId,'stat',res.value);
    });
};

Thermostat.prototype.setPending = function(values) {
    $('[data-stat="'+this.id+'"]').attr("disabled","disabled");
    this.sliderObject.slider("disable");
    $('#device-wrap'+this.id).addClass("pending");
};

Thermostat.prototype.setValues = function(values) {
    this.id = values.device_id;
    this.label = values.name;
    this.level = values.state;
    this.current = values.current;
    this.type = values.type;
    this.battery = values.battery_level;
};

Thermostat.prototype.currentTemp = function(value) {
    if(this.type == 'rad') {
        return '';
    }
    return '<div class="col-xs-4"><p class="temperature text-center">'+this.current+'&deg;</p></div>';
};
var Shortcut = function(values) {
    Device.call(this,values);
};

Shortcut.prototype = Object.create(Device.prototype);
Shortcut.prototype.constructor = Shortcut;

Shortcut.prototype.render = function(appendTo) {
    classState = 'btn-default';
    readyIcon = '';
    playIcon = '';
    href = '#';
    dataLink = '';
    if(this.type == 'room') {
        href = '/#/room/'+this.slug;
    } else {
        dataLink = 'data-scene="'+this.scene+'"';
        if(this.active) {
            readyIcon = '<span class="glyphicon glyphicon-ok"></span> ';
            classState = 'btn-success';
        } else {
             playIcon = ' <span class="glyphicon glyphicon-play">';
        }
    }
    str = '<a class="btn btn-shortcut '+classState+'" id="shortcut'+this.id+'"href="'+href+'" '+dataLink+'>'+readyIcon+this.desc+playIcon+'</span></a>';
    appendTo.append(str);
};

Shortcut.prototype.setValues = function(values) {
    this.id = values.id;
    this.type = values.type;
    if(this.type == 'room') {
        this.desc = values.room_name;
        this.slug = values.room_slug;
        this.active = null;
    } else {
        this.scene = values.scene_number;
        this.desc = values.scene_name;
        this.active = values.active;
    }
};
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
    var hWrap          = $("#dash-heating");
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
        hWrap.html("");
        bWrap.html("");
    }

    function drawShortcuts() {
        $.each(Robot.shortcuts.shortcut,function(i,s){ createShortcut(s,scWrap); });
        $.each(Robot.shortcuts.room,function(i,s){ createShortcut(s,rWrap); });
        $.each(Robot.shortcuts.heating,function(i,s){ createShortcut(s,hWrap); });
    }

    function renderHeatingStatus() {
        str = '<hr><div class="row">';
        $.each(Robot.rooms.services.devices,function(i,s){
            cls = (s.state == 'Off') ? 'info' : 'danger';
            lbl = (s.state == 'Off') ? 'Off' : 'On';
            str += '<div class="col-xs-6"><p>'+s.name+': <span class="label label-'+cls+'">'+lbl+'</label></p></div>';
        });

        str += '</div>';
        hWrap.append(str);
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
    renderHeatingStatus();
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