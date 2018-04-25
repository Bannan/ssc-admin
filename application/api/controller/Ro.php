<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\User;
use app\admin\model\Rule;
use app\admin\model\History;
use think\Request;

/**
 * 首页接口
 */
class Ro extends Api
{

    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];
    private $userinfo = null;
    public function _initialize()
    {
        parent::_initialize();

        $this->userinfo = $this->score($this->request->request("uid"));

    }

    /**
     * 创建房间
     *
     */
    public function cr()
    {
        /*验证这个用户是否为玩家*/
        if($this->userinfo["role"] == 0)
        {
            $this->error(" ");
        }
        $num = 5;
        $userinfo = $this->userinfo;
        $room = new  \app\admin\model\Room;
        $arr = json_decode(file_get_contents("http://f.apiplus.net/cqssc-1.json"),true);

        if($arr["data"][0]["expect"])
        {
            $expect = (int)$arr["data"][0]["expect"];
            $room->q = $expect+2;//当前最新期数延迟2期
            $room->f_id = $userinfo["id"];  //开房房主
            $room->r_key = md5(md5($expect+2)."key".rand(1,9999));
            $room->c_time = time();
            if(strtotime($userinfo["tianka"]) > time())
            {
                $room->save();
                $this->success('',["r_key"=>$room->r_key]);
            }
            if($userinfo["yundian"] >= $num)
            {
                $user = new User;
                $user->where("id=".$userinfo["id"])->update(["yundian"=>$userinfo["yundian"]-$num]);
                $room->save();
                $this->success('',["r_key"=>$room->r_key]);
            }

            $this->error('开房失败！请充值运点或天卡！');
        }
        else{
            $this->error("开奖接口异常！");
        }
    }

    /*
     *      user信息
     */
    private function score($uid)
    {
        return User::where("id","=",$uid)->find();
    }

    /*
     *      获取房间信息
     */

    public function init()
    {
        if($this->userinfo != null)
        {
            $room = \app\admin\model\Room::where("r_key","=",$this->request->request("key"))->find();

            if($room || $room["s"] != 1)
            {
                if($room["c_time"]+600 < time())
                {
                    \app\admin\model\Room::where("r_key",$this->request->request("key"))->update(["s"=>1]);
                    $this->success("",["state"=>0]);
                }
                if($room["f_id"] == $this->userinfo["id"])
                {
                   $result = [
                       "state"=>1,
                       "end_time"=>$room["c_time"]+600,
                       "record"=>$this->record($room["id"]),
                   ];
                }else
                {
                    $result = [
                        "state"=>2,
                        "end_time"=>$room["c_time"]+600,
                        "record"=>$this->record($room["id"]),
                        "list"=>$this->show_g()
                    ];
                }
                $this->success("",$result);
            }
        }
        $this->success("",["state"=>0]);
    }

    /*
     *      获取游戏列表
     */
    private function show_g()
    {
        return Rule::select();
    }
    /*
     *      获取下注记录
     */
    private function record($b_id)
    {
        return History::where("b_id","=",$b_id)->select();
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
