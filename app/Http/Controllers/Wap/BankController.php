<?php namespace YiZan\Http\Controllers\Wap;

use View, Session, Input,Response;

class BankController extends AuthController {

    public function bank() {
        $args = Input::all();
        if ($args['id'] == "") {
            View::share('top_title', "绑定银行卡");
            View::share('url', 'account');
        } else {
            $result = self::getbank();
            if ($result) {
                if ($args['verifyCode']) {
                    View::share('verifyCode', $args['verifyCode']);
                    View::share('data', $result['old']);
                    View::share('old', false);
                } else {
                    unset($result['data']['old']);
                    View::share('data', $result);
                    View::share('old', true);
                }
                View::share('top_title', "编辑银行卡");
                View::share('url', 'carry');
            } else {
                View::share('top_title', "绑定银行卡");
                View::share('url', 'account');
            }
        }
        return $this->display();
    }

    public function getbank() {
        $balance_result = $this->requestApi('user.bank.getbank');
        if(Input::ajax()){
            return Response::json($balance_result['data']);
        }
        return $balance_result['data'];
    }


    public function bankSve() {
        $args = Input::all();
        $result = $this->requestApi('user.bank.savebankinfo', $args);
        return Response::json($result);
    }

    public function carry() {
        $args = Input::all();
        View::share('title', '提现');
        $result = $this->requestApi('user.bank.getAccount');
        View::share('bank', $result);
        if ($args['tpl']) {
            return $this->display('carry_' . $args['tpl']);
        }
        return $this->display();
    }


    /**
     * 提现
     */
    public function withdraw()
    {
        $args = Input::all();
        $result = $this->requestApi('user.bank.withdraw', $args);
        return Response::json($result);
    }


    /**
     * 生成验证码
     */
    public function verify() {
        $mobile = Input::get('mobile');
        $result = $this->requestApi('user.mobileverify',array('mobile'=>$mobile));
        return Response::json($result);
    }

    /**
     *
     */
    public function verifyCode()
    {
        $result = self::getbank();
        View::share('data', $result);
        View::share('title', "短信验证");
        return $this->display();
    }
    /**
     * 检查银行卡短信
     */
    public function verifyCodeCk()
    {
        $args = Input::all();
        $result = $this->requestApi('user.bank.verifyCodeCk', $args);
        return Response::json($result);
    }
    /**
     * 检查银行卡短信
     */
    public function withdrawlog()
    {
        $args = Input::all();
        $args['type'] = 9;
        $result = $this->requestApi('user.getbalance', $args);
        View::share('data', $result['data']);
        if($args['item']){
            return $this->display($args['item']);
        }else{
            return $this->display();
        }
    }

}
