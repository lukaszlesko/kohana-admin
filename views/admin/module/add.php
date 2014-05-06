<?php echo Admin_View_MainMenu::factory() ?>

<div style="float: right; margin: 10px; ">
    <a href="<?php echo $module->getListUrl() ?>" class="btn btn-primary btn-lg">Powrót do listy (bez zapisu)</a>
</div>

<h2><?php echo $module->getDisplayName() ?></h2>

<?php if ($form['state'] == 'errors'): ?>
    <div class="alert alert-danger">
        <?php if ($form['global_error']): ?>
        <?php echo $form['global_error'] ?>
        <?php else: ?>
        Uzupełnij poprawnie pola oznaczone na czerwono
        <?php endif; ?>
    </div>
<?php endif; ?>

<form class="form-horizontal" role="form" method="post" enctype="multipart/form-data">
    <input type="hidden" name="add_form" value="1">
    <?php foreach ($module->getFields() as $fieldName => $field): ?>
    <div class="form-group<?php echo !empty($form['errors'][$fieldName]) ? ' has-error' : '' ?>">
        <?php if ($field['type'] == 'serial'): ?>
        <label for="input<?php echo $fieldName ?>" class="col-lg-2 control-label"><?php echo $module->getFieldDisplayName($fieldName) ?></label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo !empty($record['id']) ? $record['id'] : '-' ?></p>
        </div>
        <?php elseif ($field['type'] == 'youtube'): ?>
        <label for="input<?php echo $fieldName ?>" class="col-lg-2 control-label"><?php echo $module->getFieldDisplayName($fieldName) ?></label>
        <div class="col-lg-10">
            <p>
                <?php if (!empty($form['data'][$fieldName])): ?>
                <iframe id="ytplayer" type="text/html" width="500" height="390"
                  src="http://www.youtube.com/embed/<?php echo $form['data'][$fieldName] ?>?autoplay=0"
                  frameborder="0"></iframe>
                <?php endif; ?>
            </p>
        </div>
        <?php elseif ($field['type'] == 'image'): ?>
        <label for="input<?php echo $fieldName ?>" class="col-lg-2 control-label"><?php echo $module->getFieldDisplayName($fieldName) ?></label>
        <div class="col-lg-10">
            <p>
                <?php if (!empty($form['data'][$fieldName])): ?>
                aktualne: <img src="<?php echo URL::base() ?>upload/<?php echo $form['data'][$fieldName] ?>" width="100">
                <?php endif; ?>
                
                <input type="file" class="form-control" name="<?php echo $fieldName ?>" id="input<?php echo $fieldName ?>">
            </p>
        </div>
        <?php elseif (isset($field['editable']) && $field['editable'] === false): ?>
        <label for="input<?php echo $fieldName ?>" class="col-lg-2 control-label"><?php echo $module->getFieldDisplayName($fieldName) ?></label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo !empty($record[$fieldName]) ? $record[$fieldName] : '-' ?></p>
        </div>
        <?php elseif ($field['type'] == 'char'): ?>
        <label for="input<?php echo $fieldName ?>" class="col-lg-2 control-label"><?php echo $module->getFieldDisplayName($fieldName) ?></label>
        <div class="col-lg-10">
            <input type="text" class="form-control" value="<?php echo !empty($form['data'][$fieldName]) ? $form['data'][$fieldName] : '' ?>" name="<?php echo $fieldName ?>" id="input<?php echo $fieldName ?>" placeholder="<?php echo $module->getFieldDisplayName($fieldName) ?>">
        </div>
        <?php elseif ($field['type'] == 'text'): ?>
        <label for="input<?php echo $fieldName ?>" class="col-lg-2 control-label"><?php echo $module->getFieldDisplayName($fieldName) ?></label>
        <div class="col-lg-10">
            <textarea name="<?php echo $fieldName ?>" id="input<?php echo $fieldName ?>" class="form-control" rows="3"><?php echo !empty($form['data'][$fieldName]) ? $form['data'][$fieldName] : '' ?></textarea>
        </div>
        <?php elseif ($field['type'] == 'datetime'): ?>
        <label for="input<?php echo $fieldName ?>" class="col-lg-2 control-label"><?php echo $module->getFieldDisplayName($fieldName) ?></label>
        <div class="col-lg-10">
            <input type="datetime" class="form-control" value="<?php echo !empty($form['data'][$fieldName]) ? $form['data'][$fieldName] : '' ?>" name="<?php echo $fieldName ?>" id="input<?php echo $fieldName ?>" placeholder="<?php echo $module->getFieldDisplayName($fieldName) ?>">
        </div>
        <?php elseif ($field['type'] == 'boolean'): ?>
        <div class="col-lg-offset-2 col-lg-10">
          <div class="checkbox">
            <label>
              <input value="1" <?php echo !empty($form['data'][$fieldName]) ? ' checked="checked" ' : '' ?> type="checkbox" name="<?php echo $fieldName ?>" id="input<?php echo $fieldName ?>"> <?php echo $module->getFieldDisplayName($fieldName) ?>
            </label>
          </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
  <div class="form-group">
    <div class="col-lg-offset-2 col-lg-10">
      <button type="submit" class="btn btn-default">Zapisz</button>
    </div>
  </div>
</form>
