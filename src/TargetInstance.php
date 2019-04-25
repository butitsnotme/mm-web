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
