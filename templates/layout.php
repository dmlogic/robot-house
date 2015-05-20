<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=$this->e($title)?></title>

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/styles.css" rel="stylesheet">
    <link href="/css/bootstrap-slider.css" rel="stylesheet">

  </head>
  <body>

    <div class="container">
        <?=$this->section('content')?>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <script src="/js/bootstrap-slider.min.js"></script>
    <script src="/js/components/device.js"></script>
    <script src="/js/components/dimmer.js"></script>
    <script src="/js/components/relay.js"></script>
    <script src="/js/components/shortcut.js"></script>
    <script src="/js/components/battery.js"></script>
    <script src="/js/components/thermostat.js"></script>
    <script src="/js/components/app.js"></script>

    <?=$this->section('script')?>
  </body>
</html>