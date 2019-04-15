<?php

class TargetItem {
    protected $name;
    protected $value;
    protected $current;
    protected $start;
    protected $end;
    
    public function __construct($name, $value, $current, $start, $end) {
        if (!is_string($name)) {
            throw new Exception("\$name must be a string");
        }
        
        if (!is_numeric($value)) {
            throw new Exception("\$value must be a number");
        }
        
        if (!is_numeric($current)) {
            throw new Exception("\$current must be a number");
        }
        
        if (!($start instanceof DateTimeImmutable)) {
            throw new Exception("\$start must be a DateTimeImmutable");
        }
        
        if (!($end instanceof DateTimeImmutable)) {
            throw new Exception("\$end must be a DateTimeImmutable");
        }
        
        $this->name = $name;
        $this->value = $value;
        $this->current = $current;
        $this->start = $start;
        $this->end = $end;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getStart() {
        return $this->start;
    }
    
    public function getEnd() {
        return $this->end;
    }
    
    public function getValue() {
        return $this->value;
    }
    
    public function getCurrent() {
        return $this->current;
    }
    
    public function checkName($name) {
        return $this->name === $name;
    }
    
    public function to_array() {
        return [
            "name" => $this->name,
            "value" => $this->value,
            "current" => $this->current,
            "startDate" => $this->start,
            "endDate" => $this->end,
        ];
    }
    
    public function render() {
        $sd = $this->start->format(FinanceItem::DATE_FORMAT);
        $ed = $this->end->format(FinanceItem::DATE_FORMAT);
        $perc = $this->current / $this->value * 100;
        $rem = "<a href=\"delete.php?type=target&name={$this->name}\">[X]</a>";
        return "<tr><td>{$this->name}<td><td>$sd<td>$ed<td>\${$this->current} / \${$this->value} ($perc%) <td>$rem</tr>";
    }
    
    public static function renderBlank() {
        ob_start();
?>
<tr><td colspan="6"><h2>Add or Update a Saving Target</h2></td></tr>
<tr><form method="POST" action="update.php">
    <input type="hidden" name="type" value="target">
    <td><input type="text" name="name" placeholder="Name">
    <td><input type="number" name="current" placeholder="Amount Saved">
    <td><input type="date" name="start" placeholder="Start Date">
    <td><input type="date" name="end" placeholder="End Date">
    <td><input type="number" name="value" placeholder="Total to Save">
    <td><input type="submit" value="Submit">
</form></tr>
<?php
        
        $r = ob_get_contents();
        ob_end_clean();
        return $r;
    }
}