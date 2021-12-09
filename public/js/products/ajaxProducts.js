function ajaxRequest(url, method, name, type, price) {
    $.ajax({
        url: url,
        /* Куда пойдет запрос */
        method: method,
        dataType: 'json',
        /* Тип данных в ответе (xml, json, script, html). */
        data: {
            _token: $('input[name=_token]').val(),
            name: name,
            type_id:type,
            price,
        },
        /* Параметры передаваемые в запросе. */
        success: function (data) {
            /* функция которая будет выполнена после успешного запроса. */
            let elementName = document.getElementById('name');
            let elementType = document.getElementById('type');
            let elementPrice = document.getElementById('price');
            clearErrorStyle(elementName, elementType, elementPrice);
            if(data.success){
                button.disabled = true;
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
                let obj = data.error;
                console.log(obj = data.error);
                let keysObj = Object.keys(obj);
                keysObj.forEach(function(value){
                    let elementError = document.createElement('label');
                    elementError.className = 'is-invalid';

                    switch(value){
                        case 'name':{
                            elementName.classList.add('is-invalid');
                            elementError.id = 'name-error';
                            elementError.setAttribute('for','name');
                            elementError.textContent = obj.name;
                            elementName.parentElement.appendChild(elementError);
                        }break;
                        case 'type_id':{
                            elementType.classList.add('is-invalid');
                            elementError.id = 'type-error';
                            elementError.setAttribute('for', 'type');
                            elementError.textContent = obj.type_id;
                            elementType.parentElement.appendChild(elementError);
                        }break;
                        case 'price':{
                            elementPrice.classList.add('is-invalid');
                            elementError.id = 'price-error';
                            elementError.setAttribute('for', 'price');
                            elementError.textContent = obj.price;
                            elementPrice.parentElement.appendChild(elementError);
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

function clearErrorStyle(elementName, elementType, elementPrice){
    let elementRemove = null;
    elementName.classList.remove("is-invalid");
    elementType.classList.remove("is-invalid");
    elementPrice.classList.remove("is-invalid");
    elementRemove = document.getElementById("name-error");
    if(elementRemove){
        elementRemove.remove();
    }
    elementRemove = document.getElementById("type-error");
    if (elementRemove) {
        elementRemove.remove();
    }
    elementRemove = document.getElementById("price-error");
    if (elementRemove) {
        elementRemove.remove();
    }
}