function ajaxRequest(url, method, customer, clear_phone, type, status, user_id, items) {
    $.ajax({
        url: url,
        /* Куда пойдет запрос */
        method: method,
        /* Метод передачи  */
        dataType: 'json',
        /* Тип данных в ответе (xml, json, script, html). */
        data: {
            _token: $('input[name=_token]').val(),
            customer,
            phone: $("#phone").val(),
            clear_phone,
            type,
            status,
            user_id,
            items
        },
        /* Параметры передаваемые в запросе. */
        success: function (data) {
            /* функция которая будет выполнена после успешного запроса. */
            let elementCustomer = document.getElementById('customer');
            let elementClearPhone = document.getElementById('phone');
            let elementType = document.getElementById('type');
            let elementStatus = document.getElementById('status');
            let elementUserId = document.getElementById('user');
            let elementErrorItems = document.getElementById('error_items');
            clearErrorStyle(elementCustomer, elementClearPhone, elementType, elementStatus, elementUserId, elementErrorItems);
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
                // Если ошибки то выведем их к нужным полям
                button.disabled = false;
                let obj = data.error;
                let keysObj = Object.keys(obj);
                keysObj.forEach(function(value){
                    let elementError = document.createElement('label');
                    elementError.className = 'is-invalid';
                    switch(value){
                        case 'customer':{
                            elementCustomer.classList.add('is-invalid');
                            elementError.id = 'customer-error';
                            elementError.setAttribute('for','customer');
                            elementError.textContent = obj.customer;
                            elementCustomer.parentElement.appendChild(elementError);
                        }break;
                        case 'clear_phone':{
                            elementClearPhone.classList.add('is-invalid');
                            elementError.id = 'phone-error';
                            elementError.setAttribute('for', 'phone');
                            elementError.textContent = obj.clear_phone;
                            elementClearPhone.parentElement.appendChild(elementError);
                        }break;
                        case 'type':{
                            elementType.classList.add('is-invalid');
                            elementError.id = 'type-error';
                            elementError.setAttribute('for', 'type');
                            elementError.textContent = obj.type;
                            elementType.parentElement.appendChild(elementError);
                        }break;
                        case 'status':{
                            elementStatus.classList.add('is-invalid');
                            elementError.id = 'status-error';
                            elementError.setAttribute('for', 'status');
                            elementError.textContent = obj.status;
                            elementStatus.parentElement.appendChild(elementError);
                        }break;
                        case 'user_id': {
                            elementUserId.classList.add('is-invalid');
                            elementError.id = 'user-error';
                            elementError.setAttribute('for', 'user');
                            elementError.textContent = obj.user_id;
                            elementUserId.parentElement.appendChild(elementError);
                        } break;
                        case 'items': {
                            elementError.classList.add('is-invalid');
                            elementError.id = 'items-error';
                            elementError.setAttribute('for', 'items');
                            elementError.textContent = obj.items;
                            elementErrorItems.appendChild(elementError);
                        } break;
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

function clearErrorStyle(elementCustomer, elementClearPhone, elementType, elementStatus, elementUserId, elementErrorItems){
    // Здесь происходит очистка от ошибок
    let elementRemove = null;
    elementCustomer.classList.remove("is-invalid");
    elementClearPhone.classList.remove("is-invalid");
    elementType.classList.remove("is-invalid");
    elementStatus.classList.remove("is-invalid");
    elementUserId.classList.remove("is-invalid");
    elementErrorItems.classList.remove("is-invalid");
    elementRemove = document.getElementById("customer-error");
    if(elementRemove){
        elementRemove.remove();
    }
    elementRemove = document.getElementById("phone-error");
    if (elementRemove) {
        elementRemove.remove();
    }
    elementRemove = document.getElementById("type-error");
    if (elementRemove) {
        elementRemove.remove();
    }
    elementRemove = document.getElementById("status-error");
    if (elementRemove) {
        elementRemove.remove();
    }
    elementRemove = document.getElementById("user-error");
    if (elementRemove) {
        elementRemove.remove();
    }
    elementRemove = document.getElementById("items-error");
    if (elementRemove) {
        elementRemove.remove();
    }
}