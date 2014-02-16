<?php // Display HABTM fields
$habtm = array();

foreach ($model->hasAndBelongsToMany as $alias => $assoc) {
    if ($assoc['showInForm']) {
        $habtm[$alias] = $assoc;
    }
}

if ($habtm) { ?>

    <fieldset>
        <legend><?php echo __d('admin', 'Associate With'); ?></legend>

        <?php foreach ($habtm as $alias => $assoc) {
            $assoc['type'] = 'relation';
            $assoc['title'] = $alias;
            $assoc['habtm'] = true;

            echo $this->element('Admin.input', array(
                'field' => $alias,
                'data' => $assoc
            ));
        } ?>
    </fieldset>

<?php } ?>