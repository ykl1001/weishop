<?php namespace YiZan\Services;
  

use YiZan\Models\Proxy;
/**
 * 代理
 */
class ProxyService extends BaseService { 

    /**
     * 根据用户名查询代理
     * @param string $name 代理用户名
     * @return array $proxy 代理信息
     */
    public static function getByName($name){
        $proxy = Proxy::where('name', $name)
                      ->first();
        return $proxy;
    }

    /**
     * 根据编号查询代理
     * @param string $id 编号
     * @return array $proxy 代理信息
     */
    public static function getById($id){
        $proxy = Proxy::where('id', $id)
                      ->with('province', 'city', 'area', 'childs','parentProxy')
                      ->first();
        return $proxy;
    }

}
