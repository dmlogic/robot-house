var Device = function(values) {
    this.setValues(values);
}
Device.prototype.setValues = function(values) {
    this.id = values.id;
    this.state = values.state;
}

Device.prototype.setPending = function(values) {
}
Device.prototype.showPendingMessge = function(values) {
    str = '<div class="alert alert-success alert-dismissible" role="alert">'+
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
            'Device change is pending'+
          '</div>'
    $("#device-wrap"+this.id+" .device-title").after(str);
    setTimeout(function(){
        $(".alert").fadeOut( "slow", function() {
            $(this).remove();
        });
    },2000);
}