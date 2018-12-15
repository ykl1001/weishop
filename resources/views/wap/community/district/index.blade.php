@extends('wap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left pageloading" href="{{$backurl}}" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">我的小区</h1>
        <a class="button button-link button-nav pull-right open-popup pageloading" data-popup=".popup-about" onclick="javascript:$.href('{{u('District/add')}}');">添加</a>
    </header>
@stop


@section('content')
    <div class="content" id=''>
        @foreach($list as $item)
        <div class="card y-wywdxq">
            <div class="card-content">
                <div class="card-content-inner y-czjz">
                    <div class="y-wydel icon iconfont" data-id="{{ $item['id'] }}">&#xe630;</div>
                    <div class="y-wyright" onclick="javascript:$.href('{{u('Property/index',['districtId'=>$item['id']])}}');">
                        <div class="y-tccenter"><span class="f15">{{$item['name']}}</span><i class="icon iconfont c-gray f14">&#xe602;</i></div>
                        @if($item['status'] == 1 || ($item['status'] == 0 && $item['buildId']))
                            <div class="f12 c-gray lh16 mt5">{{$item['buildingName']}}号楼 {{$item['roomNum']}}</div>
                        @elseif($item['sellerId'] > 0 && ($item['status'] == 0 && !$item['buildId']))
                            <div class="f12 c-gray lh16 mt5">{{$item['seller']['name']}}</div>
                        @elseif($item['sellerId'] == 0)
                            <div class="f12 c-gray lh16 mt5">小区物业未入驻</div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-footer buttons-tab">
                @if($item['status'] == 1)
                    <a href="" class="tab-link button c-black">身份：
                        @if($item['type'] == 0)
                            业主
                        @elseif($item['type'] == 1)
                            租客
                        @else
                            业主家属
                        @endif
                    </a>
                @elseif($item['status'] == 0 && $item['buildId'])
                    <a class="tab-link button c-black">待审核</a>
                @elseif($item['sellerId'] > 0 && ($item['status'] == 0 && !$item['buildId']))
                    <a onclick="javascript:$.href('{{u('District/userapply',['districtId'=>$item['id'],'propertyUserId'=>$item['propertyUserId']])}}');" class="tab-link button c-black">身份认证</a>
                @elseif($item['status'] == -1)
                    <a onclick="javascript:$.href('{{u('District/userapply',['districtId'=>$item['id'],'propertyUserId'=>$item['propertyUserId']])}}');" class="tab-link button c-black">身份认证未验证</a>
                @endif

                @if($item['status'] == 1)
                    <a href="" class="tab-link button c-black y-beforebor">当前人数：{{$item['counts']}}</a>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    @include('wap.community._layouts.swiper')
@stop

@section($js) 
    <!-- <script src="{{ asset('static/infinite-scroll/jquery.infinitescroll.js') }}"></script> -->
    <script type="text/javascript">
        $(function(){
            // 删除地址
            $(document).on("touchend",".y-wydel",function(){
                var id = $(this).attr('data-id');

                $.confirm('是否确认删除？', '操作提示', function () {
                    $.deladds(id);
                });
            });

            $.deladds = function(id){
                $.showPreloader('正在删除，请稍等...');
                $.post("{{ u('District/delete') }}", {districtId: id}, function (res) {
                    $.hidePreloader();
                    if (res.code == 0) {
                        $.alert("删除成功",function(){
                            window.location.reload();
                        });
                    }else{
                        $.alert("删除失败");
                    }
                }, "json");
            };
        })
    </script>
@stop 