<?php

require_once 'TargetInstance.php';
require_once 'Period.php';


class Future {
    protected $income;
    protected $expense;
    protected $target;

    public function __construct($items, $targets) {
        $this->income = [];
        $this->expense = [];
        $this->target = [];
        
        foreach ($items as $i) {
            if ($i instanceof IncomeItem) {
                array_push($this->income, $i);
            }
            if ($i instanceof ExpenseItem) {
                array_push($this->expense, $i);
            }
        }
        
        $this->target = $targets;
    }
    
    public function calculate() {
        $this->failed = [];
        $periods = $this->generatePeriods();
        $periods = $this->balanceExpenses($periods);
        
        $this->calculateSavings($periods);
        return $periods;
    }
    
    private function generatePeriods() {
        if (0 == count($this->target)) {
            $end = new DateTimeImmutable();
            $days = intval($end->format("d"));
            $start = $end->sub(new DateInterval("P{$days}D"));
            $end = $start->add(new DateInterval("P1M"));
        } else {
            $start = min(array_map(function ($t) {
                        return $t->getStart();
                    }, $this->target));
            $end = max(array_map(function ($t) {
                        return $t->getEnd();
                    }, $this->target));
        }

        $cmp_inst = function ($a, $b) {
            return $a->getDate()->getTimestamp() - $b->getDate()->getTimestamp();
        };
        
        $inc_inst = [];
        foreach ($this->income as $i) {
            $inc_inst = array_merge($inc_inst, $i->enumerate($start, $end));
        }
        
        $exp_inst = [];
        foreach ($this->expense as $e) {
            $exp_inst = array_merge($exp_inst, $e->enumerate($start, $end));
        }
        
        $inst = array_merge($inc_inst, $exp_inst);
        usort($inst, $cmp_inst);
        
        $periods = [];
        $p = new Period();
        $p->setStart($start);
        
        foreach ($inst as $i) {
            if ($i instanceof IncomeInstance) {
                $p->setEnd($i->getDate()->modify("previous second"));
                array_push($periods, $p);
                $p = new Period();
                $p->setStart($i->getDate());
            }
            $p->add($i);
        }
        
        $p->setEnd($end);
        array_push($periods, $p);
        
        $periods = array_filter($periods, function ($p) {
            return 0 < $p->countItems();
        });
        
        return $periods;
    }
    
    private function balanceExpenses($periods) {
        $rev = array_reverse($periods);
        
        $overflow = [];
        foreach ($rev as $r) {
            $overflow = $r->balance($overflow);
        }
        
        return array_reverse($rev);
    }
    
    private function calculateSavings($periods) {
        usort($this->target, function (TargetItem $a, TargetItem $b) {
            $a_toSave = $a->getValue() - $a->getCurrent();
            $a_duration = $a->getEnd()->getTimestamp() - $a->getStart()->getTimestamp();
            $b_toSave = $b->getValue() - $b->getCurrent();
            $b_duration = $b->getEnd()->getTimestamp() - $b->getStart()->getTimestamp();
            return $b_toSave * 86400 / $b_duration - $a_toSave * 86400 / $a_duration;
        });
        
        foreach ($this->target as $t) {
            $this->calcSingleTarget($periods, $t);
        }
    }
    
    private function calcSingleTarget($periods, TargetItem $target) {
        $total = $target->getCurrent();
        $remaining = $target->getValue() - $total;
        $start = $target->getStart();
        $end = $target->getEnd();
        $name = $target->getName();
        $tVal = $target->getValue();
        
        $periods = array_filter($periods, function ($p) use ($start, $end) {
            return ($p->getStart() < $end) && ($p->getEnd() > $start);
        });
        
        while (0 < count($periods) && ($total < $tVal)) {
            $totalWiggle = array_reduce($periods, function ($acc, $p) {
                return $acc + $p->getWiggle();
            }, 0);

            if ($totalWiggle < $remaining) {
                // We have a problem...
                $target->fail();
                return false;
            }

            $p = array_shift($periods);

            $value = $remaining * $p->getWiggle() / $totalWiggle;
            $total += $value;
            $remaining -= $value;

            $inst = new TargetInstance($name, $value, $total, $tVal, $p->getEnd());

            $p->add($inst);
        }
    }
}
