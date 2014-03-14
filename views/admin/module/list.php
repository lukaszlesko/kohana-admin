<?php echo Admin_View_MainMenu::factory() ?>

<style>
.field_boolean {
    width: 80px;
}
.field_datetime {
    width: 100px;
}
.field_primary {
    width: 50px;
}
.field_actions {
    width: 100px;
}
</style>

<div style="float: right; margin: 10px; ">
    <?php if ($module->addActionEnabled()): ?>
    <a href="<?php echo $module->getRecordAddUrl() ?>" class="btn btn-primary btn-lg">Dodaj</a>
    <?php endif; ?>
</div>

<h2><?php echo $module->getDisplayName() ?></h2>

<div style="width: 75%; margin-right: 5%; float: left;">
    <?php if ($module->getListActions()): ?>
    <form method="post" class="form-horizontal" role="form">
        <input type="hidden" name="list_action" value="1">
        <div class="form-group">
        <label for="inputSearch" class="col-lg-2 control-label">Akcja</label>
        <div class="col-lg-10">
          <select class="form-control" name="action">
              <?php foreach ($module->getListActions() as $action): ?>
              <option value="<?php echo $action->getId() ?>"><?php echo $action->getDisplayName() ?></option>
              <?php endforeach; ?>
          </select>
          <button type="submit" class="btn btn-default">wykonaj</button>
        </div>
      </div>
    </form>
    <?php endif; ?>
    
    <table class="table table-striped">
        <thead>
            <tr>
            <?php foreach ($module->getListFields() as $field): ?>
                <th class="field_<?php echo $module->getFieldType($field); ?>"><?php echo $module->getFieldDisplayName($field); ?></th>
            <?php endforeach; ?>
                <th class="field_actions"></th>
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
      <h4><?php echo $module->getFieldDisplayName($filter['name']); ?></h4>
      <?php if ($filter['type'] == 'boolean'): ?>
          <div class="radio">
              <label>
                <input type="radio" name="options_<?php echo $filter['name'] ?>" value="" <?php echo empty($filters['filters'][$filter['name']]) ? ' checked' : '' ?>>
                wszystko
              </label>
          </div>
          <div class="radio">
              <label>
                <input type="radio" name="options_<?php echo $filter['name']?>" value="1" <?php echo !empty($filters['filters'][$filter['name']]) && $filters['filters'][$filter['name']] == '1' ? ' checked' : '' ?>>
                tak
              </label>
          </div>
          <div class="radio">
              <label>
                <input type="radio" name="options_<?php echo $filter['name']?>" value="2" <?php echo !empty($filters['filters'][$filter['name']]) && $filters['filters'][$filter['name']] == '2' ? ' checked' : '' ?>>
                nie
              </label>
          </div>
      <?php elseif ($filter['type'] == 'foreign'): ?>
          <div class="radio">
              <label>
                <input type="radio" name="foreign_<?php echo $filter['name'] ?>" value="" <?php echo empty($filters['filters'][$filter['name']]) ? ' checked' : '' ?>>
                wszystko
              </label>
          </div>
          <?php foreach ($filter['choices'] as $choice): ?>
          <div class="radio">
              <label>
                <input type="radio" name="foreign_<?php echo $filter['name'] ?>" value="<?php echo $choice['id'] ?>" <?php echo !empty($filters['filters'][$filter['name']]) && $filters['filters'][$filter['name']] == $choice['id'] ? ' checked' : '' ?>>
                <?php echo $choice['display'] ?>
              </label>
          </div>
          <?php endforeach; ?>
      <?php elseif ($filter['type'] == 'choices'): ?>
          <div class="radio">
              <label>
                <input type="radio" name="options_<?php echo $filter['name'] ?>" value="" <?php echo empty($filters['filters'][$filter['name']]) ? ' checked' : '' ?>>
                wszystko
              </label>
          </div>
          <?php foreach ($filter['choices'] as $choice): ?>
          <div class="radio">
              <label>
                <input type="radio" name="options_<?php echo $filter['name'] ?>" value="<?php echo $choice['id'] ?>" <?php echo !empty($filters['filters'][$filter['name']]) && $filters['filters'][$filter['name']] == $choice['id'] ? ' checked' : '' ?>>
                <?php echo $choice['display'] ?>
              </label>
          </div>
          <?php endforeach; ?>
      <?php endif; ?>
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