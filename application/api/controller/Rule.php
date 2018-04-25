<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\User;
use app\admin\model\Rule;
use app\admin\model\history;
use think\Request;

/**
 * 首页接口
 */
class Ro extends Api
{

    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    /*
     *      获取游戏列表
     */
    public function show_g()
    {
        $this->success('ok',Rule::select());
    }

    /*
     *         展示桌面信息
     *
     */
    public function show_r()
    {
        $b = new B;
        $room = $b->where("id","=",$this->request->request("r_id"))->find();
        if($room["c_time"]+600 < time())
        {
            $this->error("过期了！");
        }
        $history = new history;
    }
}
