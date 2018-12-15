@yizan_include('admin.common.serviceorder.index')
@yizan_section('navs')
<navs>
    <nav label="全部订单">
        <attrs>
            <url>{{ u('OneselfOrderService/index',['nav'=>'nav1']) }}</url>
            <css>{{$nav1}}</css>
        </attrs>
    </nav>
    <nav label="待发货订单">
        <attrs>
            <url>{{ u('OneselfOrderService/index',['status'=>'1','nav'=>'nav2']) }}</url>
            <css>{{$nav2}}</css>
        </attrs>
    </nav>
    <nav label="待完成订单">
        <attrs>
            <url>{{ u('OneselfOrderService/index',['status'=>'2','nav'=>'nav3']) }}</url>
            <css>{{$nav3}}</css>
        </attrs>
    </nav>
    <nav label="已取消订单">
        <attrs>
            <url>{{ u('OneselfOrderService/index',['status'=>'4','nav'=>'nav5']) }}</url>
            <css>{{$nav5}}</css>
        </attrs>
    </nav>
    <nav label="已完成订单">
        <attrs>
            <url>{{ u('OneselfOrderService/index',['status'=>'3','nav'=>'nav4']) }}</url>
            <css>{{$nav4}}</css>
        </attrs>
    </nav>
</navs>
@yizan_stop
@yizan_section('searchUrl')
	<search>
@yizan_stop
@yizan_section('select')
<action label="查看" css="blu">
    <attrs>
        <url>{{ u('OneselfOrderService/detail', ['id'=>$list_item['id']]) }}</url>
    </attrs>
</action>
@yizan_stop