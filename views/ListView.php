<?php

class ListView {
    
    /** @var View */
    protected $view;
    
    /** @var ListCellView */
    protected $cellView;
    
    protected $actions;
    protected $columns;
    protected $itemActions;
    protected $route;
    protected $filter;
    
    public function __construct(Framework $framework, $route, array $filter) {
        $this->view = $framework->get('view');
        $this->cellView = $framework->get('listCellView');
        $this->route = $route;
        $this->filter = $filter;
    }
    
    public function setActions(array $actions) {
        $this->actions = $actions;
    }
    
    public function setColumns(array $columns) {
        $this->columns = $columns;
    }
    
    public function setItemActions(array $itemActions) {
        $this->itemActions = $itemActions;
    }
    
    public function fetchActions($params = []) {
        $result = '';
        foreach ($this->actions as $action) {
            $result .= $this->view->fetch($action, $params);
        }
        return $result;
    }
    
    public function fetchTable($records, $params = []) {
        $mergedParams = array_merge($params, [
            'records' => $records,
            'listView' => $this
        ]);
        return $this->view->fetch(':admin/list-table', $mergedParams);
    }
    
    public function fetchHeaders() {
        $result = '';
        foreach ($this->columns as $name => $column) {
            $result .= $this->fetchHeader($name, $column);
        }
        return $result;
    }
    
    public function fetchRecord(Record $record) {
        $result = '';
        foreach ($this->columns as $name => $column) {
            $result .= $this->fetchCell($record, $name, $column);
        }
        return $result;
    }
    
    public function fetchItemActions(Record $record) {
        $result = '';
        foreach ($this->itemActions as $itemAction) {
            $result .= $this->view->fetch($itemAction, ['record' => $record]);
        }
        return $result;
    }
    
    protected function fetchHeader($name, $column) {
        $icon = '';
        $orderDir = 'asc';
        if ($this->filter['order_by'] == $name) {
            $orderDir = $this->filter['order_dir'] == 'asc' ? 'desc' : 'asc';
            $icon = '<span class="icon"><i class="fas fa-caret-';
            $icon .= $this->filter['order_dir'] == 'asc' ? 'up' : 'down';
            $icon .= '"></i></span>';
        }
        $params = ['order_by' => $name, 'order_dir' => $orderDir];
        $label = is_array($column['label']) ? text($column['label'][0], $column['label'][1]) : $column['label'];
        $align = $this->getAlignStyle($column);
        $result = '<th'.$align.'>';
        $result .= '<a href="'.route_url($this->route, $params).'" class="table-header">';
        $result .= $label.$icon.'</a></th>'."\n";
        return $result;
    }
    
    protected function fetchCell(Record $record, $name, $column) {
        $result = '';
        $params = [$record, $name];
        $viewMethod = isset($column['view']) ? $column['view'] : 'text';
        $align = $this->getAlignStyle($column);
        if (method_exists($this->cellView, $viewMethod)) {
            $result = '<td'.$align.'>';
            $result .= call_user_func_array([$this->cellView, $viewMethod], $params);
            $result .= '</td>'."\n";
        }
        return $result;
    }
    
    protected function getAlignStyle($column) {
        return isset($column['align']) ? ' style="text-align: '.$column['align'].'"' : '';
    }
}
