<?php namespace YiZan\Http\Controllers\Wap;

use View, Session, Input;
class GoodsCartController extends AuthController {

	/**
	 * 购物车列表
	 */
	public function index() {
        //清空购物车的服务
        $this->requestApi('shopping.delete', ['userId'=>$this->userId, 'type'=>2]);
        $bln = false;
		$curentAddress = Session::get('defaultAddress');

        if ((int)Input::get('addressId') > 0) {
            $address = $this->requestApi('user.address.get',['id' => (int)Input::get('addressId')]);
            $address['data']['realAddress'] = $address['data']['province']['name'].$address['data']['city']['name'].$address['data']['address'];
        } elseif ($curentAddress['isIndexSetAddress'] != '') {
            $address['data'] = $curentAddress;
        } else {
            $bln = true;
            $is_address_null = $address = $this->requestApi('user.address.getdefault');
            if (!empty($address['data'])) {
                $address['data']['realAddress'] = $address['data']['province']['name'].$address['data']['city']['name'].$address['data']['address'];
            } elseif($curentAddress['address'] != '') {
                $address['data']['address'] = $curentAddress['address'];
            }

        }
        $result_cart = $this->requestApi('shopping.lists', ['location'=>$address['data']['mapPointStr'],'cityId'=>$address['data']['cityId']]);

        if(!$bln){
            $is_address_null = $this->requestApi('user.address.getdefault');
        }
        if(!empty($is_address_null['data'])){
            $is_address_null = 1;
        }else{
            $is_address_null = -1;
        }
        View::share('isAddressNull', $is_address_null);
        View::share('cart', $result_cart['data']);
		View::share('nav', 'goodscart');
		View::share('address', $address['data']);
		View::share('args', Input::all());
		return $this->display();
	}
}
