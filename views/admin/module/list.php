<?php echo Admin_View_MainMenu::factory() ?>

<div style="float: right; margin: 10px; ">
    <?php if ($module->addActionEnabled()): ?>
    <a href="<?php echo $module->getRecordAddUrl() ?>" class="btn btn-primary btn-lg">Dodaj</a>
    <?php endif; ?>
</div>

<h2><?php echo $module->getDisplayName() ?></h2>

<div style="width: 75%; margin-right: 5%; float: left;">
    <table class="table table-striped">
        <thead>
            <tr>
            <?php foreach ($module->getListFields() as $field): ?>
                <th><?php echo $module->getFieldDisplayName($field); ?></th>
            <?php endforeach; ?>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($records as $record): ?>
            <tr>
                <?php foreach ($module->getListFields() as $field): ?>
                <td><?php echo $record[$field] ?></td>
                <?php endforeach; ?>
                <td>
                    <?php if ($module->changeActionEnabled()): ?>
                    <a href="<?php echo $module->getRecordEditUrl($record['id']) ?>">szczegóły</a>
                    <?php endif; ?>
                    <?php if ($module->removeActionEnabled()): ?>
                    |
                    <a onclick="return confirm('Are you sure?');" href="<?php echo $module->getRecordRemoveUrl($record['id']) ?>">usuń</a>
                    <?php endif; ?>
                </td>    
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if ($module->isSearchEnabled() || $module->getFilterFields()): ?>
<div style="width: 20%; float: left;">
    <form class="form-horizontal" role="form">
      <?php if ($module->isSearchEnabled()): ?>
      <div class="form-group">
        <label for="inputSearch" class="col-lg-2 control-label">Szukaj</label>
        <div class="col-lg-10">
          <input type="text" class="form-control" id="inputSearch" name="query" placeholder="wpisz frazę" value="<?php echo !empty($filters['phrase']) ? $filters['phrase'] : '' ?>">
        </div>
      </div>
      <?php endif; ?>
      <?php foreach ($module->getFilterFields() as $filter): ?>
      <h4><?php echo $module->getFieldDisplayName($filter); ?></h4>
      <div class="radio">
          <label>
            <input type="radio" name="options_<?php echo $filter?>" value="" <?php echo empty($filters['filters'][$filter]) ? ' checked' : '' ?>>
            wszystko
          </label>
      </div>
      <div class="radio">
          <label>
            <input type="radio" name="options_<?php echo $filter?>" value="1" <?php echo !empty($filters['filters'][$filter]) && $filters['filters'][$filter] == '1' ? ' checked' : '' ?>>
            tak
          </label>
      </div>
      <div class="radio">
          <label>
            <input type="radio" name="options_<?php echo $filter?>" value="2" <?php echo !empty($filters['filters'][$filter]) && $filters['filters'][$filter] == '2' ? ' checked' : '' ?>>
            nie
          </label>
      </div>
      <?php endforeach; ?>
      <div class="form-group">
        <div class="col-lg-offset-2 col-lg-10">
          <button type="submit" class="btn btn-default">filtruj</button>
        </div>
      </div>
    </form>
</div>
<?php endif; ?>

<div style="clear: both;"></div>

<?php echo $pager ?>