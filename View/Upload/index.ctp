<?php
$this->Breadcrumb->add(__d('admin', 'Upload'), array('controller' => 'upload', 'action' => 'index'));

if (CakePlugin::loaded('Uploader')) { ?>

<div class="title">
    <div class="action-buttons">
        <?php echo $this->Html->link('<span class="fa fa-upload"></span> ' . __d('admin', 'View All Uploads'),
            array('controller' => 'crud', 'action' => 'index', 'model' => 'admin.file_upload'),
            array('class' => 'button is-info', 'escape' => false)); ?>
    </div>

    <h2><?php echo $model->singularName; ?></h2>
</div>

<div class="container">
    <?php echo $this->Form->create($model->alias, array('class' => 'form--horizontal', 'type' => 'file')); ?>

    <div class="notice is-info">
        <?php echo __d('admin', 'Upload all types of files with no restrictions. Uploading also supports image transformation and remote transportation.'); ?>
        <a href="http://milesj.me/code/cakephp/admin#file-uploading" class="notice-link"><?php echo __d('admin', 'Learn more about file uploading.'); ?></a>
    </div>

    <div class="grid">
        <div class="col span-6">
            <?php
            echo $this->Form->input('path', array(
                'div' => 'field',
                'type' => 'file',
                'label' => array('class' => 'field-label col span-2', 'text' => __d('admin', 'File')),
                'class' => 'input'
            ));

            echo $this->Form->input('caption', array(
                'div' => 'field',
                'type' => 'textarea',
                'label' => array('class' => 'field-label col span-2', 'text' => __d('admin', 'Caption')),
                'class' => 'input span-8'
            )); ?>

            <fieldset>
                <legend><?php echo __d('admin', 'Transport To'); ?></legend>

                <?php
                echo $this->Form->input('FileUpload.transport.class', array(
                    'div' => 'field',
                    'label' => array('text' => __d('admin', 'Service'), 'class' => 'field-label col span-2'),
                    'data-target' => '.transport',
                    'class' => 'input',
                    'options' => array(
                        '' => __d('admin', 'None'),
                        's3' => __d('admin', 'AWS S3'),
                        'glacier' => __d('admin', 'AWS Glacier')
                    )
                )); ?>

                <div class="transport s3 glacier" style="display: none">
                    <?php
                    echo $this->Form->input('FileUpload.transport.accessKey', array(
                        'div' => 'field',
                        'label' => array('class' => 'field-label col span-2', 'text' => __d('admin', 'Access Key')),
                        'class' => 'input span-6'
                    ));

                    echo $this->Form->input('FileUpload.transport.secretKey', array(
                        'div' => 'field',
                        'label' => array('class' => 'field-label col span-2', 'text' => __d('admin', 'Secret Key')),
                        'class' => 'input span-6'
                    ));

                    echo $this->Form->input('FileUpload.transport.region', array(
                        'div' => 'field',
                        'label' => array('class' => 'field-label col span-2', 'text' => __d('admin', 'Region')),
                        'options' => array(
                            'us-east-1' => __d('admin', 'US East - Virginia'),
                            'us-west-1' => __d('admin', 'US West - California'),
                            'us-west-2' => __d('admin', 'US West 2 - Oregon'),
                            'eu-west-1' => __d('admin', 'EU West - Ireland'),
                            'ap-southeast-1' => __d('admin', 'AP Southeast - Singapore'),
                            'ap-southeast-2' => __d('admin', 'AP Southeast 2 - Sydney'),
                            'ap-northeast-1' => __d('admin', 'AP Northeast - Tokyo'),
                            'sa-east-1' => __d('admin', 'SA East - Sao Paulo'),
                        ),
                        'class' => 'input'
                    )); ?>
                </div>

                <div class="transport s3" style="display: none">
                    <?php
                    echo $this->Form->input('FileUpload.transport.bucket', array(
                        'div' => 'field',
                        'label' => array('class' => 'field-label col span-2', 'text' => __d('admin', 'Bucket')),
                        'class' => 'input'
                    ));

                    echo $this->Form->input('FileUpload.transport.folder', array(
                        'div' => 'field',
                        'label' => array('class' => 'field-label col span-2', 'text' => __d('admin', 'Folder')),
                        'after' => '<div class="field-help">' . __d('admin', 'Requires trailing slash') . '</div>',
                        'class' => 'input'
                    ));

                    echo $this->Form->input('FileUpload.transport.scheme', array(
                        'div' => 'field',
                        'label' => array('class' => 'field-label col span-2', 'text' => __d('admin', 'Scheme')),
                        'options' => array(
                            'https' => 'HTTPS',
                            'http' => 'HTTP'
                        ),
                        'class' => 'input'
                    ));
                    echo $this->Form->input('FileUpload.transport.acl', array(
                        'div' => 'field',
                        'label' => array('class' => 'field-label col span-2', 'text' => __d('admin', 'ACL')),
                        'options' => array(
                            'public-read' => __d('admin', 'Public Read'),
                            'public-read-write' => __d('admin', 'Public Read Write'),
                            'authenticated-read' => __d('admin', 'Authenticated Read'),
                            'bucket-owner-read' => __d('admin', 'Bucket Owner Read'),
                            'bucket-owner-full-control' => __d('admin', 'Bucket Owner Full Control'),
                            'private' => __d('admin', 'Private'),
                        ),
                        'class' => 'input'
                    )); ?>
                </div>

                <div class="transport glacier" style="display: none">
                    <?php
                    echo $this->Form->input('FileUpload.transport.vault', array(
                        'div' => 'field',
                        'label' => array('class' => 'field-label col span-2', 'text' => __d('admin', 'Vault')),
                        'class' => 'input'
                    ));

                    echo $this->Form->input('FileUpload.transport.accountId', array(
                        'div' => 'field',
                        'label' => array('class' => 'field-label col span-2', 'text' => __d('admin', 'Account ID')),
                        'class' => 'input'
                    )); ?>
                </div>
            </fieldset>
        </div>

        <div class="col span-6">
            <?php
            foreach (array(
                'path_large' => __d('admin', 'Resized'),
                'path_thumb' => __d('admin', 'Thumbnail')
            ) as $field => $title) {
                $currentValue = !empty($this->request->data['FileUpload']['transforms'][$field]['method']) ? $this->request->data['FileUpload']['transforms'][$field]['method'] : 'resize'; ?>

            <fieldset>
                <legend><?php echo $title; ?></legend>

                <div class="field">
                    <div class="field-col col span-8 push-2">
                        <?php
                        echo $this->Form->input('FileUpload.transforms.' . $field . '.transform', array(
                            'div' => 'checkbox',
                            'label' => __d('admin', 'Generate image'),
                            'type' => 'checkbox',
                            'checked' => true
                        )); ?>
                    </div>
                </div>

                <?php
                echo $this->Form->input('FileUpload.transforms.' . $field . '.method', array(
                    'div' => 'field',
                    'label' => array('class' => 'field-label col span-2', 'text' => __d('admin', 'Method')),
                    'data-target' => '.transform',
                    'options' => array(
                        'crop' => __d('admin', 'Crop'),
                        'resize' => __d('admin', 'Resize'),
                        'flip' => __d('admin', 'Flip'),
                        'scale' => __d('admin', 'Scale'),
                        'rotate' => __d('admin', 'Rotate'),
                        'fit' => __d('admin', 'Fit')
                    ),
                    'class' => 'input'
                ));

                echo $this->Form->input('FileUpload.transforms.' . $field . '.prepend', array(
                    'div' => 'field',
                    'label' => array('class' => 'field-label col span-2', 'text' => __d('admin', 'Prepend')),
                    'class' => 'input'
                ));

                echo $this->Form->input('FileUpload.transforms.' . $field . '.append', array(
                    'div' => 'field',
                    'label' => array('class' => 'field-label col span-2', 'text' => __d('admin', 'Append')),
                    'class' => 'input'
                ));

                echo $this->Form->input('FileUpload.transforms.' . $field . '.transportDir', array(
                    'div' => 'field',
                    'label' => array('class' => 'field-label col span-2', 'text' => __d('admin', 'Folder')),
                    'after' => '<div class="field-help">' . __d('admin', 'Requires trailing slash') . '</div>',
                    'class' => 'input'
                )); ?>

                <div class="transform resize crop fit">
                    <?php
                    echo $this->Form->input('FileUpload.transforms.' . $field . '.width', array(
                        'div' => 'field',
                        'label' => array('class' => 'field-label col span-2', 'text' => __d('admin', 'Width')),
                        'class' => 'input span-1'
                    ));

                    echo $this->Form->input('FileUpload.transforms.' . $field . '.height', array(
                        'div' => 'field',
                        'label' => array('class' => 'field-label col span-2', 'text' => __d('admin', 'Height')),
                        'class' => 'input span-1'
                    )); ?>
                </div>

                <div class="transform resize"<?php if ($currentValue !== 'resize') echo ' style="display: none"'; ?>>
                    <?php
                    echo $this->Form->input('FileUpload.transforms.' . $field . '.mode', array(
                        'div' => 'field',
                        'label' => array('class' => 'field-label col span-2', 'text' => __d('admin', 'Mode')),
                        'options' => array(
                            'width' => __d('admin', 'Width'),
                            'height' => __d('admin', 'Height')
                        ),
                        'class' => 'input'
                    )); ?>

                    <div class="field">
                        <div class="field-col col span-8 push-2">
                            <?php echo $this->Form->input('FileUpload.transforms.' . $field . '.expand', array(
                                'div' => 'checkbox',
                                'label' => __d('admin', 'Allow greater than current dimension'),
                                'type' => 'checkbox'
                            )); ?>
                        </div>
                    </div>

                    <div class="field">
                        <div class="field-col col span-8 push-2">
                            <?php echo $this->Form->input('FileUpload.transforms.' . $field . '.aspect', array(
                                'div' => 'checkbox',
                                'label' => __d('admin', 'Maintain aspect ratio'),
                                'type' => 'checkbox'
                            )); ?>
                        </div>
                    </div>
                </div>

                <div class="transform crop"<?php if ($currentValue !== 'crop') echo ' style="display: none"'; ?>>
                    <?php
                    echo $this->Form->input('FileUpload.transforms.' . $field . '.location', array(
                        'div' => 'field',
                        'label' => array('class' => 'field-label col span-2', 'text' => __d('admin', 'Location')),
                        'options' => array(
                            'center' => __d('admin', 'Center'),
                            'top' => __d('admin', 'Top'),
                            'right' => __d('admin', 'Right'),
                            'bottom' => __d('admin', 'Bottom'),
                            'left' => __d('admin', 'Left')
                        ),
                        'class' => 'input'
                    )); ?>
                </div>

                <div class="transform flip"<?php if ($currentValue !== 'flip') echo ' style="display: none"'; ?>>
                    <?php
                    echo $this->Form->input('FileUpload.transforms.' . $field . '.direction', array(
                        'div' => 'field',
                        'label' => array('class' => 'field-label col span-2', 'text' => __d('admin', 'Direction')),
                        'options' => array(
                            'vertical' => __d('admin', 'Vertical'),
                            'horizontal' => __d('admin', 'Horizontal'),
                            'both' => __d('admin', 'Both')
                        ),
                        'class' => 'input'
                    )); ?>
                </div>

                <div class="transform scale"<?php if ($currentValue !== 'scale') echo ' style="display: none"'; ?>>
                    <?php
                    echo $this->Form->input('FileUpload.transforms.' . $field . '.percent', array(
                        'div' => 'field',
                        'label' => array('class' => 'field-label col span-2', 'text' => __d('admin', 'Percent')),
                        'type' => 'number',
                        'class' => 'input span-1',
                        'value' => .5
                    )); ?>
                </div>

                <div class="transform rotate"<?php if ($currentValue !== 'rotate') echo ' style="display: none"'; ?>>
                    <?php
                    echo $this->Form->input('FileUpload.transforms.' . $field . '.degrees', array(
                        'div' => 'field',
                        'label' => array('class' => 'field-label col span-2', 'text' => __d('admin', 'Degrees')),
                        'type' => 'number',
                        'class' => 'input span-1',
                        'value' => 90
                    )); ?>
                </div>

                <div class="transform fit"<?php if ($currentValue !== 'fit') echo ' style="display: none"'; ?>>
                    <div class="field">
                        <?php echo $this->Form->label('FileUpload.transforms.' . $field . '.fill.red', __d('admin', 'Fill'), array('class' => 'field-label col span-2')); ?>

                        <div class="col span-8">
                            <?php
                            echo $this->Form->input('FileUpload.transforms.' . $field . '.fill.0', array(
                                'div' => false,
                                'label' => false,
                                'type' => 'number',
                                'class' => 'input span-1',
                                'value' => 0
                            )) . ' ' .
                            $this->Form->input('FileUpload.transforms.' . $field . '.fill.1', array(
                                'div' => false,
                                'label' => false,
                                'type' => 'number',
                                'class' => 'input span-1',
                                'value' => 0
                            )) . ' ' .
                            $this->Form->input('FileUpload.transforms.' . $field . '.fill.2', array(
                                'div' => false,
                                'label' => false,
                                'type' => 'number',
                                'class' => 'input span-1',
                                'value' => 0
                            )) . ' (RGB)'; ?>
                        </div>
                    </div>
                </div>
            </fieldset>

            <?php } ?>
        </div>

        <script type="text/javascript">
            $(function() {
                ['FileUploadTransportClass', 'FileUploadTransformsPathLargeMethod', 'FileUploadTransformsPathThumbMethod'].forEach(function(field) {
                    var input = $('#' + field);

                    input
                        .on('change', Admin.toggleUploadField)
                        .trigger('change');
                });
            });
        </script>
    </div>

    <div class="form-actions">
        <div class="redirect-to">
            <?php echo $this->Form->input('redirect_to', array(
                'div' => false,
                'class' => 'input',
                'options' => array(
                    'upload' => __d('admin', 'Continue Uploading'),
                    'read' => __d('admin', '%s Overview', $model->singularName)
                )
            )); ?>
        </div>

        <button type="submit" class="button large is-success">
            <span class="fa fa-edit icon-white"></span>
            <?php echo __d('admin', 'Upload'); ?>
        </button>
    </div>

    <?php echo $this->Form->end(); ?>
</div>

<?php } else { ?>

<div class="splash">
    <h2 class="notice-title"><?php echo __d('admin', 'Install the Uploader to upload files'); ?></h2>

    <a href="http://milesj.me/code/cakephp/uploader" target="_blank" class="button is-info large">
        <span class="fa fa-external-link"></span>
        <?php echo __d('admin', 'Install'); ?>
    </a>
</div>

<?php } ?>