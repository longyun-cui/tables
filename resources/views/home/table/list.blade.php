@extends('home.layout.layout')

@section('title','表格列表')
@section('header','表格列表')
@section('description','表格列表')
@section('breadcrumb')
    <li><a href="{{url('/home')}}"><i class="fa fa-home"></i>首页</a></li>
    <li><a href="{{url('/home/table/list')}}"><i class="fa "></i>表格列表</a></li>
    <li><a href="#"><i class="fa "></i>Here</a></li>
@endsection


@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN PORTLET-->
        <div class="box box-info">

            <div class="box-header with-border" style="margin:16px 0;">
                <h3 class="box-title">表格列表</h3>

                <div class="pull-right">
                    <a href="{{url('/home/table/create')}}">
                        <button type="button" onclick="" class="btn btn-success pull-right"><i class="fa fa-plus"></i> 添加表格</button>
                    </a>
                </div>
                <div class="pull-right" style="display:none;">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse">
                        <i class="fa fa-minus"></i></button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="" data-original-title="Remove">
                        <i class="fa fa-times"></i></button>
                </div>
            </div>

            <div class="box-body" id="table-list-body">
                <!-- datatable start -->
                <table class='table table-striped table-bordered' id='datatable_ajax'>
                    <thead>
                    <tr role='row' class='heading'>
                        <th>名称</th>
                        <th>标题</th>
                        <th>类型</th>
                        <th>所属目录</th>
                        <th>管理员</th>
                        <th>浏览次数</th>
                        <th>分享次数</th>
                        <th>是否分享</th>
                        <th>创建时间</th>
                        <th>修改时间</th>
                        <th>数据管理</th>
                        <th>操作</th>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
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
                    'url': '/home/table/list',
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
//                        d.nickname 	= $('input[name="nickname"]').val();
//                        d.certificate_type_id = $('select[name="certificate_type_id"]').val();
//                        d.certificate_state = $('select[name="certificate_state"]').val();
//                        d.admin_name = $('input[name="admin_name"]').val();
//
//                        d.created_at_from = $('input[name="created_at_from"]').val();
//                        d.created_at_to = $('input[name="created_at_to"]').val();
//                        d.updated_at_from = $('input[name="updated_at_from"]').val();
//                        d.updated_at_to = $('input[name="updated_at_to"]').val();

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
                        'data': 'share_num',
                        'orderable': false,
                        render: function(val) {
                            return val == null ? 0 : val;
                        }
                    },
                    {
                        'data': 'is_shared',
                        'orderable': false,
                        render: function(val) {
                            if(val == 1) return '<small class="label bg-green">分享</small>';
                            else return '<small class="label bg-red">私</small>';
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
                        render: function(data) {
                            return '<a href="/home/table/data?id='+data+'"><button type="button" class="btn btn-sm btn-danger">数据管理</button></a>';
                        }
                    },
                    {
                        'data': 'encode_id',
                        'orderable': false,
                        render: function(data, type, row, meta) {
                            var shared_html = '';
                            if(row.is_shared == 1)
                                shared_html = '<li><a class="table-disshared-submit" data-id="'+data+'">取消分享</a></li>';
                            else
                                shared_html = '<li><a class="table-enshared-submit" data-id="'+data+'">分享</a></li>';

                            var html =
                                '<div class="btn-group">'+
                                '<button type="button" class="btn btn-sm btn-primary">操作</button>'+
                                '<button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">'+
                                '<span class="caret"></span>'+
                                '<span class="sr-only">Toggle Dropdown</span>'+
                                '</button>'+
                                '<ul class="dropdown-menu" role="menu">'+
                                '<li><a href="/home/table/edit?id='+data+'">编辑结构</a></li>'+
                                '<li><a href="/home/table/data?id='+data+'">数据管理</a></li>'+
                                shared_html+
//                                '<li><a href="/admin/statistics/page?module=2&id='+data+'">流量统计</a></li>'+
//                                '<li><a class="download-qrcode" data-id="'+data+'">下载二维码</a></li>'+
                                '<li><a class="table-delete-submit" data-id="'+data+'" >删除</a></li>'+
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
        TableDatatablesAjax.init();
    });
</script>
<script>
    $(function() {

        // 表格【删除】
        $("#table-list-body").on('click', ".table-delete-submit", function() {
            var that = $(this);
            layer.msg('确定要删除该"表格"么', {
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

        // 表格【分享】
        $("#table-list-body").on('click', ".table-enshared-submit", function() {
            var that = $(this);
            layer.msg('确定分享该"表格"？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                            "/home/table/enshared",
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

        // 表格【取消分享】
        $("#table-list-body").on('click', ".table-disshared-submit", function() {
            var that = $(this);
            layer.msg('确定不再分享该"表格"？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                            "/home/table/disshared",
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
            window.open('/admin/download_qrcode?sort=table&id='+that.attr('data-id'));
        });

    });
</script>
@endsection
