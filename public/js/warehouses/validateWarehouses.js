$("#warehouses-form").validate({
    errorClass: "is-invalid",
    validClass: "is-valid",
    rules: {
        name: {
            required: true,
            maxlength:255
        },
        adress: {
            required: true,
            maxlength: 255
        },
    },
    messages: {
        name: {
            required: "Укажите наименование!",
            maxlength: "Максимальная длина поля - 255 символов!"
        },
        adress: {
            required: "Укажите адрес!",
            maxlength: "Максимальная длина поля - 255 символов!"
        },
    },
    ignore: ".ignore"
});