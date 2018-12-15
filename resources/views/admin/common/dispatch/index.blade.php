@extends('admin._layouts.base')
@section('css')
<style>
    .y-addrmain{line-height: 26px;text-overflow:ellipsis;white-space: nowrap;overflow: hidden;}
    /*.y-width110{width:110px;text-align: center;float:right;}*/
    .y-ggtctitle span{font-weight: normal;}
    .y-ggtctitle input{line-height: 20px;height: 20px;border-radius: 5px;border:1px solid #ccc;vertical-align: 2px;margin: 0 10px;padding:5px;}
    .y-tcsearch{line-height: 30px;height: 30px;vertical-align: 2px;border-radius: 5px;padding: 0 15px;}
    .y-tcchange{max-height: 190px;overflow-x: hidden;overflow-y: scroll;}
    .y-tcchange::-webkit-scrollbar{width: 0px!important;}
    .y-tcchange li div{float: left;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;}
    .y-tcchange50{width: 50%;}
    .y-tcchange25{width: 25%;}
    .y-tcchange li .y-radio{vertical-align: -2px;margin-right: 3px;}
    .zydialog_close{display: none;}
    .y-ggtctitleright{position: absolute;right: 11px;top: 0;display: block;overflow: hidden;font-weight: normal;font-size: 14px;}
</style>
@stop
@section('right_content')
    @yizan_begin
    <php> 
        $navs = ['nav1','nav2','nav3','nav4','nav5', 'nav6'];
        $nav = in_array(Input::get('nav'),$navs) ? Input::get('nav') : 'nav1' ; 
        $$nav = "on";
    </php>
        <yz:list>
            <tabs>
                <navs>
                    <nav label="待指派订单">
                        <attrs>
                            <url>{{ u('Dispatch/index',['status'=>'9','nav'=>'nav1']) }}</url>
                            <css>{{$nav1}}</css>
                        </attrs>
                    </nav>
                    <nav label="预指派订单">
                        <attrs>
                            <url>{{ u('Dispatch/index',['status'=>'13','nav'=>'nav5']) }}</url>
                            <css>{{$nav5}}</css>
                        </attrs>
                    </nav>
                </navs>
            </tabs>
            @yizan_yield('searchUrl')
            <search url="{{ $searchUrl }}">
            @yizan_stop
                <row>
                    <item name="sn" label="订单号"></item>
                    @yizan_yield("search_userMobile")
                    <item name="mobile" label="会员电话"></item>
                    @yizan_stop
                    <item name="sellerName" label="商家名称"></item>
                    <btn type="search"></btn>
                </row>
            </search>

            <table>
                <columns>
                    <column code="sn" label="订单信息" align="left" style="vertical-align:top;font-size: 16px;" width="80">
                        <p >取货：{{ $list_item['seller']['address'] }}-{{ $list_item['seller']['name'] }}</p>
                        <p>送至：{{ $list_item['address'] }} - {{ $list_item['user']['name'] }}   {{ $list_item['user']['mobile'] }}</p>
                        <p>订单号：{{ $list_item['sn'] }}</p>
                    </column>
                    <actions width="30">
                        <p class="tc y-addrmain">{{ $list_item['seller']['province']['name'] }} {{ $list_item['seller']['city']['name'] }} {{ $list_item['seller']['area']['name'] }}</p>
                        @if($nav == 'nav4')
                            <p class="clearfix tc">
                                异常原因：{{$list_item['cancelRemark']}}
                            </p>
                        @elseif($nav == 'nav3')
                        @else
                            <p class="clearfix tc">
                                <a href="javascript:;" class="btn mb10 hsbtn-78 ml20" onclick="$.updateStaff('{{$list_item['id']}}','{{$list_item['sellerStaffId']}}','{{ $list_item['seller']['province']['name'] }} {{ $list_item['seller']['city']['name'] }} {{ $list_item['seller']['area']['name'] }}')">更改</a>
                            </p>
                        @endif

                        @if($list_item['orderStatusStr'] == '配送中')
                            <p class="tc y-addrmain">配送员{{$list_item['staff']['name']}}({{$list_item['staff']['mobile']}})配送中</p>
                        @elseif($list_item['status'] == ORDER_STATUS_GET_SYSTEM_SEND)
                            <p class="tc y-addrmain">配送员{{$list_item['staff']['name']}}({{$list_item['staff']['mobile']}})取货中</p>
                        @elseif($list_item['status'] == '200')
                            <p class="tc y-addrmain">配送员{{$list_item['staff']['name']}}({{$list_item['staff']['mobile']}})确认送达</p>
                        @endif
                    </actions>
                </columns>
            </table>
        </yz:list>
    @yizan_end
@stop

@section('js')
<script type="text/tpl" id="showStaffHeadTpl">
    <div class="showstaff">
        <div class="y-ggtctitle">
            <span>更改派送人员</span><input type="text" class="sellerstaffname" data-id="" data-sellerStaffId="" value=""><button class="y-tcsearch" style="cursor: pointer" onclick="$.searchstaff()">搜索</button>
        </div>
        <div class="y-ggtctitleright"></div>
    </div>
</script>
<script type="text/tpl" id="showStaffBodyTpl">
    <div id="serviceContent">
        <div style="width:500px;text-align:center;padding:10px;">
            <ul class="y-tcchange tl f13 clearfix stafflist"></ul>
        </div>
    </div>
</script>
<script type="text/tpl" id="showStaffLiTpl">
    @{{~it.data :item:index}}
    <li class="clearfix">
        @{{? item.id == it.sellerStaffId }}
        <div class="y-tcchange50 checked">
            <input type="radio" name="reason" class="y-radio" value="@{{=item.id}}" checked>
        @{{??}}
        <div class="y-tcchange50">
            <input type="radio" name="reason" class="y-radio" value="@{{=item.id}}">
        @{{?}}
            <span id="cancelreason1">@{{=item.name}}</span>
        </div>
        <div class="y-tcchange25">
            待取货<span class="ml5">@{{=item.getorderNum}}</span>
        </div>
        <div class="y-tcchange25">
            待送达<span class="ml5">@{{=item.sendingNum}}</span>
        </div>
    </li>
    @{{~}}
</script>
    <script>
        $.searchstaff = function(){
            var sellerstaffname = $('.sellerstaffname').val();
            if(sellerstaffname == ""){
                alert("请填写配送员的名称");
                return false;
            }
            var id = $('.sellerstaffname').attr("data-id");
            var sellerStaffId = $('.sellerstaffname').attr("data-sellerStaffId");
            $.jsongetstaff(id,sellerStaffId,sellerstaffname);
        }

        $.jsongetstaff = function(id,sellerStaffId,sellerstaffname){
            $('.stafflist').html('配送人员加载中...');
            $.post("{{ u('Dispatch/getsendstaff') }}",{'orderId':id,'name':sellerstaffname},function(res){
                if(res.code > 0){
                    $('.stafflist').html(res.msg);
                }else if(res.data != ""){
                    res.sellerStaffId = sellerStaffId;
                    var html = $.Template($("#showStaffLiTpl").html(), res);
                    $('.stafflist').html(html);
                }else if(res.data == "" && res.code == 0){
                    $('.stafflist').html('没有配送人员');
                }
            },'json');
        }

        $.updateStaff = function(id,sellerStaffId,address){
            var html = $.Template($("#showStaffBodyTpl").html());
            var dialog = $.zydialogs.open(html, {
                boxid:'SET_GROUP_WEEBOX',
                width:700,
                title: $.Template($("#showStaffHeadTpl").html()),
                showClose:true,
                showButton:true,
                showOk:true,
                showCancel:true,
                okBtnName: '确认',
                cancelBtnName: '取消',
                contentType:'content',
                onReady: function() {
                    $.jsongetstaff(id,sellerStaffId);
                    $(".sellerstaffname").attr('data-id',id).attr('data-sellerStaffId',sellerStaffId);
                    $(".y-ggtctitleright").html(address);
                },
                onOk: function(){
                    var changesellerStaffId = $('input[name="reason"]:checked').val();
                    if(changesellerStaffId == ""){
                        alert("请选择配送人员")
                        return '';
                    }
                    $.post("{{ u('Dispatch/changestaffsystem') }}",{'id':id,'changesellerStaffId':changesellerStaffId},function(res){
                        dialog.setLoading(false);
                        if(res.code > 0) {
                            alert(res.msg);
                        }else if(res.code == 0){
                            window.location.reload();
                        }
                    },'json');
                },
                onCancel:function(){
                    $.zydialogs.close("SET_GROUP_WEEBOX");
                }
            });
            $("input[type='checkbox'],input[type='radio']").uniform();
        }
    </script>


@stop
