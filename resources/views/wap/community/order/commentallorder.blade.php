<div class="c-bgfff pt15 pb15">
    <div class="y-ddbh f12">订单编号：{{$order['sn']}}</div>
</div>
@foreach($order['cartSellers'] as $vo)
<div class="commentlist" data-id="{{$vo['id']}}" id="commentlist-{{$vo['id']}}" data-orderid="{{$vo['goodsId']}}">
    <div class="list-block media-list y-qrdd">
        <ul>
            <li>
              <a href="{{ u('Order/detail',['id' => Input::get('orderId')]) }}" class="item-link item-content" data-no-cache="true">
                <div class="item-media"><img src="{{ $vo['goodsImages'] }}" width="50"></div>
                <div class="item-inner">
                  <div class="item-title-row f14">
                    <div class="item-text">{{$vo['goodsName']}}</div>
                  </div>
                </div>
              </a>
            </li>
        </ul>
    </div>
    <div class="content-block-title x-pjstar">
        <span class="f14 c-black mr5">商品评分</span>
        <div class="y-starcont c-red" id="star-{{$vo['id']}}">
            <div class="y-star">
                <i class="icon iconfont vat mr10 f18">&#xe653;</i>
                <i class="icon iconfont vat mr10 f18">&#xe653;</i>
                <i class="icon iconfont vat mr10 f18">&#xe653;</i>
                <i class="icon iconfont vat mr10 f18">&#xe653;</i>
                <i class="icon iconfont vat mr10 f18">&#xe653;</i>
            </div>
            <div class="y-startwo">
                <i class="icon iconfont vat mr10 f18">&#xe654;</i>
                <i class="icon iconfont vat mr10 f18">&#xe654;</i>
                <i class="icon iconfont vat mr10 f18">&#xe654;</i>
                <i class="icon iconfont vat mr10 f18">&#xe654;</i>
                <i class="icon iconfont vat mr10 f18">&#xe654;</i>
            </div>
        </div>
    </div>
    <div class="c-bgfff p10">
        <textarea class="x-pjtxt f14 contentval" placeholder="您的意见很重要！来点评一下吧..."></textarea>
    </div>
    <div class="card x-postdelst x-pjpic m0">
        <div class="card-content">
            <div class="card-content-inner oh">
                <ul class="x-postpic clearfix" id="img-{{$vo['id']}}">
                    @for($i=1; $i<=4; $i++)
                        <form>
                            <label id="imglabel-{{$i}}" class="img-up-lb" for="image-form-{{$i}}">
                                <li id="li_{{$i}}">
                                    <img data-num="{{$i}}" data-id="{{$vo['id']}}" class="image_upload" src="{{ asset('wap/community/newclient/images/addpic.png') }}">
                                    <i class="delete tc none" data-id="{{$i}}"><i class="icon iconfont f20">&#xe605;</i></i>
                                </li>
                            </label>
                            <input type="text" name="images" id="upimage_{{$vo['id']}}_{{$i}}" style="display:none"> 
                        </form>
                    @endfor
                </ul>
            </div>
        </div>
    </div>
</div>
@endforeach
<div class="content-block-title x-pjstar" id="commentlist-0">
    <span class="f14 c-black mr5">店铺评分</span>
    <div class="y-starcont c-red" id="star-0">
        <div class="y-star">
            <i class="icon iconfont vat mr10 f18">&#xe653;</i>
            <i class="icon iconfont vat mr10 f18">&#xe653;</i>
            <i class="icon iconfont vat mr10 f18">&#xe653;</i>
            <i class="icon iconfont vat mr10 f18">&#xe653;</i>
            <i class="icon iconfont vat mr10 f18">&#xe653;</i>
        </div>
        <div class="y-startwo">
            <i class="icon iconfont vat mr10 f18">&#xe654;</i>
            <i class="icon iconfont vat mr10 f18">&#xe654;</i>
            <i class="icon iconfont vat mr10 f18">&#xe654;</i>
            <i class="icon iconfont vat mr10 f18">&#xe654;</i>
            <i class="icon iconfont vat mr10 f18">&#xe654;</i>
        </div>
    </div>
</div>
<div class="tr x-pjna pr10">
    <span class="mr5">匿名评价</span>
    <i class="icon iconfont c-red f17 on">&#xe612;</i>
</div>

@section($js)

<script type="text/javascript">
    $(document).on('click','.image_upload', function () {
        var thisObj = $(this); 
        var id = thisObj.data('id');

        $(this).fanweImage({
            width:320, 
            height:320, 
            callback:function(url, target) {
                thisObj.get(0).src = url;
                $("ul#img-"+id+" #upimage_"+id+"_"+thisObj.data('num')).val(url); 
                $("ul#img-"+id+" #li_"+thisObj.data('num')+" .delete").removeClass("none");
            }
        });
    });  
    
</script>

<script type="text/javascript">
    $(function() {
        var star = 0;
        var isAno = 1;
        var orderId = "{{ Input::get('orderId') }}";

        //评价星级选择
        $(document).on("uploadsucc",".x-pjpic form",function(){
            $("#" + this.id + "-li .delete").removeClass('none');
        });
        $(document).off("touchend",".x-pjpic");
        $(document).on("touchend",".x-pjna",function(){
            if($(this).children("i").hasClass("on")){
                isAno = 0;
                $(this).children("i").removeClass("on");
            }else{
                isAno = 1;
                $(this).children("i").addClass("on");
            }
        });
        //评价页面照片删除
        $(document).off("touchend",".x-pjpic .delete");
        $(document).on("touchend",".x-pjpic .delete",function(){
            var id = $(this).parents("li").find("img").data("id");

            $(this).parents("li").find("img").attr("src", "{{asset('wap/community/client/images/addpic.png')}}");
            $(this).addClass("none");

            $("#upimage_"+id+"_"+$(this).data('id')).val("");
            return false;
        });
        $(document).off("touchend","#submit");

        var is_post = 0;
        $(document).on("touchend","#submit",function(){
            if(is_post == 1){
                return false;
            }
            $("#content").blur();

            var comment = {};
            //循环评价列表
            $(".commentlist").each(function(k, v){
                var d = {};
                var images = {};
                var thisId = $(this).data('id');

                d.id = $(this).data('orderid');   //商品编号
                d.star = $("#commentlist-"+thisId+" .y-startwo").css("width").replace('%','') / 20;  //星级
                d.content = $("#commentlist-"+thisId+" .contentval").val();  //商品评价

                $("#commentlist-"+thisId+" input[name=images]").each(function(index,val){
                    if($(this).val() != "" ){
                        images[index]= $(this).val();
                    }
                })
                d.images = images;   //评价图片

                comment[k] = d;
            })

            var shopStar = $("#commentlist-0 .y-startwo").css("width").replace('%','') / 20;  //店铺星级;

            var data = {
                isAno: isAno,
                shopStar: shopStar,     // 
                orderId : orderId,      // 订单编号
                isAll : 1,              // 是否是全国店评价
                comment : comment,
            };

            is_post = 1;
            $.post("{{ u('Order/docomment') }}", data, function(res){
                if(res.code == 0) {
                    $.toast(res.msg);
					$.router.load("{!! u('Order/detail',['id'=>Input::get('orderId'),'pid'=>$tid]) !!}", true);
                }else if(res.code == '99996'){
                    $.router.load("{{ u('User/login') }}", true);
                }else{
                    is_post = 0;
                    $.toast(res.msg);
                }
            },"json");
            return false;
        })

        // 评价
        $(document).off("touchend",".x-pjstar .y-star i, .x-pjstar .y-startwo i");
        $(document).on("touchend",".x-pjstar .y-star i, .x-pjstar .y-startwo i",function(){
            var arri = $(this).parent().children();
            var index = $(this).parent().children().index(this);
            var redstar_w = (index+1) / 5 * 100;
            var id = $(this).parent().parent().attr('id');

            star = parseInt(index)+1;
            $(".x-pjstar div#"+id+" .y-star i").removeClass("on");
            $(".x-pjstar div#"+id+" .y-startwo").css("width", "0");
            $(".x-pjstar div#"+id+" .y-startwo").css("width", redstar_w + "%");
            for (i = 0; i < arri.length; i++){
                arri[i].className = i < index+1 ? "icon iconfont vat mr10 f18 on" : "icon iconfont vat mr10 f18";
            }
        });
    })
</script>
@stop