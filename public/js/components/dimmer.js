function Dimmer(values) {
    Device.call(this,values)
}

Dimmer.prototype = Object.create(Device.prototype);
Dimmer.prototype.constructor = Dimmer;

Dimmer.prototype.render = function(appendTo) {
    onClass = (this.status == 'on') ? 'success' : 'default';
    offClass = (this.status == 'on') ? 'default' : 'danger';
    str = '<div class="well device-wrap" data-device-type="dimmer">'+
            '<h3 class="device-title">'+this.label+'</h3>'+
            '<p>'+
            '<input id="device'+this.id+'" name="device'+this.id+'" data-slider-id="device'+this.id+'" type="text" data-slider-min="0" data-slider-max="100" data-slider-step="5" data-slider-value="'+this.level+'"/>'+
            '</p>'+
            '<p>'+
            '<button class="btn btn-'+onClass+'">On</button>'+
            '<button class="btn btn-'+offClass+'">Off</button>'+
            '</p>'+
        '</div>'
    appendTo.append(str);
    this.sliderObject = $("#device"+this.id).slider();
}

Dimmer.prototype.setValues = function(values) {
    this.id = values.device_id;
    this.label = values.name;
    this.level = values.state;
    this.status = 'off';
    if(values.state > 0) {
        this.status = 'on';
    }
}

Dimmer.prototype.changeDeviceValue = function(value) {
    $("#device"+this.id).val(this.state);
    this.sliderObject.slider('setValue',value);
}