<?php namespace YiZan\Models;

class SystemGoods extends Base {
	//protected $visible = ['id', 'name', 'image', 'images', 'price_type', 'price', 'market_price', 'brief', 'duration', 'detail','status', 'sort', 'create_time', 'update_time', 'cate', 'cityPrices'];

	protected $appends = array('image','checkedDisabled','imageStr');

	public function norms(){
        return $this->hasMany('YiZan\Models\SystemGoodsNorms',  'system_goods_id' ,'id');
    }

    public function goods(){
        return $this->belongsTo('YiZan\Models\Goods', 'id', 'system_goods_id' );
    }


    public function systemTagListPid() {
        return $this->belongsTo('YiZan\Models\SystemTagList', 'system_tag_list_pid', 'id');
    }

    public function systemTagListId() {
        return $this->belongsTo('YiZan\Models\SystemTagList', 'system_tag_list_id', 'id');
    }


    public function getImagesAttribute() {
    	if (!isset($this->attributes['images']) || empty($this->attributes['images'])) {
    		return [];
    	}
	    return explode(',', $this->attributes['images']);
	}

	public function getImageAttribute() {
		if (!isset($this->attributes['images']) || empty($this->attributes['images'])) {
    		return '';
    	}
	    return current(explode(',', $this->attributes['images']));
	}
    public function getImageStrAttribute() {
        return $this->attributes['images'];
    }

    public function getCheckedDisabledAttribute() {
        return  $this->attributes['status'] == 0 ? 1 : 0 ;
    }

}
