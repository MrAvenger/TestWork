jQuery.validator.addMethod("check_warehouse", function (value, element) {
    return checkSelectedWarehouse();
}, "Выберите доступный склад!");

$("#managing").validate({
    errorClass: "is-invalid",
    validClass: "is-valid",
    rules: {
        warehouse: {
            check_warehouse: true,
        },
    },
    ignore: ".ignore"
});