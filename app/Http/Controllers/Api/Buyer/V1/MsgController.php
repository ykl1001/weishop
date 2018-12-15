<?php 
namespace YiZan\Http\Controllers\Api\Buyer;
use YiZan\Services\Buyer\ReadMessageService;
use YiZan\Utils\Time;
use Config, Input, View;

/**
 * 买家消息
 */
class MsgController extends BaseController {
	/**
     * 买家消息列表
	 */
	public function lists() 
    {
		$data = ReadMessageService::getBuyerList($this->userId, max((int)$this->request('page'), 1));
      

        
		return $this->outputData($data);
	}
    /**
     * 买家消息阅读
     */
    public function read()
    {
        $result = ReadMessageService::readMessage($this->userId, $this->request('id'));
        
        return $this->output($result);
    }

    public function getdata()
    {
        $result = ReadMessageService::getDatas($this->userId, $this->request('sellerId'), max((int)$this->request('page'), 1),$this->request('team'));
        return $this->outputData($result);
    }
    /**
     * 买家消息删除
     */
    public function delete()
    {
        $result = ReadMessageService::deleteMessage($this->userId, (int)$this->request('id'));
        
        return $this->output($result);
    }

    //消息购物车等数量
    public function status() 
    {
        $data = ReadMessageService::getCounts($this->userId);
      
        return $this->outputData($data);
    }

    public function message() {
        $args = Input::get();
        $list = ReadMessageService::getBuyerList($this->userId, max((int)$this->request('page'), 1));
        View::share('list',json_decode(json_encode($list),true));
        View::share('args',$args);
        if (Input::ajax()) {
            return View::make('api.wap.message.index_item');
        } else {
            return View::make('api.wap.message.index');
        }

    }

    public function msgshow() {
        $args = Input::get();
        $list = ReadMessageService::getDatas($this->userId, $this->request('sellerId'), max((int)$this->request('page'), 1));
        View::share('list',json_decode(json_encode($list),true));
        View::share('args',$args);
        if (Input::ajax()) {
            return View::make('api.wap.message.detail_item');
        } else {
            return View::make('api.wap.message.detail');
        }

    }

    public function  systemmessage(){
        $data = ReadMessageService::systemmessage($this->userId);
        return $this->outputData($data);
    }

    public function  wealth(){
        $result = ReadMessageService::wealth($this->userId, $this->request('sellerId'), max((int)$this->request('page'), 1),$this->request('team'));
        return $this->outputData($result);
    }


    public function  orderchange(){
        $data = ReadMessageService::orderchange($this->userId,max((int)$this->request('page'), 1));
        return $this->outputData($data);
    }
}