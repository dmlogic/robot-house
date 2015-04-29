<?php $this->layout('layout', ['title' => 'Please login']) ?>

<?php if($message) :?>
    <div class="alert alert-danger" role="alert"><?=$this->e($message)?></div>
<?php endif; ?>

<form method="post" action="login">
  <div class="form-group">
    <label for="username">Username</label>
    <input type="text" class="form-control" id="username" name="username">
  </div>
  <div class="form-group">
    <label for="password">Password</label>
    <input type="password" class="form-control" id="password" name="password">
  </div>
  <button type="submit" class="btn btn-default">Submit</button>
</form>