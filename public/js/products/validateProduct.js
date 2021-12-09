jQuery.validator.addMethod("check_type", function (value, element) {
    let validated = false;
    types.forEach(function (item, i, types) {
        if (item.id == value) {
            validated = true;
        }
    });
    return validated;
}, "Выберите тип из списка!");

$("#products-form").validate({
    errorClass: "is-invalid",
    validClass: "is-valid",
    rules: {
        name: {
            required: true,
            maxlength:255
        },
        price: {
            required: true,
            number: true
        },
        type: {
            check_type: true
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