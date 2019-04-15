<?php

require_once 'FinanceInstance.php';

class TargetInstance extends FinanceInstance {
    protected $total;
    protected $current;
    
    public function __construct($name, $amount, $current, $total, $date) {
        parent::__construct($name, $amount, $date);
        
        if (!is_numeric($current)) {
            throw new Exception("\$current must be a number");
        }
        
        if (!is_numeric($total)) {
            throw new Exception("\$total must be a number");
        }
        
        $this->current = $current;
        $this->total = $total;
    }
    
    protected function getStrValue() {
        $perc = $this->current / $this->total * 100;
        return sprintf(
                "\$%.2d<td>\$%.2d<td>\$%.2d<td>%.0d%%",
                $this->value,
                $this->current,
                $this->total,
                $perc);
    }
}
