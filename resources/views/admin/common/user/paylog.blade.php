@extends('admin._layouts.base')
@section('css')
	<style>.spl_c_0{color:red;}.spl_c_1{color:green;}</style>
@stop
@section('right_content')
    @yizan_begin
	<yz:list>
        <search url="{{ $url }}">
            <row>
                <item name="beginTime" label="开始时间" type="date"></item>
                <item name="endTime" label="结束时间" type="date"></item>
                <btn type="search"></btn>
            </row>
        </search>
        <btns>
            <linkbtn label="导出到EXCEL" type="export">
                <attrs>
                    <url>{{ u('User/paylogExport', $args ) }}</url>
                </attrs>
            </linkbtn>
        </btns>
        <yz:tabs>
            <navs>
                <nav name="status" label="金额明细">
                    <attrs>
                        <url>{{ u('User/paylog',['nav'=>1,'userId'=>$args['userId']]) }}</url>
                        <css>@if( $nav == 1) on @endif</css>
                    </attrs>
                </nav>
                <nav name="status" label="积分明细">
                    <attrs>
                        <url>{{ u('User/paylog',['nav'=>2,'userId'=>$args['userId']]) }}</url>
                        <css>@if( $nav == 2 ) on @endif</css>
                    </attrs>
                </nav>
            </navs>
        </yz:tabs>
		<table>
			<columns>  
				<column label="编号" align="center" width="160">
					{{ $list_item['id'] }}
				</column>
                @if($nav == 1)
                    <column label="金额"  align="center" width="60">
                        {{ $list_item['money'] }}
                    </column>
                    <column label="描述" align="left" width="150">
                        {{ $list_item['content'] }}
                    </column>
                    <column label="创建时间" align="left" width="120">
                        {{  yzTime($list_item['createTime']) }}
                    </column>
                    <column label="支付时间" align="left" width="120">
                        {{  yzTime($list_item['payTime']) }}
                    </column>
                @else
                    <column label="积分"  align="center" width="60">
                        {{ $list_item['integral'] }}
                    </column>
                    <column label="描述" align="left" width="150">
                        {{ $list_item['remark'] }}
                    </column>
                    <column label="创建时间" align="left" width="120">
                        {{  yzTime($list_item['createTime']) }}
                    </column>
                    <column label="消费金额" align="left" width="120">
                        {{  yzTime($list_item['money']) }}
                    </column>
                @endif
			</columns>
		</table>
	</yz:list>
	@yizan_end
@stop

@section('js')

@stop
