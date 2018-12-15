<?php

return [

	/*
	|--------------------------------------------------------------------------
	| 服务人员后台授权菜单
	|--------------------------------------------------------------------------
	*/
    
	"general" => [
		'code' => 'Index',
		'name' => '概况',
		'icon' => 'th-list',
		'url'  => 'Index/index',
		'controllers' => [
			'Index' => [
				'name' => '服务概览',
				'icon' => 'bar-chart',
				'url'  => 'GoodsStatistics/index',
				'actions' => [
					'index' 	=> ['name' => '统计信息'],
					'service' 	=> ['name' => '服务器信息'],
					'upload' 	=> ['name' => '上传'],
					'repwd' 	=> ['name' => '修改密码', 'expand' => ['dopwd']], 
					'demo' 		=> ['name' => '演示'],
				]
			],
		],
	],
    
    "service" => [
        'name' => '项目',
        'icon' => 'folder-o',
        'url'  => 'Integral/index',
       'controllers' => [
           'Integral' => [
               'name' => '积分商城',
               'icon' => 'laptop',
               'url'  => 'Integral/index',
               'actions' => [
                   'index' => ['name' => '积分商城列表'],
                   'create' => ['name' => '添加商品'],
                   'edit' => ['name' => '编辑商品'],
                   'destroy' => ['name' => '删除商品'],
               ]
           ],
       ]
    ],
    "staff" => [
        'name' => '人员',
        'icon' => 'user',
        'url'  => 'Staff/index',
        'controllers' => [
            'Staff' => [
                'name' => '人员管理',
                'icon' => 'user',
                'url'  => 'Staff/index',
                'actions' => [
                    'index' => ['name' => '首页'],
                    'create' => ['name' => '添加人员'],
                    'edit' => ['name' => '编辑人员'],
                    'destroy' => ['name' => '删除人员'],
                ]
            ],
            'StaffSchedule' => [
                'name' => '人员日程',
                'icon' => 'eye-slash',
                'url'  => 'StaffSchedule/index',
                'actions' => [
                    'index' => ['name' => '首页'],
                    'check' => ['name' => '查看']
                ]
            ],
			'Staffleave' => [
                'name' => '人员请假',
                'icon' => 'user',
                'url'  => 'Staffleave/index',
                'actions' => [
                    'index' => ['name' => '首页'],
                    'agree' => ['name' => '同意'],
                    'destroy' => ['name' => '删除请假'],
                ]
            ],
        ]
    ],
    "order" => [
        'name' => '订单',
        'icon' => 'credit-card',
        'url'  => 'Comment/index',
        'controllers' => [
            "Comment" => [
                'name' => '订单评价',
                'icon' => 'comments',
                'url'  => 'Comment/index',
                'controllers' => [
                    'Comment' => [
                        'name' => '评价管理',
                        'icon' => 'comments',
                        'url'  => 'Comment/index',
                        'actions' => [
                            'index' => ['name' => '评价'],
                            'reply' => ['name' => '评价回复'],
                        ]
                    ],
                ]
            ],
        ]
    ],
   "funds" => [
        'name' => '资金',
        'icon' => 'money',
        'url'  => 'Funds/index',
        'controllers' => [
            'Funds' => [
                'name' => '账户结算',
                'icon' => 'money',
                'url'  => 'Funds/index',
                'actions' => [
                    'index' => ['name' => '账户结算'],
                ]
            ],
            'BusinessStatistics' => [
                'name' => '我的账单',
                'icon' => 'bars',
                'url'  => 'BusinessStatistics/index',
                'actions' => [
                    'index' => ['name' => '对帐单'],
                ]
            ],
            'Bank' => [
                'name' => '银行卡管理',
                'icon' => 'cc-discover',
                'url'  => 'Bank/index',
                'actions' => [
                    'index' => ['name' => '资金'],
                    'withdraw' => ['name' => '提现'],
                    'changebankcard' => ['name' => '更换绑定银行卡'],
                ]
            ],
        ]
    ],
    "report" => [
        'name' => '报表',
        'icon' => 'bar-chart',
        'url'  => 'Report/index',
        'controllers' => [
            'Report' => [
                'name' => '营业统计',
                'icon' => 'bar-chart',
                'url'  => 'Report/index',
                'actions' => [
                    'index' => ['name' => '报表'],
                ]
            ],
            'GoodsReport' => [
                'name' => '商品统计',
                'icon' => 'bar-chart',
                'url'  => 'GoodsReport/index',
                'actions' => [
                    'index' => ['name' => '报表'],
                ]
            ],
        ]
    ],
    
];
