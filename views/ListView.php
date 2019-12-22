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
    protected $orderDir;
    protected $checkboxes;
    
    public function __construct(Framework $framework, $route, array $filter) {
        $this->view = $framework->get('view');
        $this->cellView = $framework->get('listCellView');
        $this->route = $route;
        $this->filter = $filter;
    }
    
    public function hasCheckboxes() {
        return $this->checkboxes;
    }
    
    public function setCheckboxes($value) {
        $this->checkboxes = $value;
    }
    
    public function setActions(array $actions) {
        $this->actions = $actions;
    }
    
    public function getActions() {
        return $this->actions;
    }
    
    public function setColumns(array $columns) {
        $this->columns = $columns;
    }
    
    public function setItemActions(array $itemActions) {
        $this->itemActions = $itemActions;
    }
    
    public function getItemActions() {
        return $this->itemActions;
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
        $icon = $this->fetchOrderIcon($name);
        $label = $this->fetchLabel($column);
        $style = $this->fetchStyle($column);
        $result = '<th'.$style.'>';
        $result .= '<table><tr><td style="padding: 0;">';
        $result .= $this->fetchHeaderLinkBegin($name, $column);
        $result .= str_replace(' ', '&nbsp;', $label);
        $result .= $this->fetchHeaderLinkEnd($column);
        $result .= '</td><td style="padding: 0;">'.$icon.'</td></tr></table>';
        $result .= '</th>'."\n";
        return $result;
    }
    
    protected function fetchLabel($column) {
        return is_array($column['label']) ? text($column['label'][0], $column['label'][1]) : $column['label'];
    }
    
    protected function fetchOrderIcon($name) {
        // set icon and orderDir
        $icon = '';
        $this->orderDir = 'asc';
        if ($this->filter['order_by'] == $name) {
            $this->orderDir = $this->filter['order_dir'] == 'asc' ? 'desc' : 'asc';
            $icon = '<span class="icon"><i class="fas fa-caret-';
            $icon .= $this->filter['order_dir'] == 'asc' ? 'up' : 'down';
            $icon .= '"></i></span>';
        }
        return $icon;
    }
    
    protected function fetchHeaderLinkBegin($name, $column) {
        $result = '';
        if (!isset($column['disabled'])) {
            $params = ['order_by' => $name, 'order_dir' => $this->orderDir];
            $result = '<a href="'.route_url($this->route, $params).'" class="table-header">';            
        }
        return $result;
    }
    
    protected function fetchHeaderLinkEnd($column) {
        $result = '';
        if (!isset($column['disabled'])) {
            $result = '</a>';
        }
        return $result;
    }    
    
    protected function fetchCell(Record $record, $name, $column) {
        $result = '';
        $params = [$record, $name];
        $viewMethod = isset($column['view']) ? $column['view'] : 'text';
        $style = $this->fetchStyle($column);
        if (method_exists($this->cellView, $viewMethod)) {
            $result = '<td'.$style.'>';
            $result .= call_user_func_array([$this->cellView, $viewMethod], $params);
            $result .= '</td>'."\n";
        }
        return $result;
    }
    
    protected function fetchStyle($column) {
        $result = '';
        if (isset($column['align']) || isset($column['width'])) {
            $result .= ' style="';
            if (isset($column['align'])) {
                $result .= 'text-align: '.$column['align'].';';
            }
            if (isset($column['width'])) {
                $result .= 'width: '.$column['width'].';';                
            }
            $result .= '"';
        }
        return $result;
    }
}
