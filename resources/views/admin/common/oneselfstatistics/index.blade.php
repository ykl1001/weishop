@extends('admin._layouts.base')
@section('css')
    <style>
        #checkListTable thead  .numOrder,#checkListTable thead  .priceOrder{
            cursor:pointer
        }
    </style>
@stop
@section('right_content')
    @yizan_begin
    <yz:list>
        <search url="{{ $url }}">
            <row>
                <item label="统计年份">
                    <yz:select name="year" css="year_choose" options="$orderyear" textfield="yearName" valuefield="yearName"  selected="$args['year']"></yz:select>
                </item>
                <item label="月份">
                    <yz:select name="month" css="month_choose" options="1,2,3,4,5,6,7,8,9,10,11,12" texts="1月,2月,3月,4月,5月,6月,7月,8月,9月,10月,11月,12月" selected="$args['month']"></yz:select>
                </item>
                <item label="分类">
                    <yz:select name="cateId" css="cateId" options="$cate" textfield="name" valuefield="id"  selected="$args['cateId']"></yz:select>
                </item>
                <btn type="search"></btn>
                <linkbtn label="导出当前页到EXCEL" type="export" url="{{ u('OneselfStatistics/export'),$args }}"></linkbtn>
            </row>
        </search>
        <table>
            <columns>
                <column code="goodsName" label="商品\服务名称">
                    {{$list_item['goodsName']}}
                    @if($list_item['goodsNorms'])
                        ({{$list_item['goodsNorms']}})
                    @endif
                </column>
                <column code="cate" label="分类">
                    {{$list_item['categoods']['cate']['name']}}
                </column>
                <column code="num" label="销售↑↓" css="numOrder data{{$args['numOrder'] or 1}}"></column>
                <column code="totleprice" label="销量额↑↓"  css="priceOrder data{{$args['priceOrder'] or 1}}"></column>
            </columns>
        </table>
    </yz:list>
    @yizan_end
@stop
@section('js')
    <script>
        $(document).on('click','#checkListTable thead .numOrder',function(){
            var numOrder = 1;
            if($("#checkListTable thead .numOrder").hasClass("data1")){
                numOrder = 2;
            }else{
                numOrder = 1;
            }
            location.href = "{!!u('OneselfStatistics/index')!!}?year="+"{{$args['year']}}"+"&month="+"{{$args['month']}}"+"&cateId="+"{{$args['cateId']}}"+"&numOrder="+numOrder;
        });

        $(document).on('click','#checkListTable thead .priceOrder',function(){
            var priceOrder = 1;
            if($("#checkListTable thead .priceOrder").hasClass("data1")){
                priceOrder = 2;
            }else{
                priceOrder = 1;
            }
            location.href = "{!!u('OneselfStatistics/index')!!}?year="+"{{$args['year']}}"+"&month="+"{{$args['month']}}"+"&cateId="+"{{$args['cateId']}}"+"&priceOrder="+priceOrder;
        });
    </script>
@stop

{{--<column code="totleprice" label="单价">--}}
{{--{{$list_item['price']}}--}}
{{--</column>--}}