<?php

abstract class FinanceItem {
    public const SEMI_MONTHLY = 0;
    public const BI_WEEKLY = 1;
    public const MONTHLY = 2;
    public const DATE_FORMAT = "l, F jS, Y";
    
    protected $name;
    protected $value;
    protected $schedule;
    protected $startDate;
    protected $endDate;
    
    public function __construct($name, $value, $schedule, $start, $end = null) {
        if (!is_string($name)) {
            throw new Exception("\$name must be a string");
        }
        
        if (!is_numeric($value)) {
            throw new Exception("\$value must be numeric");
        }
        
        if (!in_array($schedule, [
            FinanceItem::SEMI_MONTHLY,
            FinanceItem::BI_WEEKLY,
            FinanceItem::MONTHLY ])) {
            throw new Exception("\$schedule must be a valid schedule");
        }
        
        if (!($start instanceof DateTimeImmutable)) {
            throw new Exception("\$start must be a DateTimeImmutable");
        }
        
        if (!($end instanceof DateTimeImmutable) && null !== $end) {
            throw new Exception("\$end must be a DateTimeImmutable or null");
        }
        
        $this->name = $name;
        $this->value = $value;
        $this->schedule = $schedule;
        $this->startDate = $start;
        $this->endDate = $end;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function checkName($name) {
        return $this->name === $name;
    }
    
    public function getStart() {
        return $this->startDate;
    }
    
    public function getEnd() {
        return $this->endDate;
    }
    
    public function to_array() {
        return [
            "name" => $this->name,
            "value" => $this->value,
            "schedule" => $this->schedule,
            "startDate" => $this->startDate,
            "endDate" => $this->endDate
        ];
    }
    
    public function render() {
        $sd = $this->startDate->format(FinanceItem::DATE_FORMAT);
        $ed = is_null($this->endDate) ? "Ongoing" : $this->endDate->format(FinanceItem::DATE_FORMAT);
        $sc = "";
        if (FinanceItem::SEMI_MONTHLY == $this->schedule) {
            $sc = "Semi-Monthly";
        } elseif (FinanceItem::BI_WEEKLY == $this->schedule) {
            $sc = "Bi-Weekly";
        } else {
            $sc = "Monthly";
        }
        
        $rem = "<a href=\"delete.php?name={$this->name}\">[X]</a>";
        return "<tr><td>{$this->name}</td><td>$sc</td><td>$sd</td><td>$ed</td><td>\${$this->value}</td><td>$rem</td></tr>";
    }
    
    public function enumerate(DateTimeImmutable $start, DateTimeImmutable $end) {
        $cur = $this->startDate;
        
        if ($start->getTimestamp() < $this->startDate->getTimestamp()) {
            $start = $this->startDate;
        }
        
        if (!is_null($this->endDate) && $end->getTimestamp() > $this->endDate->getTimestamp()) {
            $end = $this->endDate;
        }
        
        $nearEndOfMonth = 28 < intval($this->startDate->format("d"));
        $per = new DateInterval("P0D");
        switch ($this->schedule) {
            case FinanceItem::SEMI_MONTHLY:
                $per->d = 17;
                break;
            case FinanceItem::BI_WEEKLY:
                $per->d = 14;
                break;
            case FinanceItem::MONTHLY:
                $per->m = 1;
                break;
        }

        $s = $this->schedule;
        $advance = function (DateTimeImmutable $d) use ($per, $s, $nearEndOfMonth) {
            $d = $d->add($per);
            if (FinanceItem::MONTHLY == $s && $nearEndOfMonth) {
                $curDate = $d->format("d");
                if (15 > $curDate) {
                    $d = $d->modify("last day of last month");
                } else {
                    $targetDate = $this->startDate->format("d");
                    $curDate = $d->format("d");
                    $lastDateOfMonth = $d->format("t");
                    if ($curDate < $targetDate && $curDate <= $lastDateOfMonth) {
                        $d = $d->add(new DateInterval("P" . ($targetDate - $curDate) . "D"));
                    }
                }
            }
            if (FinanceItem::SEMI_MONTHLY == $s) {
                $day = intval($d->format("d"));
                if (15 < $day) {
                    $p = new DateInterval("P" . ($day - 15) . "D");
                    $d = $d->sub($p);
                } else {
                    $p = new DateInterval("P${day}D");
                    $d = $d->sub($p);
                }
            }
            
            return $d;
        };
        
        while ($cur < $start) {
            $cur = $advance($cur);
        }
        
        $o = [];
        while ($cur <= $end) {
            $inst = $this->getInstance($cur);
            
            array_push($o, $inst);
            $cur = $advance($cur);
        }
        
        return $o;
    }
    
    protected function getInstance(DateTimeImmutable $date) {}
    
    public static function renderBlank() {
        return "";
    }
}
