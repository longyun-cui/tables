@extends('home.layout.layout')

@section('title')
    @if(empty($encode_id)) 添加表格 @else 编辑表格 @endif
@endsection

@section('header')
    @if(empty($encode_id)) 添加表格 @else 编辑表格 @endif
@endsection

@section('description')
    @if(empty($encode_id)) 添加表格 @else 编辑表格 @endif
@endsection

@section('breadcrumb')
    <li><a href="{{url('/home')}}"><i class="fa fa-dashboard"></i>我的主页</a></li>
    <li><a href="#"><i class="fa "></i>Here</a></li>
@endsection


@section('content')
<div id="marking" style="display:none;">
    <input type="hidden" id="table_id" value="{{$encode_id or encode(0)}}" readonly>
</div>



<div class="row">
    <div class="col-md-12">
        <!-- BEGIN PORTLET-->
        <div class="box box-info">

            <div class="box-header with-border" style="margin:16px 0;">
                <h3 class="box-title"></h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse">
                        <i class="fa fa-minus"></i></button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="" data-original-title="Remove">
                        <i class="fa fa-times"></i></button>
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered" id="form-edit-table">
            <div class="box-body">

                {{csrf_field()}}
                <input type="hidden" name="operate" value="{{$operate_id or ''}}" readonly>
                <input type="hidden" name="id" value="{{$encode_id or encode(0)}}" readonly>

                {{--名称--}}
                <div class="form-group">
                    <label class="control-label col-md-2">表格后台名称 <span class="text-red">*</span></label>
                    <div class="col-md-8 ">
                        <div><input type="text" class="form-control" name="name" placeholder="请输入名称" value="{{$data->name or ''}}"></div>
                    </div>
                </div>
                {{--标题--}}
                <div class="form-group">
                    <label class="control-label col-md-2">标题 <span class="text-red">*</span></label>
                    <div class="col-md-8 ">
                        <div><input type="text" class="form-control" name="title" placeholder="请输入标题" value="{{$data->title or ''}}"></div>
                    </div>
                </div>
                {{--说明--}}
                <div class="form-group">
                    <label class="control-label col-md-2">描述</label>
                    <div class="col-md-8 ">
                        <div><input type="text" class="form-control" name="description" placeholder="描述" value="{{$data->description or ''}}"></div>
                    </div>
                </div>
                {{--cover 封面图片--}}
                @if(!empty($data->cover_pic))
                    <div class="form-group">
                        <label class="control-label col-md-2">封面图片</label>
                        <div class="col-md-8 ">
                            <div class="edit-img"><img src="{{url('http://cdn.'.$_SERVER['HTTP_HOST'].'/'.$data->cover_pic.'?'.rand(0,999))}}" alt=""></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2">更换封面图片</label>
                        <div class="col-md-8 ">
                            <div><input type="file" name="cover" placeholder="请上传封面图片"></div>
                        </div>
                    </div>
                @else
                    <div class="form-group">
                        <label class="control-label col-md-2">上传封面图片</label>
                        <div class="col-md-8 ">
                            <div><input type="file" name="cover" placeholder="请上传封面图片"></div>
                        </div>
                    </div>
                @endif

            </div>
            </form>

            <div class="box-footer">
                <div class="row" style="margin:16px 0;">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-primary" id="edit-table-submit"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" onclick="history.go(-1);" class="btn btn-default">返回</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PORTLET-->
    </div>
</div>



<div class="row">
    <div class="col-md-12">
        <!-- BEGIN PORTLET-->
        <div class="box box-warning">

            <div class="box-header with-border" style="margin:16px 0;">
                <h3 class="box-title">列管理</h3>
                <div class="box-tools pull-right">
                    <a href="{{ url('home/table/data?id='.$encode_id) }}">
                        <button type="button" onclick="" class="btn btn-success pull-right"><i class="fa fa-edit"></i> 数据管理</button></a>
                </div>
                <input type="hidden" id="column-marking" data-key="1000">
            </div>

            <form action="" method="post" class="form-horizontal form-bordered" id="form-edit-column">
                <div class="box-body">
                    {{csrf_field()}}
                    <input type="hidden" name="operate" value="{{$operate or ''}}" readonly>
                    <input type="hidden" name="table_id" value="{{$encode_id or encode(0)}}" readonly>
                    <div class="box-header with-border column-container">
                        {{--操作--}}
                        <div class="form-group">
                            <label class="control-label col-md-2">操作</label>
                            <div class="col-md-8">
                                <button type="button" class="btn btn-sm btn-success show-create-column">添加列</button>
                                <button type="button" class="btn btn-sm btn-danger delete-all-column">删除全部</button>
                            </div>
                        </div>
                    </div>

                    <div id="sortable">
                        @foreach($data->columns as $v)
                        <div class="box-body column-container column-option sort-option" data-id="{{encode($v->id)}}">
                            {{--信息--}}
                            <div class="form-group">
                                <input type="hidden" name="column[{{$v->id}}][id]" value="{{$v->id or ''}}">
                                <label class="control-label col-md-2 col-md-offset-0">第 ({{$loop->index+1}}) 列</label>
                            </div>
                            {{--标题--}}
                            <div class="form-group">
                                {{--<div class="row">--}}
                                    {{--<label class="col-md-2 control-label">列后台名称</label>--}}
                                    {{--<div class="col-md-8"><input type="text" readonly class="form-control" name="name" value="{{$v->name or ''}}"></div>--}}
                                {{--</div>--}}
                                <div class="row">
                                    <label class="col-md-2 control-label">列标题</label>
                                    <div class="col-md-8"><input type="text" readonly class="form-control" name="title" value="{{$v->title or ''}}"></div>
                                </div>
                                <div class="row">
                                    <label class="col-md-2 control-label">列描述</label>
                                    <div class="col-md-8"><input type="text" readonly class="form-control" name="description" value="{{$v->description or ''}}"></div>
                                </div>
                                <div class="row">
                                    <label class="col-md-2 control-label">列默认值</label>
                                    <div class="col-md-8"><input type="text" readonly class="form-control" name="default" value="{{$v->default or ''}}"></div>
                                </div>
                            </div>
                            {{--操作--}}
                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-2">
                                    <button type="button" class="btn btn-sm btn-success edit-this-column">编辑</button>
                                    <button type="button" class="btn btn-sm btn-danger delete-this-column">删除</button>
                                    @if(false)
                                        <button type="button" class="btn btn-xs btn-success create-next-column">添加</button>
                                        <button type="button" class="btn btn-xs moveup-this-column">上移</button>
                                        <button type="button" class="btn btn-xs movedown-this-column">下移</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                </div>
            </form>

            <div class="box-footer">
                <div class="row" style="margin:16px 0;">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-primary" id="sort-column-submit"><i class="fa fa-check"></i> 保存排序</button>
                        <button type="button" onclick="history.go(-1);" class="btn btn-default">返回</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PORTLET-->
    </div>
</div>



<div class="modal fade" id="edit-modal">
    <div class="col-md-8 col-md-offset-2" id="edit-ctn" style="margin-top:64px;margin-bottom:64px;padding-top:32px;background:#fff;"></div>
</div>

{{--clone--}}
<div class="clone-container" style="display: none">

    {{--列--}}
    <div id="column-cloner">
        <div class="box-body column-container column-option">

            <form action="" method="post" class="form-horizontal form-bordered form-edit-column">
                {{csrf_field()}}
                <input type="hidden" readonly name="operate" value="create">
                <input type="hidden" readonly name="container" value="column">
                <input type="hidden" readonly name="table_id" value="{{$encode_id or encode(0)}}">
                <input type="hidden" readonly name="id" value="{{encode(0)}}">
                <input type="hidden" readonly name="type" value="1" class="column-type">

                <div class="form-group">
                    <label class="col-md-8 col-md-offset-2 question-title">添加列</label>
                </div>

                {{--<div class="form-group">--}}
                    {{--<label class="control-label col-md-2">后台名称 <span class="text-red">*</span></label>--}}

                    {{--<div class="col-md-8">--}}
                        {{--<div><input type="text" class="form-control" name="name" placeholder="请输入后台名称" required></div>--}}
                    {{--</div>--}}
                {{--</div>--}}

                <div class="form-group">
                    <label class="control-label col-md-2">列标题 <span class="text-red">*</span></label>
                    <div class="col-md-8">
                        <div><input type="text" class="form-control" name="title" placeholder="请输入备注"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-2">备注</label>
                    <div class="col-md-8">
                        <div><input type="text" class="form-control" name="description" placeholder="请输入备注"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-2">默认值</label>
                    <div class="col-md-8">
                        <div><input type="text" class="form-control" name="default" placeholder="请输入默认值"></div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-sm btn-primary create-this-column">保存</button>
                        <button type="button" class="btn btn-sm cansel-this-column">取消</button>
                    </div>
                </div>
            </form>

        </div>
    </div>

</div>

@endsection


@section('js')
<script>
    $(function() {

        // 编辑表格内容
        $("#edit-table-submit").on('click', function() {
            var options = {
                url: "/home/table/edit",
                type: "post",
                dataType: "json",
                // target: "#div2",
                success: function (data) {
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        layer.msg(data.msg);
                        location.href = "/home/table/list";
                    }
                }
            };
            $("#form-edit-table").ajaxSubmit(options);
        });

        // 显示添加列
        $(".show-create-column").on('click', function() {

            var html = $("#column-cloner .column-option").clone();
//        $('#question-container .question-container:last').after(html);
            $('#edit-ctn').html(html);
            $('#edit-modal').modal('show');
        });
        // 显示添加列
        $(".edit-this-column").on('click', function() {

            var column = $(this).parents('.column-option');
            var id = column.attr('data-id');
            var name = column.find('input[name=name]').val();
            var title = column.find('input[name=title]').val();
            var description = column.find('input[name=description]').val();
            var t_default = column.find('input[name=default]').val();

            var html = $("#column-cloner .column-option").clone();
            html.find('input[name=id]').val(id);
            html.find('input[name=name]').val(name);
            html.find('input[name=title]').val(title);
            html.find('input[name=description]').val(description);
            html.find('input[name=default]').val(t_default);

//        $('#question-container .question-container:last').after(html);
            $('#edit-ctn').html(html);
            $('#edit-modal').modal('show');
        });

        // 添加列
        $("#edit-modal").on('click', '.create-this-column', function() {
            var options = {
                url: "/home/column/edit",
                type: "post",
                dataType: "json",
                // target: "#div2",
                success: function (data) {
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        layer.msg(data.msg);
                        location.reload();
                    }
                }
            };
            $("#edit-ctn .form-edit-column").ajaxSubmit(options);
        });

        // 取消添加or编辑
        $("#edit-modal").on('click', '.cansel-this-column', function () {
            $('#edit-ctn').html('');
            $('#edit-modal').modal('hide');
        });

        // 删除列
        $(".delete-this-column").on('click', function() {

            var column = $(this).parents('.column-option');
            var column_id = column.attr('data-id');
            var table_id = $('#table_id').val();

            layer.msg('确定要删除该"列"么，同时也会删除相应数据？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "/home/column/delete",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            table_id:table_id,
                            column_id:column_id
                        },
                        function(data){
                            if(!data.success) layer.msg(data.msg);
                            else location.reload();
                        },
                        'json'
                    );
                }
            });
        });



        // 排序
        $("#sortable").sortable();

        // 修改排序
        $("#sort-column-submit").on('click', function() {
            var options = {
                url: "/home/column/sort",
                type: "post",
                dataType: "json",
                // target: "#div2",
                success: function (data) {
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        layer.msg(data.msg);
//                    location.href = "/admin/survey/list";
                    }
                }
            };
            $("#form-edit-column").ajaxSubmit(options);
        });

    });
</script>
@endsection
