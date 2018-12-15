<?php namespace YiZan\Models;

class Adv extends Base
{
    protected $visible = ['id', 'name', 'image', 'bg_color', 'type', 'arg', 'url', 'mould_id', 'data_json'];
    protected $appends = array('url');
    /**
     * 广告url
     */
    public function getUrlAttribute()
    {
        $type = $this->attributes['type'];
        $args 	= $this->attributes['arg'];
        $url = '';
        switch ($type) {
            case '1' : $url = u('wap#Seller/index', ['id' => $args]);  break;
            case '2' : $url = ''; break;
            case '3' : $url = u('wap#Goods/detail', ['goodsId' => $args]); break;
            case '4' : $url = u('wap#Seller/detail', ['id' => $args]); break;
            case '5' : $url = $args; break;
            case '6' : $url = u('wap#Goods/detail', ['goodsId' => $args]); break;
            case '7' : $url = u('wap#Article/detail', ['id' => $args]); break;
            case '8' : $url = u('wap#UserCenter/signin'); break;
            case '9' : $url = u('wap#Integral/index'); break;
        }
        return $url;
    }
}
