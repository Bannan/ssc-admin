<?php

namespace app\admin\model;

use think\Model;

class History extends Model
{
    // 表名
    protected $name = 'history';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'y_text'
    ];
    

    
    public function getYList()
    {
        return ['1) unsigne' => __('1) unsigne')];
    }     


    public function getYTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['y'];
        $list = $this->getYList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
