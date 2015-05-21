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
    },1000);
};