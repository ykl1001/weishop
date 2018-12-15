<?php 
namespace YiZan\Http\Controllers\Api\Staff;
use YiZan\Services\Staff\ReadMessageService;
use Config, Time;

/**
 * 卖家消息
 */
class MsgController extends BaseController {
	/**
     * 卖家消息列表
	 */
	public function lists() 
    {
		$data = ReadMessageService::getStaffList($this->sellerId, $this->staffId, max((int)$this->request('page'), 1));
      
        foreach($data as $key=>$value)
        {
            $data[$key]["createTime"] = Time::toDate($value["sendTime"], 'Y-m-d H:i');
        }
		return $this->outputData($data);
	}
    /**
     * 卖家消息阅读
     */
    public function read()
    {
        $result = ReadMessageService::readMessage($this->sellerId, $this->staffId, $this->request('id'));
        
        return $this->output($result);
    }
    /**
     * 卖家消息删除
     */
    public function delete()
    {   
        $result = ReadMessageService::deleteMessage($this->staffId, $this->request('id'));
        
        return $this->output($result);
    }
    
    /* 获取单条消息
     */
    public function status()
    {
        $result = ReadMessageService::hasNewMessage($this->staffId);
        return $this->outputData(["hasNewMessage" => $result]);
    }
    /* 获取单条消息
     */
    public function getdata()
    {
        $result = ReadMessageService::getdatas($this->sellerId, $this->staffId,intval($this->request('id')));
        $result["createTime"] = Time::toDate($result["sendTime"], 'y-m-d');
        return $this->output($result);
    }
}