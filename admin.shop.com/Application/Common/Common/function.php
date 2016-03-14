<?php

/**
 * 将关联数组转换成一个下拉列表
 * @param array $data 数据库中的关联数组
 * @param string $value_field 值字段
 * @param string $name_field 名字段
 * @param string $name 表单提交的控件name属性
 * @return string 具体下拉列表的html代码
 */
function arr2select(array $data, $value_field, $name_field, $name = '', $selected_value = '') {
    $html = "<select name='$name' class='$name'>";
    $html .= '<option value="">请选择...</option>';
    foreach ($data as $item) {
        if ($selected_value == $item[$value_field]) {
            $html .= "<option value='{$item[$value_field]}' selected='selected'>{$item[$name_field]}</option>";
        } else {
            $html .= "<option value='{$item[$value_field]}'>{$item[$name_field]}</option>";
        }
    }
    $html .= "</select>";
    return $html;
}

/**
 * 将索引数组转换为关联数组
 * @param array $data
 * @param string $index_name
 */
function index2assoc(array $data, $index_name = 'id') {
    $return = array();
    foreach ($data as $item) {
        $return[$item[$index_name]] = $item;
    }
    return $return;
}

/**
 * 一位数组转换成下拉列表
 * @param array $data
 * @param string $name
 * @return string
 */
function onearr2select(array $data, $name = '', $selected_value = '') {
    
    
    $html = "<select name='$name' class='$name'>";
    $html .= '<option value="">请选择...</option>';
    foreach ($data as $key => $value) {
        $key = (string)$key;
        /**
         * 在弱类型中,0 false '' null  使用==都一样
         * 并且在post和get的数据传输中都是字符串
         */
        if ($selected_value === $key) {
            $html .= "<option value='$key' selected='selected'>$value</option>";
        } else {
            $html .= "<option value='$key'>$value</option>";
        }
    }
    $html .= "</select>";
    return $html;
}
