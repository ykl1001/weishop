<?php namespace YiZan\Models\System;

use YiZan\Utils\Encrypter;
use Config;
class User extends \YiZan\Models\User {
	/*protected $visible = ['id', 'mobile', 'name', 'avatar', 'reg_time', 'reg_ip',
		'regProvince', 'regCity', 'login_time', 'login_ip', 'loginProvince', 'loginCity', 
        'address', 'status', 'restaurant', 'seller', 'staff', 'balance'];*/

    protected $appends = array(
        'mingPwd'
    );

	public function regProvince(){
        return $this->belongsTo('YiZan\Models\Region', 'reg_province_id', 'id');
    }

    public function regCity(){
        return $this->belongsTo('YiZan\Models\Region', 'reg_city_id', 'id');
    }

    public function loginProvince(){
        return $this->belongsTo('YiZan\Models\Region', 'login_province_id', 'id');
    }
    
    public function loginCity(){
        return $this->belongsTo('YiZan\Models\Region', 'login_city_id', 'id');
    }

    public function address(){
        return $this->belongsTo('YiZan\Models\UserAddress', 'id', 'user_id');
    }

    public function seller(){
        return $this->belongsTo('YiZan\Models\Seller', 'id', 'user_id');
    }

    public function staff(){
        return $this->belongsTo('YiZan\Models\SellerStaff', 'id', 'user_id');
    }

    /**
     * 是否可以确认订单完成(买家)
     * @return bool
     */
    public function getMingPwdAttribute()
    {
        //cz fanwe
        $encrypter = new Encrypter(md5(Config::get('app.fanwefx.appsys_id')));
        $pwd = $encrypter->decrypt($this->mine_pwd);
        return $pwd;
    }
}
