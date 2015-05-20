<?php $this->layout('layout', ['title' => 'Dashboard']) ?>

<?php $this->start('script') ?>
<script>
    Robot.shortcuts = <?php echo json_encode($shortcuts) ?>;
    Robot.rooms = <?php echo json_encode($rooms) ?>;
    Robot.scenes = '<?php echo json_encode($scenes) ?>';
    Robot.route();
</script>
<?php $this->stop() ?>

<div class="alert alert-success hidden"><?=$this->e($message)?></div>

<div id="dash">

    <h1>Dashboard</h1>

    <div class="panel panel-primary">
        <div class="panel-heading"><span class="glyphicon glyphicon-star"></span> Shortcuts</div>
        <div class="panel-body" id="dash-shortcuts">
        </div>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading"><span class="glyphicon glyphicon-home"></span> Rooms</div>
        <div class="panel-body" id="dash-rooms">
        </div>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading"><span class="glyphicon glyphicon-asterisk"></span> Heating modes</div>
        <div class="panel-body" id="dash-heating">
        </div>
    </div>

    <div class="panel panel-warning hidden" id="battery-panel">
        <div class="panel-heading"><span class="glyphicon glyphicon-exclamation-sign"></span> Battery alerts</div>
        <div class="panel-body" id="battery-wrap">
        </div>
    </div>
</div>

<div id="room">
    <p class="go-back pull-right"><button class="btn btn-primary" type="button" id="back"><span class="glyphicon glyphicon-triangle-left"></span> Back</button></p>

    <h1 id="room-name"></h1>

    <div id="lights">
        <h2 class="page-header">Lights</h2>
        <div id="lights-devices"></div>
    </div>

    <div id="climate">
        <h2 class="page-header">Climate</h2>
        <div id="climate-devices"></div>
    </div>
</div>