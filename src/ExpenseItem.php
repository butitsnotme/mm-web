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

require_once 'FinanceItem.php';
require_once 'ExpenseInstance.php';

class ExpenseItem extends FinanceItem {
    protected function getInstance(\DateTimeImmutable $date) {
        return new ExpenseInstance($this->name, $this->value, $date);
    }
    
    public static function renderBlank(): string {
        ob_start();
?>
<tr><td colspan="6"><h2>Add or Update an Expense</h2></td></tr>
<tr><form method="POST" action="update.php">
    <input type="hidden" name="type" value="expense">
    <td><input type="text" name="name" placeholder="Name">
    <td><select name="schedule">
        <option value="<?php echo FinanceItem::SEMI_MONTHLY; ?>">Semi-Monthly</option>
        <option value="<?php echo FinanceItem::BI_WEEKLY; ?>">Bi-Weekly</option>
        <option value="<?php echo FinanceItem::MONTHLY; ?>">Monthly</option>
    </select>
    <td><input type="date" name="start" placeholder="Start Date">
    <td><input type="date" name="end" placeholder="End Date">
    <td><input type="number" name="value" placeholder="Amount">
    <td><input type="submit" value="Submit">
</form></tr>
<?php
        
        $r = ob_get_contents();
        ob_end_clean();
        return $r;
    }
}
