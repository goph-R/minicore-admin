<?php

abstract class AdminService {
    
    const LIST = 'list';
    const CREATE = 'create';
    const EDIT = 'edit';
    const DELETE = 'delete';
    
    /** @var Framework */
    protected $framework;
    
    /** @var Router */
    protected $router;
    
    /** @var Translation */
    protected $translation;
    
    /** @var Request */
    protected $request;

    /** @var UserSession */
    protected $userSession;
    
    /** @var UserService */
    protected $userService;
    
    /** @var Admin */
    protected $admin;
    
    private $route;

    abstract public function createForm(Record $record);
    abstract public function save(Form $form, Record $record);
    abstract public function getRoute();
    abstract public function getTitles();
    
    public function __construct(Framework $framework, $adminName) {
        $this->framework = $framework;
        $this->router = $framework->get('router');
        $this->translation = $framework->get('translation');
        $this->request = $framework->get('request');
        $this->userSession = $framework->get('userSession');
        $this->userService = $framework->get('userService');
        $this->admin = $framework->get($adminName);
        $this->route = $this->getRoute();
    }
    
    public function getAllPermissions() {
        return [
            self::LIST   => [AdminPermissions::ADMINISTRATOR],
            self::EDIT   => [AdminPermissions::ADMINISTRATOR],
            self::CREATE => [AdminPermissions::ADMINISTRATOR],
            self::DELETE => [AdminPermissions::ADMINISTRATOR],
        ];
    }
    
    public function getPermissionsFor($for) {
        $allPermissions = $this->getAllPermissions();
        return isset($allPermissions[$for]) ? $allPermissions[$for] : [];
    }

    public function getTitle($for) {
        $titles = $this->getTitles();
        $title = isset($titles[$for]) ? $titles[$for] : '';
        return $this->getText($title);
    }

    public function getText($text) {
        $result = is_array($text) ? $this->translation->get($text[0], $text[1]) : $text;
        return $result;
    }    
    
    public function saveWithMessage(Form $form, Record $record) {
        $isNew = $record->isNew();
        if (!$this->save($form, $record)) {
            return;
        }
        if ($isNew) {
            $this->setListSuccessMessage(['admin', 'create_was_successful']);
        } else {
            $this->setListSuccessMessage(['admin', 'modify_was_successful']);
        }
    }

    /**
     * @return Form
     */
    public function createFilterForm() {
        /** @var Form $form */
        $form = $this->framework->create(['Form', 'filter']);
        $form->setUseCsrf(false);
        /** @var TextInput $textInput */
        $textInput = $form->addInput(null, ['TextInput', 'text', $this->getFilterFromSession('text')]);
        $textInput->setRequired(false);
        $textInput->setRowEnd(false);
        $textInput->setPlaceholder(text('admin', 'search_placeholder'));
        /** @var SubmitInput $submitInput */
        $submitInput = $form->addInput(null, ['SubmitInput', 'submit', '<span class="icon"><i class="fas fa-search"></i></span>']);
        $submitInput->setRowBegin(false);
        return $form;
    }
    
    public function showRemoveFilter(array $filter) {
        return $filter['text'];
    }
    
    protected function removeFilter() {
        $filter['text'] = '';
        return $filter;
    }
    
    public function createListView(array $filter) {
        $listView = $this->framework->create(['ListView', $this->getListRoute(), $filter]);
        $actions = [];
        $currentUser = $this->userService->getCurrentUser();        
        if ($currentUser->hasPermission($this->getPermissionsFor(AdminService::DELETE))) {
            $actions[] = ':admin/list-action-delete';
            $listView->setCheckboxes(true);
        }
        if ($currentUser->hasPermission($this->getPermissionsFor(AdminService::CREATE))) {
            $actions[] = ':admin/list-action-create';
        }
        $listView->setActions($actions);
        $itemActions = [];
        if ($currentUser->hasPermission($this->getPermissionsFor(AdminService::EDIT))) {
            $itemActions[] = ':admin/list-item-action-modify';
        }        
        $listView->setItemActions($itemActions);
        return $listView;
    }
    
       
    public function getEmptyRecord() {
        return $this->admin->getEmptyRecord();
    }
    
    protected function getRoutePath($route, $withLocale) {
        return $withLocale ? $this->router->getPathWithLocale($route) : $route;
    }
    
    public function getListRoute($withLocale = false) {
        return $this->getRoutePath($this->route, $withLocale);
    }
    
    public function getDeleteRoute($withLocale = false) {
        return $this->getRoutePath($this->route.'/delete', $withLocale);
    }
    
    public function getEditRoute($withLocale = false) {
        return $this->getRoutePath($this->route.'/edit', $withLocale);
    }
    
    public function getCreateRoute($withLocale = false) {
        return $this->getRoutePath($this->route.'/create', $withLocale);
    }    
    
    public function createFilter(Form $form) {
        if ($this->request->get('sent')) {
            $form->bind();
        }
        $filter = $form->getValues();
        $filter['order_by'] = $this->getFilterFromRequest('order_by', 'id');
        $filter['order_dir'] = $this->getFilterFromRequest('order_dir', 'asc') == 'asc' ? 'asc' : 'desc';
        $filter['page'] = $this->getFilterFromRequest('page', 0);
        $filter['page_limit'] = $this->getFilterFromRequest('page_limit', 10);
        return $filter;
    }
    
    public function saveFilterToSession(array $filter) {
        $this->userSession->set('filter.'.$this->route, $filter);
    }
    
    protected function getFilterFromRequest($name, $defaultValue=null) {
        return $this->request->get($name, $this->getFilterFromSession($name, $defaultValue));
    }
    
    protected function getFilterFromSession($name, $defaultValue=null) {        
        $savedFilter = $this->userSession->get('filter.'.$this->route, []);
        return isset($savedFilter[$name]) ? $savedFilter[$name] : $defaultValue;
    }
    
    public function createPager(array $filter) {
        $count = $this->admin->findAllCount($filter);
        $pager = $this->framework->create('Pager');
        $page = isset($filter['page']) ? (int)$filter['page'] : 0;
        $limit = isset($filter['page_limit']) ? (int)$filter['page_limit'] : 25;
        $pager->init($page, $limit, $count, $this->route);
        return $pager;
    }
    
    public function getRemoveFilterUrl() {
        $params = [
            'filter' => $this->removeFilter(),
            'sent' => 1
        ];
        return $this->router->getUrl($this->route, $params);
    }
    
    public function findAll(array $filter) {
        return $this->admin->findAll($filter);
    }
    
    public function findById($id) {
        return $this->admin->findById($id);
    }

    public function deleteByIds(array $ids) {
        $this->admin->deleteByIds($ids);
        $this->setListSuccessMessage(['admin', 'delete_was_successful']);
    }
    
    public function setListErrorMessage($message) {
        $this->userSession->setFlash('list_error_message', $this->getText($message));
    }
    
    public function setListSuccessMessage($message) {
        $this->userSession->setFlash('list_success_message', $this->getText($message));
    }

    
}

