<?php


namespace Admin\Logic;

class DbMysqlLogic implements DbMysql{
    public function connect() {
        echo __METHOD__ . '<br />';
    }

    public function disconnect() {
        echo __METHOD__ . '<br />';
    }

    public function free($result) {
        echo __METHOD__ . '<br />';
    }

    public function getAll($sql, array $args = array()) {
        echo __METHOD__ . '<br />';
    }

    public function getAssoc($sql, array $args = array()) {
        echo __METHOD__ . '<br />';
    }

    public function getCol($sql, array $args = array()) {
        echo __METHOD__ . '<br />';
    }

    public function getOne($sql, array $args = array()) {
        $params = func_get_args();
        $sql = $params[0];
        $field = '`'.$params[1].'`';
        $table_name = '`'.$params[2].'`';
        $sql = str_replace(array('?F','?T'), array($field,$table_name), $sql);
        $rows = M()->query($sql);
        if($rows){
            $row = array_shift(array_shift($rows));
        }
        return $row;
//        var_dump($row);
//        exit;
    }

    /**
     * 获取一行结果
     * @param type $sql
     * @param array $args
     * @return array
     */
    public function getRow($sql, array $args = array()) {
//        echo __METHOD__ . '<br />';
        $sqls = preg_split('/\?[FTN]/', $sql);
        array_pop($sqls);//移除最后一个多余的元素
        $sql = '';
        $args = func_get_args();
        array_shift($args);
        foreach($sqls as $key=>$value){
            $sql .= $value . $args[$key];
        }
//        echo $sql . '<br />';
        $rows = M()->query($sql);
        return array_shift($rows);
//        echo __METHOD__ . '<br />';
    }

    /**
     * 新增一条记录
     * @param type $sql
     * @param array $args
     */
    public function insert($sql, array $args = array()) {
//        echo __METHOD__ . '<br />';
        $params = func_get_args();
        $sql = $params[0];
        $table_name = $params[1];
        $args = $params[2];
        $sql = str_replace('?T', '`'.$table_name .'`', $sql);
        $sql_tmp = array();
        foreach($args as $key=>$value){
            $sql_tmp[] = "`$key`='$value'";
        }
        $sql_tmp = implode(',', $sql_tmp);
        $sql = str_replace('?%', $sql_tmp, $sql);
//        echo $sql . '<br />';
        return M()->execute($sql);
    }

    /**
     * 执行一条sql语句,可能是update,也可能是别的
     * @param type $sql
     * @param array $args
     * @return integer|bool
     */
    public function query($sql, array $args = array()) {
//        echo __METHOD__ . '<br />';
        $sqls = preg_split('/\?[FTN]/', $sql);
        array_pop($sqls);//移除最后一个多余的元素
        $sql = '';
        $args = func_get_args();
        array_shift($args);
        foreach($sqls as $key=>$value){
            $sql .= $value . $args[$key];
        }
//        echo $sql . '<br />';
        return M()->execute($sql);
    }

    public function update($sql, array $args = array()) {
        echo __METHOD__ . '<br />';
    }

//put your code here
}
