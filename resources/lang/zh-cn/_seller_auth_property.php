<?php

return [

    /*
    |--------------------------------------------------------------------------
    | 服务人员后台授权菜单
    |--------------------------------------------------------------------------
    */

    "property" => [
        'name' => '物业',
        'icon' => 'folder-o',
        'url'  => 'SystemConfig/index',
        'controllers' => [
            'SystemConfig' => [
                'name' => '物业信息',
                'icon' => 'laptop',
                'url'  => 'SystemConfig/index',
                'actions' => [
                    'index' => ['name' => '商家列表'],
                    'changepwd'  => ['name' => '修改密码'],
                    'updatebasic'  => ['name' => '提交设置修改'],

                ]
            ],
            'Article' => [
                'name' => '公告管理',
                'icon' => 'tasks',
                'url'  => 'Article/index',
                'actions' => [
                    'index'     => ['name' => '公告列表'],
                    'create'    => ['name' => '添加公告'],
                    'edit'      => ['name' => '公告编辑'],
                    'destroy'   => ['name' => '删除公告'],
                ]
            ],
            'YellowPages' => [
                'name' => '物业黄页',
                'icon' => 'file-code-o',
                'url'  => 'YellowPages/index',
                'actions' => [
                    'index'     => ['name' => '物业黄页列表'],
                    'create'    => ['name' => '添加物业黄页'],
                    'edit'      => ['name' => '编辑物业黄页'],
                    'destroy'   => ['name' => '删除物业黄页'],
                ]
            ],

            'AdminRole' => [
                'name' => '管理员分组',
                'icon' => 'users',
                'url'  => 'AdminRole/index',
                'actions' => [
                    'index' 	=> ['name' => '组列表'],
                    'create' 	=> ['name' => '创建组'],
                    'edit' 		=> ['name' => '编辑组'],
                    'destroy' 	=> ['name' => '删除组'],
                ]
            ],
            'AdminUser' => [
                'name' => '管理员管理',
                'icon' => 'user',
                'url'  => 'AdminUser/index',
                'actions' => [
                    'index' 	=> ['name' => '管理员列表'],
                    'create' 	=> ['name' => '创建管理员'],
                    'edit' 		=> ['name' => '编辑管理员'],
                    'destroy' 	=> ['name' => '删除管理员'],
                ]
            ],


            'RepairStaff' => [
                'name' => '维修人员管理',
                'icon' => 'file-code-o',
                'url'  => 'RepairStaff/index',
                'actions' => [
                    'index'     => ['name' => '维修人员管理'],
                    'create'    => ['name' => '添加维修人员'],
                    'edit'      => ['name' => '编辑维修人员'],
                    'destroy'   => ['name' => '删除维修人员'],
                ]
            ],

        ],
       
        'nodes'=> [
             'PropertyBuilding' => [
                'name' => '房产管理',
                'icon' => 'laptop',
                'url'  => 'PropertyBuilding/index',
                'controllers' => [
                    'PropertyBuilding' => [
                        'name' => '楼宇管理',
                        'icon' => 'list-ul',
                        'url'  => 'PropertyBuilding/index',
                        'actions' => [
                            'index'     => ['name' => '楼宇列表'],
                            'create'    => ['name' => '添加楼宇'],
                            'edit'      => ['name' => '编辑楼宇'],
                            'destroy'   => ['name' => '删除楼宇'],
                            'roomindex'     => ['name' => '房间列表'],
                            'roomcreate'    => ['name' => '添加房间'],
                            'roomedit'      => ['name' => '编辑房间'],
                            'roomdestroy'   => ['name' => '删除房间'],
                            'roomsave'   => ['name' => '添加房间'],
                            'import'   => ['name' => '导入csv'],
                            'importsave'   => ['name' => '提交导入csv']
                        ]
                    ],
                ]
            ],
        ],
    ],

    "PropertyUser"=>[
        'name' => '业主管理',
        'icon' => 'laptop',
        'url'  => 'PropertyUser/index',
        'controllers' => [
            'PropertyUser' => [
                'name' => '业主信息',
                'icon' => 'laptop',
                'url'  => 'PropertyUser/index',
                'actions' => [
                    'index' => ['name' => '业主列表'],
                    'check' => ['name' => '门禁列表'],
                    'edit' => ['name' => '编辑门禁'],
                    'destroy' => ['name' => '删除'],
                    'destroydoor' => ['name' => '删除门禁'],
                    'export' => ['name' => '导出'],
                ]
            ],
            'PuserApply' => [
                'name' => '业主身份审核',
                'icon' => 'cart-plus',
                'url'  => 'PuserApply/index',
                'actions' => [
                    'index' => ['name' => '业主认证列表'],
                    'edit'  => ['name' => '详情']
                ]
            ],
        ],
    ],

    "propertyfee" => [
        'name' => '收费管理',
        'icon' => 'reorder',
        'url'  => 'PropertyFee/index',
        'controllers' => [
            'PropertyFee' => [
                'name' => '收费管理',
                'icon' => 'leanpub',
                'url'  => 'PropertyFee/index',
                'actions' => [
                    'index' => ['name' => '费用列表'],
                    'create' => ['name' => '添加费用'],
                    'edit' => ['name' => '编辑费用'],
                    'destroy' => ['name' => '删除费用'],
                    'export' => ['name' => '导出到excel'],
                    'searchroom' => ['name' => '搜索房间'],
                ]
            ],
            'RoomFee' => [
                'name' => '房间收费管理',
                'icon' => 'leanpub',
                'url'  => 'RoomFee/index',
                'actions' => [
                    'index' => ['name' => '费用列表'],
                    'create' => ['name' => '添加费用'],
                    'edit' => ['name' => '编辑费用'],
                    'destroy' => ['name' => '删除费用'],
                    'search' => ['name' => '搜索收费项目'],
                    'searchroom' => ['name' => '搜索房间'],

                ]
            ],
            'PayItem' => [
                'name' => '收费项目管理',
                'icon' => 'file-code-o',
                'url'  => 'PayItem/index',
                'actions' => [
                    'index'     => ['name' => '收费项目列表'],
                    'create'    => ['name' => '添加收费项目'],
                    'edit'      => ['name' => '公告收费项目'],
                    'destroy'   => ['name' => '删除收费项目'],
                ]
            ],
        ],
    ],

    "Repair"=>[
        'name' => '报修管理',
        'icon' => 'glass',
        'url'  => 'Repair/index',
        'controllers' => [
            'Repair' => [
                'name' => '报修管理',
                'icon' => 'glass',
                'url'  => 'Repair/index',
                'actions' => [
                    'index' => ['name' => '列表'],
                    'edit'  => ['name' => '详情']
                ]
            ],

            'RepairTotal' => [
                'name' => '报修统计',
                'icon' => 'glass',
                'url'  => 'RepairTotal/index',
                'actions' => [
                    'index' => ['name' => '列表'],
                    'destroy' => ['name' => '删除'],
                    'export' => ['name' => '导出excel'],
                    'edit'  => ['name' => '详情']
                ]
            ],


        ],
    ],



   "funds" => [
        'name' => '资金',
        'icon' => 'money',
        'url'  => 'Funds/index',
        'controllers' => [
            'Bank' => [
                'name' => '银行卡管理',
                'icon' => 'cc-discover',
                'url'  => 'Bank/index',
                'actions' => [
                    'index' => ['name' => '资金'],
                    'withdraw' => ['name' => '提现'],
                    'addInfo' => ['name' => '添加'],
                    'changebankcard' => ['name' => '更换绑定银行卡'],
                    'noInfo' => ['name' => '银行卡管理'],
                    'doAddInfo' => ['name' => '保存'],

                ]
            ],
            'PropertyOrder' => [
                'name' => '缴费记录',
                'icon' => 'bar-chart',
                'url'  => 'PropertyOrder/index',
                'actions' => [
                    'index' => ['name' => '缴费记录列表'],
                    'searchroom' => ['name' => '搜索房间'],

                ]
            ],
        ],
        'nodes'=> [
             'PropertyBuilding' => [
                'name' => '房产管理',
                'icon' => 'laptop',
                'url'  => 'PropertyBuilding/index',
                'controllers' => [
                    'Funds' => [
                        'name' => '资金管理',
                        'icon' => 'money',
                        'url'  => 'Funds/index',
                        'actions' => [
                            'index' => ['name' => '资金'],
                            'import' => ['name' => 'csv导入'],

                        ]
                    ],
                ]
            ],
        ],
    ],

    "PropertySystem"=>[
        'name' => '物业配置',
        'icon' => 'bar-chart',
        'url'  => 'PropertySystem/index',
        'controllers' => [
            'PropertySystem' => [
                'name' => '物业配置',
                'icon' => 'glass',
                'url'  => 'PropertySystem/index',
                'actions' => [
                    'index' => ['name' => '列表'],
                    'edit'  => ['name' => '详情'],
                    'create'  => ['name' => '添加物业配置'],
                    'destroy'  => ['name' => '删除物业配置']
                ]
            ],
        ],
    ],
];