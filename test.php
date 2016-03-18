<?php
$xml = '<xml id="3"><item>zhangsan</item><item>李四</item></xml>';

$xml = simplexml_load_string($xml);
echo $xml['id'];