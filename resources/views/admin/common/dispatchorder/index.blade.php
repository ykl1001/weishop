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
        .y-ptpsdiv{border: 1px solid #dadada;padding: 1rem;}
        .y-ptpsleft{width: 80%;overflow: hidden;}
        .y-ptpsleft p{text-overflow:ellipsis;overflow: hidden;white-space: nowrap;}
        .y-ptpsright{width: 20%;overflow: hidden;}
        .y-ptpsbtn{padding:0 3rem;border: 1px solid #dadada;height: 2.5rem;line-height: 2.5rem;display: inline-block;margin-top: .5rem;color: #000;}
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
                <nav label="配送中订单">
                    <attrs>
                        <url>{{ u('DispatchOrder/index',['status'=>'10','nav'=>'nav2']) }}</url>
                        <css>{{$nav2}}</css>
                    </attrs>
                </nav>
                <nav label="配送完成订单">
                    <attrs>
                        <url>{{ u('DispatchOrder/index',['status'=>'11','nav'=>'nav3']) }}</url>
                        <css>{{$nav3}}</css>
                    </attrs>
                </nav>
                <nav label="异常订单">
                    <attrs>
                        <url>{{ u('DispatchOrder/index',['status'=>'12','nav'=>'nav4']) }}</url>
                        <css>{{$nav4}}</css>
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
                <column code="id" label="编号" align="center" width="10"></column>
                <column code="sn" label="订单信息" align="center" width="20"></column>
                <column code="" label="所在城市" align="center" width="10">{{ $list_item['seller']['province']['name'] }} {{ $list_item['seller']['city']['name'] }} {{ $list_item['seller']['area']['name'] }}</column>
                <column code="" label="所属公司" align="center" width="10">
                    <p>{{ $list_item['staff']['company'] }}</p>
                </column>
                <column code="" label="商家" align="center" width="10">
                    <p>{{ $list_item['seller']['name'] }}</p>
                </column>
                <column code="" label="配送人员" align="center" width="10">
                    <p>{{ $list_item['staff']['name'] }}</p>
                </column>
                <column code="" label="会员" align="center" width="10">
                    <p>{{ $list_item['user']['name'] }}</p>
                </column>
                <column code="" label="下单时间" align="center" width="10">
                    <p>{{ yztime($list_item['createTime']) }}</p>
                </column>
                <column code="" label="配送地址" align="center" width="10">
                    <p>{{ $list_item['address'] }}</p>
                </column>
                <column code="" label="确认完成时间" align="center" width="10">
                    <p>{{ yztime($list_item['buyerFinishTime']) }}</p>
                </column>
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
                width:300,
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
                    var changesellerStaffId = $('input[name="reason"]').val();
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
