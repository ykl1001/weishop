@extends('wap.community._layouts.base')


@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="{{u('UserCenter/index')}}" data-transition='slide-out' data-no-cache="true">
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">我的订单</h1>
        <a class="button button-link button-nav pull-right showmore" href="#" data-popup=".popup-about" external>
            <span class="icon iconfont">&#xe692;</span>
            <span class="y-redc none"></span>
        </a>
    </header>
    <div class="bar bar-header-secondary y-ordernav p0">
        <div class="buttons-tab y-couponsnav">
            <a href="#" onclick="$.href('{{ u('Order/index',['status'=>0]) }}')" data-no-cache="true" class="tab-link button @if($args['status'] == 0 || $args['status'] == '')active @endif">全部</a>
            <a href="#" onclick="$.href('{{ u('Order/index',['status'=>2]) }}')" data-no-cache="true" class="tab-link button @if($args['status'] == 2)active @endif">待付款</a>
            <a href="#" onclick="$.href('{{ u('Order/index',['status'=>3]) }}')" data-no-cache="true" class="tab-link button @if($args['status'] == 3)active @endif">待发货</a>
            <a href="#" onclick="$.href('{{ u('Order/index',['status'=>4]) }}')" data-no-cache="true" class="tab-link button @if($args['status'] == 4)active @endif">待收货</a>
            <a href="#" onclick="$.href('{{ u('Order/index',['status'=>1]) }}')" data-no-cache="true" class="tab-link button @if($args['status'] == 1)active @endif">待评价</a>
        </div>
    </div>
    <ul class="x-ltmore f12 c-gray none">
        <link rel="stylesheet" href="{{ asset('wap/community/newclient/index_iconfont/iconfont.css') }}?{{ TPL_VERSION }}">
        @foreach($indexnav as $key => $i_nav)
            <li class="pl20" onclick="$.href('{{ u(Lang::get('api_system.index_link.'.$i_nav['type'])) }}')"><i class="icon iconfont mr5 vat">{{explode(",",$i_nav['icon'])[0].";"}}</i>
                {{$i_nav['name']}}
                @if(Lang::get('api_system.index_nav.'.$i_nav['type']) == 'mine' && (int)$counts['newMsgCount'] > 0)
                    <span class="x-dot f12 none">{{(int)$counts['newMsgCount'] > 99? '99+' : (int)$counts['newMsgCount']}}</span>
                    <script type="text/javascript">
                        $(".y-redc").removeClass("none");
                    </script>
                @endif
                @if(Lang::get('api_system.index_nav.'.$i_nav['type']) == 'goodscart' && (int)$counts['cartGoodsCount'] > 0)
                    <span class="x-dot f12 none" id="tpGoodsCart">{{(int)$counts['cartGoodsCount'] > 99 ? '99+' : (int)$counts['cartGoodsCount']}}</span>
                    <script type="text/javascript">
                        $(".y-redc").removeClass("none");
                    </script>
                @endif
            </li>
        @endforeach
    </ul>
@stop

@section('css')
<style>
    /*取消原因*/
    .y-cancelreason{margin: -.5rem;}
    .y-cancelreason li{}
    .y-cancelreason li span{line-height: 1.75rem;display: inline-block;max-width: 90%;text-overflow: ellipsis;overflow: hidden;white-space: nowrap;vertical-align: top;}
    .y-radio{width: .8rem;height: .8rem;display: inline-block;-webkit-appearance: radio;margin-top: .35rem;}
    .y-otherreasons{clear: both;width: 100%;resize: none;min-height: 22px;overflow:auto;
        word-break:break-all;}
</style>
@stop

@section('content')
    <div class="content infinite-scroll infinite-scroll-bottom pull-to-refresh-content" data-ptr-distance="55" data-distance="50" id="">
        <div class="pull-to-refresh-layer">
            <div class="preloader"></div>
            <div class="pull-to-refresh-arrow"></div>
        </div>
        @if(!empty($list['orderList']))
            <div class="card-container" id="wdddmain">
                @include('wap.community.order.item')
            </div>
            <div class="pa w100 tc allEnd none">
                <p class="f12 c-gray mt5 mb5">数据加载完毕</p>
            </div>
        @else
            <div class="x-null pa w100 tc">
                <i class="icon iconfont">&#xe645;</i>
                <p class="f12 c-gray mt10">很抱歉！你还没有@if($args['status'] == 1 ) 待评价 @endif订单！</p>
            </div>
        @endif
        <!-- 加载提示符 -->
        <div class="infinite-scroll-preloader none">
            <div class="preloader"></div>
        </div>
    </div>

    <script type="text/tpl" id="cancehtml">
        <ul class="y-cancelreason tl f13">
            <li><span id="cancelreason1">不想买了</span><input type="radio" name="reason" class="fr y-radio" value="1" checked></li>
            <li><span id="cancelreason2">信息填写错误，重拍</span><input type="radio" name="reason" class="fr y-radio" value="2"></li>
            <li><span id="cancelreason3">等很久了，还未接单</span><input type="radio" name="reason" class="fr y-radio" value="3"></li>
            <li class="y-otherrea">
                <span id="cancelreason4">其他原因</span><input type="radio" name="reason" class="fr y-radio" value="4">
                <textarea id="cancelreasontext" placeholder="请输入其他原因" maxlength="200" class="y-otherreasons c-gray none"  rows="1" onpropertychange="this.style.height=this.scrollHeight+'px';"  oninput="this.style.height=this.scrollHeight+'px';" style="overflow:hidden;height:16px;"></textarea>
                <!-- <div class="test_box" contenteditable="true"></div> -->
            </li>
        </ul>
    </script>
@stop

@section($js)
@include('wap.community.order.orderjs')

<script type="text/javascript">
    $(function(){

        BACK_URL = "{{$nav_back_url or u('UserCenter/index')}}";
        $(document).off('click','.url');
        $(document).off('click','.confirmorder');
        $(document).off('click','.cancelorder');
        $(document).off('click','.okorder');
        //按钮事件
        $(document).on('click','.url', function () {
            $.router.load($(this).data('url'), true);
        }).on('click','.okorder', function () {
            var oid = $(this).data('id');
            $.confirm('确认删除订单吗？', '操作提示', function () {
                $.delOrders(oid);
            });
        }).on('click','.confirmorder', function () {
            var oid = $(this).data('id');
            $.confirm('确认完成订单吗？', '操作提示', function () {
                $.confirmOrder(oid);
            });
        }).on('click','.cancelorder', function () {
            var oid = $(this).data('id');
            var status = $(this).data('status');
            var isContackCancel = $(this).data("contactcancel");
            if(isContackCancel == "1"){
                $.alert('请联系商家取消订单', '取消提示');
            }else{
                var textcancel = $("#cancehtml").html();
                        $.modal({
                            title:  '取消原因',
                            text: textcancel,
                            buttons: [
                                {text: '取消'},
                                {
                                    text: '确定',
                                    bold:true,
                                    onClick: function() {
                                        var cancelradioval = $('.y-cancelreason input[name="reason"]:checked ').val();
                                        if(cancelradioval == 4){
                                            var cancelRemark = $("#cancelreasontext").val();
                                            cancelRemark = (cancelRemark == "") ? $("#cancelreason"+cancelradioval).html() : cancelRemark;
                                        }else{
                                            var cancelRemark = $("#cancelreason"+cancelradioval).html();
                                        }
                                        $.cancelOrder(oid, cancelRemark);
                                    }
                                }
                            ]
                        })
            }
        });

        // 加载开始
        // 上拉加载
        var groupLoading = false;
        var groupPageIndex = 2;
        var nopost = 0;
        $(document).off('infinite', '.infinite-scroll-bottom');
        $(document).on('infinite', '.infinite-scroll-bottom', function() {
            if(nopost == 1){
                return false;
            }
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
            data.status = "{{ $args['status'] }}";

            $.post("{{ u('Order/indexList') }}", data, function(result){
                groupLoading = false;
                $('.infinite-scroll-preloader').addClass('none');
                result  = $.trim(result);
                if (result != '') {
                    groupPageIndex++;
                    $('#wdddmain').append(result);
                    $.refreshScroller();
                }else{
                    $(".allEnd").removeClass('none');
                    nopost = 1;
                }
            });
        });
        //下拉刷新
        $(document).off('refresh', '.pull-to-refresh-content');
        $(document).on('refresh', '.pull-to-refresh-content',function(e) {
            // 如果正在加载，则退出
            if (groupLoading) {
                return false;
            }
            groupLoading = true;
            var data = new Object;
            data.page = 1;
            data.status = "{{ $args['status'] }}";

            $.post("{{ u('Order/indexList') }}", data, function(result){
                groupLoading = false;
                result  = $.trim(result);
                if (result != "") {
                    groupPageIndex = 2;
                }
                $('#wdddmain').html(result);
                $.pullToRefreshDone('.pull-to-refresh-content');
            });
        });

        //取消原因—其他原因
        $(document).on("click",".y-cancelreason li input",function(){
            $(".y-otherreasons").addClass("none");
        }).on("click",".y-cancelreason li.y-otherrea input",function(){
            $(".y-otherreasons").removeClass("none");
        })
        
        $(document).on("click",".y-cancelreason li",function(){
            $(".y-cancelreason li input").removeAttr("checked");
            $(this).find('input').attr("checked", "checked"); 
        })

        //js刷新
        //$.pullToRefreshTrigger('.pull-to-refresh-content');
        //加载结束
        
        //右上角更多
        $(".showmore").click(function(){
            if($(".x-ltmore").hasClass("none")){
                $(".x-ltmore").removeClass("none");
            }else{
                $(".x-ltmore").addClass("none");
            }
        });

        $.herf = function(url){
            $.router.load(url, true)
        }
    });
</script>
@stop
