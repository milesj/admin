<?php
/**
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     https://github.com/milesj/admin/blob/master/license.md
 * @link        http://milesj.me/code/cakephp/admin
 */

App::uses('Admin', 'Admin.Lib');
App::uses('ActionLog', 'Admin.Model');

/**
 * @property Model $Model
 * @property AdminToolbarComponent $AdminToolbar
 */
class AdminAppController extends AppController {

    /**
     * Remove parent models.
     *
     * @type array
     */
    public $uses = array();

    /**
     * Components.
     *
     * @type array
     */
    public $components = array(
        'Session', 'Security', 'Cookie', 'Acl', 'RequestHandler',
        'Utility.AutoLogin', 'Admin.AdminToolbar',
        'Auth' => array(
            'authorize' => array('Controller')
        )
    );

    /**
     * Helpers.
     *
     * @type array
     */
    public $helpers = array(
        'Html', 'Session', 'Form', 'Time', 'Text', 'Paginator',
        'Utility.Breadcrumb', 'Utility.Utility', 'Admin.Admin'
    );

    /**
     * Plugin configuration.
     *
     * @type array
     */
    public $config = array();

    /**
     * Paginate defaults.
     *
     * @type array
     */
    public $paginate = array();

    /**
     * Use plugin layout.
     *
     * @type string
     */
    public $layout = 'admin';

    /**
     * Validate the user is authorized.
     *
     * @param array $user
     * @return bool
     * @throws ForbiddenException
     * @throws UnauthorizedException
     */
    public function isAuthorized($user = null) {
        if (!$user) {
            throw new ForbiddenException(__d('admin', 'Invalid User'));
        }

        $aro = Admin::introspectModel('Admin.RequestObject');

        if ($aro->isAdmin($user['id'])) {
            if (!$this->Session->read('Admin.crud')) {
                $this->Session->write('Admin.crud', $aro->getCrudPermissions($user['id']));
            }

            return true;
        }

        throw new UnauthorizedException(__d('admin', 'Insufficient Access Permissions'));
    }

    /**
     * Before filter.
     */
    public function beforeFilter() {
        parent::beforeFilter();

        // Set locale
        $locale = $this->Auth->user(Configure::read('User.fieldMap.locale') ?: 'locale') ?: 'eng';
        Configure::write('Config.language', $locale);

        // Set config
        $this->config = Configure::read('Admin');
    }

    /**
     * Before render.
     */
    public function beforeRender() {
        $this->set('user', $this->Auth->user());
        $this->set('config', Configure::read());
        $this->set('model', $this->Model);
    }

    /**
     * Proxy action to handle POST requests and redirect back with named params.
     */
    public function proxy() {
        if (empty($this->Model) || empty($this->request->data[$this->Model->alias])) {
            $this->redirect($this->referer());
        }

        $data = $this->request->data[$this->Model->alias];
        $named = array();

        foreach ($data as $key => $value) {
            if (
                mb_substr($key, -7) === '_filter' ||
                mb_substr($key, -11) === '_type_ahead' ||
                $value === '') {
                continue;
            }

            $named[$key] = urlencode($value);

            if (isset($data[$key . '_filter'])) {
                $named[$key . '_filter'] = urlencode($data[$key . '_filter']);
            }

            if (isset($data[$key . '_type_ahead'])) {
                $named[$key . '_type_ahead'] = urlencode($data[$key . '_type_ahead']);
            }
        }

        $url = array('action' => 'index',);

        if ($this->name === 'Crud') {
            $url['model'] = $this->Model->urlSlug;
        }

        $this->redirect(array_merge($named, $url));
    }

}
