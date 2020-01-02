<?php

class AdminController extends Controller {
    
    /** @var UserService */
    protected $userService;
    
    /** @var AdminService */
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
        $permission = $this->adminService->getPermissionFor(AdminService::LIST);
        $this->userService->requirePermission($permission);
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
        $permission = $this->adminService->getPermissionFor(AdminService::DELETE);
        $this->userService->requirePermission($permission);
        $idsString = $this->request->get('ids');
        if (!$idsString) {
            $this->redirectToList();
        }
        $ids = explode(',', $idsString);
        if (!$ids) {
            $this->redirectToList();
        }
        $this->adminService->deleteByIds($ids);
        $this->redirectToList();
    }
    
    public function create() {
        $permission = $this->adminService->getPermissionFor(AdminService::CREATE);
        $this->userService->requirePermission($permission);
        $record = $this->adminService->getEmptyRecord();
        $form = $this->adminService->createForm($record);
        $this->processForm($form, $record);
        $this->view->set([
            'translation' => $this->translation,
            'adminService' => $this->adminService,
            'title' => $this->adminService->getTitle(AdminService::CREATE),
            'action' => $this->adminService->getCreateRoute(),
            'form' => $form,
            'record' => $record,
            'id' => 0
        ]);
        $this->render($this->createTemplate);
    }
    
    public function edit() {
        $permission = $this->adminService->getPermissionFor(AdminService::EDIT);
        $this->userService->requirePermission($permission);
        $id = $this->request->get('id');
        $record = $this->adminService->findById($id);
        if (!$record) {
            $this->framework->error(404);
        }
        $form = $this->adminService->createForm($record);
        $this->processForm($form, $record);
        $this->view->set([
            'translation' => $this->translation,
            'adminService' => $this->adminService,
            'title' => $this->adminService->getTitle(AdminService::EDIT),
            'action' => $this->adminService->getEditRoute(),
            'form' => $form,
            'record' => $record,
            'id' => $id
        ]);
        $this->render($this->editTemplate);   
    }
    
    protected function processForm(Form $form, Record $record) {
        if ($form->processInput()) {
            $this->adminService->saveWithMessage($form, $record);
            $this->redirectToList();
        }        
    }
    
    protected function redirectToList() {
        $this->redirect($this->adminService->getListRoute());
    }
    
}

