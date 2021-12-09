function ajaxRequest(url, method, warehouse, items) {
    $.ajax({
        url: url,
        /* Куда пойдет запрос */
        method: method,
        dataType: 'json',
        /* Тип данных в ответе (xml, json, script, html). */
        data: {
            _token: csrf_token,
            warehouse,
            items
        },
        /* Параметры передаваемые в запросе. */
        success: function (data) {
            /* функция которая будет выполнена после успешного запроса. */
            let elementWarehouse = document.getElementById('warehouse');
            let elementErrorItems = document.getElementById('error_items');
            clearErrorStyle(elementWarehouse, elementErrorItems);
            if(data.success){
                $.toast({
                    heading: 'Успех',
                    text: data.success,
                    icon: 'success',
                })
                setTimeout(function () {
                    window.location.href = redirectLink;
                }, 1500); 
            }
            else if(data.error){
                button.disabled = false;
                let obj = data.error;
                let keysObj = Object.keys(obj);
                // Идём по ключам и выводим ошибки
                keysObj.forEach(function(value){
                    let elementError = document.createElement('label');
                    elementError.className = 'is-invalid';
                    switch(value){
                        case 'warehouse':{
                            elementWarehouse.classList.add('is-invalid');
                            elementError.id = 'warehouse-error';
                            elementError.setAttribute('for','warehouse');
                            elementError.textContent = obj.warehouse;
                            elementWarehouse.parentElement.appendChild(elementError);
                        }break;
                        case 'items':{
                            elementErrorItems.classList.add('is-invalid');
                            elementError.id = 'items-error';
                            elementError.setAttribute('for', 'items');
                            elementError.textContent = obj.items;
                            elementErrorItems.parentElement.appendChild(elementError);
                        }break;
                    }
                });
            }
        },
        error: function (e) {
            $.toast({
                heading: 'Ошибка',
                text: 'Ошибка при выполнении запроса',
                icon: 'error',
                loader: true,        
                loaderBg: '#9EC600'
            })
        }
    });
}
// Очистка от ошибок
function clearErrorStyle(elementWarehouse, elementErrorItems){
    let elementRemove = null;
   
    elementErrorItems.classList.remove("is-invalid");
    if (document.getElementById("warehouse-error")){
        elementWarehouse.classList.remove("is-invalid");
        elementRemove = document.getElementById("warehouse-error");
        if (elementRemove) {
            elementRemove.remove();
        }
    }
    elementRemove = document.getElementById("items-error");
    if (elementRemove) {
        elementRemove.remove();
    }
}