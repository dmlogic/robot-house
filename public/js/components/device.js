var Device = function(values) {
    this.setValues(values);
}
Device.prototype.setValues = function(values) {
    this.id = values.id;
    this.state = values.state;
}