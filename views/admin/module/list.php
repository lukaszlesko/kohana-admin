<?php echo Admin_View_MainMenu::factory() ?>

<div style="float: right; margin: 10px; ">
    <?php if ($module->addActionEnabled()): ?>
    <a href="<?php echo $module->getRecordAddUrl() ?>" class="btn btn-primary btn-lg">Dodaj</a>
    <?php endif; ?>
</div>

<h2><?php echo $module->getDisplayName() ?></h2>

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

<?php echo $pager ?>