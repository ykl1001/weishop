@extends('wap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left pageloading" href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else {{u('Index/index')}} @endif" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        @if(!$isData)
            <h1 class="title f16">经营类型</h1>
        @else
            <h1 class="title f16">查看类型</h1>
        @endif
    </header>
@stop

@section('content')
    @if(!$isData)
        @if(SELLER_TYPE_IS_ALL)
            <div class="bar bar-footer" style="height:3.2rem;">
                <a class="y-paybtn f16 mt10 confirm_btn">确定</a>
            </div>
        @endif
    @endif
    <div class="content" id=''>
        <div class="content-block-title f14 c-gray">请选择经营类型</div>
        <div class="list-block y-syt y-jyclass cate_item">
            <ul>
                @foreach($cate as $key => $value)
                    <php>
                        $flag = false;
                        if(SELLER_TYPE_IS_ALL){
                        foreach($current as $id){
                        if($value['id'] == $id){
                        $flag = true;
                        break;
                        }
                        }
                        }else{
                        if($value['id'] == $current){
                        $flag = true;
                        }
                        }
                    </php>

                    @if(SELLER_TYPE_IS_ALL)
                        @if($storeType == 0)
                            @if(empty($value['childs']))
                                <li class="item-content checks storeType1-{{$value['type']}} @if($flag) active @endif" data-id="{{$value['id']}}">
                                    <div class="item-inner">
                                        <div class="item-title f16 c-gray">{{$value['name']}}</div>
                                        <div class="item-after c-black"><i class="icon iconfont c-red f24">&#xe610;</i></div>
                                    </div>
                                </li>
                            @else
                                <li class="item-content y-jylxtitle storeType1-{{$value['type']}}">
                                    <div class="item-inner">
                                        <div class="item-title f16 c-gray">{{$value['name']}}</div>
                                    </div>
                                </li>
                                @foreach($value['childs'] as $k => $v)
                                    @if(SELLER_TYPE_IS_ALL)
                                        <li class="item-content checks storeType1-{{$value['type']}} @if(in_array($v['id'], $current)) active @endif" data-id="{{$v['id']}}">
                                            <div class="item-inner ml10">
                                                <div class="item-title f16">{{$v['name']}}</div>
                                                <div class="item-after c-black"><i class="icon iconfont c-red f24">&#xe610;</i></div>
                                            </div>
                                        </li>
                                    @else
                                        <li class="item-content checks storeType1-{{$value['type']}} @if($v['id'] == $current) active @endif" data-id="{{$v['id']}}">
                                            <div class="item-inner ml10">
                                                <div class="item-title f16">{{$v['name']}}</div>
                                                <div class="item-after c-black"><i class="icon iconfont c-red f24">&#xe610;</i></div>
                                            </div>
                                        </li>
                                    @endif
                                @endforeach
                            @endif
                        @endif
                        @if($storeType == 1 && $value['type'] == 1)
                            @if(empty($value['childs']))
                                <li class="item-content checks storeType1-{{$value['type']}} @if($flag) active @endif" data-id="{{$value['id']}}">
                                    <div class="item-inner">
                                        <div class="item-title f16 c-gray">{{$value['name']}}</div>
                                        <div class="item-after c-black"><i class="icon iconfont c-red f24">&#xe610;</i></div>
                                    </div>
                                </li>
                            @else
                                <li class="item-content y-jylxtitle storeType1-{{$value['type']}}">
                                    <div class="item-inner">
                                        <div class="item-title f16 c-gray">{{$value['name']}}</div>
                                    </div>
                                </li>
                                @foreach($value['childs'] as $k => $v)
                                    @if(SELLER_TYPE_IS_ALL)
                                        <li class="item-content checks storeType1-{{$value['type']}} @if(in_array($v['id'], $current)) active @endif" data-id="{{$v['id']}}">
                                            <div class="item-inner ml10">
                                                <div class="item-title f16">{{$v['name']}}</div>
                                                <div class="item-after c-black"><i class="icon iconfont c-red f24">&#xe610;</i></div>
                                            </div>
                                        </li>
                                    @else
                                        <li class="item-content checks storeType1-{{$value['type']}} @if($v['id'] == $current) active @endif" data-id="{{$v['id']}}">
                                            <div class="item-inner ml10">
                                                <div class="item-title f16">{{$v['name']}}</div>
                                                <div class="item-after c-black"><i class="icon iconfont c-red f24">&#xe610;</i></div>
                                            </div>
                                        </li>
                                    @endif
                                @endforeach
                            @endif
                        @endif
                    @else
                        @if($storeType == 1 &&  $value['type'] == 1)
                            @if(empty($value['childs']))
                                <li class="item-content confirm_btn checks storeType1-{{$value['type']}} @if($flag) active @endif" data-id="{{$value['id']}}">
                                    <div class="item-inner">
                                        <div class="item-title f16 c-gray">{{$value['name']}}</div>
                                        <div class="item-after c-black"><i class="icon iconfont c-red f24">&#xe610;</i></div>
                                    </div>
                                </li>
                            @else
                                <li class="item-content y-jylxtitle storeType1-{{$value['type']}}">
                                    <div class="item-inner">
                                        <div class="item-title f16 c-gray">{{$value['name']}}</div>
                                    </div>
                                </li>
                                @foreach($value['childs'] as $k => $v)
                                    @if(SELLER_TYPE_IS_ALL)
                                        <li class="item-content confirm_btn checks storeType1-{{$value['type']}} @if(in_array($v['id'], $current)) active @endif" data-id="{{$v['id']}}">
                                            <div class="item-inner ml10">
                                                <div class="item-title f16">{{$v['name']}}</div>
                                                <div class="item-after c-black"><i class="icon iconfont c-red f24">&#xe610;</i></div>
                                            </div>
                                        </li>
                                    @else
                                        <li class="item-content confirm_btn checks storeType1-{{$value['type']}} @if($v['id'] == $current) active @endif" data-id="{{$v['id']}}">
                                            <div class="item-inner ml10">
                                                <div class="item-title f16">{{$v['name']}}</div>
                                                <div class="item-after c-black"><i class="icon iconfont c-red f24">&#xe610;</i></div>
                                            </div>
                                        </li>
                                    @endif
                                @endforeach
                            @endif
                        @endif

                        @if($storeType == 0)
                            @if(empty($value['childs']))
                                <li class="item-content checks confirm_btn storeType1-{{$value['type']}} @if($flag) active @endif" data-id="{{$value['id']}}">
                                    <div class="item-inner">
                                        <div class="item-title f16 c-gray">{{$value['name']}}</div>
                                        <div class="item-after c-black"><i class="icon iconfont c-red f24">&#xe610;</i></div>
                                    </div>
                                </li>
                            @else
                                <li class="item-content confirm_btn y-jylxtitle storeType1-{{$value['type']}}">
                                    <div class="item-inner">
                                        <div class="item-title f16 c-gray">{{$value['name']}}</div>
                                    </div>
                                </li>
                                @foreach($value['childs'] as $k => $v)
                                    <li class="item-content confirm_btn checks storeType1-{{$value['type']}} @if(in_array($v['id'], $current)) active @endif" data-id="{{$v['id']}}">
                                        <div class="item-inner ml10">
                                            <div class="item-title f16">{{$v['name']}}</div>
                                            <div class="item-after c-black"><i class="icon iconfont c-red f24">&#xe610;</i></div>
                                        </div>
                                    </li>
                                @endforeach
                            @endif
                        @endif

                    @endif
                @endforeach
            </ul>
        </div>
    </div>
@stop
@section($js)
    <script type="text/javascript">
        $(document).off("touchend",".confirm_btn");
        $(document).on("touchend",".confirm_btn",function(){
            var obj = new Object();
            @if(SELLER_TYPE_IS_ALL)
                var arr = new Array();
                $(".cate_item .active").each(function(){
                    arr.push($(this).data('id'))
                })
            @else
                $(".checks").removeClass("confirm_btn");
                $(".checks").removeClass("active");
                $(this).addClass("active");
                arr = $(this).data('id');
            @endif
            obj.cateIds = arr;
            $.post("{{u('Seller/saveCate')}}", obj, function(res){
                if(res.status == false){
                    $(".checks").addClass("confirm_btn");
                    $.alert(res.msg);
                } else {
                    $.router.load("{{u('Seller/reg',['isdata'=>0])}}", true);
                }
            }, 'json');
        });
        @if(!$isData)
        $(document).off("click", ".y-syt ul li");
        $(document).on("click", ".y-syt ul li", function(){
            if($(this).hasClass("checks")){
                if($(this).hasClass("active")){
                    $(this).removeClass("active");
                }else{
                    // $(this).addClass("active").siblings().removeClass("active");
                    $(this).addClass("active");
                }
            }
        });
        @endif
    </script>
@stop
