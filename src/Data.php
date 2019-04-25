<?php

require_once 'Future.php';

class Data {
    private $name;
    private $display_name;
    private $items;
    private $targets;
    
    public function __construct($name, $display_name, $items, $targets) {
        $this->name = $name;
        $this->display_name = $display_name;
        $this->items = $items;
        $this->targets = $targets;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getDisplayName() {
        return $this->display_name;
    }
    
    public function addItem(FinanceItem $item) {
        $this->delItem($item->getName());
        array_push($this->items, $item);
    }
    
    public function delItem($name) {
        $this->items = array_filter($this->items, function ($i) use ($name) {
            return !$i->checkName($name);
        });
    }
    
    public function addTarget(TargetItem $target) {
        $this->delTarget($target->getName());
        array_push($this->targets, $target);
    }
    
    public function delTarget($name) {
        $this->targets = array_filter($this->targets, function ($t) use ($name) {
            return !$t->checkName($name);
        });
    }
    
    public function render($parts = null) {
        $lines = [];
        $f = new Future($this->items, $this->targets);

        $periods = $f->calculate();
        
        array_push($lines, "<table>");
        array_push($lines, "<tr><th>Name<th>Frequency<th>Start Date<th>End Date<th>Amount<th>Action</tr>");

        array_push($lines, "<tr><td colspan=\"6\"><h2>Income Sources</h2></tr>");
        $flag = false;
        foreach ($this->items as $i) {
            $flag = true;
            if ($i instanceof IncomeItem) {
                array_push($lines, $i->render());
            }
        }
        if (!$flag) {
            array_push($lines, "<tr><td colspan=\"6\">No income sources added.</tr>");
        }
        array_push($lines, IncomeItem::renderBlank());

        array_push($lines, "<tr><td colspan=\"6\"><h2>Expenses</h2></tr>");
        $flag = false;
        foreach ($this->items as $e) {
            $flag = true;
            if ($e instanceof ExpenseItem) {
                array_push($lines, $e->render());
            }
        }
        if (!$flag) {
            array_push($lines, "<tr><td colspan=\"6\">No exppenses added.</tr>");
        }
        array_push($lines, ExpenseItem::renderBlank());

        array_push($lines, "<tr><td colspan=\"6\"><h2>Saving Targets</h2></tr>");
        $flag = false;
        foreach ($this->targets as $t) {
            $flag = true;
            array_push($lines, $t->render());
        }
        if (!$flag) {
            array_push($lines, "<tr><td colspan=\"6\">No saving targets added.</tr>");
        }
        array_push($lines, TargetItem::renderBlank());
        
        array_push($lines, "</table>");
        array_push($lines, "<table>");

        array_push($lines, "<tr><td colspan=\"6\"><h2>Future Projection</h2></tr>");
        array_push($lines, "<tr><th>Name<th>Amount<th>So far<th>Target<th>Percentage<th>Date</tr>");
        $flag = false;

        foreach ($periods as $p) {
            $flag = true;
            array_push($lines, $p->render());
        }
        if (!$flag) {
            array_push($lines, "<tr><td colspan=\"6\">No future projection to show.</tr>");
        }
        
        array_push($lines, "</table>");

        return $lines;
    }
    
    public function to_array() {
        return array_merge(
                array_map(
                        function ($i) {
                    $a = $i->to_array();
                    if ($i instanceof IncomeItem) {
                        $a["type"] = "income";
                    }
                    if ($i instanceof ExpenseItem) {
                        $a["type"] = "expense";
                    }
                    return $a;
                }, $this->items),
                array_map(
                        function ($t) {
                    return array_merge($t->to_array(), ["type" => "target"]);
                }, $this->targets)
        );
    }
    
    public static function new($display_name = null) {
        $name = Storage::getName();
        if (null === $display_name) {
            $display_name = $name;
        }
        return new Data($name, $display_name, [], []);
    }
}
