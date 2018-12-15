<?php

return [
	'success' => [
		'mobile_verify' => '已成功发送验证码，请注意查收',
		'create_user_reg' => '注册成功',
		'submit' => '提交成功',
		'create_user_repwd' => '修改密码成功，点击确定重新登录',
        'create_user_repass' => '修改密码成功',
		'user_login' => '登录成功',
		'user_logout' => '退出成功',
		'update_info' => '更新成功',
		'delete_info' => '删除成功',
		'create_user_address' => '添加常用地址成功',
		'set_user_default_address' => '设为默认地址成功',
		'default_user_address' => '删除地址成功',
		'create_user_mobile' => '添加常用联系电话成功',
		'set_user_default_mobile' => '设为默认联系电话成功',
		'delete_default_user_address' => '删除联系电话成功',
		'set_goods_browse' => '更新服务浏览数成功',
		'user_exchange_promotion' => '兑换优惠券成功',
        'user_get_promotion' => '领取优惠券成功',
		'user_create_order' => '创建订单成功',
        'user_cancel_order' => '取消成功',
        'user_cancel_order_all' => '待商家核审',
		'user_pay_order'	=> '创建支付成功',
		'user_delete_order' => '删除成功',
		'user_confirm_order' => '确认成功',
		'create_order_rate' =>'评价成功',
		'collect_create' =>'收藏成功',
		'collect_delete' =>'取消收藏成功',
		'collect_seller_create' =>'收藏服务机构成功',
		'collect_seller_delete' =>'删除收藏服务机构成功',
		'collect_res_create'	=> '收藏餐厅成功',
		'collect_res_delete'	=> '删除收藏餐厅成功',
		'feedback_create' =>'感谢您的宝贵意见',
		'goods_complain_create' =>'举报成功,我们会尽快为处理',
		'order_complain_create' =>'举报成功,我们会尽快为处理',
		'order_refund_create' =>'申请退款成功,我们会尽快为处理',
		'buy_promotion' => '购买成功',
		'create_goodstype' => '创建菜单分类成功',
		'update_goodstype' => '更新菜单分类成功',
		'delete_goodstype' => '删除菜单分类成功',
		'create_sellercate' => '创建商家分类成功',
		'update_sellercate' => '更新商家分类成功',
		'delete_sellercate' => '删除商家分类成功',
        'add' => '添加成功',
        'update_property'=>'修改成功',
        'create_property'=>'添加成功',
        'verify_success' => '验证成功',
        'create_seller_reg' => '开店申请提交成功,请等待审核',
        'user_repaypass' => '修改支付密码成功',
        'updatesql' => '升级成功',
	],

    'house_type' => [
        '1' => '花园式住宅',
        '2' => '公寓式住宅',
        '3' => '经济适用房 ',
        '5' => '房改房',
        '6' => '花园别墅',
        '7' => '商住楼',
        '8' => '商业用房',
        '9' => '办公用房',
        '10' => '工业用房',
    ], 

    'repair_status' => [
        '1' => '待处理',
        '2' => '进行中',
        '3' => '已完成',
    ],

	'code' => [
        '10000' => '参数不正确',
        '10001' => '手机号码不能为空',
		'10002' => '手机号码不正确',
		'10003' => '验证码发送失败，请稍候再试，或联系客服',
		'10101' => '手机号码不能为空',
		'10102' => '手机号码不正确',
		'10103' => '验证码不能为空',
		'10104' => '验证码不正确',
		'10105' => '密码不能为空',
		'10106' => '密码错误，密码由6~20位的字符组成',
		'10107' => '注册失败，请稍候再试或者联系客服',
		'10108' => '手机号码未注册',
		'10109' => '登录密码错误',
		'10110' => '用户昵称不能为空',
		'10111' => '用户昵称含有敏感字：xxx',
		'10112' => '用户昵称错误，请输入6-30位字符',
		'10113' => '头像保存发生错误，请稍候再试或联系客服',
		'10114' => '更新信息发生错误，请稍候再试或联系客服',
		'10115' => '此账户已经被禁用，如需开通，请联系客服',
		'10116' => '用户不存在，修改密码失败',
		'10117' => '用户已存在，请勿重复注册',		
		'10118' => '正在审核中',
        '10119' => '原密码错误',
        '10120' => '原手机号错误',
        '10121' => '手机号修改失败',
        '10122' => '该账号已锁定',
        '10123' => '密码错误,密码由6位字符组成',
        '10124' => '新密码与原密码相同',
        '10125' => '支付密码错误',
        '10126' => '密码格式不正确',
        '20010' => '银行卡格式不正确',

        '10150' => '银行名称不能为空',
        '10151' => '银行卡号不能为空',
        '10152' => '请输入提现金额',
        '10153' => '提现金额不能大于可提现金额',
        '11153' => '单次提现金额不能小于100',
        '10154' => '没有找到银行卡',
        '10208' => '卡号户主不能为空',
        '10209' => '原手机号错误',
        '11001' => '不在提现周期内或资金不足',

		'10201' => '地址不能为空',
		'10202' => '地图定位不能为空',
		'10203' => '地图定位点格式错误',
		'10204' => '最多只能添加5个常用地址',
		'10205' => '保存常用地址失败',
		'10206' => '设为默认地址失败',
		'10207' => '删除地址失败',
		'10208' => '删除小区失败',
		'10209' => '收货人姓名不能为空',
		'10210' => '收货人电话不能为空',
		'10211' => '请输入门牌号',
		'10212' => '身份验证失败请重新登录',
		'10213' => '手机号码不正确',
		'10301' => '请输入优惠券兑换码',
		'10302' => '优惠券不存在',
		'10303' => '优惠券已兑换',
		'10304' => '优惠券已过期',
		'10305' => '兑换优惠券发生错误，请稍候再试或联系客服',
		'10306' => '优惠券已领取过',
		'10307' => '优惠券已领取完了',
		'10308' => '领取优惠券发生错误，请稍候再试或联系客服',
		'10309' => '优惠券不存在',
		'10401' => '收藏服务失败',
		'10402' => '删除收藏服务失败',
		'10403' => '收藏服务机构失败',
		'10404' => '删除收藏服务机构失败',
		'10405' => '收藏服务人员失败',
		'10406' => '删除收藏服务人员失败',

        '10501' => '该商家或商品不存在',
        '10502' => '收藏餐厅失败',
		'10503' => '删除收藏失败',
        '10504' => '已收藏过',

        '10601'	=> '请填写联系电话',
        '10602'	=> '联系电话格式不正确',
        '10603' => '最多只能添加5个常用联系电话',
        '10604' => '保存常用联系电话失败',
        '10605' => '设为默认联系电话失败',
        '10606' => '删除联系电话失败',

        '10607' => '已申请小区认证',
        '10608' => '请选择正确的小区',
        '10609' => '请选择正确的楼栋',
        '10610' => '请选择正确的房间',
        '10611' => '联系人姓名不能为空',
        '10612' => '联系人手机不能为空',
        '10613' => '请输入正确的手机号码',
        '10614' => '还未进行小区认证，无法申请门禁',
        '10615' => '该小区已经添加过',
        '10616' => '你还没有添加此小区，不能认证',
        //购物车


        '11000' => '配送抽佣不能大于配送服务费',
        '11001' => '请填写服务人员切换状态的时间间隔',

		'30001' => '服务机构不存在',
		'30002' => '服务人员不存在',


        '30003' => '商家名称不能为空',
        '30004' => '商家名称过短',
        '30005' => '商家名称过长',
        '30006' => 'logo图片不能为空',
        '30007' => '至少选择一个经营类型',
        '30008' => '地址不能为空',
        '30009' => '身份证号码不能为空',
        '30010' => '身份证号码无效',
        '30011' => '身份证正面照片不能为空',
        '30012' => '身份证背面照片不能为空', 
        '30013' => '公司营业执照相片不能为空',
        '30014' => '店铺介绍不能为空',
        '30015' => '未找到会员信息',
        '30016' => '上传图片失败',
        '30017' => '身份证号码已存在',
        '30018' => '注册失败，请稍候再试或者联系客服',
        '30019' => '注册失败，此会员已经开店',
        '30020' => '手机号码不能为空',
        '30021' => '手机号码不正确',
        '30022' => '此会员的开店申请已经成功或正在审核中',
        '30023' => '法人或真实姓名不能为空',
        '30024' => '服务电话不能为空',
        '30025' => '服务电话格式错误',


        '30101' => '父分类不存在',
        '30102' => '名称不能为空',
        '30201' => '请选择服务人员',
        '30202' => '名称不能为空',
        '30203' => '请设置正确的价格',
        '30204' => '请设置正确的门店价格',
        '30205' => '门店价格大于服务价格',
        '30206' => '请选择服务分类',
        '30207' => '简介不能为空',
        '30208' => '请上传服务图片',
        '30209' => '请设置服务时长',
        '30210' => '服务时长不能小于1小时',
        '30211' => '服务人员不存在',
        '30212' => '服务分类不存在',
        '30213' => '保存图片失败',
        '30401' => '订单评价不存在',
        '30402' => '回复内容不能为空',
        '30601' => '请输入手机号码',
        '30602' => '手机号码格式错误',
        '30603' => '手机号码已被注册',
        '30604' => '请输入名称',
        '30605' => '请上传LOGO图片',
        '30606' => 'LOGO图片保存失败',
        '30607' => '请选择所在省',
        '30608' => '所在省不存在',
        '30609' => '请选择所在市',
        '30610' => '所在市不存在',
        '30611' => '请选择所在县',
        '30612' => '所在县不存在',
        '30613' => '请输入地址',
        '30614' => '请选择地图定位',
        '30615' => '地图定位错误',
        '30616' => '请选择服务范围',
        '30617' => '服务范围错误',
        '30618' => '请输入真实姓名（身份认证为通过时）',
        '30619' => '请输入身份证号码（身份认证为通过时）',
        '30620' => '请输入正确的身份证号码（身份认证为通过时）',
        '30621' => '身份证号码已存在（身份认证为通过时）',
        '30622' => '请上传身份证正面图片（身份认证为通过时）',
        '30623' => '身份证正面图片保存失败（身份认证为通过时）',
        '30624' => '请上传身份证背面图片（身份认证为通过时）',
        '30625' => '身份证背面图片保存失败（身份认证为通过时）',
        '30626' => '请上传资质认证图片（资质认证为通过时）',
        '30627' => '资质认证图片保存失败（资质认证为通过时）',
        '30628' => '个人相册图片保存失败',
        '30629' => '请输入小区信息',
        '30630' => '全国店务必填写退货地址',

        '30913' => '标题不能为空',
        '30914' => '内容不能为空',
        '30915' => '请选择图片',
        '30916' => '帖子不存在',
        '30917' => '更新帖子失败', 
        '30918' => '回复帖子失败', 
        '30919' => '回复内容不能为空', 
        '30920' => '板块不存在', 
        '30921' => '删除回复失败', 
        '30922' => '点赞的帖子不存在',
        '30923' => '点赞失败', 
        '30924' => '您标题包含敏感词', 
        '30925' => '您内容包含敏感词', 
        '30926' => '该帖子已被锁定，不可编辑或回复', 
        '30927' => '论坛消息不存在', 
        '30928' => '该帖子正在审核中', 
		
        '32000' => '请选择商品标签一级分类',
        '32001' => '请选择商品标签二级分类',
        '32002' => '两级商品标签不匹配，请刷新页面重新选择',
        '32003' => '请选择商品分类',
		
		'40001' => '更新发生错误',
		'40002' => '服务不存在',
		'40003' => '请选择要预约的时长',
		'50001' => '订单不存在',
		'50002' => '订单还不能评价',
		'50003' => '请输入评价内容',
		'50004' => '保存评价图片失败',
		'50005' => '评价失败',
        '50006' => '客官,你还没有给我星星呐！',
        '50007' => '客官,别乱来呀,参数不对哦',
        '50008' => '菜品已评价咯',
        '50009' => '服务不存在',
        '50010' => '评价星级错误',
        '50011' => '该订单已评价',
        '50012' => '该订单不是全国店订单，请返回刷新重试',
        '50101' => '订单不存在',
        '50102' => '请输入处理结果',
        '50103' => '订单状态错误',
        '50104' => '该订单不允许删除',
        '50201' => '该服务人员不存在',
		'50218' => '出生日期不能大于当前日期',
		'50219' => '不存在此会员信息',
		'50220' => '不存在此商品', 
		'50221' => '不存在此商品规格', 
		'50222' => '添加到购物车失败', 
		'50223' => '超过了此商品限制的购买数量', 
		'50224' => '商品库存不足', 
		'60001' => '服务不存在',
        '61001' => '地址不存在',
		'60002' => '优惠券不存在',
		'60003' => '服务机构不支持该优惠券',
		'60004' => '优惠券已失效',
		'60005' => '优惠券已使用',
		'60006' => '服务机构在该时间段内忙碌，不能接受预约',
		'60007' => '请输入联系人',
		'60008' => '请输入联系手机',
		'60009' => '请输入正确的手机号',
		'60010' => '请输入联系地址',
		'60011' => '请选择地图定位坐标',
		'60012' => '不在服务机构的服务范围内',
		'60013' => '创建订单失败',
		'60014' => '没有找到相关订单',
		'60015' => '订单不能取消',
        '60115' => '订单预约码不正确',
        '60116' => '请填取消写备注',


		'60016' => '取消失败',
		'60017' => '订单已取消,不能进行支付',
		'60018' => '已经支付过了',
		'60019' => '不支付该支付方式',
		'60020' => '订单不能删除',
		'60021' => '订单还不能进行确认',
		'60022' => '订单已经确认过',
		'60023' => '支付失败',
		'60024' => '服务人员在该时间段内忙碌，不能接受预约',
		'60025' => '没有可分配的人员提供服务',
		'60026' => '服务人员不存在',
		'60027' => '创建支付日志失败',
		'60028' => '创建退款日志失败',
		'60030' => '未达到优惠券使用条件',
        '60201' => '父分类不存在',
        '60202' => '名称不能为空',
        '60301' => '该订单不存在',
        '60302' => '举报内容不能为空',
        '60303' => '创建举报失败',
        '60304' => '订单不可重复举报', 
        '60305' => '退款申请信息不能为空',
        '60306' => '此订单不可退款',
        '60307' => '申请退款失败',
        '60308' => '保存图片失败',
        '60309' => '洗车前图片不能为空',
        '60310' => '洗车后图片不能为空',
        '60311' => '车辆信息编号不能为空',
        '60312' => '小区编号不能为空',
        '60313' => '小区不存在',
        '60314' => '购买优惠券失败',
        '60315' => '活动不存在',
        '60316' => '外卖类型不能为空',
        '60317' => '请选择菜品',
        '60318' => '会员余额不足',
        '60319' => '生成日志失败',
        '60400' => '菜品不存在',
        '60401' => '订单内有菜品已售完',
        '60402' => '您购买的菜品已被别人先下手了',
        '60403' => '不能同时购买多个餐厅菜品',
        '60404' => '未获取到用户信息',
        '60405' => '预约午餐未开始',
        '60406' => '预约午餐已结束',
        '60407' => '类型不正确',

        '30333' => '请选择故障类型',
        '30334' => '请填写故障描述',
        '30335' => '故障类型未找到',
        '30336' => '您不在该小区',
        '30337' => '请填写维修时间',

        //下单
        '60501' => '无购物车商品',
        '60502' => '有不存在的购物车信息',
        '60503' => '超出购买限制',
        '60504' => '库存不足',
        '60505' => '商品已下架',
        '60506' => '地址信息错误',
        '60507' => '一次只许下一个单',
        '60508' => '请选择正确的时间',
        '60509' => '商家信息错误',
        '60510' => '未达到商家起送费',
        '60511' =>  '存在不同商家的商品或服务',
        '60512' =>  '不能同时存在商品和服务',
        '60513' =>  '不能同时存在两个服务',
        '60514' =>  '商家不在营业时间内',
        '60515' =>  '商家暂不支持线下支付',
        '60516' =>  '优惠券不可用',
        '60517' => '商品不存在或已下架',
        '60518' => '您的积分不足',
        '60601' => '地址信息不能为空',
        '60602' => '联系人不能为空',
        '60603' => '联系电话不能为空',
        '60604' => '门牌号不能为空',
        '60605' => '地址信息不完整，请重新选择',
        '60606' => '亲，您购买的东西还不够运费，请再买一点点吧 ~ ~',
        '60808' => '没有找到合适的配送人员!',

        '60901' => '该活动的优惠券你已领取',
        '60902' => '亲，您下手太慢了,下次快点吧.....',

        '70001' => '该服务机构不存在',
        '70002' => '反馈内容不能为空',
	    
        '77000' => '用户Id不能为空',
        '77001' => '小区名称不能为空',
		'77002' => '请选择楼宇',
		'77003' => '请选择房间号',
		'77004' => '请填写联系人信息',
		'77005' => '此用户已申请过认证',
		'77006' => '物业公司不存在',
		'77007' => '设置默认失败',
		'77008' => '设置默认成功',
		'77009' => '添加失败',
		'77010' => '删除失败',
		'77011' => '你还没有选择车系',

		'78000' => '菜品参数擦错误',
		'78001'	=> '菜品状态错误',
        '80309' => '收费项目不存在',   
        '80310' => '存在多个物业公司物业费',
        '80311' => '有已经正在支付的物业费项目',  
        '80401' => '订单已支付或不存在此订单',

        '81000' => '请选择退款服务类型',
        '81001' => '请输入或选择退款内容',
        '81002' => '该订单已经申请过了',
        '81003' => '申请退款成功,我们会尽快为处理',
        '81004' => '该订单还不能申请退款',
        '81005' => '操作失败',

        '88001' => '微信openId错误',
        '88002' => '该微信已经和其它账号绑定了',
        '88003' => '该账号已经和微信绑定了',

        /*专题*/
        '89000' => '专题不存在',


        '90001' => '该服务人员不存在',
        '90002' => '举报内容不能为空',
		'99996' => '会员需要登录才能调用此接口',
		'99997' => 'TOKEN错误',
		'99998' => '安全错误',
		'99999' => '程序处理错误',
	]
];
