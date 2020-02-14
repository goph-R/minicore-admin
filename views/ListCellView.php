<?php

class ListCellView {
    
    /** @var AdminService **/
    protected $service;
    
    public function __construct(Framework $framework, AdminService $service) {
        $this->service = $service;
    }
    
    public function text(Record $record, $name) {
        return esc($record->get($name));
    }
    
    public function check(Record $record, $name) {
        $value = $record->get($name);
        return $value ? '<span class="icon"><i class="fas fa-check"></i></span>' : '';
    }
    
    public function textArray(Record $record, $name) {
        $array = [];
        foreach ($record->get($name) as $data) {
            $array[] = str_replace(' ', '&nbsp;', (string)$data);
        }
        return join(', ', $array);
    }
    
    public function date(Record $record, $column) {
        $time = strtotime($record->get($column));
        $date = str_replace(' ', '&nbsp;', date('Y-m-d H:i', $time));
        return $date;
    }
    
    public function viewLink(Record $record, $column) {
        $value = $record->get($column);
        $viewUrl = route_url($this->service->getViewRoute(), ['id' => $record->getId()]);
        $result = '<a href="'.$viewUrl.'">'.esc($value).'</a>';       
        return $result;
    }
    
}
