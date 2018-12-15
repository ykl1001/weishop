<?php namespace YiZan\Services;

use YiZan\Models\CityLocation;
use YiZan\Models\Region;
use YiZan\Models\Seller;

use YiZan\Utils\String;
use YiZan\Utils\Http;
use DB, Cache;

class RegionService extends BaseService {

    public static function getById($id) {
        $region = Region::find($id);
        if ($region) {
            return $region->toArray();
        }
        return false;
    }

    public static function getCityByIp($ip) {
        $key = 'ip-'.$ip;
        $location = Cache::get($key);
        if ($location) {
            return $location;
        }

        $location = ['province'=>0, 'city'=>0];

        /*$result = Http::get('http://int.dpool.sina.com.cn/iplookup/iplookup.php', ['format'=>'json', 'ip' => $ip]);
        $result = empty($result) ? false : @json_decode($result, true);
        if ($result && $result['ret'] == 1) {
            if (!empty($result['province'])) {
                $location['province'] = self::getIdByName($result['province']);
                if ($location['province'] > 0) {
                    $location['city'] = self::getIdByName($result['city'], $location['province']);
                    Cache::forever($key, $location);
                    return $location;
                }
            }
        }*/

        $result = Http::get('http://ip.taobao.com/service/getIpInfo2.php', ['ip' => $ip]);
        $result = empty($result) ? false : @json_decode($result, true);
        if ($result && $result['code'] == 0 && isset($result['data'])) {
            if (!empty($result['data']['region'])) {
                $location['province'] = self::getIdByName($result['data']['region']);
                if ($location['province'] > 0) {
                    $location['city'] = self::getIdByName($result['data']['city'], $location['province']);
                    Cache::forever($key, $location);
                    return $location;
                }
            }
        }

        $result = Http::get('http://whois.pconline.com.cn/ipJson.jsp', ['ip' => $ip]);
        if (!empty($result)) {
            $result = trim($result);
            $index = strpos($result, 'IPCallBack(');
            if ($index > 0) {
                $result = iconv('GBK', 'UTF-8', substr($result,$index + 11, - 3));
                $result = @json_decode($result, true);
                if ($result && isset($result['pro']) && isset($result['city'])) {
                    if (!empty($result['pro'])) {
                        $location['province'] = self::getIdByName($result['pro']);
                        if ($location['province'] > 0) {
                            $location['city'] = self::getIdByName($result['city'], $location['province']);
                            Cache::forever($key, $location);
                            return $location;
                        }
                    }
                }
            }
        }

        Cache::forever($key, $location);
        return $location;
    }

    /**
     * 根据地区名称获取编号
     * @param  string $name  地区名称
     * @param  int 	  $pid 	 父级编号
     * @return int           地区编号
     */
    public static function getIdByName($name, $pid = 0) {
        if (empty($name)) {
            return 0;
        }

        $region = Region::select(DB::raw('id,name,MATCH(matchs) AGAINST(\'' . String::strToUnicode($name, '+') . '\') AS similarity'))
            ->whereRaw('MATCH(matchs) AGAINST(\'' . String::strToUnicode($name, '+') . '\' IN BOOLEAN MODE)')
            ->where('pid', $pid)
            ->orderBy('similarity', 'desc')
            ->first();

        if ($region) {
            return $region->id;
        } else {
            return 0;
        }
    }

    public static function getOpenCityByIp($ip) {
        $key = 'oepnip-'.$ip;
        $city = Cache::get($key);
        if ($city) {
            return $city;
        }

        $result = Http::get('http://whois.pconline.com.cn/ipJson.jsp', ['ip' => $ip]);
        if (!empty($result)) {
            $result = trim($result);
            $index = strpos($result, 'IPCallBack(');
            if ($index > 0) {
                $result = iconv('GBK', 'UTF-8', substr($result,$index + 11, - 3));
                $result = @json_decode($result, true);
                if ($result && isset($result['pro']) && isset($result['city'])) {
                    if (!empty($result['city'])) {
                        $city = self::getOpenCityByName($result['city']);
                        if ($city) {
                            Cache::forever($key, $city);
                            return $city;
                        }
                    }

                    if (!empty($result['pro'])) {
                        $city = self::getOpenCityByName($result['pro']);
                        if ($city) {
                            Cache::forever($key, $city);
                            return $city;
                        }
                    }
                }
            }
        }

        $result = Http::get('http://int.dpool.sina.com.cn/iplookup/iplookup.php', ['format'=>'json', 'ip' => $ip]);
        $result = empty($result) ? false : @json_decode($result, true);
        if ($result && $result['ret'] == 1) {
            if (!empty($result['city'])) {
                $city = self::getOpenCityByName($result['city']);
                if ($city) {
                    Cache::forever($key, $city);
                    return $city;
                }
            }

            if (!empty($result['province'])) {
                $city = self::getOpenCityByName($result['province']);
                if ($city) {
                    Cache::forever($key, $city);
                    return $city;
                }
            }
        }

        $result = Http::get('http://ip.taobao.com/service/getIpInfo2.php', ['ip' => $ip]);
        $result = empty($result) ? false : @json_decode($result, true);
        if ($result && $result['code'] == 0 && isset($result['data'])) {
            if (!empty($result['data']['city'])) {
                $city = self::getOpenCityByName($result['data']['city']);
                if ($city) {
                    Cache::forever($key, $city);
                    return $city;
                }
            }

            if (!empty($result['data']['region'])) {
                $city = self::getOpenCityByName($result['data']['region']);
                if ($city) {
                    Cache::forever($key, $city);
                    return $city;
                }
            }
        }

        $city = Region::where('is_service', 1)
            ->orderBy('is_default','desc')
            ->orderBy('sort','asc')
            ->first();
        if($city) {
            $city = $city->toArray();
            Cache::forever($key, $city);
            return $city;
        }
        return null;
    }

    public static function getOpenCityByName($name) {
        $region = Region::select(DB::raw('*,MATCH(matchs) AGAINST(\'' . String::strToUnicode($name, '+') . '\') AS similarity'))
            ->whereRaw('MATCH(matchs) AGAINST(\'' . String::strToUnicode($name, '+') . '\' IN BOOLEAN MODE)')
            ->where('is_service', 1)
            ->orderBy('similarity', 'desc')
            ->first();

        if ($region) {
            return $region->toArray();
        } else {
            return false;
        }
    }

    /**
     * 获取所有省编号
     * @return array 城市数组
     */
    public static function getProvinces() {
        return Region::where('pid', 0)
            ->orderBy('sort','asc')
            ->orderBy('id','asc')
            ->get()
            ->toArray();
    }

    /**
     * 获取开通服务的城市
     * @return array 城市数组
     */
    public static function getServiceCitys() {
        return Region::where('is_service', 1)
            ->orderBy('sort','asc')
            ->orderBy('id','asc')
            ->get()
            ->toArray();
    }

    /**
     * 获取开通服务的城市
     * @param  int $page 页码
     * @param  int $pageSize 每页数
     * @return array 城市数组
     */
    public static function getSystemServiceCitys($provinceId, $cityId, $areaId, $cityName, $page, $pageSize,$nonew = 0) {
//        //生成sm-city-picker.js
//        $Region = Region::where('pid', 0)->select('id','name')->get()->toArray();
//        $zx = array("1", "18", "795", "2250");
//        foreach($Region as $k=>$val){
//            if(in_array($val['id'],$zx)){
//                $Region[$k]['type'] = 0;
//                $citylocation = CityLocation::where('id',$val['id'])->select('lat','lng')->get()->toArray();
//                $Region[$k]['mappoint'] = $citylocation[0]['lat'].",".$citylocation[0]['lng'];
//
//                $Region[$k]['sub'] = Region::where('pid', $val['id'])->select('id','name')->get()->toArray();
//            }else{
//                $Region[$k]['type'] = 1;
//                $Region[$k]['sub'] = Region::where('pid', $val['id'])->select('id','name')->get()->toArray();
//                foreach($Region[$k]['sub'] as $key=>$value){
//                    $citylocation = CityLocation::where('id',$value['id'])->select('lat','lng')->get()->toArray();
//                    $Region[$k]['sub'][$key]['mappoint'] = $citylocation[0]['lat'].",".$citylocation[0]['lng'];
//
//                    $Region[$k]['sub'][$key]['sub'] = Region::where('pid', $value['id'])->select('id','name')->get()->toArray();
//                }
//            }
//        }
//        echo json_encode($Region);
//        exit;

        $Region = Region::where('is_service', 1);

        if($areaId > 0)
        {
            $Region->where('id', $areaId);
        }
        elseif($cityId > 0)
        {
            $Region->where('id', $cityId)->orWhere('pid', $cityId);
        }
        elseif($provinceId > 0)
        {
            $cityIds = Region::where('pid', $provinceId)->lists('id');
            $Region->where('id', $provinceId)->orWhere('pid', $provinceId)->orWhereIn('pid', $cityIds);
        }

        if(!empty($cityName))
        {
            $Region->where('name', 'like', '%'.$cityName.'%');
        }

        if($nonew == 0){
            $result = $Region->orderBy('sort','asc')
                             ->orderBy('id','asc')
                             ->get()
                             ->toArray();
        }elseif($nonew == 2){
            $result = $Region->where(function ($query) {
                                $query->whereIn('id',[1,18,795,2250])
                                    ->orWhere(function($query){
                                        $query->where('level', '=', 2)->whereNotIn('pid',[1,18,795,2250]);
                                    });
                            })
                            ->orderBy('sort','asc')
                            ->orderBy('id','asc')
                            ->get()
                            ->toArray();
        }else{
            $list = $Region;
            $result['totalCount'] = $list->count();
            $result['list'] = $list->skip(($page - 1) * $pageSize)
                ->take($pageSize)
                ->orderBy('sort','asc')
                ->orderBy('id','asc')
                ->get()
                ->toArray();

            $zx = array("1", "18", "795", "2250");
            //把2级找出来  把1级找出来
            foreach($result['list'] as $k=>$v){
                //把4个直辖弄出来
                if(in_array($v['pid'],$zx)){
                    $cityname = Region::where('id',$v['pid'])->pluck('name');
                    $provincename = '';
                }else if($v['level'] == 3){
                    $city_info = Region::where('id',$v['pid'])->select('name','pid')->first();
                    $cityname = $city_info->name;
                    $provincename = Region::where('id',$city_info->pid)->pluck('name');
                }else if($v['level'] == 2){
                    $provincename = Region::where('id',$v['pid'])->pluck('name');
                    $cityname = '';
                }
                $result['list'][$k]['cityname'] = $cityname;
                $result['list'][$k]['provincename'] = $provincename;
            }
        }

        foreach ($result['list'] as $key => $value) {
            $result['list'][$key]['canDelete'] = self::getSystemServiceCitys_candelete($value['id']);
        }
        return $result;
    }

    /**
     * 当前城市是否可删除
     */
    public static function getSystemServiceCitys_candelete($cityId) {
        $city = Region::where("id", $cityId)->first();

        if($city->level < 3){
            $is_have_city = Region::where("pid", $cityId)->where('is_service',1)->count();
            if($is_have_city >= 1){
                return 0;   //不可删除
            }
        }

        switch($city->level){
            case 1:
                //城市还有没有商家 城市还有没有商品
                $is_have = Seller::where('province_id',$cityId)->count();
                break;
            case 2:
                $is_have = Seller::where('city_id',$cityId)->count();
                break;
            case 3:
                $is_have = Seller::where('area_id',$cityId)->count();
                break;
        }

        if($is_have > 1){
            return 0;   //不可删除
        }

        return 1; //可以删除
    }

    public static function createjs(){
        chmod(base_path()."/public/upload/opencity.js",0777);

        $region = Region::where('is_service', 1)
            ->orderBy('sort','asc')
            ->orderBy('id','asc')
            ->select('id','pid','level','name')
            ->get()
            ->toArray();

        $region2 = [];
        foreach($region as $k=>$v){
            if($v['level'] == 1){
                $region2[$v['id']]['i'] = $v['id'];
                $region2[$v['id']]['n'] = $v['name'];
            }else if($v['level'] == 2){
                $region2[$v['pid']]['child'][$v['id']]['i'] = $v['id'];
                $region2[$v['pid']]['child'][$v['id']]['n'] = $v['name'];
            }else if($v['level'] == 3){
                foreach($region2 as $k2=>$v2){
                    foreach($v2['child'] as $k3=>$v3){
                        if($v['pid'] == $k3){
                            $region2[$k2]['child'][$k3]['child'][$v['id']]['i'] = $v['id'];
                            $region2[$k2]['child'][$k3]['child'][$v['id']]['n'] = $v['name'];
                        }
                    }
                }
            }
        }

        $html = 'var ZY_CITYS = ';
        $html .= json_encode($region2);
        $file = base_path()."/public/upload/opencity.js";
        // var_dump($file);
        // var_dump($html);
        file_put_contents($file, $html);
    }

    /**
     * 添加开通城市
     * @param int $cityId 城市编号
     * @param int $sort 排序
     * @return array 添加结果
     */
    public static function create($cityId, $sort){
        $result = array(
            'code'	=> self::SUCCESS,
            'data'	=> null,
            'msg'	=> ''
        );

        $city = Region::where("id", $cityId)->first();

        if($city == false)
        {
            $result['code']	= 10601; // 城市不存在

            return $result;
        }

        if($city->is_service == true)
        {
            $result['code']	= 10602; // 城市已添加

            return $result;
        }


        $zx = array("1", "18", "795", "2250");
        if(!in_array($cityId,$zx)){
            if($city->level == 1){
                $result['code']	= 41425; // 城市已添加
                return $result;
            }
        }else{
            Region::where("pid", $cityId)->update(["is_service"=>1, "sort"=>$sort]);
        }

        switch($city->level){
            case 2:
                Region::where("id", $city->pid)->update(["is_service"=>1, "sort"=>$sort]);
                Region::where("pid", $cityId)->update(["is_service"=>1, "sort"=>$sort]);
                break;
            case 3:
                Region::where("id", $city->pid)->update(["is_service"=>1, "sort"=>$sort]);
                $city2 = Region::where("id", $city->pid)->first();
                Region::where("id", $city2->pid)->update(["is_service"=>1, "sort"=>$sort]);
                break;
        }

        Region::where("id", $cityId)->update(["is_service"=>1, "sort"=>$sort]);

        self::createjs();

        return $result;
    }
    /**
     * 添加开通城市
     * @param int $cityId 城市编号
     * @return array 添加结果
     */
    public static function delete($cityIds){
        $result = array(
            'code'	=> self::SUCCESS,
            'data'	=> null,
            'msg'	=> ''
        );

        foreach ($cityIds as $key => $cityId) {
            $city = Region::where("id", $cityId)->first();

            if($city == false)
            {
                $result['code'] = 10601; // 城市不存在
                return $result;
            }

            if($city->level < 3){
                $is_have_city = Region::where("pid", $cityId)->where('is_service',1)->count();
                if($is_have_city >= 1){
                    $result['code'] = 41423;
                    return $result;
                }
            }

            switch($city->level){
                case 1:
                    //城市还有没有商家 城市还有没有商品
                    $is_have = Seller::where('province_id',$cityId)->count();
                    break;
                case 2:
                    $is_have = Seller::where('city_id',$cityId)->count();
                    break;
                case 3:
                    $is_have = Seller::where('area_id',$cityId)->count();
                    break;
            }

            if($is_have > 1){
                $result['code'] = 41424;
                return $result;
            }
        }
        

        Region::whereIn("id", $cityIds)->update(["is_service"=>0]);

        self::createjs();
        return $result;
    }

    public static function setDefault($cityId) {
        Region::where("id", '<>', $cityId)->update(["is_default"=>0]);
        Region::where("id", $cityId)->update(["is_default"=>1]);
    }

    /**
     * 获取开通城市列表
     * @return [type] [description]
     */
    public static function getOpenCitys(){
        $regions = Region::where('is_service', 1)
            ->orderBy('level','asc')
            ->orderBy('sort','asc')
            ->orderBy('id','asc')
            ->get()
            ->toArray();

        $regions_data = [];

        $provinces = [];

        $citys = [];

        foreach ($regions as $item)
        {
            if($item['level'] == 1)
            {
                $provinces[$item['id']] = 1;

                $tempItem = ['id'=>$item['id'],'name'=>$item['name']];

                $child_citys = self::getCitysById($item['id']);

                foreach ($child_citys as $citem)
                {
                    $citys[$citem['id']] = 1;

                    $tempCityItem = ['id'=>$citem['id'],'name'=>$citem['name']];

                    $last_citys = self::getCitysById($citem['id']);

                    foreach ($last_citys as $litem)
                    {
                        $tempCityItem['area'][] = ['id'=>$litem['id'],'name'=>$litem['name']];
                    }

                    $tempItem['city'][] = $tempCityItem;
                }

                $regions_data[] = $tempItem;
            }
            else if($item['level'] == 2)
            {
                $citys[$item['id']] = 1;

                if(empty($provinces[$item['pid']]))
                {
                    $pcity = self::getCitysById($item['pid'],'id',false);

                    $tempItem = ['id'=>$pcity['id'],'name'=>$pcity['name']];

                    $tempCityItem = ['id'=>$citem['id'],'name'=>$citem['name']];

                    $last_citys = self::getCitysById($item['id']);

                    foreach ($last_citys as $litem)
                    {
                        $tempCityItem['area'][] = ['id'=>$litem['id'],'name'=>$litem['name']];
                    }

                    $tempItem['city'][] = $tempCityItem;

                    $regions_data[] = $tempItem;
                }
            }
            else
            {
                $city = self::getCitysById($item['pid'],'id',false);

                $pcity = self::getCitysById($city['pid'],'id',false);

                if(empty($citys[$item['pid']]))
                {
                    $regions_data[] =
                        [
                            'id'=>$pcity['id'],
                            'name'=>$pcity['name'],
                            'city'=>
                                [
                                    'id'=>$city['id'],
                                    'name'=>$city['name'],
                                    'area'=>['id'=>$item['id'],'name'=>$item['name']]
                                ]
                        ];
                }
            }

        }
        return $regions_data;
    }

    private static function getCitysById($id,$field = 'pid',$isList = true){
        if($field != 'pid'){
            $field = 'id';
        }
        $regions = Region::where($field, $id)
            ->orderBy('sort','asc')
            ->orderBy('id','asc');
        if($isList){
            $regions = $regions->get()
                ->toArray();
        } else {
            $regions = $regions->first()
                ->toArray();
        }

        return $regions;
    }


    /**
     * 获取开通的城市列表
     */
    public static function getSystemOpenCitys(){
        $list = Region::where('is_service', 1)
            ->where(function ($query) {
                $query->whereIn('id',[1,18,795,2250])
                    ->orWhere(function($query){
                        $query->where('level', '=', 2)->whereNotIn('pid',[1,18,795,2250]);
                    });
            })
            ->with('citylocation')
            ->orderBy('sort','asc')
            ->orderBy('id','asc')
            ->get()
            ->toArray();
        return $list;
    }


    /**
     * 一键开通城市
     */
    public function  open(){
        Region::where("is_service",0)->update(["is_service"=>1]);
        self::createjs();
    }


    /**
     * [getCityList 获取城市]
     * @param  integer $pid   [description]
     * @param  [type]  $level [description]
     * @return [type]         [description]
     */
    public static function getCityList($pid=NULL, $level=NULL)
    {
        $regions = Region::where('is_service', 1);

        if($pid)
        {
            $regions->where("pid", $pid);
        }
        if($level)
        {
            $regions->where("level", $level);
        }

        $regions = $regions->orderBy('sort','asc')
            ->orderBy('id','asc')
            ->get()
            ->toArray();
        return $regions;
    }

}
