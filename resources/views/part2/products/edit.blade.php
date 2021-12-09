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
            <form id="products-form">
                @csrf
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="card-title">Изменение товара</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="container">
                            <div class="row validated">
                                <div class="col-md-12 col-lg-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Наименование</label><span class="text-danger ms-1">*</span>
                                        <input type="text" class="form-control" id="name" name="name" value = "{{$product->name}}">
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-6">
                                    <label for="type" class="form-label">Тип товара</label><span class="text-danger ms-1">*</span>
                                    <select class="form-select" id="type" name="type" aria-label="Default select example">
                                        <option value="select" selected>Выберите тип</option>
                                        @foreach ($types as $type)
                                        <option value="{{$type->id}}" {{$product->type_id ==$type->id ? 'selected': ''}}>{{$type->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row validated">
                                <div class="col-md-12 col-lg-6">
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Цена за 1 ед</label><span class="text-danger ms-1">*</span>
                                        <input type="text" class="form-control" id="price" name="price" value="{{$product->price}}">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="col-xl-12 text-end">
                            <button id="sendBtn" type="button" class="btn btn-primary">Изменить</button>
                            <a href="{{route('products.index')}}" class="btn btn-danger">Отмена</a>
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
    const redirectLink = "{{route('products.index')}}";
    let button = undefined;
    const types = <?php echo json_encode($types)?>;
    $(document).ready(function() {
        button = document.getElementById('sendBtn');
        $("#price").on("input",function() {
            this.value = parseFloat(this.value).toFixed(2);
            if(isNaN(this.value)){
                this.value = 1;
            }
        })
        $("#sendBtn").click(
            function() {
                ajaxRequest("{{route('products.update',$product->id)}}", 'PUT', $("#name").val(), $("#type").val(), $("#price").val());
            }
        );

    });

</script>
<script src="/js/products/ajaxProducts.js"></script>
<script src="/js/products/validateProduct.js"></script>

@endsection
