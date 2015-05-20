function Thermostat(values) {
    Device.call(this,values)
}

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
}

Thermostat.prototype.setPending = function(values) {
    $('[data-stat="'+this.id+'"]').attr("disabled","disabled");
    this.sliderObject.slider("disable");
    $('#device-wrap'+this.id).addClass("pending");
}

Thermostat.prototype.setValues = function(values) {
    this.id = values.device_id;
    this.label = values.name;
    this.level = values.state;
    this.current = values.current;
    this.type = values.type;
    this.battery = values.battery_level;
}

Thermostat.prototype.currentTemp = function(value) {
    if(this.type == 'rad') {
        return '';
    }
    return '<div class="col-xs-4"><p class="temperature text-center">'+this.current+'&deg;</p></div>';
}