@extends('admin._layouts.base')
@section('css')
<style type="text/css">
	/*a  upload */
	.a-upload {
		padding: 4px 10px;
		height: 20px;
		line-height: 20px;
		position: relative;
		cursor: pointer;
		color: #888;
		background: #fafafa;
		border: 1px solid #ddd;
		border-radius: 4px;
		overflow: hidden;
		display: inline-block;
		*display: inline;
		*zoom: 1
	}

	.a-upload  input {
		position: absolute;
		font-size: 100px;
		right: 0;
		top: 0;
		opacity: 0;
		filter: alpha(opacity=0);
		cursor: pointer
	}

	.a-upload:hover {
		color: #444;
		background: #eee;
		border-color: #ccc;
		text-decoration: none
	}
	fieldset {
            border: solid 1px #999;
            border-radius: 4px;
            width: 100%;
            font-size:14px;
        }

        fieldset legend {
            padding: 0 5px;
            width:auto;
            border:none;
            margin:0;
            font-size:14px;
        }

        fieldset div.actions {
            width: 96%;
            margin: 5px 10px;
        }

        fieldset div label{display:inline-block; margin-right:15px;}

        .blank15 {
            height: 15px;
            line-height: 10px;
            clear: both;
            visibility: hidden;
        }
        .actions label{margin-right:10px!important;}
        .actions span{ font-size:12px;}

        .my_fieldset{width: 100%;}
</style>

@stop
@section('right_content')
	@yizan_begin
    <yz:form id="yz_form" action="save" method="post" file="1" noajax="0" nobtn="1">
		<div class="formdiv" style="width:100%;">
			<fieldset class="fieldset-1">
				<legend class="checked_all">
					<label><span>版本检测</span></label>
				</legend>
				<div class="actions">
					<div class="blank15"></div>
					<fieldset class="my_fieldset fieldset-2">
						<legend  class="checked_module">
							<label>
								当前版本
							</label>
						</legend>
						<div class="blank15"></div>
						<div class="actions fieldset-3">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>版本号</span>：{{$data['sysVersion']}}
						</div>
                        @if(!empty($data['filePath']))
                        <div class="actions fieldset-3" style="color: red;font-size:9px;">
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>注意</span>：SQL文件请勿随意运行以免程序错误!
                        </div>
                        @endif
						<div class="blank15"></div>
					</fieldset>
				</div>
				<div class="actions">
					<div class="blank15"></div>
					<fieldset class="my_fieldset fieldset-2">
						<legend  class="checked_module">
							<label>
                                @if(empty($data['filePath']))
								    上传SQL
                                @else
                                    SQL文件
                                @endif
							</label>
						</legend>
						<div class="blank15"></div>
                        @if(empty($data['filePath']))
                            <div class="actions fieldset-3">
                                <yz:fitem label="上传SQL文件" type="file" name="zipFile" accept=".sql"></yz:fitem>
                            </div>
                        @else
                            <div class="actions fieldset-3 fl" style="width:70%">
                                <yz:fitem label="SQL文件" name="zipName" type="text"></yz:fitem>
                            </div>
                            @if(!$upzip)
                                @if($data['showUpdate'])
                                    <div class="actions fieldset-3 tr" >
                                        <a href="#" class="btn btn-green" onclick="$.unzipProject()">执行SQL文件</a >
                                    </div>
                                @endif
                            @endif
                        @endif
						@if(!$data['showUpdate'] && !empty($data['filePath']))
							<div class="actions fieldset-3 tr" >
								<a href="{{u('Project/index',['index'=>'updateSave'])}}" class="btn btn-green" onclick="">上传SQL文件</a >
							</div>
						@endif
					</fieldset>
					<div class="blank15"></div>
				</div>
			</fieldset>
            <div class="tc list-btns @if(!empty($data['filePath']))none @endif">
                <button type="submit" class="u-addspbtn  btn-green" style="width: 10%">提交</button>
            </div>
		</div>
	</yz:form>
	@yizan_end
@stop

@section('js')
    <script>
        $.unzip = function (){
            $.zydialogs.open("<p style='margin: 30px'>正在解压补丁文件···</p>",{
                width:300,
                title:"解压",
                showButton:false,
                showClose:false,
                showLoading:true
            }).setLoading();
            $.post("{{u('Project/unzip')}}",{v:"{{ str_replace('.sql' ,'',str_replace('update_' ,'',$data['zipName'])) }}"},function(res){
                if(res.status == -1){
                    $.zydialogs.close();
                    $.ShowAlert("当前版本与新版本冲突,无法执行");
                    location.href = "{{ u('Project/index') }}";
                }else{
                    $("time").html("&nbsp;&nbsp;&nbsp;&nbsp;解压耗时："+res);
                    $(".unzip").remove();
                    location.href = "{{ u('Project/index') }}?time="+res;
                }
            },"json");
        };
        $.unzipProjectbak  = function (){
            $.post("{{u('Project/unzipProject')}}",{v:"{{ str_replace('.zip' ,'',str_replace('patch_update-' ,'',$data['zipName'])) }}"},function(res){
                if(res.status == -1){
                    $.ShowAlert("当前版本与新版本冲突,无法执行");
                    location.href = "{{ u('Project/index') }}";
                }
                if(res.status == 2){
                    $.zydialogs.open("<p style='margin: 30px'>正在执行···</p>",{
                        width:300,
                        title:"备份",
                        showButton:false,
                        showClose:false,
                        showLoading:true
                    }).setLoading();
                    $.post("{{u('Project/zipProjectbak')}}",{v:"{{ str_replace('.zip' ,'',str_replace('patch_update-' ,'',$data['zipName'])) }}"},function(res){
                        if(res.status == 3){
                            $(".zydialog_head .zydialog_title").html("正在执行SQL...");
                            $(".zydialog_mc p").html("正在进行升级请耐心等待！");
                            $.post("{{u('Project/updatezip')}}",{v:"{{ str_replace('.zip' ,'',str_replace('patch_update-' ,'',$data['zipName'])) }}"},function(res){
                                if(res.status == -1){
                                    $.zydialogs.close();
                                    $.ShowAlert("当前版本与新版本冲突,无法执行");
                                }else{
                                    if(res.status == 0){
                                        $.zydialogs.close();
                                        $.ShowAlert("升级失败!");
										location.href = "{{ u('Project/index') }}";
                                    }else if(res.status == 3){
                                        $.zydialogs.close();
                                        $.ShowAlert("SQL数据更新失败!");
                                        location.href = "{{ u('Project/index') }}";
									}else if(res.status == 2){
                                        $.zydialogs.close();
                                        $.ShowAlert("SQL数据已更新过啦!");
                                        location.href = "{{ u('Project/index') }}";
									}
									else{
                                        $.zydialogs.close();
                                        $.ShowAlert("升级成功!");
                                        location.href = "{{ u('Project/index') }}";
                                    }
                                }
                            },"json");
                        }
                    },"json");
                }
            },"json");
        };


        $.unzipProject  = function (){
            $.zydialogs.open("<p style='margin: 30px'>正在进行升级请耐心等待···</p>",{
                width:300,
                title:"正在执行SQL...",
                showButton:false,
                showClose:false,
                showLoading:true
            }).setLoading();

            $.post("{{u('Project/updatezip')}}",{v:"{{ str_replace('.sql' ,'',str_replace('update_' ,'',$data['zipName'])) }}"},function(res){
                if(res.status == -1){
                    $.zydialogs.close();
                    $.ShowAlert("版本冲突,执行SQL失败!");
                }else{
                    if(res.status == 0){
                        $.zydialogs.close();
                        $.ShowAlert("升级失败!");
                        location.href = "{{ u('Project/index') }}";
                    }else if(res.status == 3){
                        $.zydialogs.close();
                        $.ShowAlert("SQL数据更新失败!");
                        location.href = "{{ u('Project/index') }}";
                    }else if(res.status == 1){
                        $.zydialogs.close();
                        $.ShowAlert("未获取到SQL文件!");
                        location.href = "{{ u('Project/index') }}";
                    }else if(res.status == 2){
                        $.zydialogs.close();
                        $.ShowAlert("SQL数据已更新过啦!");
                        location.href = "{{ u('Project/index') }}";
                    }
                    else{
                        $.zydialogs.close();
                        $.ShowAlert("升级成功!");
                        location.href = "{{ u('Project/index') }}";
                    }
                }
            },"json");
        };
    </script>
@stop
