@extends('layouts.app')
@push('css')

@endpush
@section('content')
    <div class="container-fluid">
        <h1 class="text-black-50">Master Item</h1>
    
        @include('master-item.modal-create')
    
        <section class="content">
            @if (session()->has('alert'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-check"></i> Alert!</h4>
                    {{ session('alert') }}
                </div>
            @endif
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-default">
                        <div class="box-header with-border">
                            <div class="pull-right">
                                <a class="btn btn-primary" onclick="createMasterItem()"><i class="fa fa-plus"></i> Add Master Item</a>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12 col-xs-12 col-lg-12">
                                    <div class="form-group">
                                        <table id="masterItemTable" class="table table-bordered table-striped" width="100%">
                                            <thead>
                                                <tr>
                                                    <th style="width:40%; text-align: center;">Item Name</th>
                                                    <th style="width:40%; text-align: center;">Item Code</th>
                                                    <th style="width:10%; text-align: center;">Status</th>
                                                    <th style="width:10%; text-align: center;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.box -->
        </section>
    </div>

@endsection
@push('js')
    <script>
        let masterItemTableOrderColumn = localStorage.getItem('masterItemTable_order_column') && localStorage.getItem('masterItemTable_order_column') != 'undefined' ? localStorage.getItem('masterItemTable_order_column') : 0;
        let masterItemTableOrderDir = localStorage.getItem('masterItemTable_sorting') && localStorage.getItem('masterItemTable_sorting') != 'undefined' ? localStorage.getItem('masterItemTable_sorting') : 'asc';
        let masterItemTableDataLength = localStorage.getItem('masterItemTable_length') && localStorage.getItem('masterItemTable_length') != 'undefined' ? parseInt(localStorage.getItem('masterItemTable_length')) : 10;

        $(function() {
            masterItemTable = $('#masterItemTable').DataTable({
                responsive: true,
                ordering: {
                    indicators: false
                },
                columnControl: ['orderStatus'],
                order: [[ masterItemTableOrderColumn, masterItemTableOrderDir ]],
                pageLength: masterItemTableDataLength,
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/master-item/data'
                },
                drawCallback: function (settings) { 
                    var response = settings.json;

                    localStorage.setItem('masterItemTable_order_column', response.orderColumnIndex);
                    localStorage.setItem('masterItemTable_sorting', response.orderDir);
                    localStorage.setItem('masterItemTable_length', response.dataLength);
                },
                createdRow: function( row, data, dataIndex ) {
                    $(row).attr('data-id', data.id);
                    $(row).find('td:eq(0)').css('text-align', 'center').addClass('item-name-text');
                    $(row).find('td:eq(1)').css('text-align', 'center').addClass('item-code-text');
                    $(row).find('td:eq(2)').css('text-align', 'center').addClass('status-text');
                    $(row).find('td:eq(3)').css('text-align', 'center');
                },
                columns: [
                    { data: 'item_name', name: 'item_name' },
                    { data: 'item_code', name: 'item_code' },
                    { 
                        data: 'format_status', 
                        name: 'status',
                        render: function ( data, type, row, meta ) {
                            let color = row.status === 1 ? 'green' : 'red';

                            return `<p style="color: ${color}">${data}</p>`;
                        }
                    },
                    {
                        data: 'id',
                        orderable: false,
                        render: function(data) {
                            return `
                                <div style="display: flex; gap: 6px; justify-content: center;">
                                    <button type="button" class="btn btn-sm btn-success" onclick="detailMasterItem('${data}')">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="editMasterItem('${data}')">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteMasterItem('${data}')">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            `;
                        }
                    }
                ],
            });
        })

        function createMasterItem() {
            $('#formMasterItemModal .modal-title').text('Add Master Item');

            $('#formMasterItem')[0].reset();

            $('#dataMasterItemModal').find("input:text").prop('readonly', false);
            $('#dataMasterItemModal').find('select').prop('disabled', false);

            $('#form-status').hide();
            $('#btn-save-master-item').show();
            $('#btn-update-master-item').hide();

            $("#formMasterItemModal").modal("toggle");
        }

        $('#btn-save-master-item').on('click', function(e) {
            e.preventDefault();
            
            $.ajax({
                url: '/master-item/store',
                method: "POST",
                dataType: "JSON",
                data: {
                    "_token": "{{ csrf_token() }}",
                    item_name: $('#item-name').val(),
                    item_code: $('#item-code').val()
                },
                beforeSend: function() {
                    $(document).find('span.error-text').text('');
                },
                success: function(result) {
                    alert(result.message);

                    if (result.status == 200) {
                        $("#formMasterItemModal").modal("toggle");

                        masterItemTable.draw();
                    }
                }, 
                error: function(xhr, status, error) {
                    if (xhr.status == 500) {
                        alert(result.message);
                    }
                }
            })
        })

        function detailMasterItem(id) {
            $.get(`/master-item/show/${id}`, function(result) {
                $('#formMasterItemModal .modal-title').text('Detail Master Item');

                $("#master-item-id").val(id);

                $('#dataMasterItemModal').find("input:text").val('').prop('readonly', true);
                $('#dataMasterItemModal').find('select').val('').change().prop('disabled', true);
                $('#form-status').show();

                $('#item-name').val(result.data.item_name);
                $('#item-code').val(result.data.item_code);
                $('#status').val(result.data.status).change();

                $('#btn-save-master-item').hide();
                $('#btn-update-master-item').hide();

                $("#formMasterItemModal").modal("toggle");
            })
        }

        function editMasterItem(id) {
            $.get(`/master-item/edit/${id}`, function(result) {
                $('#formMasterItemModal .modal-title').text('Edit Master Item');

                $("#master-item-id").val(id);

                $('#dataMasterItemModal').find("input:text").val('').prop('readonly', false);
                $('#dataMasterItemModal').find('select').val('').change().prop('disabled', false);
                $('#form-status').show();

                $('#item-name').val(result.data.item_name);
                $('#item-code').val(result.data.item_code);
                $('#status').val(result.data.status).change();

                $('#btn-save-master-item').hide();
                $('#btn-update-master-item').show();

                $("#formMasterItemModal").modal("toggle");
            })
        }

        $('#btn-update-master-item').on('click', function(e) {
            e.preventDefault();
            
            id = $('#master-item-id').val();

            $.ajax({
                url: '/master-item/update/'+id,
                method: "PUT",
                dataType: "JSON",
                data: {
                    "_token": "{{ csrf_token() }}",
                    item_name: $('#item-name').val(),
                    item_code: $('#item-code').val(),
                    status: $('#status').val()
                },
                beforeSend: function() {
                    $(document).find('span.error-text').text('');
                },
                success: function(result) {
                    alert(result.message);

                    if (result.status == 200) {
                        $("#formMasterItemModal").modal("toggle");

                        masterItemTable.draw();
                    }
                }, 
                error: function(xhr, status, error) {
                    if (xhr.status == 500) {
                        alert(result.message);
                    }
                }
            })
        })

        function deleteMasterItem(id) {
            el = $(`tr#master_item_${id}`);

            $.ajax({
                url: '/master-item/delete/'+id,
                type: "DELETE",
                data: {
                    _token: $("input[name=_token]").val()
                },
                success: function(response) {
                    if (response.status == 200) {
                        alert(response.message);
                        
                        el.remove();
                    } else {
                        alert(reponse.message);
                    }
                }
            });
        }
    </script>
@endpush