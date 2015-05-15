<?php $this->layout('layout', ['title' => 'Dashboard']) ?>

<?php if($message) :?>
    <div class="alert alert-success" role="alert"><?=$this->e($message)?></div>
<?php endif; ?>

<h1>Dashboard</h1>

<div class="panel panel-primary">
    <div class="panel-heading"><span class="glyphicon glyphicon-star"></span> Shortcuts</div>
    <div class="panel-body">
        <a class="btn btn-default" href="#">Arm doors <span class="glyphicon glyphicon-play"></span></a>
        <a class="btn btn-success" href="#"><span class="glyphicon glyphicon-ok"></span> Disarm doors</a>
        <a class="btn btn-default" href="#">Hot water boost <span class="glyphicon glyphicon-play"></span></a>
    </div>
</div>

<div class="panel panel-primary">
    <div class="panel-heading"><span class="glyphicon glyphicon-home"></span> Rooms</div>
    <div class="panel-body">
        <a class="btn btn-default" href="#">Lounge</a>
        <a class="btn btn-default" href="#">Hallway</a>
        <a class="btn btn-default" href="#">Outside</a>
    </div>
</div>

<div class="panel panel-primary">
    <div class="panel-heading"><span class="glyphicon glyphicon-asterisk"></span> Heating modes</div>
    <div class="panel-body">
        <a class="btn btn-default" href="#">Evening <span class="glyphicon glyphicon-play"></a>
        <a class="btn btn-success" href="#"><span class="glyphicon glyphicon-ok"></span> Daytime</a>
        <a class="btn btn-default" href="#">Night <span class="glyphicon glyphicon-play"></a>
        <a class="btn btn-default" href="#">Cold weekend <span class="glyphicon glyphicon-play"></a>
        <a class="btn btn-default" href="#">Cold working day <span class="glyphicon glyphicon-play"></a>
    </div>
</div>

<div class="panel panel-warning">
    <div class="panel-heading"><span class="glyphicon glyphicon-exclamation-sign"></span> Battery alerts</div>
    <div class="panel-body">
        <p>Bathroom Thermostat</p>
        <div class="progress">
            <div class="progress-bar progress-bar-striped progress-bar-danger" style="width: 10%;">10%</div>
        </div>
        <p>Bedroom Radiator</p>
        <div class="progress">
            <div class="progress-bar progress-bar-striped progress-bar-warning" style="width: 20%;">20%</div>
        </div>
    </div>
</div>