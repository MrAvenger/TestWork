function ajaxRequest(url, method, name, adress) {
    $.ajax({
        url: url,
        /* Куда пойдет запрос */
        method: method,
        dataType: 'json',
        /* Тип данных в ответе (xml, json, script, html). */
        data: {
            _token: $('input[name=_token]').val(),
            name,
            adress,
        },
        /* Параметры передаваемые в запросе. */
        success: function (data) {
            /* функция которая будет выполнена после успешного запроса. */
            let elementName = document.getElementById('name');
            let elementAdress = document.getElementById('adress');
            clearErrorStyle(elementName, elementAdress);
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
                        case 'adress':{
                            elementAdress.classList.add('is-invalid');
                            elementError.id = 'adress-error';
                            elementError.setAttribute('for', 'adress');
                            elementError.textContent = obj.adress;
                            elementAdress.parentElement.appendChild(elementError);
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

function clearErrorStyle(elementName, elementAdress){
    let elementRemove = null;
    elementName.classList.remove("is-invalid");
    elementAdress.classList.remove("is-invalid");
    elementRemove = document.getElementById("name-error");
    if(elementRemove){
        elementRemove.remove();
    }
    elementRemove = document.getElementById("adress-error");
    if (elementRemove) {
        elementRemove.remove();
    }
}