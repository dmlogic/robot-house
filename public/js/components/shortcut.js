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