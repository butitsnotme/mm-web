<?php

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
