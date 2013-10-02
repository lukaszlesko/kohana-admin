<script>
// auto logout
setTimeout(function() {
    top.location.href = '<?php echo URL::base() ?>admin/logout';
}, <?php echo $config['auto_logout_time_miliseconds'] ?>);
</script>

<nav class="navbar navbar-default" role="navigation">
  <!-- Brand and toggle get grouped for better mobile display -->
  <div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
    <a class="navbar-brand" href="<?php echo URL::base() ?>admin/index">Admin</a>
  </div>

  <!-- Collect the nav links, forms, and other content for toggling -->
  <div class="collapse navbar-collapse navbar-ex1-collapse">
    <ul class="nav navbar-nav">
        <?php foreach ($modules as $module): ?>
        <li><a href="<?php echo $module->getListUrl() ?>"><?php echo $module->getDisplayName() ?></a></li>
        <?php endforeach; ?>
    </ul>
    <ul class="nav navbar-nav navbar-right">
      <li><a href="<?php echo URL::base() ?>admin/logout">Wyloguj się</a></li>
    </ul>
  </div><!-- /.navbar-collapse -->
</nav>