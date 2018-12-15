@extends('wap.community._layouts.base')


@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" onclick="$.href('{{u('Logistics/refundview',$args)}}')" href="#" data-transition='slide-out' data-no-cache="true">
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <a class="button button-link button-nav pull-right y-splistcd" href="#" data-transition='slide-out'>
            <span class="icon iconfont">&#xe692;</span>
            @foreach($indexnav as $key => $i_nav)
                @if(Lang::get('api_system.index_nav.'.$i_nav['type']) == 'mine' && (int)$counts['newMsgCount'] > 0)
                    <span class="y-redc"></span>
                @endif
            @endforeach
        </a>
        <h1 class="title f16">选择物流</h1>
    </header>
    <nav class="bar bar-tab y-heightnone">
        <a href="#" class="button button-fill button-danger y-button" id="udb_js_bnt_tj">提交</a>
    </nav>
@stop

@section('content')
    <style> 
        .popup-image{background-image: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQAQMAAAAlPW0iAAAAA3NCSVQICAjb4U/gAAAABlBMVEXMzMz////TjRV2AAAACXBIWXMAAArrAAAK6wGCiw1aAAAAHHRFWHRTb2Z0d2FyZQBBZG9iZSBGaXJld29ya3MgQ1M26LyyjAAAABFJREFUCJlj+M/AgBVhF/0PAH6/D/HkDxOGAAAAAElFTkSuQmCC");background-repeat:repeat;}
        .cut-btns{display:table; width:100%; height:100%;}
        .cut-btns div{display:table-cell; text-align:center; vertical-align:middle;}
        .cut-photo{width:100%; height:100%; position:relative; overflow:hidden;}
        .cut-photo .cut-photo-box{position:absolute; top:50%; left:50%; box-sizing: border-box;}
        .cut-photo .cut-photo-box div{position:absolute; z-index:2; box-shadow:0 0 0 2000px rgba(0,0,0, .5); width:100%; height:100%;}
        .cut-photo .cut-photo-box img{position:absolute; z-index:1;}
        .none{display:none!important;}
    </style>
    <ul class="x-ltmore f12 c-gray current_icon none">
        <link rel="stylesheet" href="{{ asset('wap/community/newclient/index_iconfont/iconfont.css') }}?{{ TPL_VERSION }}">
        @foreach($indexnav as $key => $i_nav)
            <li class="pl20" onclick="$.href('{{ u(Lang::get('api_system.index_link.'.$i_nav['type'])) }}')">
                <i class="icon iconfont mr5 vat">{{explode(",",$i_nav['icon'])[0].";"}}</i>
                {{$i_nav['name']}}
            </li>
        @endforeach
    </ul>
    <div class="content" id=''>
        <div class="list-block media-list">
            <ul class="y-nobor2">
                <li>
                    <a class="item-link item-content p0">
                        <div class="item-inner pr10 pl10">
                            <div class="item-title-row">
                                <div class="item-title">物流公司</div>
                                <div class="item-after f_aaa f13" onclick="$.href('{{ u('Logistics/flow',$args) }}')"><span id="newtip">@if($args['name']){{$args['name']}}@else请选择公司@endif</span><i class="icon iconfont c-gray2 vat">&#xe602;</i></div>
                                <input type="hidden" value="{{$args['name']}}" name="name"/>
                            </div>
                        </div>
                    </a>
                </li>
                <li>
                    <a class="item-link item-content p0">
                        <div class="item-inner pr10 pl10">
                            <div class="item-title-row">
                                <div class="item-title">退货单号</div>
                                <div class="item-after c-gray f12">
                                    <input type="text" class="tr" placeholder="请输入物流单号" name="no">
                                </div>
                            </div>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
        <div class="list-block media-list y-qrddqt">
            <ul>
                <li>
                    <a href="#" class="item-link item-content">
                        <div class="item-inner">
                            <div class="item-title-row f14">
                                <div class="item-title">退款地址</div>
                            </div>
                            <div class="item-text f12 c-gray5 mt5">
                                {{$data['sellerAddress']}}
                            </div>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
        <div class="content-block-title f14">上传退货凭证</div>
        <ul class="y-addimg clearfix">
            @for($i=1; $i<=4; $i++)
                <form>
                    <li id="li_{{$i}}">
                        <label id="imglabel-{{$i}}" class="img-up-lb" for="image-form-{{$i}}">
                            <img data-num="{{$i}}" class="image_upload" src="{{ asset('wap/community/newclient/images/addpic.png') }}">
                        </label>
                        <i class="delete tc none" data-id="{{$i}}"><i class="icon iconfont f20">&#xe605;</i></i>
                        <input type="text" name="images" id="upimage_{{$i}}" style="display:none"> 
                    </li>

                </form>
            @endfor
        </ul>

    </div>
@stop

@section($js)
    <script type="text/javascript">
        $(function(){
            $(document).on('click','.image_upload', function () {
                var thisObj = $(this); 
                $(this).fanweImage({
                    width:320, 
                    height:320, 
                    callback:function(url, target) {
                        thisObj.get(0).src = url;
                        $("#upimage_"+thisObj.data('num')).val(url); 
                        $("#li_"+thisObj.data('num')+" .delete").removeClass("none");
                    }
                });
            });  
 
            $(document).off("click", ".y-splistcd");
            $(document).on("click", ".y-splistcd", function(){
                if($(".x-ltmore").hasClass("none")){
                    $(".x-ltmore").removeClass("none");
                }else{
                    $(".x-ltmore").addClass("none");
                }
            });

            $(document).on("click", ".content", function(){
                $(".x-ltmore").addClass("none");
            });
            $(document).off("click", "#udb_js_bnt_tj");
            $(document).on("click", "#udb_js_bnt_tj", function(){
                var data = {}
                data.name = $(".page-current input[name='name']").val();
                data.no = $(".page-current input[name='no']").val();
                var images = [];
                $(".page-current input[name=images]").each(function(){
                    if($(this).val() != "" ){
                        images.push($(this).val());
                    }
                })
                data.images = images;
                data.id = "{{$data['id']}}";
                data.orderId = "{{$data['orderId']}}";
                var url = '{{u('Logistics/logisticssave')}}';
                $.post(url,data,function(res){
                    $.toast("物流信息填写成功!");
                    $.href("{{u('Logistics/refundview')}}?orderId="+data.orderId);
                },'json');
            });
        });
    </script>
@stop