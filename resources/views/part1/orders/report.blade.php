@extends('part1.layout')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="card-title">Отчёт</h5>
                </div>
                <div class="card-body p-4">
                    <form action="">
                        @csrf
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12 col-lg-6">
                                    <div class="mb-3">
                                        <label for="date" class="form-label">Дата</label>
                                        <input type="date" name="date" class="form-control" id="date" />
                                        @csrf
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <h3>Результаты</h3>
                                <div class="col-md-12 col-lg-6">
                                    <label class="control-label">Количество заказов: </label><b id="totalOrders"></b>
                                </div>
                                <div class="col-md-12 col-lg-6">
                                    <label class="control-label">Суммарная стоимость: </label><b id="totalCost"></b>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('page_scripts')
<script>
    $(document).ready(function() {
        let _token = $("input[name='_token']").val();
        $("#date").on("change", function() {
            $.ajax({
                url: "{{route('orders.getReport')}}",
                /* Куда пойдет запрос */
                method: 'POST',
                /* Метод передачи (post или get) */
                dataType: 'json',
                /* Тип данных в ответе (xml, json, script, html). */
                data: {
                    _token: _token
                    , completed_at: $("#date").val()
                },
                /* Параметры передаваемые в запросе. */
                success: function(data) {
                    /* функция которая будет выполнена после успешного запроса. */
                    if (data) {
                        if (data.orders && data.orders.success) {
                            $("#totalOrders").html(data.orders.countOrders);
                            $("#totalCost").html(data.orders.totalCost);
                            $.toast({
                                heading: 'Успех'
                                , text: 'Завершённые заказы найдены, информация выведена!'
                                , icon: 'success'
                                , loader: true
                            , })

                        } else {
                            $.toast({
                                heading: 'Уведомление'
                                , text: 'Завершённых заказов не найдено'
                                , icon: 'info'
                                , loader: true
                            , })
                        }
                    }
                }
                , error: function(e) {
                    $.toast({
                        heading: 'Ошибка'
                        , text: 'Ошибка получения данных'
                        , icon: 'error'
                        , loader: true
                    , })

                }
            });

        });

    });

</script>
@endsection
