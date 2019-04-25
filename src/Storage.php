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

require_once 'IncomeItem.php';
require_once 'ExpenseItem.php';
require_once 'TargetItem.php';
require_once 'Data.php';

class Storage {
    private const KEY_MAP = [
        "t" => "type",
        "n" => "name",
        "v" => "value",
        "c" => "current",
        "s" => "startDate",
        "e" => "endDate",
        "sc" => "schedule",
        "d" => "date",
    ];
    
    private $dir;
    
    public function __construct($dir) {
        $this->dir = $dir;
    }
    
    public function load($name) {
        $fname = "{$this->dir}/{$name}";
        if (!is_file($fname)) {
            throw new Exception("\$name ($name) does not reference a file");
        }

        $data = json_decode(gzdecode(file_get_contents($fname)), true);
        
        $display_name = $data["name"];
        $data = $data["data"];
        
        $items = [];
        $targets = [];
        
        
        foreach ($data as $d) {
            $a = [];
            foreach ($d as $k => $v) {
                $k = Storage::KEY_MAP[$k];
                if (in_array($k, [  "date", "startDate", "endDate" ])) {
                    $temp = new DateTimeImmutable();
                    $v = $temp->setTimestamp($v);
                }
                $a[$k] = $v;
            }
            
            switch ($a["type"]) {
                case "income":
                    $i = new IncomeItem(
                            $a["name"],
                            $a["value"],
                            $a["schedule"],
                            $a["startDate"],
                            (array_key_exists("endDate", $a) ? $a["endDate"] : null));
                    array_push($items, $i);
                    break;
                case "expense":
                    $e = new ExpenseItem(
                            $a["name"],
                            $a["value"],
                            $a["schedule"],
                            $a["startDate"],
                            (array_key_exists("endDate", $a) ? $a["endDate"] : null));
                    array_push($items, $e);
                    break;
                case "target";
                    $t = new TargetItem(
                            $a["name"],
                            $a["value"],
                            $a["current"],
                            $a["startDate"],
                            $a["endDate"]);
                    array_push($targets, $t);
                    break;
            }
        }
        
        return new Data($name, $display_name, $items, $targets);
    }
    
    public function save(Data $d) {     
        $key_map = array_flip(Storage::KEY_MAP);
        $a = [];
        foreach ($d->to_array() as $i) {
            $a_i = [];
            foreach ($i as $k => $v) {
                $k = $key_map[$k];
                if ($v instanceof DateTimeImmutable) {
                    $v = $v->getTimestamp();
                }
                
                if (null !== $v) {
                    $a_i[$k] = $v;
                }
            }
            array_push($a, $a_i);
        }
        
        $a = [ "name" => $d->getDisplayName(), "data" => $a];
        
        $fname = "{$this->dir}/{$d->getName()}";
        file_put_contents($fname, gzencode(json_encode($a), 9));
    }
    
    public function delete($name) {
        $fname = "{$this->dir}/$name";
        
        unlink($fname);
    }
    
    public static function getName() {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_";

        $id = "";

        for ($i = 0; $i < 64; $i++) {
            $id .= $chars[random_int(0, 63)];
        }

        return $id;
    }
}
