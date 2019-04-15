<?php

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
