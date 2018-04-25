<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\User;
use app\admin\model\Room;
use app\admin\model\Rule;
use app\admin\model\History;
use think\Request;

/**
 * 下注
 */
class Bet extends Api
{

    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];
    static public $userinfo = null;
    private $room_info = null;
    public function _initialize()
    {
        parent::_initialize();

        $this->userinfo = $this->score($this->request->request("uid"));
        $this->room = $this->room_info($this->request->request("key"));

        if(!$this->room)
        {
            //不存在这个房间，直接跳到主页
            $this->error("不存在！");
        }
        if($this->room["s"] == 1)
        {
            //房间已经结束，跳到历史记录
            $this->success("",["state"=>0]);
        }
        if($this->room["c_time"]+600 < time())
        {
            Room::where("r_key", $this->room["r_key"])->update(["s"=>1]);
            //房间已经结束，跳到历史记录
            $this->success("",["state"=>0]);
        }
    }

   /*
    *   下注接口
    */
    public function x()
    {
        $post = $this->request->request();
        if($post["g"] && $post["s"] && $post["num"])
        {
            $info = $this->check($post["g"],$post["s"]);

            $u_num = json_decode($this->userinfo["json_score"],true);



            if($post["num"] > $u_num[$this->userinfo["id"]])
            {
                //积分不足
                $this->success("积分不足！",["state"=>1]);
            }

            $u_num[$this->userinfo["id"]] = $u_num[$this->userinfo["id"]] - $post["num"];

            if(User::where("id", "=", $this->userinfo["id"])->update(["json_score"=>json_encode($u_num)]))
            {
                $h = new History;
                $h->b_id = $this->room["id"];
                $h->u_id = $this->userinfo["id"];
                $h->g = $post["g"];
                $h->s = $post["s"];
                $h->info = $info;
                $h->num = $post["num"];
                $h->c_time = time();
                $h->save();
                $this->success("",["state"=>3]);
            }
        }
        $this->success("参数不齐全",["state"=>2]);
    }
    /*
     *      user信息
     */
    private function score($uid)
    {
        return User::where("id", "=", $uid)->find();
    }

    private function room_info($key)
    {
        return Room::where("r_key", "=", $key)->find();
    }

    /*
     *      验证下注数据是否真实
     */

    private function check($g,$s)
    {

        $arr = explode("-",$s);
        $rule = Rule::select();
        switch($g)
        {
            case "1": //大小单双
                if($arr[0] > 5 || $arr[0] < 0 || $arr[1] > 5 || $arr[1] < 0 || count($arr) < 2)
                {
                    $this->error("数据不对！");
                }
                if($arr[1] == 5)
                {
                    if($arr[2] > 9 || $arr[2] < 0 || count($arr) != 3)
                    {
                        $this->error("数据不对！");
                    }
                }
                $str = isset($arr[2]) ? "-".$arr[2] : "";

                return $arr[0]."号球(".explode(",", $rule[0]["rule"])[$arr[1]-1].$str.")";
                break;
            case "2": //龙虎和
                if($arr[0] >3 || $arr[0] < 0 || !isset($arr[0]))
                {
                    $this->error("数据不对！");
                }

                return explode(",", $rule[1]["rule"])[$arr[0]-1];
                break;
            case "3": //豹子
                if($arr[0] >3 || $arr[0] < 0 || !isset($arr[0]))
                {
                    $this->error("数据不对！");
                }

                return "豹子-".explode(",", $rule[2]["rule"])[$arr[0]-1];
                break;
            case "4": //买总
                if($arr[0] >4 || $arr[0] < 0 || !isset($arr[0]))
                {
                    $this->error("数据不对！");
                }

                return "买总-".explode(",", $rule[3]["rule"])[$arr[0]-1];
                break;
        }
    }
}
