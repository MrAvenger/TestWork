@extends('part2.layout')
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
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Перевод между складами</h5>
                </div>
                <div class="card-body">
                    <form id="transfer">
                        <div class="container">
                            <div class="row">
                                <div class="mb-3">
                                    <label for="from_warehouse" class="form-label">С какого склада</label>
                                    <select id="from_warehouse" name="from_warehouse" class="form-select" aria-label="Default select example">
                                        <option selected>Выберите склад из списка</option>
                                        @if($warehouses)
                                        @foreach ($warehouses as $warehouse)
                                        @if($warehouse->hasProducts())
                                        <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                        @endif
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="mb-3">
                                    <label for="to_warehouse" class="form-label">На какой склад</label>
                                    <select id="to_warehouse" name="to_warehouse" class="form-select">
                                        <option selected>Выберите склад из списка</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="product" class="form-label">Товар</label>
                                        <select id="product" name="product" class="form-select">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="exampleFormControlInput1" class="form-label">Кол-во</label>
                                        <input type="text" class="form-control" id="count" name="count">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-footer">
                <div class="col-xl-12 text-end">
                    <button id="btn-send" class="btn btn-primary">Перевести</button>
                    <a href="{{route('managing.index')}}" class="btn btn-danger">Назад</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('page_scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
<script>
    const warehouses = <?php echo json_encode($warehouses)?>;
    let products = []; // Товары
    let maxCount = 0; // Макс кол-во выбранного
    let currentProduct = null; // Текущий товар
    const redirectLink = "{{route('transfer')}}";
    $(document).ready(function() {
        loadSelect();
        ajaxProducts();
        setMaxCount($("#product").val());
        $("#from_warehouse").on('change', function() {
            loadSelect();
            ajaxProducts();
        });
        $("#product").on('change', function() {
            setMaxCount(this.value);
            setValue();
        });
        $("#btn-send").on('click', function(){
            if($("#from_warehouse").val()!=$("#to_warehouse").val()){
                if($("#count").val()<=maxCount &&$("#count").val()>0){
                    ajaxTransfer();
                }
                else{
                    $.toast({
                        heading: 'Информация',
                        text: 'Проверьте указанные значения!',
                        icon: 'info',
                        loader: true, 
                        loaderBg: '#9EC600' 
                    });

                }
            }
            else{
                $.toast({
                    heading: 'Информация',
                    text: 'Склады должны быть разными',
                    icon: 'info',
                    loader: true, 
                    loaderBg: '#9EC600' 
                });
            }
        });
        $("#count").on('input', function(){
            this.value = parseInt(this.value);
            if(isNaN(this.value)){
                this.value = 1;
            }
            setValue();
        });
    });

    function setValue(){
        if (currentProduct && ($("#count").val() > maxCount || $("#count").val() <= 0)) { 
            $.toast({ heading: 'Информация' , text: 'Было установлено допустимое значение' ,
                icon: 'info' ,
                loader: true,
                loaderBg: '#9EC600'
            }); 
            $("#count").val(1);
        }

    }

    function loadSelect() {
        let select_to = document.getElementById('to_warehouse');
        $('#to_warehouse').empty();
        if (warehouses) {
            $('#to_warehouse').append('<option>Выберите склад из списка</option>');
            warehouses.forEach(function(warehouse) {
                if ($("#from_warehouse").val() != warehouse.id) {
                    $('#to_warehouse').append('<option value="' + warehouse.id + '">' + warehouse.name + '</option>');
                }
            });
        }
    }

    function ajaxProducts() {
        $.ajax({
            url: "{{route('transfer.getProducts')}}",
            /* Куда пойдет запрос */
            method: 'POST',
            /* Метод передачи */
            dataType: 'json',
            /* Тип данных в ответе (xml, json, script, html). */
            data: {
                _token: "{{csrf_token()}}",
                id: $("#from_warehouse").val()
            },
            /* Параметры передаваемые в запросе. */
            success: function(data) {
                products = [];
                $("#product").empty();
                if(data.products){
                    products = data.products;
                    $('#product').append('<option>Выберите товар из списка</option>');
                    products.forEach(function(product){
                        $('#product').append('<option value="' + product.id + '">' + product.name + '</option>');
                    });
                }
                else{
                    products = [];
                }
            },
            error:function(e){
                console.log(e);
            }
        });
    }

    function ajaxTransfer(){
        $.ajax({
            url: "{{route('transfer.store')}}",
            /* Куда пойдет запрос */
            method: 'POST',
            /* Метод передачи */
            dataType: 'json',
            /* Тип данных в ответе (xml, json, script, html). */
            data: {
                _token: "{{csrf_token()}}",
                from_warehouse: $("#from_warehouse").val(),
                to_warehouse: $("#to_warehouse").val(),
                count: $("#count").val(),
                newcount:maxCount-Number($("#count").val()),
                product:$("#product").val()
            },
            /* Параметры передаваемые в запросе. */
            success: function(data) {
                let elementFrom = document.getElementById('from_warehouse');
                let elementTo = document.getElementById('to_warehouse');
                let elementCount = document.getElementById('product');
                let elementProduct = document.getElementById('count');
                clearErrors(elementFrom,elementTo,elementCount,elementProduct);
                if(data.success){
                    $.toast({
                        heading: 'Успех',
                        text: 'Перевод совершён!',
                        icon: 'success',
                        loader: true, 
                        loaderBg: '#9EC600' 
                    });
                    setTimeout(function () {
                        window.location.href = redirectLink;
                    }, 1500);

                }
                else if(data.error){
                    let obj = data.error;
                    let keysObj = Object.keys(obj);
                    keysObj.forEach(function (key) {
                        let elementError = document.createElement('label');
                        elementError.className = 'is-invalid';
                        switch(key){
                            case 'from_warehouse':{
                                elementFrom.classList.add('is-invalid');
                                elementError.id = 'from_warehouse-error';
                                elementError.setAttribute('for','from_warehouse');
                                elementError.textContent = obj.from_warehouse;
                                elementFrom.parentElement.appendChild(elementError);
                            }break;
                            case 'to_warehouse':{
                                elementTo.classList.add('is-invalid');
                                elementError.id = 'to_warehouse-error';
                                elementError.setAttribute('for', 'to_warehouse');
                                elementError.textContent = obj.to_warehouse;
                                elementTo.parentElement.appendChild(elementError);
                            }break;
                            case 'product': {
                                elementProduct.classList.add('is-invalid');
                                elementError.id = 'product-error';
                                elementError.setAttribute('for', 'product');
                                elementError.textContent = obj.product;
                                elementProduct.parentElement.appendChild(elementError);
                            } break;
                            case 'count': {
                                elementCount.classList.add('is-invalid');
                                elementError.id = 'count-error';
                                elementError.setAttribute('for', 'count');
                                elementError.textContent = obj.count;
                                elementCount.parentElement.appendChild(elementError);
                            } break;

                        }
                    });
                };
            },
            error:function(e){
                console.log(e);
            }
        });
    }

    function setMaxCount(id) {
        currentProduct = null;
        if(products){
            products.forEach(function(product) {
                if ($("#product").val() == product.id) {
                maxCount = product.pivot.count;
                currentProduct = product;
                }
            });
        }
        else{
            currentProduct = null;
        }
    }

    function clearErrors(elementFrom,elementTo,elementCount,elementProduct){
        let elementRemove = null;
        elementFrom.classList.remove("is-invalid");
        elementTo.classList.remove("is-invalid");
        elementCount.classList.remove("is-invalid");
        elementProduct.classList.remove("is-invalid");
        elementRemove = document.getElementById("from_warehouse-error");
        if(elementRemove){
            elementRemove.remove();
        }
        elementRemove = document.getElementById("to_warehouse-error");
        if (elementRemove) {
            elementRemove.remove();
        }
        elementRemove = document.getElementById("product-error");
        if (elementRemove) {
            elementRemove.remove();
        }
        elementRemove = document.getElementById("count-error");
        if (elementRemove) {
            elementRemove.remove();
        }

    }

</script>
<script src="/js/transfer/validateTransfer.js"></script>
@endsection
