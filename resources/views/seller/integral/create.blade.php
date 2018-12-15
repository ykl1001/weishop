@extends('seller._layouts.base')
@section('content')
    @yizan_begin
    <div>
        <div class="m-zjgltbg">
            <div class="p10">
                <!-- 订单管理 -->
                <div class="g-fwgl">
                    <p class="f-bhtt f14 clearfix" style="border-bottom:0;">
                        <span class="ml15 fl">积分商城</span>
                    </p>
                </div>
                <yz:form id="yz_form" action="save">
                    <yz:fitem name="name" label="商品标题"></yz:fitem>
                    <yz:fitem name="stock" label="库存" val="0"></yz:fitem>
                    <yz:fitem name="exchangeIntegral" label="积分"></yz:fitem>
                    <yz:fitem label="商品图片">
                        <yz:imageList name="images." images="$data['images']"></yz:imageList>
                    </yz:fitem>
                    <yz:fitem name="brief" label="商品描述">
                        <yz:Editor name="brief" value="{{ $data['brief'] }}"></yz:Editor>
                    </yz:fitem>
                    <yz:fitem label="是否配送">
                        <php> $isVirtual = (int)$data['isVirtual'] </php>
                        <yz:radio name="isVirtual" options="0,1" texts="否,是" checked="$isVirtual"></yz:radio>
                    </yz:fitem>
                    <yz:fitem label="商品状态">
                        <php> $status = (int)$data['status'] </php>
                        <yz:radio name="status" options="0,1" texts="下架,上架" checked="$status"></yz:radio>
                    </yz:fitem>
                    <yz:fitem name="sort" label="排序"></yz:fitem>
                </yz:form>
            </div>
        </div>
    </div>
    @yizan_end
@stop
@section('js')
    <script type="text/javascript">
    </script>
@stop
