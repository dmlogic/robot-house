<?php $this->layout('layout', ['title' => 'Dashboard']) ?>

<?php $this->start('script') ?>
<script>
    Robot.shortcuts = '<?php echo json_encode($shortcuts) ?>';
    Robot.rooms = '<?php echo json_encode($rooms) ?>';
    Robot.heating = '<?php echo json_encode($heating) ?>';
    Robot.dash();
</script>
<?php $this->stop() ?>

<?php if($message) :?>
    <div class="alert alert-success" role="alert"><?=$this->e($message)?></div>
<?php endif; ?>

<h1>Dashboard</h1>

<div class="panel panel-primary">
    <div class="panel-heading"><span class="glyphicon glyphicon-star"></span> Shortcuts</div>
    <div class="panel-body">
        <a class="btn btn-default btn-shortcut" href="#">Arm doors <span class="glyphicon glyphicon-play"></span></a>
        <a class="btn btn-success btn-shortcut" href="#"><span class="glyphicon glyphicon-ok"></span> Disarm doors</a>
        <a class="btn btn-default btn-shortcut" href="#">Hot water boost <span class="glyphicon glyphicon-play"></span></a>
    </div>
</div>
<script>
 var
</script>

<div class="panel panel-primary">
    <div class="panel-heading"><span class="glyphicon glyphicon-home"></span> Rooms</div>
    <div class="panel-body">
        <a class="btn btn-default btn-shortcut" href="room/lounge">Lounge</a>
        <a class="btn btn-default btn-shortcut" href="#">Hallway</a>
        <a class="btn btn-default btn-shortcut" href="#">Outside</a>
    </div>
</div>

<div class="panel panel-primary">
    <div class="panel-heading"><span class="glyphicon glyphicon-asterisk"></span> Heating modes</div>
    <div class="panel-body">
        <a class="btn btn-default btn-shortcut" href="#">Evening <span class="glyphicon glyphicon-play"></span></a>
        <a class="btn btn-success btn-shortcut" href="#"><span class="glyphicon glyphicon-ok"></span> Daytime</a>
        <a class="btn btn-default btn-shortcut" href="#">Night <span class="glyphicon glyphicon-play"></span></a>
        <a class="btn btn-default btn-shortcut" href="#">Cold weekend <span class="glyphicon glyphicon-play"></span></a>
        <a class="btn btn-default btn-shortcut" href="#">Cold working day <span class="glyphicon glyphicon-play"></span></a>
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