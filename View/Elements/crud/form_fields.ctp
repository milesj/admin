<fieldset class="is-legendless">
    <?php // Loop over primary model fields
    foreach ($model->fields as $field => $data) {
        if (($this->action === 'create' && $field === $model->primaryKey) || in_array($field, $model->admin['hideFormFields'])) {
            continue;
        }

        if($field!='content') :
        echo $this->element('Admin.input', array(
            'field' => $field,
            'data' => $data
        ));
            elseif($field == 'content') :
               echo $this->Media->tinymce('content', array("label" => "Contenu","name"=>"data[Post][content]"));
        endif;
    
       
    } ?>
</fieldset>
