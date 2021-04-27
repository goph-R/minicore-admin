<?php

class AdminApp extends App {

    public function __construct($env='dev', $configPath='config.ini.php') {
        parent::__construct($env, $configPath);
        $this->addModule('AdminModule');
        $this->addModule('BulmaModule');
        $this->addModule('UsersModule');
        $this->addModule('UsersAdminModule');
    }

    public function init() {
        parent::init();
        $framework = Framework::instance();
        $app = $framework->get('app');
        $module = $app->getModule('minicore-admin');
        $folder = $module->getFolder();
        $this->translation->add('admin', $folder.'translations');
        $this->view->addFolder(':admin', $folder.'templates');
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