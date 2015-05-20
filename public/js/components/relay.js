function Relay(values) {
    Device.call(this,values)
}

Relay.prototype = Object.create(Device.prototype);
Relay.prototype.constructor = Relay;

Relay.prototype.render = function(appendTo) {
    onClass = (this.status == 'on') ? 'success' : 'default';
    offClass = (this.status == 'on') ? 'default' : 'danger';
    str = '<div class="well device-wrap">'+
            '<h3 class="device-title">'+this.label+'</h3>'+
            '<button class="btn btn-'+onClass+'">On</button>'+
            '<button class="btn btn-'+offClass+'">Off</button>'+
            '</p>'+
        '</div>'
    appendTo.append(str);
}

Relay.prototype.setValues = function(values) {
    this.id = values.device_id;
    this.label = values.name;
    this.level = values.state;
    this.status = 'off';
    if(values.state > 0) {
        this.status = 'on';
    }
}

Relay.prototype.changeDeviceValue = function(value) {
    $("#device"+this.id).val(this.state);
}