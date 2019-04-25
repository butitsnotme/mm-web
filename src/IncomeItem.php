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
require_once 'IncomeInstance.php';

class IncomeItem extends FinanceItem {
    public function getInstance(\DateTimeImmutable $date) {
        return new IncomeInstance($this->name, $this->value, $date);
    }
    
    public static function renderBlank(): string {
        ob_start();
?>
<tr><td colspan="6"><h2>Add or Update an Income Source</h2></td></tr>
<form method="POST" action="update.php">
    <tr><input type="hidden" name="type" value="income">
    <td><input type="text" name="name" placeholder="Name"></td>
    <td><select name="schedule">
        <option value="<?php echo FinanceItem::SEMI_MONTHLY; ?>">Semi-Monthly</option>
        <option value="<?php echo FinanceItem::BI_WEEKLY; ?>">Bi-Weekly</option>
        <option value="<?php echo FinanceItem::MONTHLY; ?>">Monthly</option>
        </select></td>
    <td><input type="date" name="start" placeholder="Start Date"></td>
    <td><input type="date" name="end" placeholder="End Date"></td>
    <td><input type="number" name="value" placeholder="Amount"></td>
    <td><input type="submit" value="Submit"></td></tr>
</form>
<?php
        
        $r = ob_get_contents();
        ob_end_clean();
        return $r;
    }
}
