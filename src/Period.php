<?php


class Period {
    protected $start;
    protected $end;
    protected $income;
    protected $expense;
    protected $savings;
    
    public function __construct() {
        $this->income = [];
        $this->expense = [];
        $this->savings = [];
    }
    
    public function setStart($date) {
        if (!($date instanceof DateTimeImmutable)) {
            throw new Exception("\$date must be a DateTimeImmutable");
        }
        
        $this->start = $date;
    }
    
    public function setEnd($date) {
        if (!($date instanceof DateTimeImmutable)) {
            throw new Exception("\$date must be a DateTimeImmutable");
        }
        
        $this->end = $date;
    }
    
    public function add($item) {
        if ($item instanceof IncomeInstance) {
            array_push($this->income, $item);
        } elseif ($item instanceof ExpenseInstance) {
            array_push($this->expense, $item);
        } elseif ($item instanceof TargetInstance) {
            array_push($this->savings, $item);
        } else {
            throw new Exception("\$item must be on of: " .
                    "IncomeInstance, ExpenseInstance, or TargetInstance");
        }
    }
    
    public function getStart() {
        return $this->start;
    }
    
    public function getEnd() {
        return $this->end;
    }
    
    public function getWiggle() {
        $income = array_reduce($this->income, function ($carry, $i) {
            return $carry + $i->getValue();
        }, 0);
        
        $expense = array_reduce($this->expense, function ($carry, $e) {
            return $carry + $e->getValue();
        }, 0);
        
        $saving = array_reduce($this->savings, function ($carry, $t) {
            return $carry + $t->getValue();
        }, 0);
        
        return $income - $expense - $saving;
    }
    
    public function getLength() {
        return $this->end->getTimestamp() - $this->start->getTimestamp();
    }
    
    public function countItems() {
        return count($this->income) + count($this->expense) + count($this->savings);
    }
    
    public function render() {
        $lines = [ "<tr><td colspan=\"6\"><h3>{$this->start->format(FinanceItem::DATE_FORMAT)} - {$this->end->format(FinanceItem::DATE_FORMAT)}</h3></tr>" ];
        
        foreach (array_merge($this->income, $this->expense, $this->savings) as $i) {
            array_push($lines, $i->render());
        }
        
        array_push($lines, sprintf("<tr><td>Remaining Wiggle Room<td>\$%0.2d</tr>", $this->getWiggle()));
        
        if (2 == count($lines)) {
            return "";
        }
        
        return join("\n", $lines);
    }
    
    public function balance($overflow) {
        if (0 < $this->getWiggle() && 0 < count($overflow)) {
            usort($overflow, function ($a, $b) {
                return $a->getValue() - $b->getValue();
            });
            
            while (0 < ($wiggle = $this->getWiggle()) && 0 < count($overflow)) {
                $of = array_shift($overflow);
                
                if ($of->getValue() <= $this->getWiggle()) {
                    array_push($this->expense, $of);
                } else {
                    $name = $of->getName();
                    $date = $of->getDate();
                    $leftover = $of->getValue() - $wiggle;
                    
                    $ours = new ExpenseInstance($name, $wiggle, $date);
                    $theirs = new ExpenseInstance($name, $leftover, $date);
                    
                    array_push($this->expense, $ours);
                    array_push($overflow, $theirs);
                }
            }
            
        } else {
            usort($this->expense, function ($a, $b) {
                return $b->getValue() - $a->getValue();
            });
            while (0 > $this->getWiggle() && 0 < count($this->expense)) {
                $overdraft = 0 - $this->getWiggle();
                $old = array_shift($this->expense);
                if ($overdraft < $old->getValue()) {
                    $new_value = $overdraft - $old->getValue();
                    $name = $old->getName();
                    $date = $old->getDate();
                    $temp = new ExpenseInstance($name, $overdraft, $date);
                    $new = new ExpenseInstance($name, $new_value, $date);
                    
                    array_unshift($this->expense, $new);
                    array_push($overflow, $temp);
                } else {
                    array_push($overflow, $old);
                }
            }
        }
        
        return $overflow;
    }
}
