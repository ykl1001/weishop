<?php 
namespace YiZan\Models;
/**
 * 运费模版
 */
class FreightTmp extends Base 
{
	public function tmpcity()
    {
        return $this->hasMany('YiZan\Models\FreightTmpCity', 'freight_tmp_id', 'id');
    }

    public function region()
    {
    	return $this->belongsTo('YiZan\Models\Region', 'region_id', 'id');
    }
}
