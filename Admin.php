<?php

abstract class Admin {
    
    protected $dbInstanceName = 'database';
    protected $recordClass = '';
    protected $tableName = '';
    protected $sqlParams = [];
    
    /** @var Record */
    protected $record;
    
    public function __construct(Framework $framework) {
        $this->db = $framework->get($this->dbInstanceName);
        $this->record = $framework->create([$this->recordClass, $this->dbInstanceName]);
    }
    
    public function getEmptyRecord() {
        return $this->record;
    }
    
    protected function addSqlParams(array $params) {
        $this->sqlParams = array_merge($this->sqlParams, $params);
    }
    
    public function findAllCount(array $filter) {
        $this->sqlParams = [];
        $query = "SELECT COUNT(1) FROM {$this->tableName}";
        $query .= $this->addSqlWhere($filter);
        return $this->db->fetchColumn($query, $this->sqlParams);        
    }

    public function findAll(array $filter) {
        $this->sqlParams = [];
        $query = "SELECT * FROM {$this->tableName}";
        $query .= $this->addSqlWhere($filter);
        $query .= $this->addSqlOrder($filter);
        $query .= $this->addSqlLimit($filter);
        return $this->db->fetchAll($this->recordClass, $query, $this->sqlParams);
    }
    
    public function findById($id) {
        $query = "SELECT * FROM {$this->tableName} WHERE id = :id LIMIT 1";
        return $this->db->fetch($this->recordClass, $query, ['id' => $id]);
    }

    protected function addSqlWhere(array $filter) {
        return '';
    }
    
    protected function addSqlOrder(array $filter) {
        if (!isset($filter['order_by']) || !isset($filter['order_dir'])) {
            return '';
        }
        $column = $filter['order_by'];
        if (!$this->record->columnExists($column)) {
            return '';
        }
        $direction = $filter['order_dir'] == 'asc' ? 'asc' : 'desc';
        return ' ORDER BY '.$column.' '.$direction;
    }
    
    protected function addSqlLimit(array $filter) {
        if (!isset($filter['page']) || !isset($filter['page_limit'])) {
            return '';
        }
        $page = (int)$filter['page'];
        if ($page < 0) {
            $page = 0;
        }
        $pageLimit = (int)$filter['page_limit'];
        if ($pageLimit < 1 || $pageLimit > 100) {
            $pageLimit = 25;
        }            
        $start = $page * $pageLimit;
        return ' LIMIT '.$start.', '.$pageLimit;
    }
    
}
