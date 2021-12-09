jQuery.validator.addMethod("check_warehouse", function (value, element) {
    let validated = false;
    if(warehouses){
        warehouses.forEach(function (item, i, types) {
            if (item.id == value) {
                validated = true;
            }
        });
    }
    return validated;
}, "Выберите склад из списка!");

jQuery.validator.addMethod("check_product", function (value, element) {
    let validated = false;
    if (products) {
        products.forEach(function (item, i, types) {
            if (item.id == value) {
                validated = true;
            }
        });
    }
    return validated;
}, "Выберите товар из списка!");

$("#transfer").validate({
    errorClass: "is-invalid",
    validClass: "is-valid",
    rules: {
        to_warehouse: {
            check_warehouse:true
        },
        from_warehouse: {
            check_warehouse:true
        },
        product: {
            check_product: true
        },
    },
    messages: {
        name: {
            required: "Укажите наименование!",
            maxlength: "Максимальная длина поля - 255 символов!"
        },
        price: {
            required: "Укажите цену!",
            number: "Цена - число!"
        },
    },
    ignore: ".ignore"
});