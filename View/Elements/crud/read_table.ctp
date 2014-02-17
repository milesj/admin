<table class="table">
    <tbody>
        <?php foreach ($model->fields as $field => $data) { 
            if (in_array($field, $model->admin['hideReadFields'])) {
                continue;
            }
            ?>
            <tr>
                <td class="span-2">
                    <b><?php echo $data['title']; ?></b>
                </td>
                <td>
                    <?php echo $this->element('Admin.field', array(
                        'result' => $result,
                        'field' => $field,
                        'data' => $data,
                        'value' => $result[$model->alias][$field],
                        'model' => $model
                    )); ?>
                </td>
            </tr>

        <?php } ?>
    </tbody>
</table>