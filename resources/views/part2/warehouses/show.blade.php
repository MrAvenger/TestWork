@extends('part2.layout')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="card-title">Информация о складе</h5>
                </div>
                <div class="card-body p-4">
                    <div class="container">
                        <div class="row validated">
                            <div class="col-md-12 col-lg-6">
                                <div class="mb-3">
                                    <label for="customer" class="form-label">Наименование</label>
                                    <p>{{$warehouse->name}}</p>
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-6">
                                <div class="mb-3">
                                    <label for="" class="form-label">Адрес</label>
                                    <p>{{$warehouse->adress}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="col-xl-12 text-end">
                        <a href="{{route('warehouses.edit',$warehouse->id)}}" class="btn btn-primary">Редактировать</a>
                        <a href="{{url()->previous()}}" class="btn btn-danger">Назад</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
