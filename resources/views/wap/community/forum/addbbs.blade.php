@extends('wap.community._layouts.base')

@section('css')
<style type="text/css">
    .x-postmsg.list-block .item-inner {
        padding: 1.3rem 0;
    }
    .del{
        padding-right: 0.1rem;
    }
</style>
@stop

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left pageloading" href="@if(!empty($nav_back_url)) {!! $nav_back_url !!} @else {{u('Forum/index')}} @endif" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">发帖</h1>
        <a class="button button-link button-nav pull-right open-popup toedit submitbbs" data-popup=".popup-about">@if($data) 保存 @else 发布 @endif</a>
    </header>
@stop

@section('content')
    <div class="content" id=''>
        <!-- 填写内容 -->
        <div class="card x-posting">
            <div class="card-header f14">
                <div class="fl">发布到：</div>
                <div>@if($data){{$data['plate']['name']}}@else{{$plate['name']}}@endif</div>
                <input type="hidden" id="plateId" value="@if($data){{$data['plate']['id']}}@else{{$plate['id']}}@endif">
            </div>
            <div class="card-header f14">
                <div class="fl"><span class="mr15">标</span><span>题：</span></div>
                <input type="text" placeholder="请填写标题" id="title" value="@if($option['title'] && $option['postId'] == $data['id']){{$option['title']}}@else{{$data['title']}}@endif">
            </div>
            <div class="card-content f14">
                <div class="fl"><span class="mr15">内</span><span>容：</span></div>
                <div class="postr"><textarea placeholder="请填写内容" class="w100" id="content">@if($option['content'] && $option['postId'] == $data['id']){{$option['content']}}@else{{$data['content']}}@endif</textarea></div>
            </div>
        </div>
        <!-- 添加图片 -->
        <div class="card x-postdelst m0">
            <div class="card-content x-pjpic">
                <div class="card-content-inner oh">
                    <ul class="x-postpic clearfix">
                        @for($i = 1; $i <= 4; $i++)
                            <form>
                                <label for="image-form-{{$i}}">
                                <li  id="image-form-{{$i}}-li">
                                    <img  src="@if($data['images'][$i-1]){{formatImage($data['images'][$i-1],71,71)}}@elseif($option['images'][$i-1]){{formatImage($option['images'][$i-1],71,71)}} @else{{asset('wap/community/client/images/addpic.png')}}@endif" id="img{{$i}}" data-num="{{$i}}" class="upimage">
                                    <i class="delete showdelete{{$i}} @if(!$data['images'][$i-1]) none @endif" data-index="{{$i}}"><i class="icon iconfont f20">&#xe605;</i></i>
                                </li>
                                </label>
                                <input type="hidden" name="images" id="upimage_{{$i}}" value="@if($data['images'][$i-1]){{$data['images'][$i-1]}}@elseif($option['images'][$i-1]){{$option['images'][$i-1]}}@endif">
                            </form>
                        @endfor
                    </ul>
                </div>
            </div>
        </div>
        <!-- 联系方式 -->
        <div class="content-block-title c-black">联系方式（非必填）</div>
        @if(!$option['addressId'] && !$data['address']) 
            <div class="list-block media-list x-postmsg addmsg">
                <ul>
                    <li>
                        <a href="#" class="item-link item-content">
                            <div class="item-inner update">
                                <div class="item-subtitle f14">
                                    <i class="icon iconfont c-red f18 add mr10">&#xe61d;</i><span class="c-red">添加联系方式</span>
                                </div>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        @else
            <div class="list-block media-list x-postmsg addmsg none">
                <ul>
                    <li>
                        <a href="#" class="item-link item-content">
                            <div class="item-inner update">
                                <div class="item-subtitle f14">
                                    <i class="icon iconfont c-red f18 add mr10">&#xe61d;</i><span class="c-red">添加联系方式</span>
                                </div>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="list-block media-list x-postmsg delmsg">
                <ul>
                    <li>
                        <a href="#" class="item-link item-content">
                            <div class="item-subtitle">
                                <i class="icon iconfont c-red f18 delete del">&#xe620;</i>
                            </div>
                            <div class="item-inner f14 update">
                                <div class="item-title-row">
                                    <div class="item-title na" id="userName" style=" padding-bottom: 20px; margin-top: -12px;">@if($data['address']){{$data['address']['name']}}@else{{$address['name']}}@endif</div>
                                    <div class="item-after phone" id="userMobile" style=" padding-bottom: 20px; margin-top: -12px;">@if($data['address']){{$data['address']['mobile']}}@else{{$address['mobile']}}@endif</div>
                                </div>
                                <div class="item-text ha f14" id="userAddress" style="margin-bottom: -10px;">
                                    @if(empty($address)){{$data['address']['address']}}{{$data['address']['doorplate']}}@else{{$address['address']}}{{$address['doorplate']}}@endif
                                </div>
                            </div>
                            <div class="item-subtitle">
                                <i class="icon iconfont c-gray fr">&#xe602;</i>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
            <input type="hidden" id="addressId" value="@if(empty($address)){{$data['address']['id']}}@else{{$address['id']}}@endif">
        @endif
    </div>
@stop

@section($js)
    <!-- <script src="{{ asset('static/infinite-scroll/jquery.infinitescroll.js') }}"></script> --> 

    <script type="text/javascript">
        $(document).on('click','.upimage', function () {
            var thisObj = $(this); 
            $(this).fanweImage({
                width:320, 
                height:320, 
                callback:function(url, target) {
                    thisObj.get(0).src = url;
                    $("#upimage_"+thisObj.data('num')).val(url); 
                    $(".showdelete"+thisObj.data('num')).removeClass("none");
                }
            });
        });  
        //var BACK_URL = "@if(!empty($nav_back_url)) {!! $nav_back_url !!} @else {{u('Forum/index')}} @endif";
        var newAddressId = $("#addressId").val();
        if(newAddressId != ''){
            var add = new Object();
            add.addressId = newAddressId;
            $.post("{{ u('Forum/getaddress') }}",add,function(res){
                document.getElementById('addressId').value = res.id;
                document.getElementById('userName').innerHTML = res.name;
                document.getElementById('userMobile').innerHTML = res.mobile;
                document.getElementById('userAddress').innerHTML = res.address;
            }, "json");
        }

        // 列表
        function getData(){
            var obj = new Object();
            images = new Array();
            $("input[name=images]").each(function(index,val){
                if($(this).val() != "" ){
                    images.push($(this).val());
                }
            })
            obj.images = images;
            obj.plateId = "{{ $plate['id'] }}";
            obj.postId = "{{ $args['postId'] }}";
            obj.addressId = $("#addressId").val();
            obj.title = $("#title").val();
            obj.content = $("#content").val();
            return obj;
        }

        $(function() {
            $(document).on("touchend",".x-postmsg .update",function(){
                var data = getData();
                $.post("{{ u('Forum/savebbsData') }}",data,function(res){
                    $.router.load("{!! u('UserCenter/address',['plateId'=>$plate['id'], 'postId'=>(int)$args['postId']]) !!}", true);
                },"json");
            })
            $(document).on("touchend",".x-delico3",function(){
                $(".x-postmsg").unbind();
                var data = getData();
                data.addressId = '';
                $.post("{{ u('Forum/savebbsData') }}",data,function(res){
                    $.router.load("{!! u('Forum/addbbs',['plateId'=>$plate['id'],'postId'=>$args['postId']]) !!}", true);
                },"json");
            });
            
            //上传图片
            $(document).on("uploadsucc",".x-pjpic form",function(){
                $("#" + this.id + "-li .delete").removeClass('none');
            });
            //删除图片
            // $(document).on("touchend",".x-pjpic .delete",function(){
            // {
            //     $(this).parents("li").find("img").attr("src", "{{ asset('wap/community/newclient/images/addpic.png') }}");
            //     $(this).addClass("none");
            //     var index = $(this).data('index');
            //     $("#image-from-val-" + index).val("");
            // });
            $(document).off('touchend', '.submitbbs');
            $(document).on("touchend",".submitbbs",function(){
                    var data = getData();
                    $.post("{{ u('Forum/savebbs') }}",data,function(res){
                        if(res.code == 0){
                            var return_url = "{{ u('Forum/lists',['plateId'=>$plate['id']]) }}";
                            $.alert(res.msg, function(){
                                $.router.load(return_url, true);
                            });
                        }else{
                            $.alert(res.msg);
                        }
                    },"json");
            });

            // 删除图片
            $(document).on("touchend",".x-postpic .delete",function(){
                $(this).parents("li").find("img").attr("src", "{{asset('wap/community/client/images/addpic.png')}}");
                $(this).addClass("none");
                $(this).parents("li").find("input").val("");
                return false;
            });
            // 编辑用户信息
            $(document).on("touchend",".x-postmsg .del",function(){
                $(".delmsg").addClass("none");
                $(".addmsg").removeClass("none");
                $.post("{{ u('Forum/deleteAddressId') }}", function(res){

                });
                document.getElementById('addressId').value = '';
            });
        });
    </script>
@stop