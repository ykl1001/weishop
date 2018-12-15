@extends('admin._layouts.base')
@section('right_content')
    @yizan_begin
    <yz:form id="yz_form" action="save">
        <yz:fitem name="name" label="商品标题"></yz:fitem>
        <yz:fitem name="stock" label="库存" val="0"></yz:fitem>
        <yz:fitem name="exchangeIntegral" label="积分"></yz:fitem>
        <yz:fitem label="商品图片">
            <yz:imageList name="images." images="$data['images']"></yz:imageList>
            <div><small class='cred pl10 gray'>建议尺寸：640px*540px，支持JPG/PNG格式</small></div>
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
    @yizan_end
@stop
@section('js')
    <script type="text/javascript">
    </script>
@stop
