jQuery.validator.addMethod("check_status", function (value, element) {
    if (value == 'completed' || value == 'active' || value == 'canceled') {
        return true;
    }
    return false;
}, "Недопустимое значение!");

jQuery.validator.addMethod("check_phone", function (value, element) {
    let str = $("#phone").mask();
    console.log(str.length);
    if (str.length == 10) {
        return true;
    }
    return false;
}, "Номер телефона должен содержать 11 цифр!");

jQuery.validator.addMethod("check_type", function (value, element) {
    if (value == 'online' || value == 'offline') {
        return true;
    }
    return false;
}, "Недопустимое значение!");


jQuery.validator.addMethod("check_user", function (value, element) {
    let validatedUser = false;
    if (value != 'select') {
        users.forEach(function (user, i, users) {
            if (user.id == value) {
                validatedUser = true;
            }
        });

    }
    return validatedUser;
}, "Такого пользователя нет!");

$("#orders-form").validate({
    errorClass: "is-invalid",
    validClass: "is-valid",
    rules: {
        customer: {
            required: true,
            minlength: 2,
            maxlength:255
        },
        phone: {
            check_phone: true,
        },
        type: {
            check_type: true
        },
        status: {
            check_status: true
        },
        user_id: {
            check_user: true
        }
    },
    messages: {
        customer: {
            required: "Укажите клиента!",
            minlength: "Минимальная длина поля - 2 символа!",
            maxlength: "Максимальная длина поля - 255 символов!"
        },
        phone: {
            check_phone: "Номер телефона должен содержать 11 цифр!"
        },
    },
    ignore: ".ignore"
});