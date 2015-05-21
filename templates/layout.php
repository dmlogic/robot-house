<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=$this->e($title)?></title>

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
    <?php if(ENVIRONMENT === 'local') :?>
    <link href="/css/components/styles.css" rel="stylesheet">
    <link href="/css/components/bootstrap-slider.css" rel="stylesheet">
    <?php else : ?>
    <link href="/css/compiled.min.css?v=<?php echo ASSETS_VERSION ?>" rel="stylesheet">
    <?php endif; ?>

  </head>
  <body>

    <div class="container">
        <?=$this->section('content')?>
    </div>

    <?=$this->section('script')?>
  </body>
</html>