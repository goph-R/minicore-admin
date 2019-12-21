<?php

class AdminApp extends App {

    public function __construct(Framework $framework, $env='dev', $configPath='config.ini.php') {
        parent::__construct($framework, $env, $configPath);
        $this->addModule('UsersModule');
        $this->addModule('BulmaModule');
        $this->framework->add([
            'listCellView' => 'ListCellView'
        ]);
    }

    public function init() {
        parent::init();        
        $this->translation->add('admin', 'modules/minicore-admin/translations');
        $this->view->addFolder(':admin', 'modules/minicore-admin/templates');
        $this->view->changePath(':pager/pager', ':admin/pager');
        $this->view->changePath(':user/login', ':admin/user/login');
        $this->view->changePath(':user/forgot', ':admin/user/forgot');
        $this->view->changePath(':user/message', ':admin/user/message');
        $this->view->changePath(':user/message-box', ':admin/user/message-box');
        $this->view->changePath(':user/settings', ':admin/user/settings');
        $this->view->set([
            'userSession' => $this->framework->get('userSession'),
            'userService' => $this->framework->get('userService')
        ]);
    }

}