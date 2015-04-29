var Device = function(id,state) {
    this.id = id;
    this.setState(state);
}
Device.prototype.setState = function(value) {
    this.state = value;
}