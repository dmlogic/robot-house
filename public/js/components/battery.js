function Battery(values) {
    Device.call(this,values)
}

Battery.prototype = Object.create(Device.prototype);
Battery.prototype.constructor = Battery;

Battery.prototype.render = function(appendTo) {
    str = '<p>'+this.desc+'</p>'+this.markup();
    appendTo.append(str);
}

Battery.prototype.markup = function() {
    return '<div class="progress">'+
            '<div class="progress-bar progress-bar-striped progress-bar-'+this.bar_class+'" style="width: '+this.level+'%;">'+this.level+'%</div>'+
        '</div>';
}

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
}