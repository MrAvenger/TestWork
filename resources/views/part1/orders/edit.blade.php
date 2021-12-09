@extends('part1.layout')
@section('page_styles')
<style>
    label.is-invalid {
        color: red;
    }

</style>
@endsection
@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <form id="orders-form">
                @csrf
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="card-title">Редактирование заказа</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="container">
                            <div class="row validated">
                                <div class="col-md-12 col-lg-6">
                                    <div class="mb-3">
                                        <label for="customer" class="form-label">Имя клиента</label>
                                        <input type="text" class="form-control" id="customer" name="customer" value={{$order->customer}}>
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-6">
                                    <div class="mb-3">
                                        <label for="" class="form-label">Номер телефона клиента</label>
                                        <input type="text" class="form-control" id="phone" name="phone" aria-describedby="phoneHelp" value={{$order->phone}}>
                                        <input type="hidden" id="clear_phone" name="clear_phone">
                                        <div id="phoneHelp" class="form-text">Пример номера: +7 (916) 343-2231</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row validated">
                                <div class="col-md-12 col-lg-6">
                                    <div class="mb-3">
                                        <label for="type" class="form-label">Тип заказа</label>
                                        <select class="form-select" id="type" name="type" aria-label="Default select example">
                                            <option value="select" selected>Выберите тип</option>
                                            <option value="online" {{$order->type == 'online' ? 'selected' : ''}}>Онлайн</option>
                                            <option value="offline" {{$order->type == 'offline' ? 'selected' : ''}}>Оффлайн</option>

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-6">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Статус</label>
                                        <select class="form-select" id="status" name="status" aria-label="Default select example">
                                            <option value="select" selected>Выберите статус</option>
                                            <option value="active" {{$order->status == 'active' ? 'selected' : ''}}>Активный</option>
                                            <option value="completed" {{$order->status == 'completed' ? 'selected' : ''}}>Завершён</option>
                                            <option value="canceled" {{$order->status == 'canceled' ? 'selected' : ''}}>Отменён</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row validated">
                                <div class="col-md-12 col-lg-6">
                                    <div class="mb-3">
                                        <label for="user" class="form-label">Менеджер</label>
                                        <select class="form-select" id="user" name="user_id" aria-label="Default select example">
                                            <option value="select" selected>Выберите пользователя</option>
                                            @foreach ($users as $user)
                                            <option value={{$user->id}} {{$order->user_id == $user->id ? 'selected' : ''}}>{{$user->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <h2>Корзина</h2>
                                <div class="d-flex align-items-center">
                                    <div class="col-3 me-2">
                                        <div class="mb-3">
                                            <label for="product">Товар</label>
                                            <select id="product" name="product" class="form-select ignore">
                                                <option value="select" selected>Выберите товар</option>
                                                @foreach ($products as $product)
                                                    @if($product->stock>0)
                                                        <option value={{$product->id}}>{{$product->name}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-3 me-2">
                                        <div class="mb-3">
                                            <label for="count">Количество</label>
                                            <input id="count" name="count" type="number" min="1" class="form-control count ignore">
                                        </div>
                                    </div>
                                    <div class="col-3 me-2">
                                        <div class="mb-3">
                                            <label for="discount">Скидка</label>
                                            <input id="discount" name="discount" type="text" min="1" class="form-control float ignore">
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="mb-3">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text" id="cost-span">Итого (позиция):</span>
                                            <input type="text" readonly class="form-control ignore" id="cost">

                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <a onClick="addToCart();" class="btn btn-info" role="button">Добавить</a>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div id="error_items" class="col">

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="text-end">
                                        <b id="totalCart">Итоговая стоимость: 0</b>
                                    </div>
                                </div>

                                <table id="table" class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Товар</th>
                                            <th scope="col">Количество</th>
                                            <th scope="col">Цена за 1 ед</th>
                                            <th scope="col">Скидка</th>
                                            <th scope="col">Стоимость</th>
                                            <th scope="col">Действие</th>
                                        </tr>
                                    </thead>
                                    <tbody id="products_list">
                                    </tbody>
                                </table>
                                <input type="hidden" id="items" name="items">
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="col-xl-12 text-end">
                            <button id="sendBtn" type="button" class="btn btn-primary">Изменить</button>
                            <a href="{{route('orders.index')}}" class="btn btn-danger">Отмена</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('page_scripts')
{{-- <script src="{{asset('/js/masked_input.js')}}" rel="javascript" type="text/javascript"></script> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery.maskedinput@1.4.1/src/jquery.maskedinput.min.js" type="text/javascript"></script>
<script>
    const users = <?php echo json_encode($users)?>;
    const products = <?php echo json_encode($products)?>;
    const redirectLink = "{{route('orders.index')}}";
    const savedProducts = <?php echo $order->items? json_encode($order->items):'null' ?>;
    let button = undefined;
    $(document).ready(function() {
        loadCart();
        setCorrectValuesForSelect();
        button = document.getElementById('sendBtn');
        $("#phone").mask("+7 (999) 999-9999", {
            autoclear: false
        });
        $("#phone").val("<?php echo $order->phone ?>");
        $("#phone").trigger('paste');
        $("#sendBtn").click(
            function() {
                button.disabled = true;
                $("#clear_phone").val("7" + $("#phone").mask());
                ajaxRequest("{{route('orders.update',$order->id)}}", 'PUT', $("#customer").val(), $("#clear_phone").val(), $("#type").val(), $("#status").val(), $("#user_id").val(), JSON.stringify([...cart]));
                return false;
            }
        );
    });
</script>
<script src="/js/orders/ajaxOrders.js"></script>
<script src="/js/orders/orderCart.js"></script>
<script src="/js/orders/validateOrder.js"></script>

@endsection
