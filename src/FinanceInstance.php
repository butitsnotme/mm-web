<?php

/**
 * mm-web
 * Copyright (C) 2019  Dennis Bellinger
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

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
