<?php

abstract class Admin {
    
    protected $dbInstanceName = 'database';
    protected $recordClass = '';
    protected $tableName = '';
    protected $sqlParams = [];
    
    /** @var Database */
    protected $db;
    
    /** @var Record */
    protected $record;
    
    /** @var Translation */
    protected $translation;
    
    public function __construct() {
        $framework = Framework::instance();
        $this->translation = $framework->get('translation');
        $this->db = $framework->get($this->dbInstanceName);
        $this->record = $framework->create([$this->recordClass, $this->dbInstanceName]);
    }
    
    public function getEmptyRecord() {
        return $this->record;
    }
    
    protected function clearSqlParams() {
        $this->sqlParams = [];
    }
    
    protected function addSqlParams(array $params) {
        $this->sqlParams = array_merge($this->sqlParams, $params);
    }
    
    public function getSelect($fields='*', array $filter=[]) {        
        $query = "SELECT $fields FROM {$this->tableName}";
        return $query;
    }
    
    public function findAll(array $filter) {
        $this->clearSqlParams();
        $query = $this->getSelect('*', $filter);
        $query .= $this->getWhere($filter);
        $query .= $this->getOrder($filter);
        $query .= $this->getLimit($filter);
        return $this->db->fetchAll($this->recordClass, $query, $this->sqlParams);
    }

    public function findAllCount(array $filter) {
        $this->clearSqlParams();
        $query = $this->getSelect('COUNT(1)', $filter);
        $query .= $this->getWhere($filter);
        return $this->db->fetchColumn($query, $this->sqlParams);        
    }
    
    public function findById($id) {
        $this->clearSqlParams();
        $query = $this->getSelect();
        $query .= " WHERE id = :id LIMIT 1";
        $this->addSqlParams(['id' => $id]);
        return $this->db->fetch($this->recordClass, $query, $this->sqlParams);
    }
    
    public function deleteByIds($ids) {
        $in = $this->db->getInConditionAndParams($ids);
        $query = "DELETE FROM {$this->tableName} WHERE id IN (".$in['condition'].") LIMIT ".count($in['params']);
        $this->db->query($query, $in['params']);
    }

    protected function getWhere(array $filter) {
        return '';
    }
    
    protected function getOrder(array $filter) {
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
    
    protected function getLimit(array $filter) {
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
