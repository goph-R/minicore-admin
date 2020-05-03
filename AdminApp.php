<?php

class AdminApp extends App {

    public function __construct($env='dev', $configPath='config.ini.php') {
        parent::__construct($env, $configPath);
        $this->addModule('UsersModule');
        $this->addModule('BulmaModule');
    }

    public function init() {
        parent::init();
        $framework = Framework::instance();
        $this->translation->add('admin', 'modules/minicore-admin/translations');
        $this->view->addFolder(':admin', 'modules/minicore-admin/templates');
        $this->view->changePath(':app/layout', ':admin/layout');
        $this->view->changePath(':pager/pager', ':admin/pager');
        $this->view->changePath(':user/login', ':admin/user/login');
        $this->view->changePath(':user/forgot', ':admin/user/forgot');
        $this->view->changePath(':user/message', ':admin/user/message');
        $this->view->changePath(':user/message-box', ':admin/user/message-box');
        $this->view->changePath(':user/settings-messages', ':admin/user/settings-messages');
        $this->view->set([
            'userSession' => $framework->get('userSession'),
            'userService' => $framework->get('userService')
        ]);
    }

}