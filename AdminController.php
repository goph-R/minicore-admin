<?php

class AdminController extends Controller {
    
    /** @var UserService */
    protected $userService;
    
    protected $adminService;    
    protected $listTemplate = ':admin/list';
    protected $createTemplate = ':admin/form';
    protected $editTemplate = ':admin/form';
    
    public function __construct(Framework $framework, $adminServiceName) {
        parent::__construct($framework);
        $this->userService = $framework->get('userService');
        $this->adminService = $framework->get($adminServiceName);
    }
    
    public function index() {
        $this->userService->requireLogin(); // TODO: require admin right        
        /** @var Form $filterForm */
        $filterForm = $this->adminService->createFilterForm();
        $filter = $this->adminService->createFilter($filterForm);
        $pager = $this->adminService->createPager($filter);
        $filter['page'] = $pager->getPage(); // limit the maximum page in filter
        $this->adminService->saveFilterToSession($filter);
        $this->view->set([
            'filterForm' => $filterForm,
            'filter' => $filter,
            'records' => $this->adminService->findAll($filter),
            'pager' => $pager,
            'adminService' => $this->adminService,
            'listView' => $this->adminService->createListView($filter),
            'router' => $this->router
        ]);
        $this->render($this->listTemplate);
    }
    
    public function delete() {
        $this->userService->requireLogin(); // TODO: require admin right        
        $this->adminService->deleteByIds($this->request->get('ids'));
        $this->redirectToList();
    }
    
    public function create() {
        $this->userService->requireLogin(); // TODO: require admin right        
        $record = $this->adminService->getEmptyRecord();
        $form = $this->adminService->createForm($record);
        $this->processForm($form, $record);
        $this->view->set([
            'adminService' => $this->adminService,
            'title' => $this->adminService->getCreateTitle(),
            'action' => $this->adminService->getCreateRoute(),
            'form' => $form,
            'id' => 0
        ]);
        $this->render($this->createTemplate);
    }
    
    public function edit() {
        $this->userService->requireLogin(); // TODO: require admin right        
        $id = $this->request->get('id');
        $record = $this->adminService->findById($id);
        if (!$record) {
            $this->framework->error(404);
        }
        $form = $this->adminService->createForm($record);
        $this->processForm($form, $record);
        $this->view->set([
            'adminService' => $this->adminService,
            'title' => $this->adminService->getEditTitle(),
            'action' => $this->adminService->getEditRoute(),
            'form' => $form,
            'id' => $id
        ]);
        $this->render($this->editTemplate);   
    }
    
    protected function processForm(Form $form, Record $record) {
        if ($form->processInput()) {
            $this->adminService->save($form, $record);
            $this->redirectToList();
        }        
    }
    
    protected function redirectToList() {
        $this->redirect($this->adminService->getListRoute());
    }
    
}

