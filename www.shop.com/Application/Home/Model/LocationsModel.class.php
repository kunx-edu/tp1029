<?php


namespace Home\Model;

class LocationsModel extends \Think\Model{
    /**
     * 获取省级城市
     */
    public function getTopLocations(){
        return $this->where('parent_id=0')->select();
    }
    /**
     * 获取子级列表
     * @param type $parent_id
     * @return type
     */
    public function getLoctionsByParentId($parent_id){
        return $this->where('parent_id='.$parent_id)->select();
    }
    
    
    
}
