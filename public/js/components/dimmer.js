function Dimmer(values) {
    Device.call(this,values)
}

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
        '</div>'
    appendTo.append(str);
    this.sliderObject = $("#device"+this.id).slider();
    var resId = this.id;
    this.sliderObject.on("slideStop",function(res){
        Robot.setDevice(resId,'dimmer',res.value);
    });
}

Dimmer.prototype.setPending = function(values) {
    $('[data-device-id="'+this.id+'"]').attr("disabled","disabled");
    this.sliderObject.slider("disable");
    $('#device-wrap'+this.id).addClass("pending");
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