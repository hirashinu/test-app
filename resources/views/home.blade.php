@extends('layouts.app')
@push('css')

@endpush
@section('content')
    <div class="container-fluid">
        <h1 class="text-black-50">Transaction</h1>
    
        @include('transaction.modal-create')
    
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
                                <a class="btn btn-primary" onclick="createTransaction()"><i class="fa fa-plus"></i> Add Transaction</a>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12 col-xs-12 col-lg-12">
                                    <div class="form-group">
                                        <table id="transactionTable" class="table table-bordered table-striped" width="100%">
                                            <thead>
                                                <tr>
                                                    <th style="width:50%; text-align: center;">Item Code</th>
                                                    <th style="width:40%; text-align: center;">Quantity</th>
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
        let transactionTableOrderColumn = localStorage.getItem('transactionTable_order_column') && localStorage.getItem('transactionTable_order_column') != 'undefined' ? localStorage.getItem('transactionTable_order_column') : 0;
        let transactionTableOrderDir = localStorage.getItem('transactionTable_sorting') && localStorage.getItem('transactionTable_sorting') != 'undefined' ? localStorage.getItem('transactionTable_sorting') : 'asc';
        let transactionTableDataLength = localStorage.getItem('transactionTable_length') && localStorage.getItem('transactionTable_length') != 'undefined' ? parseInt(localStorage.getItem('transactionTable_length')) : 10;

        $(function() {
            transactionTable = $('#transactionTable').DataTable({
                responsive: true,
                ordering: {
                    indicators: false
                },
                columnControl: ['orderStatus'],
                order: [[ transactionTableOrderColumn, transactionTableOrderDir ]],
                pageLength: transactionTableDataLength,
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/transaction/data'
                },
                drawCallback: function (settings) { 
                    var response = settings.json;

                    localStorage.setItem('transactionTable_order_column', response.orderColumnIndex);
                    localStorage.setItem('transactionTable_sorting', response.orderDir);
                    localStorage.setItem('transactionTable_length', response.dataLength);
                },
                createdRow: function( row, data, dataIndex ) {
                    $(row).attr('data-id', data.id);
                    $(row).find('td:eq(0)').css('text-align', 'center').addClass('item-code-text');
                    $(row).find('td:eq(1)').css('text-align', 'center').addClass('quantity-text');
                    $(row).find('td:eq(2)').css('text-align', 'center');
                },
                columns: [
                    { data: 'item_code', name: 'item_code' },
                    { data: 'quantity', name: 'quantity' },
                    {
                        data: 'id',
                        orderable: false,
                        render: function(data) {
                            return `<button class="btn btn-danger" onclick="deleteTransaction(${data})">Delete</button>`;
                        }
                    }
                ],
            });
        })

        function createTransaction() {
            $('#formTransaction')[0].reset();

            $("#formTransactionModal").modal("toggle");
        }

        $('#btn-save-transaction').on('click', function(e) {
            e.preventDefault();
            
            $.ajax({
                url: '/transaction/store',
                method: "POST",
                dataType: "JSON",
                data: {
                    "_token": "{{ csrf_token() }}",
                    item_code: $('#item-code').val(),
                    quantity: $('#quantity').val()
                },
                beforeSend: function() {
                    $(document).find('span.error-text').text('');
                },
                success: function(result) {
                    alert(result.message);

                    if (result.status == 200) {
                        $("#formTransactionModal").modal("toggle");

                        transactionTable.draw();
                    }
                }, 
                error: function(xhr, status, error) {
                    if (xhr.status == 500) {
                        alert(result.message);
                    }
                }
            })
        })

        function deleteTransaction(id) {
            el = $(`tr#transaction_${id}`);

            $.ajax({
                url: '/transaction/delete/'+id,
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