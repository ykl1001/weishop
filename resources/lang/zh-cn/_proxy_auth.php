<?php

return [

	/*
	|--------------------------------------------------------------------------
	| 代理后台授权菜单
	|--------------------------------------------------------------------------
	*/
	"general" => [
		'code' => 'Index',
		'name' => '概况',
		'icon' => 'th-list',
		'url'  => 'Index/index',
		'controllers' => [
			'Index' => [
				'name' => '统计概览',
				'icon' => 'bar-chart',
				'url'  => 'Index/index',
				'actions' => [
					'index' 	=> ['name' => '统计信息', 'show_menu' => 1],
				]
			],

		],
		
	],
	"sellergoods" => [
		'name' => '商家',
		'icon' => 'th-large',
		'url'  => 'Service/index',
		'nodes'=> [
            'sellermanage' => [
				'name' => '商家管理',
				'icon' => 'user',
				'url'  => 'Service/index',
				'controllers' => [
					'Service' => [
						'name' => '商家管理',
						'icon' => 'tasks',
						'url'  => 'Service/index',
						'actions' => [
		                    'index' 	=> ['name' => '商家列表', 'show_menu' => 1],
		                    'create'	=> ['name' => '添加商家'],
		                    'edit'		=> ['name' => '商家详情', 'expand' => ['banksave', 'delbank', 'gettimes', 'showtime', 'updatetime', 'addtime', 'deldatatime']],
		                    'destroy' 	=> ['name' => '删除商家'],
		                    'export'	=> ['name' => '导出到Excel'],

		                    'cateLists' => ['name'=>'分类列表'],
		                    'cateEdit' => ['name'=>'分类详情', 'expand' => ['catesave']],

		                    'serviceLists' 	=> ['name' => '服务列表'],
		                    'serviceEdit' => ['name'=>'服务详情', 'expand' => ['serviceSave']],

		                    'goodsLists' => ['name'=>'商品列表'],
		                    'goodsEdit' => ['name'=>'商品详情', 'expand' => ['goodsSave']],

		                    'staffLists' => ['name'=>'人员列表'],
		                    'staffEdit' => ['name'=>'人员详情'],
						]
					], 
				]
			],
        ],
		'controllers' => [
            'Sellerapply' => [
				'name' => '商家审核',
				'icon' => 'comments',
				'url'  => 'Sellerapply/index',
				'actions' => [
						'index' 	=> ['name' => '商家审核列表', 'show_menu' => 1],
						'edit' 		=> ['name' => '商家详情'], 
					]
			], 
		]

	], 
	"propertymanage" => [
		'name' => '物业',
		'icon' => 'th-large',
		'url'  => 'Property/index',
        'nodes'=> [
            'propertygs' => [
				'name' => '物业公司管理',
				'icon' => 'user',
				'url'  => 'Property/index',
				'controllers' => [
					'Property' => [
						'name' => '物业公司',
						'icon' => 'tasks',
						'url'  => 'Property/index',
						'actions' => [
		                    'index' 			=> ['name' => '物业公司列表'],
		                    'create'			=> ['name' => '添加物业公司'],
		                    'edit'				=> ['name' => '物业公司编辑'],
		                    'destroy' 			=> ['name' => '删除物业公司'], 
		                    'export'			=> ['name' => '导出到Excel'],

		                    'dooropenlog' 		=> ['name' => '门禁记录'],
		                    'dooraccess' 		=> ['name' => '门禁列表'], 
		                    'dooredit' 			=> ['name' => '添加门禁','expand' => ['doorsave']],  

		                    'buildingindex' 	=> ['name' => '楼宇列表'],
							'buildingcreate' 	=> ['name' => '添加楼宇'],
							'buildingedit' 		=> ['name' => '编辑楼宇','expand' => ['buildingsave']],
							'buildingdestroy' 	=> ['name' => '删除楼宇'],

							'roomindex' 		=> ['name' => '房间列表'],
							'roomcreate' 		=> ['name' => '添加房间'],
							'roomedit' 			=> ['name' => '编辑房间','expand' => ['roomsave']],
							'roomdestroy' 		=> ['name' => '删除房间'],

							'puserindex' 		=> ['name' => '业主列表'],
							'pusercheck' 		=> ['name' => '查看门禁'],
							'pusercreate'		=> ['name' => '添加业主门禁'],
							'puseredit' 		=> ['name' => '编辑门禁','expand' => ['pusersave']],
							'puserdestroyaccess'=> ['name' => '删除门禁'],
							'puserdestroy' 		=> ['name' => '删除业主'],

							'repairindex' 		=> ['name' => '报修管理'],
							'repairdetail' 		=> ['name' => '报修详情'],
							'repairsave' 		=> ['name' => '保存报修'],

                            'staffindex' 		=> ['name' => '维修人员管理'],
                            'staffcreate' 		=> ['name' => '添加维修人员'],
                            'staffedit' 		=> ['name' => '编辑维修人员'],
                            'staffsave' 		=> ['name' => '保存维修人员'],

							'articleindex' 		=> ['name' => '公告列表'],
							'articlecreate' 	=> ['name' => '添加公告'],
							'articleedit' 		=> ['name' => '编辑公告','expand' => ['articlesave']],
							'articledestroy' 	=> ['name' => '删除公告'],
						]
					],
					'Propertyapply' => [
						'name' => '物业审核',
						'icon' => 'comments',
						'url'  => 'Propertyapply/index',
						'actions' => [
							'index' 	=> ['name' => '商家审核列表'], 
		                    'detail'	=> ['name' => '物业公司信息'],
						]
					],
				]
			],
        ], 
	],
	"order" => [
		'name' => '订单',
		'icon' => 'credit-card',
		'url'  => 'Order/index',
        'nodes'=> [
            'ordermanage' => [
                'name' => '订单管理',
                'icon' => 'th-large',
                'url'  => 'Order/index',
                'controllers' => [
                    'Order' => [
                        'name' => '商品订单',
                        'icon' => 'th-large',
                        'url'  => 'Order/index',
                        'actions' => [
                            'index' 	=> ['name' => '商品订单列表', 'show_menu' => 1],
                            'detail' 	=> ['name' => '编辑商品订单', 'expand' => ['reassign', 'refundRemark']],
                            'export'	=> ['name' => '商品订单导出Excel'],
                            'destroy' 	=> ['name' => '删除商品订单'],
                        ]
                    ],
                    'Serviceorder' => [
                        'name' => '服务订单',
                        'icon' => 'th-large',
                        'url'  => 'Serviceorder/index',
                        'actions' => [
                            'index' 	=> ['name' => '服务订单列表', 'show_menu' => 1],
                            'detail' 	=> ['name' => '编辑服务订单', 'expand' => ['reassign', 'refundRemark']],
                            'export'	=> ['name' => '服务订单导出Excel'],
                            'destroy' 	=> ['name' => '删除服务订单'],
                        ]
                    ], 
                ],
            ],
        ],
		'controllers' => [
            'Orderrate' => [
                'name' => '评价管理',
                'icon' => 'comments',
                'url'  => 'Orderrate/index',
                'actions' => [
                    'index' 	=> ['name' => '评价列表', 'show_menu' => 1],
                    'detail' 	=> ['name' => '编辑评价', 'expand' => ['saveRate']],
                    'rateReply' => ['name' => '评价回复'],
                    'destroy' 	=> ['name' => '删除评价'],
                ]
            ],
		] 
	], 
	"district" => [
		'name' => '小区',
		'icon' => 'credit-card',
		'url'  => 'District/index',
		'nodes'=> [
			'district' => [
				'name' => '小区管理',
				'icon' => 'district',
				'url'  => 'District/index',
				'controllers' => [
					'District' => [
						'name' => '小区管理',
						'icon' => 'th-large',
						'url'  => 'District/index',
						'actions' => [
							'index' 	=> ['name' => '小区列表'],
							'create' 	=> ['name' => '创建小区'],
							'edit' 		=> ['name' => '小区详情'], 
						]
					],
				]
			],
		],
	], 
	"proxys" => [
		'name' => '代理',
		'icon' => 'cube',
		'url'  => 'Proxy/index', 
		'controllers' => [ 
            'Proxy' => [
                'name' => '代理管理',
                'icon' => 'list-ol',
                'url'  => 'Proxy/index',
                'actions' => [
                    'index'  => ['name' => '代理列表', 'show_menu' => 1],  
                    'detail' => ['name' => '代理详情'],
					'edit' 	 => ['name' => '编辑代理'],
					'create' => ['name' => '创建代理'],
					'repwd' => ['name' => '修改密码']
                ]
            ], 
            'Proxyaudit' => [
					'name' => '代理审核',
					'icon' => 'user-plus',
					'url'  => 'Proxyaudit/index',
					'actions' => [
							'index' 	=> ['name' => '代理审核列表', 'show_menu' => 1], 
							'edit' 		=> ['name' => '代理详情'],
					]
			],
		]
	],  
	"report" => [
		'name' => '报表统计',
		'icon' => 'money',
		'url'  => 'Businessstatistics/index',
		'controllers' => [ 
            'Businessstatistics' => [
                'name' => '商家营业统计',
                'icon' => 'list-ol',
                'url'  => 'Businessstatistics/index',
                'actions' => [
                    'index' 		=> ['name' => '商家营业统计', 'show_menu' => 1], 
                    'monthAccount'	=> ['name' => '对帐单'],
                    'dayAccount'	=> ['name' => '明细']
                ]
            ],
            'Platformstatistics' => [
                'name' => '代理数据统计',
                'icon' => 'th-list',
                'url'  => 'Platformstatistics/index',
                'actions' => [
                    'index' 	=> ['name' => '销售数据统计', 'show_menu' => 1], 
                ]
            ], 
		]
	],

];
