@extends('part2.layout')
@section('page_styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css">
@endsection
@section('content')
<div class="container">
    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
        <symbol id="info-fill" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
        </symbol>
    </svg>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Общая история движений товаров</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-primary d-flex align-items-center" role="alert">
                        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Info:">
                            <use xlink:href="#info-fill" /></svg>
                        <div>
                            Для поиска определённого склада или товара, воспользуйтесь строкой поиска таблицы
                        </div>
                    </div>
                    <table id="history" class="table">
                        <thead>
                            <tr>
                                <th scope="col">Код</th>
                                <th scope="col">С какого склада</th>
                                <th scope="col">На какой</th>
                                <th scope="col">Продукт</th>
                                <th scope="col">Количество</th>
                                <th scope="col">Дата</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $item)
                                <tr>
                                    <td>{{$item->id}}</td>
                                    <td>{{$item->warehouse_from->name}}</td>
                                    <td>{{$item->warehouse_to->name}}</td>
                                    <td>{{$item->product->name}}</td>
                                    <td>{{$item->count}}</td>
                                    <td>{{$item->created_at->format('d-m-Y')}}</td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <div class="text-end">
                        <a href="{{url()->previous()}}" class="btn btn-primary">Назад</a>
                    </div>
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
        $('#history').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.11.3/i18n/ru.json"
            }
        , });
    });
</script>
@endsection

