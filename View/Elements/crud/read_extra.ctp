<?php // Loop over the types of associations
$properties = array(
    'hasOne' => __d('admin', 'Has One'),
    'hasMany' => __d('admin', 'Has Many'),
    'hasAndBelongsToMany' => __d('admin', 'Has and Belongs to Many')
);

foreach ($properties as $property => $title) {
    $associations = array();

    foreach ($model->{$property} as $alias => $assoc) {
        if ($property === 'hasOne') {
            $foreignModel = $this->Admin->introspect($assoc['className']);

            if (!empty($result[$alias][$foreignModel->primaryKey])) {
                $associations[$alias] = $assoc;
            }
        } else {
            $associations[$alias] = $assoc;
        }
    }

    if ($associations) { ?>

        <div class="associations">
            <h3><?php echo $title; ?></h3>

            <?php // Loop over the model relations
            foreach ($associations as $alias => $assoc) {
                echo $this->element('Admin.crud/' . Inflector::underscore($property), array(
                    'alias' => $alias,
                    'assoc' => $assoc,
                    'results' => $result[$alias]
                ));
            } ?>
        </div>

<?php } } ?>