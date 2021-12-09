@extends('part2.layout')
@section('page_styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css">
@endsection
@section('content')
<div class="container">
    <div class="row my-2">
        <div class="col-xl-12 text-end">
            <a href="{{route('products.create')}}" class="btn btn-primary">Добавить продукт</a>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Продукты</h5>
                </div>
                <div class="card-body">
                    <table id="products" class="table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Наименование</th>
                                <th scope="col">Тип</th>
                                <th scope="col">Цена за 1 ед</th>
                                <th scope="col">Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                            <tr>
                                <th scope="row">{{$product->id}}</th>
                                <td>{{$product->name}}</td>
                                <td>{{$product->type->name}}</td>
                                <td>{{$product->price}}</td>
                                <td>
                                    <a href="{{route('products.show',$product->id)}}" class="btn btn-sm btn-light far fa-eye"></a>
                                    <a href="{{route('products.edit',$product->id)}}" class="btn btn-sm btn-primary far fa-edit"></a>
                                    <a onclick='deleteProduct("{{route("products.destroy",$product->id)}}")' class="btn btn-sm btn-danger fas fa-trash-alt" title="Delete"></a>
                                </td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('page_scripts')
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>
<script>
    let deleteClicked = false;
    $(document).ready(function() {
        $('#products').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.11.3/i18n/ru.json"
            }
        , });
    });

    function deleteProduct(url) {
        if (confirm("Вы точно хотите удалть данную запись?") && !deleteClicked) {
            $.ajax({
                url: url
                , method: 'DELETE'
                , dataType: 'json'
                , data: {
                    _token: "{{csrf_token()}}"
                },
                /* Параметры передаваемые в запросе. */
                success: function(data) {
                    /* функция которая будет выполнена после успешного запроса. */
                    if (data) {
                        if (data.success) {
                            deleteClicked = true;
                            $.toast({
                                heading: 'Успех'
                                , text: data.success
                                , icon: 'success'
                            , })
                            setTimeout(function() {
                                window.location.href = "{{route('products.index')}}";
                            }, 1500);

                        }
                    }
                }
                , error: function(e) {
                    console.log(e);
                }
            });

        }
    }

</script>

@endsection

