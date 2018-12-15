<?php namespace YiZan\Services;

use YiZan\Models\System\SystemConfig;
use YiZan\Models\UserIntegral;
use YiZan\Models\User;
use YiZan\Utils\Time;

/**
 * Class UserIntegralService  会员积分
 * @package YiZan\Services
 */
class UserIntegralService extends BaseService {

    /**
     * 会员积分日志列表
     * @param  string $username 会员名
     * @param string $mobile 会员手机号
     * @param date $beginTime 开始时间
     * @param date $endTime 结束时间
     * @param int $page 页码
     * @param int $pageSize 每页数
     */
    public static function  getList($username, $mobile, $beginTime, $endTime, $page, $pageSize){
        $list = UserIntegral::orderBy('id', 'desc');
        //根据会员名查询
        if ($username != '') {
            $list->whereIn('user_id', function($query) use ($username){
                $query->select('id')
                    ->where('name', 'like', '%' . $username . '%')
                    ->from('user');
            });
        }
        //根据会员手机号码查询
        if ($mobile != '') {
            $list->whereIn('user_id', function($query) use ($mobile){
                $query->select('id')
                    ->where('mobile', $mobile)
                    ->from('user');
            });
        }

        //开始时间
        if ($beginTime != '') {
            $beginTime = Time::toTime($beginTime);
            $list->where('create_time', '>', $beginTime);
        }

        //结束时间
        if ($endTime != '') {
            $endTime = Time::toTime($endTime);
            $list->where('create_time', '<=', $endTime);
        }

        $totalCount = $list->count();

        $list =  $list->with('user')
                    ->skip($pageSize * ($page - 1))
                    ->take($pageSize)
                    ->get();
        return ['list' => $list, 'totalCount' => $totalCount];
    }

    /**
     * 增加会员积分日志
     * @param int $userId 会员编号
     * @param int $type 类型 1:获得 2:消费
     * @param int $relatedType 关联类型 1:签到 2:注册 3:消费 4:抵现 5:回复 6:发帖 7:抵现退回 8: 积分兑换商品
     * @param int $relatedId 关联编号
     * @param double $money 金额
     * @param int $integral 积分 当$relatedType为7时,必传
     * @param int $status 状态
     * @param string $remark 备注
     */
    public static function createIntegralLog($userId, $type, $relatedType, $relatedId, $money = 0, $integral = 0, $status = 1, $remark = '') {
        $data = [
            'user_id' => $userId,
            'type' => $type,
            'related_type' => $relatedType,
            'related_id' => $relatedId,
            'integral' => $integral,
            'status' => $status,
            'remark' => $remark,
            'create_time' => UTC_TIME,
            'create_day' => UTC_DAY
        ];
        $relatedTypeConfig = [
            '1' => 'sign_integral',
            '2' => 'reg_integral',
            '3' => 'cost_integral',
            '4' => 'cash_integral',
            '5' => 'reply_integral',
            '6' => 'posts_integral'
        ];
        $user = User::where('id', $userId)->first();
        if ($integral == 0 && $relatedType != 7) {
            $val = SystemConfig::where('code',  $relatedTypeConfig[$relatedType])->pluck('val');
            $data['integral'] = $val;
            //消费得积分
            if ($relatedType == 3) {
                $cost = UserIntegral::where('related_type', $relatedType)->where('related_id', $relatedId)->first();
                $data['integral'] = $cost ? 0 : (int)($val / 100 * $money);
            }

            //每天只能签到一次
            if ($relatedType == 1) {
               $signin = UserIntegral::where('user_id', $userId)
                   ->where('related_type', $relatedType)
                   ->where('create_day', UTC_DAY)
                   ->first();
                if ($signin) {
                   $data['integral'] = 0;
                }
            }

            //发帖和回复帖子 是否已经达到每日上限
            if ($relatedType == 5 || $relatedType == 6) {
                $limitType = $relatedType == 5 ? 'limit_reply_integral' : 'limit_posts_integral';
                $todayIntegral = UserIntegral::where('user_id', $userId)
                    ->where('related_type', $relatedType)
                    ->where('create_day', UTC_DAY)
                    ->sum('integral');
                $limitVal = SystemConfig::where('code',  $limitType)->pluck('val');
                $data['integral'] = $todayIntegral >= $limitVal ? 0 : $data['integral'];
            }
        }

        //积分备注
        $integralRemark = [
            '1' => '签到获得' . $data['integral'] . '个积分',
            '2' => '注册获得' . $data['integral'] . '个积分',
            '3' => '消费获得' . $data['integral'] . '个积分',
            '4' => '抵现使用' . $data['integral'] . '个积分',
            '5' => '回复获得' . $data['integral'] . '个积分',
            '6' => '发帖获得' . $data['integral'] . '个积分',
            '7' => '订单取消,抵现退回' . $data['integral'] . '个积分',
            '8' => '积分兑换商品使用' . $data['integral'] . '个积分'
        ];
        $data['remark'] = $integralRemark[$relatedType];
        //更新会员积分
        if($data['integral'] > 0) {
            if ($type == 1) {
                if ($relatedType != 7) {
                    $user_data['total_integral'] = $user->total_integral + $data['integral'];
                }
                $user_data['integral'] = $user->integral + $data['integral'];
                User::where('id', $userId)->update($user_data);
            }/* elseif ($user->integral >= $data['integral']) {
                $user->integral = $user->integral - $data['integral'];
                $user->save();
            }*/
            UserIntegral::insert($data);
        }
        $result = [
            'code' => 0,
            'data' => null,
            'msg' => '',
        ];
        return $result;

    }

    /**
     * 会员积分日志列表
     * @param  int $userId 会员编号
     * @param int $page 页码
     * @param int $pageSize 每页数
     */
    public static function  getUserList($userId, $type, $page, $pageSize){
        $list =  UserIntegral::where('user_id', $userId)
                ->orderBy('id', 'desc');

        if($type > 0) {
            $list->where('related_type', $type);
        }

       $list = $list ->skip($pageSize * ($page - 1))
        ->take($pageSize)
        ->get()->toArray();
        $integral = User::where('id', $userId)->pluck('integral');
        return ['list' => $list, 'integral' => $integral];
    }
}
