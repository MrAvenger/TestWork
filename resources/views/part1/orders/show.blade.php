@extends('part1.layout')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="card-title">Информация о заказе</h5>
                </div>
                <div class="card-body p-4">
                    <div class="container">
                        <div class="row validated">
                            <div class="col-md-12 col-lg-6">
                                <div class="mb-3">
                                    <label for="customer" class="form-label">Имя клиента</label>
                                    <p>{{$order->customer}}</p>
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-6">
                                <div class="mb-3">
                                    <label for="" class="form-label">Номер телефона клиента</label>
                                    <p>{{$order->phone}}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row validated">
                            <div class="col-md-12 col-lg-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Тип заказа</label>
                                    <p>{{$order->getType()}}</p>
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Статус</label>
                                    <p>{{$order->getStatus()}}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row validated">
                            <div class="col-md-12 col-lg-6">
                                <div class="mb-3">
                                    <label for="user" class="form-label">Менеджер</label>
                                    <p>{{$order->user->name}}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="text-end">
                                <b id="totalCart">Итоговая стоимость: {{$order->getTotalCostItems()}}</b>
                            </div>
                        </div>
                        <div class="row">
                            <h2>Корзина</h2>
                            <div class="d-flex align-items-center">
                                <table id="table" class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">Код</th>
                                            <th scope="col">Товар</th>
                                            <th scope="col">Количество</th>
                                            <th scope="col">Цена за 1 ед</th>
                                            <th scope="col">Скидка</th>
                                            <th scope="col">Стоимость</th>

                                        </tr>
                                    </thead>
                                    <tbody id="products_list">
                                        @foreach ($order->items as $item)
                                        <tr>
                                            <td>
                                                {{$item->id}}
                                            </td>
                                            <td>
                                                {{$item->name}}
                                            </td>
                                            <td>
                                                {{$item->pivot->count}}
                                            </td>
                                            <td>
                                                {{$item->price}}
                                            </td>
                                            <td>
                                                {{$item->discount}}
                                            </td>
                                            <td>
                                                {{$item->pivot->cost}}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="col-xl-12 text-end">
                        <a href="{{route('orders.edit',$order->id)}}" class="btn btn-primary">Редактировать</a>
                        <a href="{{route('orders.index')}}" class="btn btn-danger">Назад</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
