<div style="max-width: 300px; margin: 0 auto;">

<h2>Log in</h2>

<?php if ($form['state'] == 'errors'): ?>
    <div class="alert alert-danger">
        <?php if ($form['global_error']): ?>
        <?php echo $form['global_error'] ?>
        <?php else: ?>
        Wpisz login oraz hasło
        <?php endif; ?>
    </div>
<?php endif; ?>

<form role="form" method="post">
    <input type="hidden" name="login_form" value="1">
  <div class="form-group<?php echo !empty($form['errors']['username']) ? ' has-error' : '' ?>">
    <label for="exampleInputUsername1">Username</label>
    <input type="text" name="username" class="form-control" id="exampleInputUsername1" placeholder="Enter username" value="<?php echo !empty($form['data']['username']) ? $form['data']['username'] : '' ?>">
  </div>
  <div class="form-group<?php echo !empty($form['errors']['password']) ? ' has-error' : '' ?>">
    <label for="exampleInputPassword1">Password</label>
    <input type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
  </div>
  <button type="submit" class="btn btn-default">Submit</button>
</form>

</div>