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
<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
    <symbol id="check-circle-fill" fill="currentColor" viewBox="0 0 16 16">
        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
    </symbol>
    <symbol id="info-fill" fill="currentColor" viewBox="0 0 16 16">
        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
    </symbol>
    <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
    </symbol>
</svg>

<div class="container">
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Добавление новых данных о поступлении товаров на склад</h5>
                </div>
                <div class="card-body">
                    <form id="managing">
                        <div class="container">
                            <div class="row">
                                <div class="alert alert-primary d-flex align-items-center" role="alert">
                                    <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Info:">
                                        <use xlink:href="#info-fill" /></svg>
                                    <div>
                                        Внимание! Через данную форму можно добавить информацию о товарах только для того склада, на котором на текущий момент нет товаров!
                                        Чтобы добавить новые товары на склад, на который раньше добавлялись товары, <a href="{{url()->previous()}}">вернитесь назад</a> и отредактируйте информацию
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="mb-3">
                                        <label for="warehouse" class="form-label">Укажите склад</label>
                                        <select class="form-select" id="warehouse" name="warehouse">
                                            <option selected>Выберите склад</option>
                                            @if($warehouses)
                                            @foreach($warehouses as $warehouse)
                                            @if(!$warehouse->hasProducts())
                                            <option value="{{$warehouse->id}}">{{$warehouse->name}} ({{$warehouse->adress}})</option>
                                            @endif
                                            @endforeach
                                            @endif
                                        </select>
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
    const warehouses = <?php echo json_encode($warehouses)?> ;
    const csrf_token = "{{csrf_token()}}";
    const redirectLink ="{{route('managing.index')}}";
    let button = undefined;
    $(document).ready(function() {
        button = document.getElementById('sendBtn');
        $("#sendBtn").click(
            function() {
                button.disabled = true;
                ajaxRequest("{{route('managing.store')}}", 'POST', $("#warehouse").val(), JSON.stringify([...storage]));
            }
        );

    });

</script>
<script src="/js/managing/warehouseStorage.js"></script>
<script src="/js/managing/validateStorage.js"></script>
<script src="/js/managing/ajaxManaging.js"></script>
@endsection
