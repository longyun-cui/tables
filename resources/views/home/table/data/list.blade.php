@extends('home.layout.layout')

@section('title','表格数据')
@section('header','表格数据')
@section('description','表格数据')
@section('breadcrumb')
    <li><a href="{{url('/home')}}"><i class="fa fa-home"></i>首页</a></li>
    <li><a href="{{url('/home/table/list')}}"><i class="fa "></i>表格列表</a></li>
    <li><a href="#"><i class="fa "></i>Here</a></li>
@endsection


@section('content')
<div style="display:none;">
    <input type="hidden" id="table_id" value="{{$table_encode or ''}}" readonly>
</div>

{{--表格--}}
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN PORTLET-->
        <div class="box box-info">

            <div class="box-header with-border" style="margin:16px 0;">
                <h3 class="box-title">表格数据</h3>

                <div class="pull-right">
                    <button type="button" onclick="" class="btn btn-success pull-right show-add-data"><i class="fa fa-plus"></i> 添加数据</button>
                </div>
            </div>

            <div class="box-body" id="content-main-body">
                <!-- datatable start -->
                <table class='table table-striped table-bordered' id='datatable_ajax'>
                    <thead>
                    <tr role='row' class='heading'>
                        @foreach($data->columns as $column)
                            <th>{{$column->title}}</th>
                        @endforeach
                        <th>操作</th>
                    </tr>
                    <tr>
                        @foreach($data->columns as $column)
                            <td></td>
                        @endforeach
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-success">搜索</button>
                                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown">
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="#">重置</a></li>
                                    <li class="divider"></li>
                                    <li><a href="#">Separated link</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data->rows as $row)
                    <tr class="row-option" data-id="{{encode($row->id)}}">
                        @foreach($row->datas as $content)
                            <td>{{$content->content or ''}}</td>
                        @endforeach
                        <td>

                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-primary">操作</button>
                                <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a class="show-edit-data" data-id="{{encode($row->id)}}">编辑</a></li>
                                    <li><a class="delete-this-row" data-id="{{encode($row->id)}}">删除</a></li>
                                    {{--<li><a class="article-enable-submit" data-id="'+value+'">启用</a></li>--}}
                                    {{--<li><a class="article-disable-submit" data-id="'+value+'">禁用</a></li>--}}
                                    {{--<li><a href="/admin/statistics/page?module=2&id='+value+'">流量统计</a></li>--}}
                                    {{--<li><a class="download-qrcode" data-id="'+value+'">下载二维码</a></li>--}}
                                    <li class="divider"></li>
                                    <li><a href="#">Separated link</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                <!-- datatable end -->
            </div>

            <div class="box-footer">
                <div class="row" style="margin:16px 0;">
                    <div class="col-md-offset-0 col-md-10">
                        <button type="button" onclick="" class="btn btn-primary _none"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" onclick="history.go(-1);" class="btn btn-default">返回</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PORTLET-->
    </div>
</div>



{{--modal--}}
<div class="modal fade" id="edit-modal">
    <div class="col-md-8 col-md-offset-2" id="edit-ctn" style="margin-top:54px;margin-bottom:64px;padding:32px 0;background:#fff;"></div>
</div>



{{--图--}}
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN PORTLET-->
        <div class="box box-warning">

            <div class="box-header with-border" style="margin:16px 0;">
                <h3 class="box-title">Charts 表图</h3>

                <div class="pull-right">
                    <button type="button" onclick="" class="btn btn-success pull-right show-add-chart"><i class="fa fa-plus"></i> 创建图</button>
                </div>
            </div>

            <div class="box-body">
                <!-- datatable start -->
                <table class='table table-striped table-bordered' id='datatable_ajax'>
                    <thead>
                    <tr role='row' class='heading'>
                        <th>名称</th>
                        <th>标题</th>
                        <th>备注</th>
                        <th>图标类型</th>
                        <th>操作</th>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-success">搜索</button>
                                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown">
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="#">重置</a></li>
                                    <li class="divider"></li>
                                    <li><a href="#">Separated link</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data->charts as $chart)
                        <tr class="chart-option" data-id="{{encode($chart->id)}}">
                            <td>{{$chart->name or ''}}</td>
                            <td>{{$chart->title or ''}}</td>
                            <td>{{$chart->description or ''}}</td>
                            <td>
                                @if($chart->type == 1)  折线图
                                @elseif($chart->type == 2)  柱状图
                                @elseif($chart->type == 3)  饼图
                                @elseif($chart->type == 4)  雷达图
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-primary">操作</button>
                                    <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                        <span class="caret"></span>
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a target="_blank" href="/chart?id={{encode($chart->id)}}">查看</a></li>
                                        <li><a class="show-edit-chart" data-id="{{encode($chart->id)}}">编辑</a></li>
                                        <li><a class="delete-this-chart" data-id="{{encode($chart->id)}}">删除</a></li>
                                        {{--<li><a class="article-enable-submit" data-id="'+value+'">启用</a></li>--}}
                                        {{--<li><a class="article-disable-submit" data-id="'+value+'">禁用</a></li>--}}
                                        {{--<li><a href="/admin/statistics/page?module=2&id='+value+'">流量统计</a></li>--}}
                                        {{--<li><a class="download-qrcode" data-id="'+value+'">下载二维码</a></li>--}}
                                        <li class="divider"></li>
                                        <li><a href="#">Separated link</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <!-- datatable end -->
            </div>

            <div class="box-footer">
                <div class="row" style="margin:16px 0;">
                    <div class="col-md-offset-0 col-md-10">
                        <button type="button" onclick="" class="btn btn-primary _none"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" onclick="history.go(-1);" class="btn btn-default">返回</button>
                    </div>
                </div>
            </div>

        </div>
        <!-- END PORTLET-->
    </div>
</div>


@endsection



@section('js')
<script>
    var TableDatatablesAjax = function () {
        var datatableAjax = function () {

            var dt = $('#datatable_ajax');
            var ajax_datatable = dt.DataTable({
                "aLengthMenu": [[20, 50, 200, 500, -1], ["20", "50", "200", "500", "全部"]],
                "processing": true,
                "serverSide": true,
                "searching": false,
                "ajax": {
                    'url': '/home/table/data/list',
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                    },
                },
                "pagingType": "simple_numbers",
                "order": [],
                "orderCellsTop": true,
                "columns": [
                    {
                        "data": "encode_id",
                        'orderable': false,
                        render: function(data, type, row, meta) {
                            return '<a target="_blank" href="/table?id='+data+'">'+row.name+'</a>';
                        }
                    },
                    {
                        'data': 'title',
                        'orderable': false,
                        render: function(val) {
                            return val == null ? '' : val;
                        }
                    },
                    {
                        'data': 'type',
                        'orderable': true,
                        render: function(type) {
                            return type == null ? '' : type;
                        }
                    },
                    {
                        'data': 'menu_id',
                        'orderable': true,
                        render: function(data, type, row, meta) {
                            return row.menu == null ? '未分类' : row.menu.name;
                        }
                    },
                    {
                        "data": "id",
                        'orderable': false,
                        render: function(data, type, row, meta) {
                            return row.admin == null ? '未知' : row.admin.nickname;
                        }
                    },
                    {
                        'data': 'visit_num',
                        'orderable': false,
                        render: function(val) {
                            return val == null ? 0 : val;
                        }
                    },
                    {
                        'data': 'active',
                        'orderable': false,
                        render: function(val) {
                            if(val == 0) return '<small class="label bg-teal">未启用</small>';
                            else if(val == 1) return '<small class="label bg-green">启</small>';
                            else return '<small class="label bg-red">禁</small>';
                        }
                    },
                    {
                        'data': 'created_at',
                        'orderable': true,
                        render: function(data) {
                            newDate = new Date();
                            newDate.setTime(data * 1000);
                            return newDate.toLocaleString('chinese',{hour12:false});
                        }
                    },
                    {
                        'data': 'updated_at',
                        'orderable': true,
                        render: function(data) {
                            newDate = new Date();
                            newDate.setTime(data * 1000);
                            return newDate.toLocaleString('chinese',{hour12:false});
                        }
                    },
                    {
                        'data': 'encode_id',
                        'orderable': false,
                        render: function(value) {
                            var html =
                                    '<div class="btn-group">'+
                                    '<button type="button" class="btn btn-sm btn-primary">操作</button>'+
                                    '<button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">'+
                                    '<span class="caret"></span>'+
                                    '<span class="sr-only">Toggle Dropdown</span>'+
                                    '</button>'+
                                    '<ul class="dropdown-menu" role="menu">'+
                                    '<li><a href="/home/table/edit?id='+value+'">编辑</a></li>'+
                                    '<li><a href="/home/table/data?id='+value+'">数据管理</a></li>'+
                                    '<li><a class="article-delete-submit" data-id="'+value+'" >删除</a></li>'+
                                    '<li><a class="article-enable-submit" data-id="'+value+'">启用</a></li>'+
                                    '<li><a class="article-disable-submit" data-id="'+value+'">禁用</a></li>'+
                                    '<li><a href="/admin/statistics/page?module=2&id='+value+'">流量统计</a></li>'+
                                    '<li><a class="download-qrcode" data-id="'+value+'">下载二维码</a></li>'+
                                    '<li class="divider"></li>'+
                                    '<li><a href="#">Separated link</a></li>'+
                                    '</ul>'+
                                    '</div>';
                            return html;
                        }
                    }
                ],
                "drawCallback": function (settings) {
                    ajax_datatable.$('.tooltips').tooltip({placement: 'top', html: true});
                    $("a.verify").click(function(event){
                        event.preventDefault();
                        var node = $(this);
                        var tr = node.closest('tr');
                        var nickname = tr.find('span.nickname').text();
                        var cert_name = tr.find('span.certificate_type_name').text();
                        var action = node.attr('data-action');
                        var certificate_id = node.attr('data-id');
                        var action_name = node.text();

                        var tpl = "{{trans('labels.crc.verify_user_certificate_tpl')}}";
                        layer.open({
                            'title': '警告',
                            content: tpl
                                    .replace('@action_name', action_name)
                                    .replace('@nickname', nickname)
                                    .replace('@certificate_type_name', cert_name),
                            btn: ['Yes', 'No'],
                            yes: function(index) {
                                layer.close(index);
                                $.post(
                                        '/admin/medsci/certificate/user/verify',
                                        {
                                            action: action,
                                            id: certificate_id,
                                            _token: '{{csrf_token()}}'
                                        },
                                        function(json){
                                            if(json['response_code'] == 'success') {
                                                layer.msg('操作成功!', {time: 3500});
                                                ajax_datatable.ajax.reload();
                                            } else {
                                                layer.alert(json['response_data'], {time: 10000});
                                            }
                                        }, 'json');
                            }
                        });
                    });
                },
                "language": { url: '/admin/i18n' },
            });


            dt.on('click', '.filter-submit', function () {
                ajax_datatable.ajax.reload();
            });

            dt.on('click', '.filter-cancel', function () {
                $('textarea.form-filter, select.form-filter, input.form-filter', dt).each(function () {
                    $(this).val("");
                });

                $('select.form-filter').selectpicker('refresh');

                ajax_datatable.ajax.reload();
            });

        };
        return {
            init: datatableAjax
        }
    }();
    $(function () {
//        TableDatatablesAjax.init();
    });
</script>
<script src="https://cdn.bootcss.com/echarts/3.7.2/echarts.min.js"></script>
<script>
    $(function() {

        // 显示添加数据
        $(".show-add-data").on('click', function() {

            $.post(
                "/home/table/data/get/add",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    table_id:$('#table_id').val()
                },
                function(data){
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        var html = data.data.html;
                        $('#edit-ctn').html(html);
                        $('#edit-modal').modal('show');
                    }
                },
                'json'
            );

        });

        // 显示编辑数据
        $(".show-edit-data").on('click', function() {

            $.post(
                "/home/table/data/get/edit",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    table_id:$('#table_id').val(),
                    row_id:$(this).attr('data-id')
                },
                function(data){
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        var html = data.data.html;
                        $('#edit-ctn').html(html);
                        $('#edit-modal').modal('show');
                    }
                },
                'json'
            );

        });

        // 添加数据
        $("#edit-modal").on('click', '.add-this-data', function() {
            var options = {
                url: "/home/table/data/edit",
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

        // 显示创建图
        $(".show-add-chart").on('click', function() {

            $.post(
                "/home/table/chart/get/add",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    table_id:$('#table_id').val()
                },
                function(data){
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        var html = data.data.html;
                        $('#edit-ctn').html(html);
                        $('#edit-modal').modal('show');
                    }
                },
                'json'
            );

        });

        // 显示编辑数据
        $(".show-edit-chart").on('click', function() {

            $.post(
                "/home/table/chart/get/edit",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    chart_id:$(this).attr("data-id")
                },
                function(data){
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        var html = data.data.html;
                        $('#edit-ctn').html(html);
                        $('#edit-modal').modal('show');
                    }
                },
                'json'
            );

        });

        // 添加图
        $("#edit-modal").on('click', '.add-this-chart', function() {
            var options = {
                url: "/home/table/chart/edit",
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
        $("#edit-modal").on('click', '.select-this-chart-data', function () {
            if($(".chart-type").val() == 4)
            {
                var chart_max = $(this).parents(".chart-data-option").find(".chart-max");
                if($(this).is(':checked')) chart_max.show();
                else chart_max.hide();
            }
        });

        // 取消添加or编辑
        $("#edit-modal").on('click', '.cansel-this-modal', function () {
            $('#edit-ctn').html('');
            $('#edit-modal').modal('hide');
        });

        // 显示编辑数据
        $(".delete-this-row").on('click', function() {
            var that = $(this);
            layer.msg('确定要删除该"记录"么', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "/home/table/data/delete",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            table_id:$('#table_id').val(),
                            row_id:that.parents('.row-option').attr('data-id')
                        },
                        function(data){
                            if(!data.success) layer.msg(data.msg);
                            else
                            {
                                layer.msg(data.msg);
                                location.reload();
                            }
                        },
                        'json'
                    );
                }
            });
        });

        // 显示编辑数据
        $(".delete-this-chart").on('click', function() {
            var that = $(this);
            layer.msg('确定要删除该"图"么', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "/home/table/chart/delete",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            table_id:$('#table_id').val(),
                            chart_id:that.parents('.chart-option').attr('data-id')
                        },
                        function(data){
                            if(!data.success) layer.msg(data.msg);
                            else
                            {
                                layer.msg(data.msg);
                                location.reload();
                            }
                        },
                        'json'
                    );
                }
            });
        });





        // 【删除】 文章
        $("#article-main-body").on('click', ".article-delete-submit", function() {
            var that = $(this);
            layer.msg('确定要删除该"文章"么', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                            "/home/table/delete",
                            {
                                _token: $('meta[name="_token"]').attr('content'),
                                id:that.attr('data-id')
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

        // 【启用】 文章
        $("#article-main-body").on('click', ".article-enable-submit", function() {
            var that = $(this);
            layer.msg('确定启用该"文章"？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                            "/admin/article/enable",
                            {
                                _token: $('meta[name="_token"]').attr('content'),
                                id:that.attr('data-id')
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

        // 【禁用】 文章
        $("#article-main-body").on('click', ".article-disable-submit", function() {
            var that = $(this);
            layer.msg('确定禁用该"文章"？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                            "/admin/article/disable",
                            {
                                _token: $('meta[name="_token"]').attr('content'),
                                id:that.attr('data-id')
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

        // 【下载】 二维码
        $("#article-main-body").on('click', ".download-qrcode", function() {
            var that = $(this);
            window.open('/admin/download_qrcode?sort=article&id='+that.attr('data-id'));
        });

    });


    function select_chart_type()
    {
        var chart = $('#edit-modal .chart-container');
        var vs = chart.find('select option:selected').val();
        if(vs == 4)
        {
            var checkboxs = chart.find('input[type=checkbox]');
            checkboxs.each(function() {
                if($(this).is(':checked'))
                {
                    $(this).parents(".chart-data-option").find('.chart-max').show();
                }
            });
        }
        else chart.find(".chart-max").hide();
    }


</script>
@endsection
