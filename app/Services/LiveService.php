<?php
namespace YiZan\Services;

use YiZan\Utils\Time;
use YiZan\Utils\String;
use YiZan\Utils\Http;
use YiZan\Models\Region;
use YiZan\Models\LiveLog;
use YiZan\Utils\Helper;
use DB, Exception,Validator,Cache,Config;

/**
 * 管理员组
 */
class LiveService extends BaseService {
    /*
     * $typepay 1 水 2电 3燃气
     */
    public function getCompany($name,$id,$pid,$level,$typepay){
        //找出省份
        $province = self::getProvince();
        $province = json_decode($province,true);
        $province = $province['Data']['Province'];
        if($level == 2){
            $my_province = Region::where('id',$pid)->first();
            if(!empty($my_province)){
                $my_province = $my_province->toArray();
            }
        }else{
            $my_province['name'] = $name;
        }

        $province_must = [];
        foreach($province as $k=>$v){
            if(strpos($my_province['name'], $v['ProvinceName']) !== false){
                $province_must['provinceId'] = $v['ProvinceId'];
                $province_must['provinceName'] = $v['ProvinceName'];
            }
        }
        if(empty($province_must['provinceId'])){
            return '';
        }

        //找出市
        $city = self::getCity($province_must['provinceId']);
        $city = json_decode($city,true);
        //print_r($city);
        $city = $city['Data']['City'];
        $city_must = [];
        foreach($city as $k=>$v){
            if(strpos($name, $v['CityName']) !== false){
                $city_must['cityId'] = $v['CityId'];
                $city_must['cityName'] = $v['CityName'];
            }
        }
        if(empty($city_must['cityId'])){
            return '';
        }
        //找出 哪些可以交钱
        $project = self::getProject($province_must['provinceId'],$city_must['cityId']);
        $project = json_decode($project,true);
        $project = $project['Data']['PayProject'];

        $project_must = [];
        $my_project_arr = [1=>'水',2=>'电',3=>'燃气'];
        $my_project = $my_project_arr[$typepay];
        foreach($project as $k=>$v){
            if(strpos($v['PayProjectName'], $my_project) !== false){
                $project_must['provinceId'] = $v['ProvinceId'];
                $project_must['cityId'] = $v['CityId'];
                $project_must['payProjectId'] = $v['PayProjectId'];
                $project_must['payProjectName'] = $v['PayProjectName'];
            }
        }
        if(empty($project_must['cityId'])){
            return '';
        }

        //找出单位
        $unit = self::getUnit($project_must['provinceId'],$project_must['cityId'],$project_must['payProjectId']);
        $unit = json_decode($unit,true);
        $unit = $unit['Data']['PayUnit'];
        //print_r($unit);
        if(empty($unit)){
            return '';
        }else{
            foreach($unit as $k=>$v){
                $unit[$k]['cityName'] = $city_must['cityName'];
                $unit[$k]['cityId'] = $city_must['cityId'];
                $unit[$k]['provinceName'] = $province_must['provinceName'];
                $unit[$k]['provinceId'] = $province_must['provinceId'];
                $unit[$k]['payUnitName'] = $v['PayUnitName'];
                $unit[$k]['payUnitId'] = $v['PayUnitId'];
                $unit[$k]['payProjectId'] = $v['PayProjectId'];
            }
        }
        //print_r($unit);exit;

        return $unit;
    }

    /** 查询余额
     * @param $provinceName
     * @param $provinceId
     * @param $cityName
     * @param $cityId
     * @param $code
     * @param $unitname
     * @param $account
     * @param $type
     * @param $payProjectId
     */
    public function getArrearage($provinceName,$provinceId,$cityName,$cityId,$code,$unitname,$account,$type,$payProjectId,$cardid,$productName){

        $url = 'http://p.apix.cn/apixlife/pay/utility/query_owe';
        $data['provname'] = $provinceName;
        $data['cityname'] = $cityName;
        $data['corpid'] = $code;
        $data['type'] = "00".$type;
        $data['corpname'] = $unitname;
        $data['account'] = $account;
        $data['cardid'] = $cardid;
        $arrearage = self::api($url,$data);
        $arrearage = json_decode($arrearage,true);

        return $arrearage;
    }

    public function getQuery($provinceId,$cityId,$code,$payProjectId){
        $query = Cache::get('query_json_'.$cityId."_".$code);
        if(empty($query)){
            $url = 'http://p.apix.cn/apixlife/pay/utility/product_info';
            $data['provid'] = $provinceId;
            $data['cityid'] = $cityId;
            $data['corpid'] = $code;
            $data['type'] = $payProjectId;
            $query = self::api($url,$data);
            Cache::put('query_json_'.$cityId."_".$code, $query, 720);
        }
        $query = json_decode($query,true);
        $query = $query['Data']['Card'][0];
        $query['productId'] = $query['ProductId'];
        $query['productName'] = $query['ProductName'];

        return $query;
    }

    public function getProvince(){
        $resutl_json = Cache::get('province_json');
        if(empty($resutl_json)){
            $url = 'http://p.apix.cn/apixlife/pay/utility/query_province';
            $resutl_json = self::api($url,'');
            Cache::put('province_json', $resutl_json, 720);
        }
        return $resutl_json;
    }

    public function getCity($provid){
        $resutl_json = Cache::get('city_json_'.$provid);
        if(empty($resutl_json)){
            $url = 'http://p.apix.cn/apixlife/pay/utility/query_city';
            $data['provid'] = $provid;
            $resutl_json = self::api($url,$data);
            Cache::put('city_json_'.$provid, $resutl_json, 720);
        }
        return $resutl_json;
    }

    public function getProject($provid,$cityid){
        $resutl_json = Cache::get('project_json_'.$cityid);
        if(empty($resutl_json)){
            $url = 'http://p.apix.cn/apixlife/pay/utility/recharge_type';
            $data['provid'] = $provid;
            $data['cityid'] = $cityid;
            $resutl_json = self::api($url,$data);
            Cache::put('project_json_'.$cityid, $resutl_json, 720);
        }
        return $resutl_json;
    }

    public function getUnit($provid,$cityid,$type){
        $resutl_json = Cache::get('unit_json_'.$cityid."_".$type);
        if(empty($resutl_json)){
            $url = 'http://p.apix.cn/apixlife/pay/utility/recharge_company';
            $data['provid'] = $provid;
            $data['cityid'] = $cityid;
            $data['type'] = $type;
            $resutl_json = self::api($url,$data);
            Cache::put('unit_json_'.$cityid."_".$type, $resutl_json, 720);
        }
        return $resutl_json;
    }

    public function getOrder($sn){
        $livelog = LiveLog::where('sn',$sn)->first();
        if (!$livelog) {
            return false;
        }
        $livelog = $livelog->toArray();
        $extend = json_decode(base64_decode($livelog['extend']),true);
        if(empty($extend)){
            return false;
        }
        //print_r($extend);exit;

        $data['provid'] = $extend['provinceId'];
        $data['cityid'] = $extend['cityId'];
        $data['type'] = "00".$extend['type'];
        $data['corpid'] = $extend['code'];
        $data['cardid'] = $extend['cardid'];
        $data['account'] = $extend['account'];
        $data['orderid'] = $livelog['sn'];
        $data['fee'] = $livelog['money'];
        $data['callback_url'] = Config::get('app.callback_url').'payment/live/notify';
        $data['sign'] = md5($data['provid'].$data['cityid'].$data['type'].$data['corpid'].$data['cardid'].$data['account'].$data['orderid']);
        if(!empty($extend['balance']['ContractNo'])){
            $data['contractid'] = $extend['balance']['ContractNo'];
        }
        if(!empty($extend['balance']['PayMentDay'])){
            $data['paymentday'] = $extend['balance']['PayMentDay'];
        }
        if(!empty($extend['balance']['Param1'])){
            $data['param1'] = $extend['balance']['Param1'];
        }
        $url = 'http://p.apix.cn/apixlife/pay/utility/utility_recharge';
        $result = self::api($url,$data);
        $result = json_decode($result,true);

        return $result;
    }

    public function api($url,$data){
        $curl = curl_init();
        if($data != ""){
            $url = $url."?".http_build_query($data);
        }
        $live_key = SystemConfigService::getConfigByCode('live_key');
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "apix-key: ".$live_key,
                "content-type: application/json"
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

    public function getlist($userId, $page){
        $data = ['commentNum' => 0, 'orderList' => []];

        $data['commentNum'] = LiveLog::where('user_id', $userId)->count();
        $data['orderList'] = LiveLog::where('user_id', $userId)->orderBy('id', 'desc')->skip(($page - 1) * 20)->take(20)->get()->toArray();
        foreach($data['orderList'] as $k=>$v){
            $data['orderList'][$k]['extend'] = json_decode(base64_decode($v['extend']),true);
        }

        return $data;
    }
}
