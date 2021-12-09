@extends('part2.layout')
@section('page_styles')
<style>
    label.is-invalid {
        color: red;
    }

</style>

<link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css">
@endsection
@section('content')

<div class="container">
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Изменение данных о поступивших товарах</h5>
                </div>
                <div class="card-body">
                    <form id="managing">
                        <div class="container">
                            <div class="row">
                                <div class="col">
                                    <div class="mb-3">
                                        <p>Склад: {{$warehouse->name}} ({{$warehouse->adress}})</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="product" class="form-label">Укажите товар</label>
                                        <select class="form-select" id="product">
                                            <option selected>Выберите товар
                                                @if($products)
                                                @foreach($products as $product)
                                            <option value="{{$product->id}}">{{$product->name}}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="count" class="form-label">Количество</label>
                                        <input id="count" type="text" class="form-control ignore" id="exampleFormControlInput1">
                                    </div>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <a id="add_product" class="btn btn-info">Добавить</a>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="alert" id="error_items">

                                </div>
                            </div>
                            <div class="row">
                                <table id="managing" class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">Товар</th>
                                            <th scope="col">Кол-во</th>
                                            <th scope="col">Действие</th>
                                        </tr>
                                    </thead>
                                    <tbody id="products_list">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-footer">
                <div class="col-xl-12 text-end">
                    <button id="sendBtn" type="button" class="btn btn-primary">Создать</button>
                    <a id="cancel" href="{{url()->previous()}}" class="btn btn-danger">Отмена</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('page_scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
<script>
    const products = <?php echo json_encode($products)?>;
    const savedProducts = <?php echo json_encode($warehouse->products)?>;
    const csrf_token = "{{csrf_token()}}";
    const redirectLink ="{{route('managing.index')}}";
    let button = undefined;
    $(document).ready(function() {
        loadStorage();
        button = document.getElementById('sendBtn');
        $("#sendBtn").click(
            function() {
                button.disabled = true;
                ajaxRequest("{{route('managing.update', $warehouse->id)}}", 'PUT', "{{$warehouse->id}}", JSON.stringify([...storage]));
            }
        );

    });

</script>
<script src="/js/managing/warehouseStorage.js"></script>
<script src="/js/managing/validateStorage.js"></script>
<script src="/js/managing/ajaxManaging.js"></script>
@endsection
