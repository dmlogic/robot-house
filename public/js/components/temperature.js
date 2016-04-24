var Temperature = function(values) {
    Device.call(this,values);
};

Temperature.prototype = Object.create(Device.prototype);
Temperature.prototype.constructor = Temperature;

Temperature.prototype.render = function(appendTo) {

    bat = new Battery([this.battery,this.label]);

    str =  '<div class="well device-wrap" data-type="stat" id="device-wrap'+this.id+'">'+
                '<h3 class="device-title">'+this.label+'</h3>'+
                '<div class="row">'+
                    this.currentTemp()+
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
};

Temperature.prototype.setValues = function(values) {
    this.id = values.device_id;
    this.label = values.name;
    this.level = values.state;
    this.current = values.current;
    this.type = values.type;
    this.battery = values.battery_level;
};

Temperature.prototype.currentTemp = function(value) {
    return '<div class="col-xs-6 col-xs-offset-2"><p class="temperature">'+this.current+'&deg;</p></div>';
};