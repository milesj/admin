<?php
/**
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     https://github.com/milesj/admin/blob/master/license.md
 * @link        http://milesj.me/code/cakephp/admin
 */

App::uses('AdminAppModel', 'Admin.Model');

class FileUpload extends AdminAppModel {

    /**
     * Use caption for display.
     *
     * @type string
     */
    public $displayField = 'caption';

    /**
     * Belongs to.
     *
     * @type array
     */
    public $belongsTo = array(
        'User' => array(
            'className' => USER_MODEL
        )
    );

    /**
     * Admin settings.
     *
     * @type array
     */
    public $admin = array(
        'iconClass' => 'fa-upload',
        'imageFields' => array(
            'path',
            'path_large',
            'path_thumb' => array('index', 'read')
        ),
        'hideFields' => array('path_thumb', 'path_large', 'size', 'ext', 'type', 'width', 'height'),
        'paginate' => array(
            'order' => array('FileUpload.created' => 'DESC')
        )
    );

    /**
     * Validation.
     *
     * @type array
     */
    public $validate = array(
        'caption' => 'notEmpty'
    );

    /**
     * Define Uploader Attachment configuration dynamically.
     *
     * @param bool $id
     * @param string $table
     * @param string $ds
     */
    public function __construct($id = false, $table = null, $ds = null) {
        if (CakePlugin::loaded('Uploader')) {
            $transforms = array(
                'path' => array('method' => 'exif', 'self' => true)
            ) + Configure::read('Admin.uploads.transforms');

            $this->actsAs['Uploader.Attachment'] = array(
                'path' => array(
                    'nameCallback' => 'formatName',
                    'overwrite' => true,
                    'metaColumns' => array(
                        'size' => 'size',
                        'ext' => 'ext',
                        'type' => 'type',
                        'height' => 'height',
                        'width' => 'width'
                    ),
                    'transforms' => $transforms,
                    'transport' => Configure::read('Admin.uploads.transport')
                )
            );

            $this->actsAs['Uploader.FileValidation'] = array(
                'path' => Configure::read('Admin.uploads.validation')
            );
        } else {
            $this->admin = false;
        }

        parent::__construct($id, $table, $ds);
    }

    /**
     * Format the file name.
     *
     * @param string $name
     * @param \Transit\File $file
     * @return string
     */
    public function formatName($name, $file) {
        return md5($name . time());
    }

    /**
     * Return the original file name.
     *
     * @param string $name
     * @param \Transit\File $file
     * @return string
     */
    public function formatTransformName($name, $file) {
        return $this->getUploadedFile()->name();
    }

    /**
     * Remove transforms if file is not an image.
     *
     * @param array $options
     * @return array
     */
    public function beforeUpload($options) {
        $data = $this->data[$this->alias];

        // Remove transforms for non-image files
        if (!empty($data['path']['type']) && strpos($data['path']['type'], 'image') === false) {
            $options['transforms'] = array();
        }

        // Overwrite transforms from UploadController::index()
        if (!empty($data['transforms'])) {
            $oldTransforms = $options['transforms'];
            $options['transforms'] = array();

            foreach (array('path_thumb', 'path_large') as $field) {
                $newTransform = empty($oldTransforms[$field]) ? array() : $oldTransforms[$field];

                // Only apply if checkbox is checked
                if (!empty($data['transforms'][$field]['transform'])) {
                    $options['transforms'][$field] = array_merge($newTransform, $data['transforms'][$field]);
                }
            }
        }

        // Overwrite transport from UploadController::index()
        if (!empty($data['transport'])) {
            $options['transport'] = array();

            if (!empty($data['transport']['class'])) {
                $options['transport'] = $data['transport'];
            }
        }

        return $options;
    }

}