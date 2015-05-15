<?php $this->layout('layout', ['title' => $title]) ?>

<?php $this->start('script') ?>
<script>
    $("#ex1,#ex2").slider();
</script>
<?php $this->stop() ?>


<?php if($message) :?>
    <div class="alert alert-success" role="alert"><?=$this->e($message)?></div>
<?php endif; ?>

<p class="go-back pull-right"><a href="/" class="btn btn-primary"><span class="glyphicon glyphicon-triangle-left"></span> Back</a></p>
<h1><?php echo $title ?></h1>

<h2 class="page-header">Lights</h2>

<div class="well device-wrap">
    <h3 class="device-title">Ceiling lights</h3>
    <p>
        <input id="ex1" data-slider-id='ex1Slider' type="text" data-slider-min="0" data-slider-max="100" data-slider-step="5" data-slider-value="25"/>
    </p>
    <p>
        <button class="btn btn-success">On</button>
        <button class="btn btn-default">Off</button>
    </p>
</div>

<div class="well device-wrap">
    <h3 class="device-title">Shelf lights</h3>
    <p>
        <button class="btn btn-success">On</button>
        <button class="btn btn-default">Off</button>
    </p>
</div>

<h2 class="page-header">Climate</h2>

<div class="well device-wrap">
    <h3 class="device-title">Room temperature</h3>
    <div class="row">
        <div class="col-xs-8">
            <input id="ex2" data-slider-id='ex2Slider' type="text" data-slider-min="5" data-slider-max="25" data-slider-step="1" data-slider-value="16"/>
        </div>
        <div class="col-xs-4">
            <p class="temperature text-center">16&deg;</p>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-8">
            <input type="number" class="form-control" value="16">
        </div>
        <div class="col-xs-4">
            <p>Battery</p>
            <div class="progress">
                <div class="progress-bar progress-bar-striped progress-bar-warning" style="width: 20%;"></div>
            </div>
        </div>
    </div>
</div>

<div class="well device-wrap">
    <h3 class="device-title">Large Radiator</h3>
    <div class="row">
        <div class="col-xs-8">
            <input id="ex2" data-slider-id='ex2Slider' type="text" data-slider-min="5" data-slider-max="25" data-slider-step="1" data-slider-value="16"/>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-8">
            <input type="number" class="form-control" value="16">
        </div>
        <div class="col-xs-4">
            <p>Battery</p>
            <div class="progress">
                <div class="progress-bar progress-bar-striped progress-bar-danger" style="width: 10%;"></div>
            </div>
        </div>
    </div>
</div>

