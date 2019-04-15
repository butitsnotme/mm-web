<?php

class FinanceInstance {
    protected $name;
    protected $value;
    protected $date;
    
    public function __construct($name, $value, $date) {
        if (!is_string($name)) {
            throw new Exception("\$name must be a string");
        }
        
        if (!is_numeric($value)) {
            throw new Exception("\$value must be numeric");
        }
        
        if (!($date instanceof DateTimeImmutable)) {
            throw new Exception("\$date must be a DateTimeImmutable");
        }
        
        $this->name = $name;
        $this->value = $value;
        $this->date = $date;
    }
    
    public function render() {
        ob_start();
?>
<tr>
    <td><?php echo $this->name; ?>
    <td><?php echo $this->getStrValue(); ?>
    <td><?php echo $this->date->format(FinanceItem::DATE_FORMAT); ?>
</tr>
<?php
        $r = ob_get_contents();
        ob_end_clean();
        return $r;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getDate() {
        return $this->date;
    }
    
    public function getValue() {
        return $this->value;
    }
    
    protected function getStrValue() {
        return "\${$this->value}<td><td><td>";
    }
}
