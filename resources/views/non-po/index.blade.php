@extends('layouts.main')

@section('content')
<div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">Pilih SKK</button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="javascript:void()">Janurari</a>
                                        <a class="dropdown-item" href="javascript:void()">Februari</a>
                                    </div>
                                </div>
                                <a href="/non-po/buat-non-po" type="button" class="btn btn-primary mr-auto ml-3 ">Buat Non-PO<span
                                        class="btn-icon-right"><i class="fa fa-plus-circle"></i></span>
                                </a>
                                <div class="input-group search-area position-relative">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><a href="javascript:void(0)"><i class="flaticon-381-search-2"></i></a></span>
                                    </div>
                                    <input id="search" type="search" name="search" class="form-control" placeholder="Search here..." />
                                </div>
                            </div>
                            <div id="" class="card-body">
                            <div class="table-responsive">
                                    <table class="table table-responsive-md" id="read">
                                        <thead>
                                            <tr align="center" valign="middle">
                                                <th class="width80">No.</th>                                            
                                                <th>No. RPBJ</th>                                                
                                                <th>No SKK</th>
                                                <th>No PRK</th>                                                
                                                <th>Total Harga</th>                                                
                                                <th> </th>
                                            </tr>
                                        </thead>
                                        <tbody class="alldata">
                                            @foreach ($nonpos as $nonpo)
                                                <tr>
                                                    <td align="center" valign="middle"><strong>{{$loop->iteration}}</strong></td>
                                                    <td>{{$nonpo->nomor_rpbj}}</td>
                                                    <td>{{$nonpo->skks->nomor_skk}}</td>
                                                    <td>{{$nonpo->prks->no_prk}}</td>
                                                    <td>@currency($nonpo->total_harga)</td>                                                                                                       
                                                <td>
                                                    <div class="dropdown">
                                                        <button type="button" class="btn btn-warning light sharp" data-toggle="dropdown">
                                                            <svg width="20px" height="20px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"/><circle fill="#000000" cx="5" cy="12" r="2"/><circle fill="#000000" cx="12" cy="12" r="2"/><circle fill="#000000" cx="19" cy="12" r="2"/></g></svg>
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            <a class="dropdown-item" href="preview-pdf-khs">Download KAK</a>
                                                            <a class="dropdown-item" href="non-po/export-pdf-khs/{{$nonpo->id}}">Export (pdf) <i class="bi bi-file-earmark-pdf-fill"></i></a>
                                                            <a class="dropdown-item" href="export-excel-khs">Export (excel) <i class="bi bi-file-earmark-excel-fill"></i></a>
                                                        </div>
                                                    </div>
                                                </td>

                                                </tr>                                                                                                
                                            @endforeach                                            
                                        </tbody>
                                        <tbody id="Content" class="searchdata">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
</div>
<script type="text/javascript">
    $('#search').on('keyup',function(){
        $value=$(this).val();

        if($value){
            $('.alldata').hide();
            $('.searchdata').show();
        }

        else{
            $('.alldata').show();
            $('.searchdata').hide();

        }

    $.ajax({

        type: 'get',
        url:'{{URL::to('search-pokhs') }}',
        data:{'search':$value},

        success:function(data){
            console.log(data);
            $('#Content').html(data);
        }

    });
        
    });
</script>
@endsection

