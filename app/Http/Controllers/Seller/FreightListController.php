<?php 
namespace YiZan\Http\Controllers\Seller;

use YiZan\Models\Order;
use YiZan\Http\Requests\OrderCreatePostRequest;
use View, Input, Lang, Route, Page, Form, Format, Response,Time,Redirect,Session;
/**
 * 运费管理
 */
class FreightListController extends AuthController {

	public function index() {


        $args = Input::all();
        $res1 = $this->requestApi('sellerstaff.freightList', ['isDefault' => 1]);
        if ($res1['code'] == 0) {
            $list['default'] = $res1['data'][0];
        }
        $res2 = $this->requestApi('sellerstaff.freightList', ['isDefault' => 0]);
        if ($res2['code'] == 0) {
            foreach ($res2['data'] as $key => $value) {
                $list['other'][$key]['city'] = $value['city'];
                $list['other'][$key]['data'] = $value;
            }
        }
        //获取城市
        $city = $this->requestApi('city.getCityList', ['pid' => 0, 'level' => 1]);
        $cityList = [];
        foreach ($city['data'] as $key => $value) {
            $cityList[$value['id']] = $value['name'];
        }
        $data = [];
        foreach ($list['other'] as $key => $value) {
            $first = true;
            foreach ($value['city'] as $k => $v) {
                if ($first) {
                    if(!is_array($v)){
                        $list['other'][$key]['pid'] =  $v;
                    }else{
                        $list['other'][$key]['pid'] = $k;
                        $data['show'][$value['data']['id']]['city'][$k] =  $v;
                    }
                    $list['other'][$key]['cityName'] = $cityList[$k];
                    $first = false;
                } else {
                    if(!is_array($v)){
                        $list['other'][$key]['pid'] .= ',' . $v;
                    }else{
                        $list['other'][$key]['pid'] .= ',' . $k;
                        $data['show'][$value['data']['id']]['city'][$k] =  $v;
                    }
                    $list['other'][$key]['cityName'] .= ',' . $cityList[$k];
                }

            }
        }
        View::share('list', $list);
		View::share('args',$args);
        Session::set('saveCheckLocationSeller', $data['show']);
        Session::save();
		return $this->display();
	}
    public function region() {

        $args = Input::all();
        //获取开通城市
        $result = $this->requestApi('city.getCityList', ['pid' => 0, 'level' => 1]);
        if ($result['code'] == 0) {
            $lists = $this->letterAsc($result['data'], $args['modelId']);
            View::share("lists", $lists);
        }
        return $this->display();
    }
    public function regionajax() {

        $args = Input::all();
        $s = [];
        if(Input::ajax()){
            $s = ['pid' => $args['pid'], 'level' => 2];
        }else{
            $s = ['pid' => 0, 'level' => 1];
        }
        $result = $this->requestApi('city.getCityList',$s );
        if ($result['code'] == 0) {
            $lists = $this->letterAsc($result['data'], $args['modelId']);
            View::share("lists", $lists);
        }
        return Response::json($lists);
    }
    /**
     * [saveCheckLocation 保存已选地址]
     * @return [type] [description]
     */
    public function saveCheckLocation()
    {
        $args = Input::all();
        $data = Session::get('saveCheckLocationSeller');
        if ($args['pid'] > 0) {
            //保存二级城市编号
            if ($args['ids']) {
                $data[$args['modelId']]['city'][$args['pid']] = $args['ids'];
            } else {
                unset($data[$args['modelId']]['city'][$args['pid']]);
            }

        } else {
            //清除一级城市编号
            foreach ($data[$args['modelId']]['city'] as $key => $value) {
                if (gettype($value) == "string") {
                    unset($data[$args['modelId']]['city'][$key]);
                }
            }
            //保存一级城市编号
            foreach ($args['ids'] as $key => $value) {
                if ($value)
                    $data[$args['modelId']]['city'][$value] = $value;
            }
        }

        Session::set('saveCheckLocationSeller', $data);
        Session::save();
    }

    /**
     * [letterAsc 按字母排序]
     * @param  [type] $lists [description]
     * @return [type]        [description]
     */
    public function letterAsc($data, $modelId)
    {
        $lists = [];
        $selected = Session::get('saveCheckLocationSeller');
        $selected = $selected[$modelId]['city'];

        foreach ($data as $key => $value) {
            if ($value['pid'] > 0) {
                $value['selected'] = in_array($value['id'], $selected[$value['pid']]);  //是否选择（二级）
            } else {
                if (gettype($selected[$value['id']]) == "string") {
                    $value['allselected'] = true;   //全选（一级）
                } else {
                    $value['selected'] = array_key_exists($value['id'], $selected); //非全选（一级）
                }
            }
            $lists[$value['firstChar']][] = $value;
        }

        ksort($lists);
        return $lists;
    }
    /**
     * [saveFreight 保存运费模版]
     * @return [type] [description]
     */
    public function save()
    {
        $args = Input::all();
        $data = [];
        $selected = Session::get('saveCheckLocationSeller');
        $data[0] = [
            'defaultNum' => $args['defaultNum'],
            'defaultMoney' => $args['defaultMoney'],
            'defaultAddNum' => $args['defaultAddNum'],
            'defaultAddMoney' => $args['defaultAddMoney'],
            'isDefault' => 1,
        ];
        foreach($args['otherNum'] as $k => $v){
            if(!$args['ids'][$k]){
                return $this->error('请选择城市');
                return false;
            }
            $data[$k+1] = [
                'otherNum' => $args['otherNum'][$k],
                'otherMoney' => $args['otherMoney'][$k],
                'otherAddNum' => $args['otherAddNum'][$k],
                'otherAddMoney' => $args['otherAddMoney'][$k],
                'pid' => $args['ids'][$k],
                'cid' => $selected[$args['cid'][$k]]['city'],
            ];
        }
        $result = $this->requestApi('sellerstaff.saveFreight', ['data'=>$data]);

        if ($result['code'] == 0) {
            //删除闪存数据
            Session::set('saveCheckLocationSeller', null);
            Session::save();
        }

        if( $result['code'] > 0 ) {
            return $this->error($result['msg']);
        }
        return $this->success(Lang::get('admin.code.98008'), u('FreightList/index'), $result['data']);
    }
}
