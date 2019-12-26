<?php

class ListCellView {
    
    public function __construct(Framework $framework) {
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
    
}
