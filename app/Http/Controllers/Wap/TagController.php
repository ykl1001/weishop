<?php namespace YiZan\Http\Controllers\Wap;

use Illuminate\Support\Facades\Cache;
use View, Input, Redirect, Response, Session;

class TagController extends BaseController {
	public function __construct() {
		parent::__construct();
		View::share('nav','tag');
	}

	public function index() {
        $is_service = [];
        $is_service['data'] = 1;
        $defaultAddress = Session::get("defaultAddress");
        if(!empty($defaultAddress)){
            //判断该城市是否开通
            $is_service =  $this->requestApi('user.address.getisservice',['cityId'=>$defaultAddress['cityId']]);
        }
        View::share("cityIsService", $is_service['data']);

        //分类
        //用户端只查询未锁定的分类
        $args = ['status'=>1];
        $data = [];

        $data = Cache::get('tagindex');
        if(empty($data)){
            $result = $this->requestApi('systemTag.lists',$args);
            while (count($result['data']) > 0) {
                $value = array_shift($result['data']);
                if($value['pid'] == 0)
                {
                    //  一级分类
                    $data[$value['id']]['name'] = $value['name'];
                    $data[$value['id']]['id'] = $value['id'];
                    $data[$value['id']]['sort'] = $value['sort'];
                }
                else{
                    //二级分类
                    if($value['tag']['id'] > 0)
                    {
                        //存在二级分类标签
                        if(!$data[$value['pid']]['twoLevel'][$value['tag']['id']])
                        {
                            $data[$value['pid']]['twoLevel'][$value['tag']['id']]['name'] = $value['tag']['name'];
                        }

                        //三级分类
                        $data[$value['pid']]['twoLevel'][$value['tag']['id']]['threeLevel'][$value['id']] = $value;
                        unset($data[$value['pid']]['twoLevel'][$value['tag']['id']]['threeLevel'][$value['id']]['tag']);
                        unset($data[$value['pid']]['twoLevel'][$value['tag']['id']]['threeLevel'][$value['id']]['sort']);
                        unset($data[$value['pid']]['twoLevel'][$value['tag']['id']]['threeLevel'][$value['id']]['status']);
                        unset($data[$value['pid']]['twoLevel'][$value['tag']['id']]['threeLevel'][$value['id']]['createTime']);
                    }
                    else
                    {
                        //不存在二级分类标签
                        if(!$data[$value['pid']]['twoLevel'][0])
                        {
                            $data[$value['pid']]['twoLevel'][0]['name'] = $value['tag']['name'];
                        }
                        //三级分类
                        $data[$value['pid']]['twoLevel'][0]['threeLevel'][$value['id']] = $value;
                        unset($data[$value['pid']]['twoLevel'][0]['threeLevel'][$value['id']]['tag']);
                        unset($data[$value['pid']]['twoLevel'][0]['threeLevel'][$value['id']]['sort']);
                        unset($data[$value['pid']]['twoLevel'][0]['threeLevel'][$value['id']]['status']);
                        unset($data[$value['pid']]['twoLevel'][0]['threeLevel'][$value['id']]['createTime']);
                    }
                }
            }
            $data = arraySort($data, 'sort', 'asc');
            Cache::put('tagindex',$data,5);
        }

        View::share('data', $data);
        View::share('nav_back_url', $_SERVER["HTTP_REFERER"]);
        return $this->display();
	}

	public function goodsLists() {
		return $this->goodsListsItem('goodslists');
	}

	public function goodsListsItem($tpl='goodslistsitem') {
		$args = Input::all();
		$args['type'] = !empty($args['type']) ? $args['type'] : 1;  //1=价格 2=距离

		$tag = $this->requestApi('systemTag.checktag',['tagPid'=>$args['pid'],'tagId'=>$args['id']]);
		if($tag['code'] == 0)
		{
			View::share('tag', $tag['data']);
		}
		
        $address = Session::get("defaultAddress");
        $goodsArgs['apoint'] = $address['mapPointStr'];
		$goodsArgs['systemListId'] = $tag['data']['id'];
		$goodsArgs['type'] = $args['type'];
		$goodsArgs['page'] = $args['page'];

		$lists =  $this->requestApi('goods.goodstaglists',$goodsArgs);
		if($lists['code'] == 0)
		{
			View::share('lists', $lists['data']);
		}
		// dd($lists['data']);
		View::share('args', $args);

		return $this->display($tpl);
	}

}
