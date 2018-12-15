<?php namespace YiZan\Http\Controllers\Wap;
use Illuminate\Support\Facades\Response;
use Input, View, Cache, Session,Time;

/**
 * 物业报修
 */
class RepairController extends UserAuthController {

	public function __construct() {
		parent::__construct();
		View::share('nav','forum');
	}

    public function index() {
    	$args = Input::all();
    	$list = $this->requestApi('property.repairlists', $args);

    	if ($list['code'] == 0) {
    		View::share('list', $list['data']);
    	}
    	View::share('args', $args);
        $districtId = Input::get('districtId');
        if (!empty($districtId) ) {
            $nav_back_url = u('Property/index', ['districtId'=>$districtId]);
            View::share('nav_back_url',$nav_back_url);
        }

        return $this->display();
    }
    
    public function detail() {
    	$args = Input::all();
    	$data = $this->requestApi('property.repairget', $args);
//    	print_r($data);exit;
    	if ($data['code'] == 0) {
    		View::share('data', $data['data']);
    	}
        View::share('args', $args);

        return $this->display();
    }

    public function repair() {
    	$args = Input::all();
    	$data = $this->requestApi('district.get', ['districtId'=>$args['districtId']]);
    	View::share('data', $data['data']);

    	$list = $this->requestApi('property.typelists');
    	//print_r($list);
    	if ($list['code'] == 0) {
    		View::share('list', $list['data']);
    	}
        $apitime = $this->apitime();
        View::share('apitime', $apitime);


    	View::share('args', $args);
        return $this->display();
    }

    //连续7天的时间
    public function apitime(){
        $timeList = [];
        $time = [];
        $weekarray = array("日","一","二","三","四","五","六");

        for($i=0;$i<7;$i++) {
            $timeList[$i]['time'] = [];
            for ($o = 0; $o < 48; $o++) {
                $hours = Time::toDate(Time::getNowDay() + $o * 1800, "H:i");
                array_push($timeList[$i]['time'], $hours);
            }
        }

        for($i=0;$i<7;$i++){
            //获取未来每一天的时间戳 和 星期
            $nowTime = UTC_TIME + 86400 * $i;
            $week = date("w",  UTC_TIME + 86400 * $i);

            if($i == 0) {
                $dayName = '今天';
            }
            else if($i == 1) {
                $dayName = '明天';
            }
            else {
                $dayName = explode('-', Time::toDate($nowTime, 'm-d')); //x月x号
                $dayName = $dayName[0].'月'.$dayName[1].'日';
            }
            $dayName .= '(周' . $weekarray[$week] . ')'; //周几

            $time[] = [
                'time'		=> Time::toDate($nowTime, 'Y-m-d'),
                'dayName' 	=> $dayName,
                'list'	  	=> $timeList[$week]['time'],
            ];

            foreach ($time as $key => $value) {
                foreach ($value['list'] as $k => $v) {
                    $time[$key]['timestamp'][$k] = Time::toTime($value['time'].' '.$v);
                }
            }

            //8点之前不上班 9点上班
            foreach ($timeList[$week]['time'] as $key => $value) {
                if (str_replace(":", "", $value) < 900 || str_replace(":", "", $value) >2100) {
                    unset($time[$i]['list'][$key]);
                    unset($time[$i]['timestamp'][$key]);
                }
            }

            if(Time::toDate($nowTime, 'Y-m-d') == Time::toDate(UTC_TIME, 'Y-m-d'))
            {
                $go = true;	//是否是立即送出
                foreach ($timeList[$week]['time'] as $key => $value)
                {
                    //删除不可预约时间
                    if( str_replace(":", "", $value) < Time::toDate(UTC_TIME+ 30 * 60, 'Hi') )
                    {
                        unset($time[0]['list'][$key]);
                        unset($time[0]['timestamp'][$key]);
                    }
                    else
                    {
                        //立即送出
                        if($go)
                        {
                            $time[0]['list'][0] = Time::toDate(UTC_TIME + 30 * 60, 'H:i');
                            $time[0]['timestamp'][0] = UTC_TIME + 30 * 60;
                            $go = false;
                        }
                    }
                }
            }

            ksort($time[0]['list']);
            ksort($time[0]['timestamp']);
        }

        return $time;
    }

    public function save() {
	    $args = Input::all();
	   
	    $result = $this->requestApi('property.createrepair',$args);
	    return Response::json($result);
	}

    public function rate(){
        $args = Input::all();
        View::share('args',$args);

        return $this->display();
    }

    public function dorate(){
        $data = Input::all();
        $result = $this->requestApi("property.createrate",$data);	//周边店评价

        return Response::json($result);
    }

}