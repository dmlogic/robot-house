function Dimmer(id,state) {
    Device.call(this,id,state)
}

Dimmer.prototype = Object.create(Device.prototype);
Dimmer.prototype.constructor = Dimmer;

Dimmer.prototype.render = function(appendTo) {
    str = '<div class="slider-wrap">'+
          '<input name="device'+this.id+'" id="device'+this.id+'" value="'+this.state+'"/>'
          '</div>';
    appendTo.append(str);
    // this.sliderObject = $("#device"+this.id).slider();
}

Dimmer.prototype.setState = function(value) {
    this.state = value;
    $("#device"+this.id).val(value);
    // this.sliderObject.slider('setValue',value);
}

Dimmer.prototype.changeDeviceValue = function(value) {
}