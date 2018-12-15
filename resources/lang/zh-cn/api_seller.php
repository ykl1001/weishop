<?php

return 
[
	'success' => 
	[
		'schedule_update' => '状态更新成功',
	],
    'property' =>[
        'charging_item' => [
            '0' => '建面|金额＝单价*建筑面积',
            '1' => '套内|金额＝单价*套内面积',
            '2' => '定额|金额＝单价',
        ],
        'charging_unit' => [
            '0' => '月',
        ]
    ],
	'code' =>
    [
        '10101' => '手机号码不能为空',
        '10102' => '手机号码不正确',
        '10103' => '验证码不能为空',
        '10104' => '验证码不正确',
        '10105' => '密码不能为空',
        '10106' => '密码错误，密码由5~20位的字符组成',
        '10107' => '注册失败，请稍候再试或者联系客服',
        '10108' => '手机号码未注册',
        '10109' => '登录密码错误',
        '20001' => '没有找到相关订单',
        '20002' => '订单状态不合法',



		'40001' => '状态不合法',
        '40002' => '小时列表不能为空',
		'40003' => '状态更新失败',

        '99996' => '需要登录才能调用此接口',
        '99997' => 'TOKEN错误',
        '99998' => '安全错误',
        '99999' => '程序处理错误',
	]
];