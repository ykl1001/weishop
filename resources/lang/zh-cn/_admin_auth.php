<?php

return [

    /*
    |--------------------------------------------------------------------------
    | 后台授权菜单
    |--------------------------------------------------------------------------
    */
    "general" => [
        'code' => 'Index',
        'name' => '首页',
        'icon' => 'th-list',
        'url'  => 'Index/index',
        'nodes'=> [
            'Dashboard' => [
                'name' => '后台首页 ',
                'icon' => 'bar-chart',
                'url'  => 'Index/index',
                'controllers' => [
                    'Index' => [
                        'name' => '后台首页',
                        'icon' => 'bar-chart',
                        'url'  => 'Index/index',
                        'actions' => [
                            'index' 	=> ['name' => '系统概况', 'show_menu' => 1],
                        ]
                    ],
                    'AdminUser' => [
                        'name' => '修改密码',
                        'icon' => 'user',
                        'url'  => 'AdminUser/repwd',
                        'actions' => [
                            'repwd'		=> ['name' => '修改密码', 'expand' => ['checkRepwd']],
                        ]
                    ],
                ],
            ],
            'AdminManage' => [
                'name' => '管理员管理 ',
                'icon' => 'bar-chart',
                'url'  => 'AdminUser/index',
                'controllers' => [
                    'AdminUser' => [
                        'name' => '管理员列表',
                        'icon' => 'user',
                        'url'  => 'AdminUser/index',
                        'actions' => [
                            'index' 	=> ['name' => '管理员列表', 'show_menu' => 1],
                            'create' 	=> ['name' => '创建管理员'],
                            'edit' 		=> ['name' => '编辑管理员'],
                            'destroy' 	=> ['name' => '删除管理员'],
                        ]
                    ],
                    'AdminRole' => [
                        'name' => '管理员分组',
                        'icon' => 'users',
                        'url'  => 'AdminRole/index',
                        'actions' => [
                            'index' 	=> ['name' => '组列表', 'show_menu' => 1],
                            'create' 	=> ['name' => '创建组'],
                            'edit' 		=> ['name' => '编辑组'],
                            'destroy' 	=> ['name' => '删除组'],
                        ]
                    ],
                ],
            ],
        ],


    ],
    "oneself" => [
        'name' => '商城',
        'icon' => 'ship',
        'url'  => 'OneselfConfig/index',
        'nodes'=> [
            'OneselfConfig' => [
                'name' => '商城配置 ',
                'icon' => 'cog',
                'url'  => 'OneselfConfig/index',
                'controllers' => [
                    'OneselfConfig' => [
                        'name' => '基础配置',
                        'icon' => 'tasks',
                        'url'  => 'OneselfConfig/index',
                        'actions' => [
                            'index' 	=> ['name' => '配置详情', 'show_menu' => 1],
                            'create'	=> ['name' => '添加商品'],
                            'edit'		=> ['name' => '编辑商品'],
                            'save'		=> ['name' => '保存配置'],
                            'destroy' 	=> ['name' => '删除商品'],
                            'showtime' 	=> ['name' => '获取营业时间'],
                            'deldatatime' 	=> ['name' => '删除营业时间'],
                            'addtime' 	=> ['name' => '添加营业时间'],
                            'updatetime' 	=> ['name' => '修改营业时间'],
                            'gettimes' 	=> ['name' => '获取单个营业时间'],
                        ]
                    ],
                    'OneselfMenu' => [
                        'name' => '菜单配置',
                        'icon' => 'tasks',
                        'url'  => 'OneselfMenu/index',
                        'actions' => [
                            'index' 	=> ['name' => '菜单列表', 'show_menu' => 1],
                            'create'	=> ['name' => '添加菜单'],
                            'edit'		=> ['name' => '编辑菜单'],
                            'destroy' 	=> ['name' => '删除菜单']
                        ]
                    ],
                    'OneselfAdv' => [
                        'name' => '广告管理',
                        'icon' => 'tasks',
                        'url'  => 'OneselfAdv/index',
                        'actions' => [
                            'index' 	=> ['name' => '广告列表', 'show_menu' => 1],
                            'create'	=> ['name' => '添加广告'],
                            'edit'		=> ['name' => '编辑广告'],
                            'destroy' 	=> ['name' => '删除广告']
                        ]
                    ],
                ]
            ],
            'OneselfGoods' => [
                'name' => '商品管理',
                'icon' => 'ship',
                'url'  => 'OneselfGoods/index',
                'controllers' => [
                    'OneselfGoods' => [
                        'name' => '商品列表',
                        'icon' => 'tasks',
                        'url'  => 'OneselfGoods/index',
                        'actions' => [
                            'index' 	=> ['name' => '商品列表', 'show_menu' => 1],
                            'create'	=> ['name' => '添加商品'],
                            'edit'		=> ['name' => '编辑商品'],
                            'destroy'   => ['name' => '删除商品'],
                            'serviceSave'   => ['name' => '保存商品'],
                            'goodsedit'   => ['name' => '保存商品'],
                            'systemGoods'   => ['name' => '通用商品库']
                        ]
                    ],
                    'OneselfService' => [
                        'name' => '服务列表',
                        'icon' => 'tasks',
                        'url'  => 'OneselfService/index',
                        'actions' => [
                            'index' 	=> ['name' => '服务列表', 'show_menu' => 1],
                            'create'	=> ['name' => '添加服务'],
                            'edit'		=> ['name' => '编辑服务'],
                            'serviceSave'   => ['name' => '保存服务'],
                            'destroy' 	=> ['name' => '删除服务'],
                            'search' 	=> ['name' => '搜索员工']
                        ]
                    ],
                ]
            ],
            'OneselfStaff' => [
                'name' => '员工管理',
                'icon' => 'user',
                'url'  => 'OneselfStaff/index',
                'controllers' => [
                    'OneselfStaff' => [
                        'name' => '员工列表',
                        'icon' => 'list-ul',
                        'url'  => 'OneselfStaff/index',
                        'actions' => [
                            'index' 	=> ['name' => '人员列表', 'expand' => ['search'], 'show_menu' => 1],
                            'create' 	=> ['name' => '添加人员'],
                            'edit' 	=> ['name' => '编辑人员'],
                            'destroy' 	=> ['name' => '删除人员'],
                        ]
                    ],
                ]
            ],

        ],
        'controllers' => [
            'OneselfTagList' => [
                'name' => '分类管理',
                'icon' => 'tag',
                'url'  => 'OneselfTagList/index',
                'actions' => [
                    'index' 	=> ['name' => '分类列表', 'expand' => ['search'], 'show_menu' => 1],
                    'create' 	=> ['name' => '新建分类'],
                    'edit' 		=> ['name' => '编辑分类'],
                    'destroy' 	=> ['name' => '删除分类'],
                    'isWapStatus'=> ['name' => '推荐到首页'],
                ]
            ],
            'OneselfNotice' => [
                'name' => '公告管理',
                'icon' => 'comments',
                'url'  => 'OneselfNotice/index',
                'actions' => [
                    'index' 	=> ['name' => '公告列表', 'show_menu' => 1],
                    'create' 	=> ['name' => '添加公告'],
                    'edit' 		=> ['name' => '编辑公告'],
                    'destroy' 	=> ['name' => '删除公告'],
                ]
            ],
        ],
    ],
    "sellergoods" => [
        'name' => '加盟',
        'icon' => 'th-large',
        'url'  => 'Service/index',
        'nodes'=> [
            'sellermanage' => [
                'name' => '商家管理',
                'icon' => 'user',
                'url'  => 'Service/index',
                'controllers' => [
                    'Service' => [
                        'name' => '商家列表',
                        'icon' => 'tasks',
                        'url'  => 'Service/index',
                        'actions' => [
                            'index' 	=> ['name' => '商家列表', 'show_menu' => 1],
                            'create'	=> ['name' => '添加商家'],
                            'createGoods'	=> ['name' => '添加商品'],
                            'createService'	=> ['name' => '添加服务'],
                            'updatebalance' => ['name'=>'线下充值操作'],
                            'edit'		=> ['name' => '商家编辑', 'expand' => ['banksave', 'delbank', 'gettimes', 'showtime', 'updatetime', 'addtime', 'deldatatime']],
                            'destroy' 	=> ['name' => '删除商家'],
                            'export'	=> ['name' => '导出到Excel'],
                            'systemGoods'   => ['name' => '通用商品库'],
                            'systemgoodsedit'   => ['name' => '添加通用商品库'],
                            'cateLists' => ['name'=>'分类列表'],
                            'cateedit' => ['name'=>'分类编辑', 'expand' => ['catesave']],

                            'serviceLists' 	=> ['name' => '服务列表'],
		                    'serviceEdit' => ['name'=>'服务编辑', 'expand' => ['serviceSave']],
		                    'serviceSave' => ['name'=>'保存服务'],

		                    'goodsLists' => ['name'=>'商品列表'],
                            'goodsEdit' => ['name'=>'商品编辑', 'expand' => ['goodsSave']],
		                    'goodsSave' => ['name'=>'保存商品'],

                            'creategoodscate' => ['name'=>'添加商品分类'],
                            'goodsDestroy'=> ['name'=>'删除商品'],
                        ]
                    ],
                    'Staff' => [
                        'name' => '人员管理',
                        'icon' => 'list-ul',
                        'url'  => 'Staff/index',
                        'actions' => [
                            'index' 	=> ['name' => '人员列表', 'expand' => ['search'], 'show_menu' => 1],
                            'create' 	=> ['name' => '添加人员'],
                            'edit' 	=> ['name' => '编辑人员'],
                            'destroy' 	=> ['name' => '删除人员'],
                        ]
                    ],
                    'SellerAuthIcon' => [
                        'name' => '认证列表',
                        'icon' => 'list-ul',
                        'url'  => 'SellerAuthIcon/index',
                        'actions' => [
                            'index' 	=> ['name' => '图标列表'],
                            'create' 	=> ['name' => '添加图标'],
                            'edit' 	=> ['name' => '编辑图标'],
                            'destroy' 	=> ['name' => '删除图标']
                        ]
                    ],
                    'SellerApply' => [
                        'name' => '商家审核',
                        'icon' => 'comments',
                        'url'  => 'SellerApply/index',
                        'actions' => [
                            'index' 	=> ['name' => '商家审核列表', 'show_menu' => 1],
                            'edit' 		=> ['name' => '编辑商家审核'],
                            'destroy' 	=> ['name' => '删除商家审核'],
                        ]
                    ],
                    'SellerCate' => [
                        'name' => '商家分类',
                        'icon' => 'comments',
                        'url'  => 'SellerCate/index',
                        'actions' => [
                            'index' 	=> ['name' => '分类列表', 'show_menu' => 1],
                            'create' 	=> ['name' => '添加分类'],
                            'edit' 		=> ['name' => '编辑分类'],
                            'destroy' 	=> ['name' => '删除分类'],
                        ]
                    ]
                ]
            ],
            'SystemGoods' => [
                'name' => '商品库管理',
                'icon' => 'user',
                'url'  => 'SystemGoods/index',
                'controllers' => [

                    'SystemGoods' => [
                        'name' => '商品列表',
                        'icon' => 'tasks',
                        'url'  => 'SystemGoods/index',
                        'actions' => [
                            'index' 	=> ['name' => '商品列表', 'show_menu' => 1],
                            'create'	=> ['name' => '添加商品'],
                            'edit'		=> ['name' => '编辑商品'],
                            'destroy' 	=> ['name' => '删除商品']
                        ]
                    ],
                ]
            ],
            'Stock' => [
                'name' => '库存库管理',
                'icon' => 'user',
                'url'  => 'Stock/index',
                'controllers' => [
                    'Stock' => [
                        'name' => '库存库列表',
                        'icon' => 'tasks',
                        'url'  => 'Stock/index',
                        'actions' => [
                            'index' 	=> ['name' => '库存库列表', 'show_menu' => 1],
                            'create'	=> ['name' => '添加库存库'],
                            'edit'		=> ['name' => '编辑库存库'],
                            'destroy' 	=> ['name' => '删除库存库']
                        ]
                    ],
                ]
            ],
            'SystemTagList' => [
                'name' => '商品标签管理',
                'icon' => 'user',
                'url'  => 'SystemTagList/index',
                'controllers' => [
                    'SystemTagList' => [
                        'name' => '标签列表',
                        'icon' => 'tasks',
                        'url'  => 'SystemTagList/index',
                        'actions' => [
                            'index' 	=> ['name' => '标签商品列表', 'show_menu' => 1],
                            'create'	=> ['name' => '新建商品标签'],
                            'edit'		=> ['name' => '编辑商品标签'],
                            'item'		=> ['name' => '分类详情'],
                            'destroy' 	=> ['name' => '删除商品标签'],
                            'secondLevel' 	=> ['name' => '获取分类']
                        ]
                    ],
                    'SystemTag' => [
                        'name' => '标签分类',
                        'icon' => 'list-ul',
                        'url'  => 'SystemTag/index',
                        'actions' => [
                            'index' 	=> ['name' => '标签分类列表', 'expand' => ['search'], 'show_menu' => 1],
                            'create' 	=> ['name' => '新建标签分类'],
                            'edit' 		=> ['name' => '编辑标签分类'],
                            'destroy' 	=> ['name' => '删除标签分类'],
                        ]
                    ],
                ]
            ],
            'propertygs' => [
                'name' => '物业管理',
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

                            'propertysystemindex' 		=> ['name' => '菜单配置列表'],
                            'propertysystemcreate' 	=> ['name' => '添加菜单配置'],
                            'propertysystemedit' 		=> ['name' => '编辑菜单配置','expand' => ['propertysystemsave']],
                            'propertysystemdestroy' 	=> ['name' => '删除菜单配置'],
                        ]
                    ],
                    'PropertyApply' => [
                        'name' => '物业审核',
                        'icon' => 'comments',
                        'url'  => 'PropertyApply/index',
                        'actions' => [
                            'index' 	=> ['name' => '商家审核列表'],
                            'detail'	=> ['name' => '物业公司信息'],
                        ]
                    ],
                    'RepairType' => [
                        'name' => '报修类型管理',
                        'icon' => 'comments',
                        'url'  => 'RepairType/index',
                        'actions' => [
                            'index' 	=> ['name' => '报修类型列表', 'show_menu' => 1],
                            'create' 	=> ['name' => '添加报修类型'],
                            'edit' 		=> ['name' => '编辑报修类型'],
                            'destroy' 	=> ['name' => '删除报修类型'],
                        ]
                    ],
                ]
            ],
            'DistrictManage' => [
                'name' => '小区管理',
                'icon' => 'delicious',
                'url'  => 'District/index',
                'controllers' => [
                    'District' => [
                        'name' => '小区列表',
                        'icon' => 'delicious',
                        'url'  => 'District/index',
                        'actions' => [
                            'index' 	=> ['name' => '小区列表', 'show_menu' => 1],
                            'create' 	=> ['name' => '小区城市'],
                            'destroy' 	=> ['name' => '删除小区'],
                            'edit' 	=> ['name' => '编辑小区'],
                            'search' 	=> ['name' => '查询小区'],
                        ]
                    ],
                ]
            ],
            'AliasSetting' => [
                'name' => '加盟配置',
                'icon' => 'cog',
                'url'  => 'Menu/index',
                'controllers' => [
                    'UserAppAdvPosition' => [
                        'name' => '广告位管理',
                        'icon' => 'picture-o',
                        'url'  => 'UserAppAdvPosition/index',
                        'actions' => [
                            'index' 	=> ['name' => '广告位列表', 'show_menu' => 1],
                            'create' 	=> ['name' => '创建广告位'],
                            'edit' 		=> ['name' => '编辑广告位'],
                            'destroy' 	=> ['name' => '删除广告位'],
                        ]
                    ],
                    'UserAppAdv' => [
                        'name' => '广告管理',
                        'icon' => 'picture-o',
                        'url'  => 'UserAppAdv/index',
                        'actions' => [
                            'index' 	=> ['name' => '广告列表', 'show_menu' => 1],
                            'create' 	=> ['name' => '创建广告'],
                            'edit' 		=> ['name' => '编辑广告'],
                            'create_adv' 	=> ['name' => '创建模板广告'],
                            'edit_adv' 	=> ['name' => '编辑模板广告'],
                            'destroy' 	=> ['name' => '删除广告'],
                        ]
                    ],
                    'Menu' => [
                        'name' => '首页菜单',
                        'icon' => 'picture-o',
                        'url'  => 'Menu/index',
                        'actions' => [
                            'index' 	=> ['name' => '菜单列表', 'show_menu' => 1],
                            'create' 	=> ['name' => '创建菜单'],
                            'edit' 		=> ['name' => '编辑菜单'],
                            'destroy' 	=> ['name' => '删除菜单'],
                        ]
                    ],
                ]
            ],

        ],
        'controllers' => [

        ]

    ],


    "order" => [
        'name' => '订单',
        'icon' => 'credit-card',
        'url'  => 'OrderStatistics/index',
        'nodes'=> [
            'ordermanage' => [
                'name' => '商家订单',
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
                    'ServiceOrder' => [
                        'name' => '服务订单',
                        'icon' => 'th-large',
                        'url'  => 'ServiceOrder/index',
                        'actions' => [
                            'index' 	=> ['name' => '服务订单列表', 'show_menu' => 1],
                            'detail' 	=> ['name' => '编辑服务订单', 'expand' => ['reassign', 'refundRemark']],
                            'export'	=> ['name' => '服务订单导出Excel'],
                            'destroy' 	=> ['name' => '删除服务订单'],
                        ]
                    ],
                    'AllOrder' => [
                        'name' => '全国店订单',
                        'icon' => 'th-large',
                        'url'  => 'AllOrder/index',
                        'actions' => [
                            'index' 	=> ['name' => '全国订单列表', 'show_menu' => 1],
                            'detail' 	=> ['name' => '查看全国订单', 'expand' => ['reassign', 'refundRemark']],
                            'export'	=> ['name' => '全国订单导出Excel'],
                            'destroy' 	=> ['name' => '删除全国订单'],
                        ]
                    ],

                ],
            ],
            'OneselfOrder' => [
                'name' => '商城订单',
                'icon' => 'calendar-o',
                'url'  => 'OneselfOrder/index',
                'controllers' => [
                    'OneselfOrder' => [
                        'name' => '商品订单列表',
                        'icon' => 'order',
                        'url'  => 'OneselfOrder/index',
                        'actions' => [
                            'index' 	=> ['name' => '订单列表', 'show_menu' => 1],
                            'edit'		=> ['name' => '订单详情'],
                            'destroy'   => ['name' => '删除订单']
                        ]
                    ],
                    'OneselfOrderService' => [
                        'name' => '服务订单列表',
                        'icon' => 'calendar-o',
                        'url'  => 'OneselfOrderService/index',
                        'actions' => [
                            'index' 	=> ['name' => '订单列表', 'show_menu' => 1],
                            'edit'		=> ['name' => '订单详情'],
                            'destroy' 	=> ['name' => '删除订单']
                        ]
                    ],
                ]
            ],
        ],
        'controllers' => [
            'IntegralOrder' => [
                'name' => '兑换订单',
                'icon' => 'calendar-o',
                'url'  => 'IntegralOrder/index',
                'actions' => [
                    'index' 	=> ['name' => '积分订单列表', 'show_menu' => 1],
                    'detail' 	=> ['name' => '编辑积分订单', 'expand' => ['reassign', 'refundRemark']],
                    'export'	=> ['name' => '积分订单导出Excel'],
                    'destroy' 	=> ['name' => '删除积分订单'],
                ]
            ],
            'OrderRate' => [
                'name' => '评价管理',
                'icon' => 'comments',
                'url'  => 'OrderRate/index',
                'actions' => [
                    'index' 	=> ['name' => '评价列表', 'show_menu' => 1],
                    'detail' 	=> ['name' => '编辑评价', 'expand' => ['saveRate']],
                    'rateReply' => ['name' => '评价回复'],
                    'destroy' 	=> ['name' => '删除评价'],
                ]
            ],
            'OrderStatistics' => [
                'name' => '订单统计',
                'icon' => 'bar-chart',
                'url'  => 'OrderStatistics/index',
                'actions' => [
                    'index' => ['name' => '统计信息', 'show_menu' => 1],
                ]
            ],
            'OrderConfig' => [
                'name' => '参数配置',
                'icon' => 'cog',
                'url'  => 'OrderConfig/index',
                'actions' => [
                    'index' => ['name' => '编辑配置', 'show_menu' => 1, 'expand' => ['save']],
                ]
            ],
        ]
    ],
    "Promotion" => [
        'name' => '营销',
        'icon' => 'money',
        'url'  => 'Promotion/index',
        'nodes'=> [
            'Invitation' => [
                'name' => '邀请返现',
                'icon' => 'user',
                'url'  => 'InvitationSet/set',
                'controllers' => [
                    'InvitationSet' => [
                        'name' => '返现设置',
                        'icon' => 'cog',
                        'url'  => 'InvitationSet/index',
                        'actions' => [
                            'index' => ['name' => '返现设置', 'show_menu' => 1],
                            'save' => ['name' => '保存设置', 'show_menu' => 1],
                        ]
                    ],
                    'InvitationOrder' => [
                        'name' => '返现订单',
                        'icon' => 'cog',
                        'url'  => 'InvitationOrder/index',
                        'actions' => [
                            'index' => ['name' => '返现订单', 'show_menu' => 1],
                        ]
                    ],
                    'InvitationUser' => [
                        'name' => '邀请会员列表',
                        'icon' => 'cog',
                        'url'  => 'InvitationUser/index',
                        'actions' => [
                            'index' => ['name' => '返现会员', 'show_menu' => 1],
                            'invitationList' => ['name' => '被推荐的会员'],
                        ]
                    ],
                    'InvitationMoney' => [
                        'name' => '返现缴费记录',
                        'icon' => 'cog',
                        'url'  => 'InvitationMoney/index',
                        'actions' => [
                            'index' => ['name' => '缴费列表', 'show_menu' => 1]
                        ]
                    ],
                ],
            ],

            'Integral' => [
                'name' => '积分活动',
                'icon' => 'shopping-cart',
                'url'  => 'IntegralConfig/edit',
                'controllers' => [
                    'IntegralConfig' => [
                        'name' => '积分配置',
                        'icon' => 'cog',
                        'url'  => 'IntegralConfig/edit',
                        'actions' => [
                            'edit' => ['name' => '积分配置', 'show_menu' => 1],
                            'save' => ['name' => '保存积分配置'],
                        ]
                    ],
                    'UserIntegral' => [
                        'name' => '积分记录',
                        'icon' => 'th-large',
                        'url'  => 'UserIntegral/index',
                        'actions' => [
                            'index' => ['name' => '积分记录列表']
                        ]
                    ],
                    'Integral' => [
                        'name' => '积分商城',
                        'icon' => 'shopping-cart',
                        'url'  => 'Integral/index',
                        'actions' => [
                            'create' => ['name' => '积分设置', 'show_menu' => 1],
                            'edit' => ['name' => '积分修改', 'show_menu' => 1],
                            'index' => ['name' => '商城列表'],
                            'save' =>  ['name' => '保存商品'],
                            'save' =>  ['name' => '保存商品'],

                            'destroy' =>  ['name' => '删除商品'],
                            'saveIntegral' =>  ['name' => '修改积分'],

                        ]
                    ]
                ],
            ],
        ],
        'controllers' => [
            'Promotion' => [
                'name' => '优惠券管理',
                'icon' => 'th-large',
                'url'  => 'Promotion/index',
                'actions' => [
                    'index' 	=> ['name' => '优惠券列表'],
                    'create' => ['name' => '添加优惠券'],
                    'edit' 		=> ['name' => '编辑优惠券'],
                    'sendsn' 		=> ['name' => '发放优惠券'],
                    'sendsnlist' =>  ['name' => '发放列表'],
                    'updatestatus' 		=> ['name' => '更新优惠券状态'],
                    'destroy' 		=> ['name' => '删除优惠券'],
                    'searchUser' 		=> ['name' => '会员搜索'],
                    'send' 		=> ['name' => '发放'],
                    'searchSeller' 		=> ['name' => '搜索商家'],
                ]
            ],
            'PromotionSn' => [
                'name' => '优惠券发放管理',
                'icon' => 'th-large',
                'url'  => 'PromotionSn/index',
                'actions' => [
                    'index' 	=> ['name' => '发放列表'],
                    'destroy' 	=> ['name' => '删除优惠券'],
                ]
            ],
            'Activity' => [
                'name' => '营销管理',
                'icon' => 'th-large',
                'url'  => 'Activity/index',
                'actions' => [
                    'index' 	=> ['name' => '营销列表'],
                    'create' 	=> ['name' => '添加营销类型'],
                    'add' 	    => ['name' => '添加营销'],
                    'edit' 		=> ['name' => '查看营销'],
                    'destroy' 	=> ['name' => '删除营销'],
                    'addSeller' => ['name' => '添加商家'],
                    'save_register_activity' => ['name' => '添加或编辑注册活动'],
                    'getpromotion' => ['name' => '获取优惠券'],
                    'share_activity' => ['name' => '分享活动'],
                    'save_full_activity' => ['name' => '添加满减活动'],
                    'save_new_activity' => ['name' => '添加首单立减'],
                    'save_full_data' => ['name' => '保存活动数据'],
                    'addSeller' => ['name' => '添加活动商家'],
                    'saveSellerIds' => ['name' => '保存已经选择的商家编号数据'],
                    'deleteSellerIds' => ['name' => '删除已经选择的商家编号'],
                    'cancellation' => ['name' => '作废'],
                    'save_share_activity' => ['name' => '保存分享'],

                ]
            ],
            'Special' => [
                'name' => '专题管理',
                'icon' => 'th-large',
                'url'  => 'Special/index',
                'actions' => [
                    'index' 	=> ['name' => '专题列表'],
                   // 'create' => ['name' => '添加专题'],
                    'edit' 		=> ['name' => '编辑专题'],
                   // 'destroy' 	=> ['name' => '删除专题'],
                ]
            ],

        ],

    ],
    "user" => [
        'name' => '会员',
        'icon' => 'user',
        'url'  => 'User/index',
        'nodes'=> [
            'user' => [
                'name' => '会员管理',
                'icon' => 'user',
                'url'  => 'User/index',
                'controllers' => [
                    'User' => [
                        'name' => '会员列表',
                        'icon' => 'th-large',
                        'url'  => 'User/index',
                        'actions' => [
                            'index' 	=> ['name' => '会员列表'],
                            'create' 	=> ['name' => '创建会员'],
                            'edit' 		=> ['name' => '编辑会员'],
                            'destroy' 	=> ['name' => '删除会员'],
                            'updatebalance' 	=> ['name' => '修改余额'],
                            'export' 	=> ['name' => '会员导出'],
                            'paylog' 	=> ['name' => '账户明细'],
                            'paylogExport' 	=> ['name' => '账户明细导出'],
                        ]
                    ],
                ]
            ],
            'Friend' => [
                'name' => '生活圈管理',
                'icon' => 'sellsy',
                'url'  => 'ForumPlate/index',
                'controllers' => [
                    'ForumPlate' => [
                        'name' => '板块管理',
                        'icon' => 'cubes',
                        'url'  => 'ForumPlate/index',
                        'actions' => [
                            'index' 	=> ['name' => '板块列表'],
                            'create' 	=> ['name' => '添加板块'],
                            'destroy' 	=> ['name' => '删除板块'],
                            'edit' 		=> ['name' => '编辑板块'],
                        ]
                    ],
                    'ForumPosts' => [
                        'name' => '帖子管理',
                        'icon' => 'list',
                        'url'  => 'ForumPosts/index',
                        'actions' => [
                            'index' 	=> ['name' => '帖子列表'],
                            'destroy' 	=> ['name' => '删除帖子'],
                            'edit' 		=> ['name' => '编辑帖子'],
                            'detail' 	=> ['name' => '帖子详情'],
                        ]
                    ],
                    'PostsCheck' => [
                        'name' => '发帖审核',
                        'icon' => 'eye',
                        'url'  => 'PostsCheck/index',
                        'actions' => [
                            'index' 	=> ['name' => '审核帖子列表'],
                        ]
                    ],
                    'KeyWords' => [
                        'name' => '关键字',
                        'icon' => 'info',
                        'url'  => 'KeyWords/index',
                        'actions' => [
                            'index' 	=> ['name' => '关键字过滤'],
                            'save' 	=> ['name' => '保存关键字'],
                        ]
                    ],
                    'ForumComplain' => [
                        'name' => '帖子举报',
                        'icon' => 'recycle',
                        'url'  => 'ForumComplain/index',
                        'actions' => [
                            'index' 	=> ['name' => '帖子举报管理'],
                            'dispose' 	=> ['name' => '帖子处理'],
                            'destroy' 	=> ['name' => '删除举报'],
                        ]
                    ],
                    'ForumMessage' => [
                        'name' => '论坛消息',
                        'icon' => 'envelope',
                        'url'  => 'ForumMessage/index',
                        'actions' => [
                            'index' 	=> ['name' => '消息列表'],
                            'destroy' 	=> ['name' => '删除消息'],
                        ]
                    ],
                ]
            ],
            'MessagesPush' => [
                'name' => '站内消息',
                'icon' => 'sellsy',
                'url'  => 'UserAppMessageSend/index',
                'controllers' => [
                    'UserAppMessageSend' => [
                        'name' => '会员消息推送',
                        'icon' => 'list',
                        'url'  => 'UserAppMessageSend/index',
                        'actions' => [
                            'index' 	=> ['name' => '推送列表', 'show_menu' => 1],
                            'create' 	=> ['name' => '创建推送', 'expand' => ['send']],
                            'destroy' 	=> ['name' => '删除推送'],
                            'search' 	=> ['name' => '搜索会员'],
                        ]
                    ],
                    'StaffAppMessageSend' => [
                        'name' => '商家消息推送',
                        'icon' => 'list',
                        'url'  => 'StaffAppMessageSend/index',
                        'actions' => [
                            'index' 	=> ['name' => '推送列表', 'show_menu' => 1],
                            'create' 	=> ['name' => '创建推送', 'expand' => ['send']],
                            'destroy' 	=> ['name' => '删除推送'],
                        ]
                    ]
                ]
            ],
            'Feedback' => [
                'name' => '意见反馈',
                'icon' => 'sellsy',
                'url'  => 'UserAppFeedback/index',
                'controllers' => [
                    'UserAppFeedback' => [
                        'name' => '会员反馈列表',
                        'icon' => 'comments',
                        'url'  => 'UserAppFeedback/index',
                        'actions' => [
                            'index' 	=> ['name' => '反馈列表', 'show_menu' => 1],
                            'edit' 		=> ['name' => '回复反馈'],
                            'destroy' 	=> ['name' => '删除反馈'],
                        ]
                    ],
                    'StaffAppFeedback' => [
                        'name' => '商家意见反馈',
                        'icon' => 'comments',
                        'url'  => 'StaffAppFeedback/index',
                        'actions' => [
                            'index' 	=> ['name' => '反馈列表', 'show_menu' => 1],
                            'edit' 		=> ['name' => '回复反馈'],
                            'destroy' 	=> ['name' => '删除反馈'],
                        ]
                    ]
                ],
            ],

            'Performance' => [
                'name' => '人员业绩',
                'icon' => 'line-chart',
                'url'  => 'Performance/index',
                'actions' => [
                    'index' => ['name' => '业绩排行榜'],
                    'bonus' => ['name' => '抽成排行榜'],
                    'sellerperformance' => ['name' => '卖家业绩查看'],
                    'staffperformance' => ['name' => '员工业绩查看'],
                ]
            ],
            'SellerMoneyLog' => [
                'name' => '资金流水',
                'icon' => 'check-circle-o',
                'url'  => 'SellerMoneyLog/index',
                'actions' => [
                    'index' 	=> ['name' => '流水列表'],
                ]
            ],
        ]
    ],
    "finance" => [
        'name' => '财务',
        'icon' => 'yen',
        'url'  => 'SellerWithdraw/index',
        'nodes'=> [
            'Statistics' => [
                'name' => '数据统计',
                'icon' => 'list-ol',
                'url'  => 'BusinessStatistics/index',
                'controllers' => [
                    'BusinessStatistics' => [
                        'name' => '商家营业统计',
                        'icon' => 'list-ol',
                        'url'  => 'BusinessStatistics/index',
                        'actions' => [
                            'index' 		=> ['name' => '商家营业统计', 'show_menu' => 1],
                            'monthAccount'	=> ['name' => '对帐单'],
                            'dayAccount'	=> ['name' => '明细'],
                            'export'	=> ['name' => '导出营业统计列表'],
                            'monthaccountexport'	=> ['name' => '导出商家营业统计对账单'],
                            'dayaccountexport'	=> ['name' => '导出商家营业统计日交易明细'],
                        ]
                    ],
                    'PlatformStatistics' => [
                        'name' => '平台数据统计',
                        'icon' => 'th-list',
                        'url'  => 'PlatformStatistics/index',
                        'actions' => [
                            'index' 	=> ['name' => '平台数据统计', 'show_menu' => 1],
                            'export'	=> ['name' => '导出报表平台数据统计列表'],
                        ]
                    ],
                    'ProxyStatistics' => [
                        'name' => '代理数据统计',
                        'icon' => 'table',
                        'url'  => 'ProxyStatistics/index',
                        'actions' => [
                            'index' 	=> ['name' => '代理数据统计', 'show_menu' => 1],
                            'sellerLists'	=> ['name' => '代理商家列表'],
                            'monthAccount'	=> ['name' => '代理商家对帐单'],
                            'dayAccount'	=> ['name' => '代理商家明细'],
                            'export'	=> ['name' => '导出代理列表'],
                            'sellerexport'	=> ['name' => '导出代理下商家月收支明细'],
                            'monthaccountexport'	=> ['name' => '导出代理下某商家月交易明细'],
                            'dayaccountexport'	=> ['name' => '导出代理下某商家日交易明细'],
                        ]
                    ],
                    'OneselfStatistics' => [
                        'name' => '商城商品统计',
                        'icon' => 'order',
                        'url'  => 'OneselfStatistics/index',
                        'actions' => [
                            'index' 	=> ['name' => '统计列表', 'show_menu' => 1],
                        ]
                    ],
                    'OneselfBusinessStatistics' => [
                        'name' => '商城营业统计',
                        'icon' => 'order',
                        'url'  => 'OneselfBusinessStatistics/index',
                        'actions' => [
                            'index' 	=> ['name' => '统计列表', 'show_menu' => 1],
                            'export'	=> ['name' => '导出商城营业统计报表'],
                        ]
                    ]

                ]
            ],
            'OneselfConfig' => [
                'name' => '退款管理 ',
                'icon' => 'cog',
                'url'  => 'OneselfConfig/index',
                'controllers' => [
                    'UserRefund' => [
                        'name' => '会员退款管理',
                        'icon' => 'user',
                        'url'  => 'UserRefund/index',
                        'actions' => [
                            'index' 	=> ['name' => '退款列表', 'show_menu' => 1],
                            'dispose' 	=> ['name' => '操作退款'],
                        ]
                    ],
                    'Nationwide' => [
                        'name' => '全国店退款管理',
                        'icon' => 'user',
                        'url'  => 'Nationwide/index',
                        'actions' => [
                            'index' 	=> ['name' => '退款列表', 'show_menu' => 1],
                            'dispose' 	=> ['name' => '操作退款'],
                        ]
                    ],
                ]
            ],
        ],
        'controllers' => [
            'PayLog' => [
                'name' => '会员支付日志',
                'icon' => 'money',
                'url'  => 'PayLog/index',
                'actions' => [
                    'index' 	=> ['name' => '日志列表', 'show_menu' => 1],
                    'export' 	=> ['name' => '导出会员支付日志', 'show_menu' => 1],
                ]
            ],
            'SellerPayLog' => [
                'name' => '商家支付日志',
                'icon' => 'money',
                'url'  => 'SellerPayLog/index',
                'actions' => [
                    'index' 	=> ['name' => '商家支付列表', 'show_menu' => 1, 'expand' =>['search']],
                ]
            ],
            'SellerWithdraw' => [
                'name' => '商家提现管理',
                'icon' => 'cc-discover',
                'url'  => 'SellerWithdraw/index',
                'actions' => [
                    'index' 	=> ['name' => '提现列表', 'show_menu' => 1],
                    'edit'		=> ['name' => '提现处理'],
                    'dispose'	=> ['name' => '处理提现'],
                    'export'	=> ['name' => '导出数据'],
                    'getWithdrawMessage'	=> ['name' => '获取提现未处理条数'],
                ]
            ],
            'UserWithdraw' => [
                'name' => '会员提现管理',
                'icon' => 'cc-discover',
                'url'  => 'UserWithdraw/index',
                'actions' => [
                    'index' 	=> ['name' => '提现列表', 'show_menu' => 1],
                    'edit'		=> ['name' => '提现处理'],
                    'dispose'	=> ['name' => '处理提现'],
                    'export'	=> ['name' => '导出数据'],
                    'getWithdrawMessage'	=> ['name' => '获取提现未处理条数'],
					'actions' => [
							'index' => ['name' => '更新缓存', 'show_menu' => 1, 'expand' => ['clear', 'local']],
					]
			]			
		],
		'SellerStaffWithdraw' => [
			'name' => '配送人员提现管理',
			'icon' => 'cc-discover',
			'url'  => 'SellerStaffWithdraw/index',
			'actions' => [
				'index' 	=> ['name' => '提现列表', 'show_menu' => 1],
				'edit'		=> ['name' => '提现处理'],
				'dispose'	=> ['name' => '处理提现'],
				'export'	=> ['name' => '导出数据'],
				'getWithdrawMessage'	=> ['name' => '获取提现未处理条数'],
			]
		],
		'Payment' => [
			'name' => '支付接口管理',
			'icon' => 'th-large',
			'url'  => 'Payment/index',
			'actions' => [
					'index' 	=> ['name' => '方式列表', 'show_menu' => 1],
					'edit' 		=> ['name' => '编辑方式'],
					'update'	=> ['name' => '编辑方式'],
				]
		],
	],

    ],
    "system" => [
        'name' => '系统',
        'icon' => 'cogs',
        'url'  => 'Config/index',
        'nodes'=> [
            'userapp' => [
                'name' => '移动端配置',
                'icon' => 'user',
                'url'  => 'UserAppConfig/index',
                'controllers' => [
                    'UserAppConfig' => [
                        'name' => '买家手机端配置',
                        'icon' => 'cog',
                        'url'  => 'UserAppConfig/index',
                        'actions' => [
                            'index' => ['name' => '编辑配置', 'show_menu' => 1, 'expand' => ['edit']],
                        ]
                    ],
                    'StaffAppConfig' => [
                        'name' => '商家手机端配置',
                        'icon' => 'cog',
                        'url'  => 'StaffAppConfig/index',
                        'actions' => [
                            'index' => ['name' => '编辑配置', 'show_menu' => 1, 'expand' => ['store']],
                            'edit'  => ['name' => '保存编辑'],
                        ]
                    ],
                ],
            ],
            'systemconfig' => [
                'name' => '系统配置',
                'icon' => 'user-secret',
                'url'  => 'Config/index',
                'controllers' => [
                    'Config' => [
                        'name' => '系统配置',
                        'icon' => 'cogs',
                        'url'  => 'Config/index',
                        'actions' => [
                            'index' => ['name' => '系统管理', 'show_menu' => 1, 'expand' => ['save']]
                        ]
                    ],
                    'HotLists' => [
                        'name' => '热门搜索词管理',
                        'icon' => 'list-ul',
                        'url'  => 'HotLists/index',
                        'actions' => [
                            'index' => ['name' => '热门搜索词列表', 'show_menu' => 1, 'expand' => ['store']],
                            'create' 	=> ['name' => '添加热门搜索词'],
                            'edit' 		=> ['name' => '编辑热门搜索词'],
                            'destroy' 	=> ['name' => '删除热门搜索词'],
                        ]
                    ],
                    'IndexNav' => [
                        'name' => '系统导航配置',
                        'icon' => 'list-ul',
                        'url'  => 'IndexNav/index',
                        'actions' => [
                            'index' => ['name' => '系统导航配置', 'show_menu' => 1],
                            'create' 	=> ['name' => '添加系统导航'],
                            'edit' 		=> ['name' => '编辑系统导航'],
                            'destroy' 	=> ['name' => '删除系统导航'],
                        ]
                    ],
                    'Watermark' => [
                        'name' => '水印设置',
                        'icon' => 'list-ul',
                        'url'  => 'Watermark/index',
                        'actions' => [
                            'index' => ['name' => '水印设置', 'show_menu' => 1],
                        ]
                    ],
                    'VcodeSet' => [
                        'name' => '验证码设置',
                        'icon' => 'list-ul',
                        'url'  => 'VcodeSet/index',
                        'actions' => [
                            'index' => ['name' => '验证码设置', 'show_menu' => 1],
                        ]
                    ],
                ]
            ],
            'smsconfig' => [
                'name' => '短信配置',
                'icon' => 'envelope',
                'url'  => 'SmsConfig/index',
                'controllers' => [
                    'MsgModel' => [
                        'name' => '短信模版',
                        'icon' => 'list-ul',
                        'url'  => 'MsgModel/index',
                        'actions' => [
                            'index' => ['name' => '短信模版配置'],
                            'getId' => ['name' => '获取模版'],
                            'save' => ['name' => '保存模板信息']
                        ]
                    ],
                    'SmsConfig' => [
                        'name' => '接口配置',
                        'icon' => 'cogs',
                        'url'  => 'SmsConfig/index',
                        'actions' => [
                            'index' => ['name' => '短信账号配置', 'show_menu' => 1],
                            'save' => ['name' => '保存账号'],
                            'surplus' => ['name' => '查询余额']
                        ]
                    ],
                ]
            ],
            'articles' => [
                'name' => '文章管理',
                'icon' => 'envelope',
                'url'  => 'Article/index',
                'controllers' => [
                    'Article' => [
                        'name' => '文章管理',
                        'icon' => 'file-text-o',
                        'url'  => 'Article/index',
                        'actions' => [
                            'index' 	=> ['name' => '文章列表', 'show_menu' => 1],
                            'create' 	=> ['name' => '添加文章'],
                            'edit' 		=> ['name' => '编辑文章'],
                            'destroy' 	=> ['name' => '删除文章'],
                        ]
                    ],
                    'ArticleCate' => [
                        'name' => '文章分类',
                        'icon' => 'list-ul',
                        'url'  => 'ArticleCate/index',
                        'actions' => [
                            'index' 	=> ['name' => '分类列表', 'show_menu' => 1],
                            'create' 	=> ['name' => '创建分类'],
                            'edit' 		=> ['name' => '编辑分类'],
                            'destroy' 	=> ['name' => '删除分类'],
                        ]
                    ],
                ]
            ],
            'proxy' => [
                'name' => '代理管理',
                'icon' => 'envelope',
                'url'  => 'Proxy/index',
                'controllers' => [
                    'Proxy' => [
                        'name' => '代理列表',
                        'icon' => 'user-plus',
                        'url'  => 'Proxy/index',
                        'actions' => [
                            'index' 	=> ['name' => '代理列表', 'show_menu' => 1],
                            'create' 	=> ['name' => '添加代理'],
                            'destroy' 	=> ['name' => '删除代理'],
                            'edit' 		=> ['name' => '编辑代理'],
                            'export' 	=> ['name' => '导出代理列表'],
                        ]
                    ],
                    'ProxyAudit' => [
                        'name' => '代理审核',
                        'icon' => 'user-plus',
                        'url'  => 'ProxyAudit/index',
                        'actions' => [
                            'index' 	=> ['name' => '代理审核列表', 'show_menu' => 1],
                            'destroy' 	=> ['name' => '删除审核代理'],
                            'edit' 		=> ['name' => '编辑审核代理'],
                            'audit' 	=> ['name' => '代理审核']
                        ]
                    ],
                ]
            ],
            /*'Project' => [
                'name' => '系统更新',
                'icon' => 'level-up',
                'url'  => 'Proxy/index',
                'controllers' => [
                    'Project' => [
                        'name' => '检查更新文件',
                        'icon' => 'level-up',
                        'url'  => 'Project/index',
                        'actions' => [
                            'index' => ['name' => '系统升级', 'show_menu' => 1, 'expand' => ['clear', 'local']],
                             'save' => ['name' => '项目解压', 'show_menu' => 1, 'expand' => ['clear', 'local']]
                        ]
                    ]
                ]
            ],*/
        ],
        'controllers' => [
            'City' => [
                'name' => '城市管理',
                'icon' => 'list',
                'url'  => 'City/index',
                'actions' => [
                    'index' 	=> ['name' => '开通城市列表', 'show_menu' => 1],
                    'create' 	=> ['name' => '添加开通城市', 'expand' => ['isdefault']],
                    'destroy' 	=> ['name' => '删除开通城市'],
                    'open' 	=> ['name' => '一键开通'],
                ]
            ],
            'Cache' => [
                'name' => '缓存管理',
                'icon' => 'cog',
                'url'  => 'Cache/index',
                'actions' => [
                    'index' => ['name' => '更新缓存', 'show_menu' => 1, 'expand' => ['clear', 'local']],
                ]
            ]
        ]
    ],




    "sendcenter" => [
        'name' => '配送中心',
        'icon' => 'th-list',
        'url'  => 'Dispatch/index',
        'nodes'=> [
            'Dispatch' => [
                'name' => '调度 ',
                'icon' => 'level-up',
                'url'  => 'Dispatch/index',
                'controllers' => [
                    'Dispatch' => [
                        'name' => '调度',
                        'icon' => 'bar-chart',
                        'url'  => 'Dispatch/index',
                        'actions' => [
                            'index' 	=> ['name' => '调度', 'show_menu' => 1],
                            'getsendstaff' 	=> ['name' => '获取配送员'],
                            'changestaffsystem' 	=> ['name' => '更改配送人员'],
                        ]
                    ],
                    'DispatchOrder' => [
                        'name' => '订单管理',
                        'icon' => 'cog',
                        'url'  => 'DispatchOrder/index',
                        'actions' => [
                            'index' 	=> ['name' => '订单管理', 'show_menu' => 1],
                            'getsendstaff' 	=> ['name' => '获取配送员'],
                            'changestaffsystem' 	=> ['name' => '更改配送人员'],
                        ]
                    ],
                ],
            ],
            'SystemStaff' => [
                'name' => '管理',
                'icon' => 'user',
                'url'  => 'SystemStaff/index',
                'controllers' => [
                    'SystemStaff' => [
                        'name' => '人员管理',
                        'icon' => 'bar-chart',
                        'url'  => 'SystemStaff/index',
                        'actions' => [
                            'index' 	=> ['name' => '人员管理', 'show_menu' => 1],
                            'create' 	=> ['name' => '添加人员'],
                            'edit' 	=> ['name' => '编辑人员'],
                            'destroy' 	=> ['name' => '删除人员'],
                        ]
                    ],
                ],
            ],
            'Total' => [
                'name' => '数据统计 ',
                'icon' => 'bar-chart',
                'url'  => 'SendDataSet/index',
                'controllers' => [
                    'SendDataSet' => [
                        'name' => '配送设置',
                        'icon' => 'cog',
                        'url'  => 'SendDataSet/index',
                        'actions' => [
                            'index' => ['name' => '配送设置', 'show_menu' => 1, 'expand' => ['edit']],
                            'save'  => ['name' => '保存配送设置'],
                        ]
                    ],
                    'SendDataStaff' => [
                        'name' => '人员配送数据',
                        'icon' => 'cog',
                        'url'  => 'SendDataStaff/index',
                        'actions' => [
                            'index' => ['name' => '人员配送数据', 'show_menu' => 1, 'expand' => ['store']],
                        ]
                    ],
                    'SendDataInfo' => [
                        'name' => '数据概况',
                        'icon' => 'cog',
                        'url'  => 'SendDataInfo/index',
                        'actions' => [
                            'index' => ['name' => '数据概况', 'show_menu' => 1, 'expand' => ['store']],
                        ]
                    ],
                ],
            ],
        ],
    ],

    "fanwefx" => [
        'name' => '分销平台',
        'icon' => 'exchange',
        'url'  => 'FxExchange/index',
        'controllers' => [
            'FxExchange' => [
                'name' => '兑换设置',
                'icon' => 'exchange',
                'url'  => 'FxExchange/index',
                'actions' => [
                    'index' 	=> ['name' => '分销平台积分兑换设置', 'show_menu' => 1],
                    'save' 	    => ['name' => '保存分销平台兑换数据'],
                ]
            ],
            'FxManageUrl' => [
                'name' => '登录分销平台',
                'icon' => 'line-chart',
                'url'  => 'FxManageUrl/index',
                'actions' => [
                    'index' => ['name' => '登录分销平台', 'show_menu' => 1],
                ]
            ],
            'Sharechapman' => [
                'name' => '分销用户审核',
                'icon' => 'cog',
                'url'  => 'Sharechapman/index',
                'actions' => [
                    'index' => ['name' => '分销用户审核', 'show_menu' => 1],
                    'dispose'		=> ['name' => '审核处理']
                ]
            ]
        ]
    ]
];
