<?php

namespace app\admin\model;

use think\Model;

class Room extends Model
{
    // 表名
    protected $name = 'room';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'c_time_text',
        's_text'
    ];
    

    
    public function getSList()
    {
        return ['1) unsigne' => __('1) unsigne')];
    }     


    public function getCTimeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['c_time'];
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getSTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['s'];
        $list = $this->getSList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    protected function setCTimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }


}
