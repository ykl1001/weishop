<?php namespace YiZan\Services;
use YiZan\Models\Payment;
use YiZan\Models\DoorAccess;
use YiZan\Models\PropertyBindDeivce;
use YiZan\Utils\Http;
use Config;
class PropertyUserService extends BaseService {
	
	/*
	 *绑定
	 */
	 public function bindDeivce($user, $ticket,$openid, $deviceId,$ksid,$ktype,$isopen) {
		$ass = WeixinService::getAccessToken();
		if(!empty($ass['errcode'])){
			return false;
		}
		$data = [
			'app_key' => Config::get('app.lock_sdk_api.app_key'),
			'agt_num' => Config::get('app.lock_sdk_api.agt_num'),
			'openid'	=>$openid,
			'device_id'	=>$deviceId,
			'ticket'	=> $ticket,
			'access_token'	=> $ass
		];
		

		$bind = PropertyBindDeivce::where('user_id',$user['id'])->where('mobile',$user['mobile'])->where('device_id',$deviceId)->where('openid',$openid)->first();
         if(empty($bind) || $bind->status == 0){
             $result = Http::post("http://wx.hzblzx.com/mdwxserver/pservice/bindDevice.aspx", $data);
             if(!$bind){
                 $bind = new PropertyBindDeivce;
             }
			$bind->device_id = $deviceId;
			$bind->user_id = $user['id'];
			$bind->mobile = $user['mobile'];
			$bind->status = 1;
			$bind->openid = $openid;
			$bind->ticket = $ticket;
			$bind->save();
		}
		if($isopen == 1){
			return self::openDoor($openid, $ktype,$ksid,$user['mobile']);				
		}else{
			return ['code' => 0 ];
		}
    }
	
	/*
	 *绑定
	 */
	 public function isBindDeivce($user,$openid, $deviceId) {
		$bindDeivce = PropertyBindDeivce::where('user_id',$user['id'])->where('mobile',$user['mobile'])->where('device_id',$deviceId)->where('openid',$openid)->first();
		if(empty($bindDeivce)){
			$result['status'] = 0;
		}else{
			$result['status'] = $bindDeivce->status;
			$result['ticket'] = $bindDeivce->ticket;
		}
		return $result;
    }
	
	/*
	 * 获取
	 */
	 public function qryAllKeys($user, $openid, $mtype,$auid,$districtId) {
		 
		$door =DoorAccess::where('district_id',$districtId)->get()->toArray();
		if(!empty($door)){
			$config = Payment::where('code', 'weixinJs')->where('status', 1)->first();
			$gid = $config->config['originalId'];
			$data = [
				'app_key' => Config::get('app.lock_sdk_api.app_key'),
				'agt_num' => Config::get('app.lock_sdk_api.agt_num'),
				'openid'  => $openid,
				'gid'	  => $gid,
				'mtype'	  => $mtype,
				'auid'	  => $user['mobile']
			];
			$result = Http::post("http://wx.hzblzx.com/mdwxserver/pservice/qryAllKeysService.aspx", $data);
			$users =  json_decode($result, true);

			if($users['status'] != "fail"){
				$data =[];
				sort($users['msg'] );
				foreach($door as $ks=> $dr){
					
					$data[$dr['pid']] = $dr;	
					foreach($users['msg'] as $k=> $v){
						
						if($dr['pid'] == $v['pid']){
							
							$data[$dr['pid']]['is_bind_deivce'] = self::isBindDeivce($user,$openid,  $v['device_id'])['status'];
							$data[$dr['pid']]['msg']	 = $v;
							
						}
						
					}
					
				}
				
				return $data;
			}else{
				return null;			
			}
		}else{			
			return null;		
		}
    }
	
	/*
	 * 开门
	 */
	 public function openDoor($openid, $ktype,$ksid,$auid) {
		 
		 
		$config = Payment::where('code', 'weixinJs')->where('status', 1)->first();
        $gid = $config->config['originalId'];
		$data = [
			'app_key' => Config::get('app.lock_sdk_api.app_key'),
			'agt_num' => Config::get('app.lock_sdk_api.agt_num'),
			'openid'  => $openid,
			'gid'	  => $gid,
			'ktype'	  => $ktype,
			'auid'	  => $auid,
			'ksid'	  => $ksid
		];
		$result = Http::post("http://wx.hzblzx.com/mdwxserver/pservice/openDoorService.aspx", $data);
		if($result['code'] == 0){
			return json_decode($result, true);
		}else{
			return null;			
		}
    }
	
	
}
