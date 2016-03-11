<?php

$sql = 'SELECT ?F, ?F, ?F, ?F FROM ?T WHERE ?F = ?N';
$params = array('SELECT ?F, ?F, ?F, ?F FROM ?T WHERE ?F = ?N','parent_id','lft','rght','level','goods_category','id',12);

array_shift($params);
$sqls = preg_split('/\?[FTN]/', $sql);
array_pop($sqls);
var_dump($sqls);
//exit;
$tmp_url = '';
foreach($sqls as $key=>$item){
    $tmp_url .= $item .  $params[$key];
}
echo $tmp_url;