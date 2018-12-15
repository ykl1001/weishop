@extends('wap.community._layouts.base')

@section('css')
@stop

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" onclick="$.href('{{ $nav_back_url }}')" href="#" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <a class="button button-link button-nav pull-right open-popup" data-popup=".popup-about" href="{{ u('UserCenter/addressdetail',['SetNoCity'=>Input::get('SetNoCity'),'cartIds'=>Input::get('cartIds'),'arg'=>Input::get('arg'), 'plateId'=>Input::get('plateId'), 'postId'=>Input::get('postId'),'change'=>Input::get('change'),'newadd'=>Input::get('newadd')]) }}">
            新增
        </a>
        <h1 class="title f16">我的{{ $title }}</h1>
    </header>
@stop

@section('content')
    @if(!empty($list))
        <div class="content infinite-scroll infinite-scroll-bottom pull-to-refresh-content" data-ptr-distance="55" data-distance="50" id=''>
            <!-- 加载提示符 -->
            <div class="pull-to-refresh-layer">
                <div class="preloader"></div>
                <div class="pull-to-refresh-arrow"></div>
            </div>
            <div id="list">
                @include('wap.community.usercenter.address_item')
            </div>
            <!-- 加载完毕提示 -->
            <div class="pa w100 tc allEnd none">
                <p class="f12 c-gray mt5 mb5">数据加载完毕</p>
            </div>
            <!-- 加载提示符 -->
            <div class="infinite-scroll-preloader none">
                <div class="preloader"></div>
            </div>
        </div>
    @else
        <div class="x-null pa w100 tc">
            <i class="icon iconfont">&#xe645;</i>
            <p class="f12 c-gray mt10">很抱歉！你还没有添加地址！</p>
        </div>
    @endif
@stop

@section($js)

<script type="text/javascript">
    // 列表
    $(function() {
        BACK_URL = "{{$nav_back_url or u('UserCenter/index')}}";

        var isChange = "{{ (int)Input::get('change') }}";
        var cartIds = "{{ Input::get('cartIds') }}";
        var plateId = "{{ Input::get('plateId') }}";
        var postId = "{{ Input::get('postId') }}";
        var goodsId = "{{ Input::get('goodsId') }}";
        var arg = "{{ Input::get('arg') }}";
        var sellerId = "{{ Input::get('sellerId') }}";


        //位移计算
        var start = 0,move = 0,movelang=0;

        $(document).off('touchstart',".content");
        $(document).on('touchstart',".content", function (e) {
            var point = e.touches ? e.touches[0] : e;
            start = point.screenY;
        });

        $(document).off('touchmove',".content");
        $(document).on('touchmove',".content", function (e) {
            var point = e.touches ? e.touches[0] : e;
            move = point.screenY;
            movelang = Math.abs(move - start);
        });


        $(document).on("touchend", ".y-address .card-content-inner", function ()
        {
            //禁用触发跳转
            if(movelang > 20)
            {
                movelang = 0;
                return false;
            }

            var id = $(this).data('id');
            if(sellerId > 0){
                if(id == 0){
                    return false;
                }
            }
            if(cartIds != ''){
                if(cartIds == 10)
                {
                    var url = "{!! u('Order/order',['addressId' => ADDID, 'cartIds'=> ARG]) !!}".replace("ADDID", id).replace("ARG", arg);
                }
                else
                {
                    var url = "{!! u('GoodsCart/index',['addressId' => ADDID]) !!}".replace("ADDID", id);
                }
                $.router.load(url, true);
            }else if(isChange == "1"){
                $.setDefaultAdd(id);
                $.router.load("{!! u('Index/index') !!}", true);
            } else if(isChange == "2"){
                $.setDefaultAdd(id);
                $.router.load("{!! u('Index/addressmap') !!}", true);
            } else if (plateId > 0) {
                var url = "{!! u('Forum/addbbs',['plateId'=>$args['plateId'], 'postId'=>$args['postId'],'addressId' => ADDID]) !!}".replace("ADDID", id);
                $.router.load(url, true);
            } else if (goodsId > 0) {
                var url = "{!! u('Order/integralorder',['goodsId'=>Input::get('goodsId'), 'addressId' => ADDID]) !!}".replace("ADDID", id);
                $.router.load(url, true);
            }
        })
		//编辑地址
        $(document).on("touchend",".urlte",function(){
            var url = "{!! urldecode(u('UserCenter/addressdetail' ,array('id' => 'ids','SetNoCity'=>Input::get('SetNoCity'),'gps'=>1,'arg'=>Input::get('arg'))))!!}".replace("ids", $(this).parents(".y-address").data('id'))+"&change="+isChange;
            $.router.load(url, true);
        });

        // 删除地址
        $(document).on("touchend",".y-address .y-del",function(){
            var id = $(this).parents(".y-address").attr('data-id');

            $.confirm('是否确认删除？', '操作提示', function () {
                $.deladds(id);
            });
        });

        $.deladds = function(id){
            var obj = $(".y-address"+id);
            var cartIds = "{{ Input::get('cartIds') }}";

            //if(cartIds == '' && plateId == 0) {
                $.showPreloader('正在删除，请稍等...');
                $.post("{{ u('UserCenter/deladdress') }}", {id: id}, function (res) {
                    $.hidePreloader();
                    if (res.code == 0) {
                        $.alert("删除成功",function(){
                            window.location.reload();
                        });
                    }else{
                        $.alert("删除失败");
                    }
                }, "json");
            //}
        };

        // 设置默认地址
        $(document).on("touchend", ".y-address .x-setDuf", function ()
        {
            var obj = $(this).parents(".y-address");
            var athis = $(this);
            var id = obj.data('id');
                $.setDefaultAdd(id);
        });

        $.setDefaultAdd = function(id){
            var obj = $(".y-address"+id);
            var change = "{{ (int)Input::get('change') }}";
            $.post("{{ u('UserCenter/setdefault') }}",{id:id,change:change},function(res){
                if(res.code == 0){
                    $(".y-address").removeClass("active");
                    obj.addClass("active");
                    $(".y-address").find("a").removeClass("x-okaddress").addClass("x-okaddress1");
                    obj.find(".x-setDuf").removeClass("x-okaddress1").addClass("x-okaddress");
                    obj.addClass("active").siblings().removeClass("active");
                    obj.find("span.y-mraddr").text("默认");
                    obj.siblings().find("span.y-mraddr").text("设为默认");
                }
            },"json");
        }

        $(document).on("touchend","#set-here",function(){
            $.post("{{ u('Index/here') }}",function(){
                $.router.load("{{ u('Index/index') }}", true);
            })
        });


        // 加载开始
        // 上拉加载
        var groupLoading = false;
        var groupPageIndex = 2;
        $(document).off('infinite', '.infinite-scroll-bottom');
        $(document).on('infinite', '.infinite-scroll-bottom', function() {
            // 如果正在加载，则退出
            if (groupLoading) {
                return false;
            }
            //隐藏加载完毕显示
            $(".allEnd").addClass('none');

            groupLoading = true;

            $('.infinite-scroll-preloader').removeClass('none');
            $.pullToRefreshDone('.pull-to-refresh-content');

            var data = new Object;
            data.page = groupPageIndex;

            $.post("{{ u('UserCenter/addressList') }}", data, function(result){
                groupLoading = false;
                $('.infinite-scroll-preloader').addClass('none');
                result  = $.trim(result);
                if (result != '') {
                    groupPageIndex++;
                    $('#list').append(result);
                    $.refreshScroller();
                }else{
                    $(".allEnd").removeClass('none');
                }
            });
        });

        // 下拉刷新
        $(document).off('refresh', '.pull-to-refresh-content');
        $(document).on('refresh', '.pull-to-refresh-content',function(e) {
            // 如果正在加载，则退出
            if (groupLoading) {
                return false;
            }
            groupLoading = true;
            var data = new Object;
            data.page = 1;

            $.post("{{ u('UserCenter/addressList') }}", data, function(result){
                groupLoading = false;
                result  = $.trim(result);
                if (result != "") {
                    groupPageIndex = 2;
                }
                $('#list').html(result);
                $.pullToRefreshDone('.pull-to-refresh-content');
            });
        });
        // 加载结束
    });
</script>
@stop