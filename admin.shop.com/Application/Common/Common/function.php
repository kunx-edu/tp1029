<?php
/**
 * 将关联数组转换成一个下拉列表
 * @param array $data 数据库中的关联数组
 * @param string $value_field 值字段
 * @param string $name_field 名字段
 * @param string $name 表单提交的控件name属性
 * @return string 具体下拉列表的html代码
 */
function arr2select(array $data,$value_field,$name_field,$name=''){
    $html = "<select name='$name' class='$name'>";
    $html .= '<option value="0">请选择...</option>';
    foreach($data as $item){
        $html .= "<option value='{$item[$value_field]}'>{$item[$name_field]}</option>";
    }
    $html .= "</select>";
    return $html;
}