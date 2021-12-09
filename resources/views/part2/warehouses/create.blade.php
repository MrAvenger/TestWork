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
        <div class="col-12">
            <form id="warehouses-form">
                @csrf
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="card-title">Создание склада</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="container">
                            <div class="row validated">
                                <div class="col-md-12 col-lg-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Наименование</label><span class="text-danger ms-1">*</span>
                                        <input type="text" class="form-control" id="name" name="name">
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Адрес</label><span class="text-danger ms-1">*</span>
                                        <input type="text" class="form-control" id="adress" name="adress">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="col-xl-12 text-end">
                            <button id="sendBtn" type="button" class="btn btn-primary">Создать</button>
                            <a href="{{route('warehouses.index')}}" class="btn btn-danger">Отмена</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('page_scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
<script>
    const redirectLink = "{{route('warehouses.index')}}";
    let button = undefined;
    $(document).ready(function() {
        button = document.getElementById('sendBtn');
        $("#sendBtn").click(
            function() {
                ajaxRequest("{{route('warehouses.store')}}", 'POST', $("#name").val(), $("#adress").val());
            }
        );

    });

</script>
<script src="/js/warehouses/ajaxWarehouses.js"></script>
<script src="/js/warehouses/validateWarehouses.js"></script>

@endsection
