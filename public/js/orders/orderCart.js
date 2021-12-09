let cart = new Map(); //Корзина
let currentProduct = undefined; //Текущий товар
$(document).ready(function () {
    getOutput();
    setCorrectValuesForSelect(); // Выбран ли продукт для добавления
    $('input.float').on('input', function () {
        this.value = parseFloat(this.value).toFixed(2); // Приводим к flaot с двумся знаками после точки
        if (checkSelectedProduct()){
            checkMaxDiscount($("#product").val(), $("#discount").val(), 'new'); // Проверка на макс скидку
        }
        else{
            disableNewOrderFields(); // Отключение полей
        }
    });

    $('input.count').on('input', function () {
        this.value = parseInt(this.value);
        if (checkSelectedProduct()) {
            checkMaxCount($("#product").val(), $("#count").val(), 'new'); // Проверка макс кол-ва
        }
        else {
            disableNewOrderFields(); // Отключение полей
        }
    });

    $("#product").on('change',function(){
        setCorrectValuesForSelect(); // Проверяем поле
    })

});
// Загрузка корзины (если есть эелементы у заказа)
function loadCart() {
    if (savedProducts){
        // Пройдёмся по массиву
        savedProducts.forEach(function(product){
            let pivot = product.pivot; //Извлечём объект с нужными ключ-значениями
            delete pivot.order_id; //Удаляем ненужный ключ
            delete pivot.product_id; //Удаляем ненужный ключ
            delete product.pivot; //Удаляем ненужный ключ
            let newObject = Object.assign(product, pivot); //Объединяем объекты в один
            cart.set(newObject.id, newObject); //Добавляем в корзину
            addRow("products_list", newObject, newObject.id); //Создаём строку в таблице
        });
    }
}
// Добавление в корзину
function addToCart() {
    if (checkSelectedProduct()){
        // Если количество указано и больше 0
        if ($("#count").val() > 0) {
            let id = Number(product.value); // Приводим к числу
            getProduct(id) //Получаем товар из сохранённых данных
            // Если товар есть
            if (currentProduct != undefined) {
                // Если в корзине нет такого товара
                if (!cart.has(id)) {
                    // Максимальное количество (проверка на допустимое кол-во)
                    if (checkMaxCount(id, $("#count").val(), 'new')) {
                        // Создаём новый объект
                        let item = {
                            'id': id,
                            'name': currentProduct.name,
                            'count': Number($("#count").val()),
                            'price': Number(currentProduct.price),
                            'cost': setCartItemResult(currentProduct.price, Number($("#count").val()), Number($("#discount").val())),
                            'discount': Number($("#discount").val()),
                            'stock': Number(currentProduct.stock)
                        };
                        cart.set(Number(id), item); // Записываем его в корзину
                        addRow("products_list", item, id); // Добавляем строку с данными в таблицу корзины
                        getOutput();
                    }
                    else {
                        // Уведомление о недопустимом количестве (при попытке добавить в корзину)
                        $.toast({
                            heading: 'Ошибка'
                            , text: 'Недопустимое количество!'
                            , showHideTransition: 'fade'
                            , icon: 'error'
                        });
                    }
                }
                // Если всё же в корзине уже есть такой товар
                else if (cart.has(id)) {
                    let current = cart.get(id); // Получаем элемент корзины
                    let newCount = Number(current.count) + Number($("#count").val()); // Обновляем только количество, скидку не трогаем (можно изменить в корзине)
                    // Проверяем максимальное кол-во
                    if (checkMaxCount(id, newCount, 'old')) {
                        current.count = newCount; // Устанавливаем макс кол-во
                        cart.set(id, current); // Обновляем данные в корзине
                        updateCartItem(current.id); // Обновляем элемент корзины в таблице
                        getOutput();
                    }
                    else {
                        // Уведомление если недопустимое кол-во
                        $.toast({
                            heading: 'Ошибка'
                            , text: 'Недопустимое количество!'
                            , showHideTransition: 'fade'
                            , icon: 'error'
                        });
                    }
                }
            }
            else {
                // Уведомление если невыбран товар
                $.toast({
                    heading: 'Ошибка'
                    , text: 'Выберите товар!'
                    , showHideTransition: 'fade'
                    , icon: 'error'
                });
            }

        } else {
            // Уведомление если недопустимое кол-во (неуказано)
            $.toast({
                heading: 'Ошибка'
                , text: 'Укажите количество!'
                , showHideTransition: 'fade'
                , icon: 'error'
            });
        }
    }
    else{
        // Если невыбран товар
        $.toast({
            heading: 'Ошибка'
            , text: 'Укажите товар!'
            , showHideTransition: 'fade'
            , icon: 'error'
        });
    }

}
// Удаление строки
function removeElement(row, id) {
    let rowElement = document.getElementById(row); // Получаем строку
    rowElement.parentNode.removeChild(rowElement); // Удаляем строку
    cart.delete(id); // Удаляем из корзины
    getOutput();
}
// Обновление элеммента корзины
function updateCartItem(id){
    let currentCartItem = cart.get(id); // Текущий элемент корзины
    let discount = 0; // Скидка 0 по умолчанию
    if (currentCartItem.discount != undefined){
        discount = currentCartItem.discount; // Получаем скидку из элемента корзины
    }
    if (setCartItemResult(currentCartItem.price, currentCartItem.count, discount) > 0) {
        currentCartItem.cost = setCartItemResult(currentCartItem.price, currentCartItem.count, discount); // Устанавливаем стоимость (итого)
        $("#cost-item-" + currentCartItem.id).html(currentCartItem.cost); // Выводим итог по элементу корзины
        $("#discount-item-" + currentCartItem.id).val(currentCartItem.discount); // Выводим значение скидки элемента корзины
        $("#count-item-" + currentCartItem.id).val(currentCartItem.count); // Выводим значение кол-ва элемента корзины
        cart.set(currentCartItem.id, currentCartItem); // Обновляем элемент корзины
    }
    else{
        $.toast({
            heading: 'Ошибка'
            , text: 'Скидка слишком большая для такого количества, либо введено некорректное значение!'
            , showHideTransition: 'fade'
            , icon: 'error'
        });
        $("#discount-item-" + currentCartItem.id).val(0);
    }
}
// Добавление строки
function addRow(id, item, key) {
    let rowPref = 'cart-item-' + key; // Формирование id для строки
    let t = document.getElementById(id); // Получаем tbody таблицы корзины
    let newRow = t.insertRow(0); // Добавляем строку
    newRow.id = rowPref; //Установка id для строки
    let newCell = newRow.insertCell(0); // Добавление столбца (Номер строки)
    newCell.innerHTML = cart.size; // Устанавливаем значение в столбец (указываем номер строки используя размер корзины)
    newCell = newRow.insertCell(1); // Новый столбец (Наименование товара)
    newCell.innerHTML = item.name; // Устанавливаем значение в столбец
    newCell = newRow.insertCell(2); // Добавление столбца (Количество)
    let inputCount = document.createElement('input'); // Поле ввода количества
    inputCount.type = 'number'; // Указываем тип поля
    inputCount.id = 'count-item-' + key; // Указываем id поля
    inputCount.setAttribute('class', 'form-control ignore'); //Устанавливаем аттрибут
    inputCount.setAttribute('min', 1); //Устанавливаем аттрибут
    // Устанавливаем событие oninput
    inputCount.oninput = function () {
        $(this).val(parseInt($(this).val()));   // Только int
        //Если превышено макс кол-во - изменяем значение
        if (!checkMaxCount(item.id, $(this).val(), 'old')){
            $(this).val(1);
        }
        setCurrentCountCartItem(item.id, $(this).val()); //Обновляем кол-во объекта
        updateCartItem(item.id) //Обновляем внешние данные
    }
    newCell.appendChild(inputCount); //Вставляем в столбец
    let priceOne = document.createElement('b'); // Создали элемент b
    priceOne.id = "price-item-"+item.id; // Установили ид элементу
    priceOne.textContent = item.price; // Вставили количество за штуку (текст)
    newCell = newRow.insertCell(3); // Добавление столбца (Цена за 1 ед)
    newCell.appendChild(priceOne); //Вставляем в столбец
    newCell = newRow.insertCell(4); // Добавление столбца (Скидка)
    let inputDiscount = document.createElement('input'); // Поле ввода скидки
    inputDiscount.type = 'text'; // Указываем тип поля (можно было бы поставить number, но при ручном вводе float числа, значение сбросится)
    inputDiscount.id = 'discount-item-' + key; // Устанавливаем id
    inputDiscount.setAttribute('class', 'form-control ignore');// Устанавливаем аттрибут
    inputDiscount.setAttribute('min', 0);   // Устанавливаем аттрибут
     // Устанавливаем событие oninput
    inputDiscount.oninput = function () {
        $(this).val(parseFloat($(this).val()).toFixed(2)); // Можно к Float привести и регул.выражением, но решил сделать так
        if (!checkMaxDiscount(item.id, $(this).val(), 'old')) {
            $(this).val(1); // Если превышен лимит 1 проц.
        }
        setCurrentDiscountCartItem(item.id, $(this).val()); // Обновляем скидку элемента корзины
        updateCartItem(item.id); // Обноавляем значения таблицы
    }
    newCell.appendChild(inputDiscount); // Вставляем поле ввода в столбец
    newCell = newRow.insertCell(5); // Добавление столбца (Стоимость)
    let cost = document.createElement('b'); // Создание элемента b
    cost.id = "cost-item-" + item.id; // Установка id элементу
    cost.textContent = item.cost; // Добавляем текстовый контент
    newCell.appendChild(cost); // Вставляем в столбец элемент
    newCell = newRow.insertCell(6); // Добавление столбца (Действие)
    let deleteLink = document.createElement('a'); // Ссылка для удаления
    // Функция нажатия на ссылку
    deleteLink.onclick = function () { 
        removeElement(rowPref, key); // Обновляем строку
    };
    deleteLink.textContent = 'Удалить'; // Текст ссылки
    deleteLink.setAttribute('href', '#' + rowPref); // Атрибут адреса ссылки
    deleteLink.setAttribute('class', 'btn btn-danger'); // Устанавливаем класс
    newCell.appendChild(deleteLink); // Вставка ссылки в столбец
    updateCartItem(item.id) // Обновляем элемент в корзине (внеш.вид)
}
// Получаем товар
function getProduct(id) {
    currentProduct = undefined; // Сбрасываем текущий товар
    // Цикл по товарам
    products.forEach(function (product) {
        if (product.id == id) {
            currentProduct = product; //Устанавливаем товар, который имеет нужный id
        }
    });
}
// Считаем результат
function setCartItemResult(price, count, discount) {
    if (isNaN(discount) || !discount) {
        discount = 0.00;
    }
    let result = 0;
    if (result <= 0) {
        result = (price * count) - ((discount*price*count)/100); // price, count - обычные значения. discount - проценты
        return result;   // Возвращаем результат
    }
    return false;
}
// Обновление элемента корзины (обновление кол-ва)
function setCurrentCountCartItem(id,count){
    let currentCartItem = cart.get(id); // Получаем элемент корзины
    currentCartItem.count = count; // Устанавливаем значение
    cart.set(id, currentCartItem); // Обновляем элемент корзины
}
// Обновление элемента корзины (обновление скидки)
function setCurrentDiscountCartItem(id, discount) {
    let currentCartItem = cart.get(id); // Берём элемент корзины
    currentCartItem.discount = discount; // Устанавливаем скидку
    cart.set(id, currentCartItem); // Сохраняем
}
// Проверка на допустимое значение кол-ва (устанавливаем корректное знач)
function checkMaxCount(id,count,type) {
    let res = true;
    let value = 0;
    switch (type) {
        case 'new': {
            getProduct(id);
            if ((currentProduct.stock <= count) || (count <=0)) {
                $("#count").val(currentProduct.stock);
                res = false;
            }
            if(!res){
                $.toast({
                    heading: 'Уведомление'
                    , text: 'Количество было изменено на допустимое значение'
                    , showHideTransition: 'fade'
                    , icon: 'info'
                });
            } 
            value = (currentProduct.price * count) - (($("#discount").val() * count * currentProduct.price) / 100);
            $("#cost").val(value);
            getOutput();
            return true;
        } break;
        case 'old': {
            let currentCartItem = cart.get(id);
            if ((currentCartItem.stock < count) || (count <= 0)) {
                $("#count-item-" + currentCartItem.id).val(currentCartItem.stock);
                res = false;
            }
            if (!res) {
                $.toast({
                    heading: 'Уведомление'
                    , text: 'Количество было изменено на допустимое значение'
                    , showHideTransition: 'fade'
                    , icon: 'info'
                });
            }
            value = (currentCartItem.price * count) - (($("#discount-item-" + currentCartItem.id).val() * currentCartItem.price * count));
            $("#cost-item-" + currentCartItem.id).val(value);
            getOutput();
            return true;
        } break;
    }
}
// Проверка на допустимое значение скидки (устанавливаем корректное знач)
function checkMaxDiscount(id, discount, type) {
    let res = true;
    let value = 0;
    let rigth_discount = 0; // Правильная скидка здесь - 40% от цены и не больше (для того чтобы не уйти в минус )
    switch (type) {
        case 'new': {
            getProduct(id);
            rigth_discount = (currentProduct.price * $("#count").val()) * 0.4;
            if ((currentProduct.price * $("#count").val()) <= rigth_discount || 40 <= discount) {
                discount = 40;
                $("#discount").val(discount);
                res = false;
            }
            if(!res){
                $.toast({
                    heading: 'Уведомление'
                    , text: 'Скидка была изменена на допустимое значение. Максимальный размер скидки 40%'
                    , showHideTransition: 'fade'
                    , icon: 'info'
                });
            }
            value = (currentProduct.price * $("#count").val()) - ((discount * currentProduct.price * $("#count").val())/100);
            $("#cost").val(value);
            getOutput();
            return true;
        } break;
        case 'old': {
            let currentCartItem = cart.get(id);
            rigth_discount = (currentCartItem.price * $("#count-item-" + currentCartItem.id).val()) * 0.4;
            if ((currentCartItem.price * $("#count-item-" + currentCartItem.id).val()) <= rigth_discount || 40 <= discount) {
                discount = 40;
                $("#discount-item-"+currentCartItem.id).val(discount);
                setCurrentDiscountCartItem(currentCartItem.id, discount);
                res = false;
            }
            if (!res) {
                $.toast({
                    heading: 'Уведомление'
                    , text: 'Скидка была изменена на допустимое значение. Максимальный размер скидки 40%'
                    , showHideTransition: 'fade'
                    , icon: 'info'
                });
            }
            value = (currentCartItem.price * $("#count-item-" + currentCartItem.id).val()) - discount;
            $("#cost-item-" + currentCartItem.id).val(value);
            getOutput();
            return true;
        } break;
    }
}
// Проверяем выбранный продукт
function checkSelectedProduct(){
    let returned = false; // Возвращаемое значение
    // Проходим по циклу товаров
    products.forEach(function(product_item){
        if (product_item.id == product.value){
            returned = true;
        }
    });
    return returned;
}
// Проверяем поля по изменению select
function setCorrectValuesForSelect(){
    if (checkSelectedProduct()) {
        enableNewOrderFields(); // Включаем поля
        checkMaxCount($("#product").val(), $("#count").val(), 'new'); // Проверка на допустимое значение кол-ва (устанавливаем корректное знач)
        checkMaxDiscount($("#product").val(), $("#discount").val(), 'new'); // Проверка на допустимое значение скидки (устанавливаем корректное знач)
    }
    else {
        disableNewOrderFields(); // Отключаем поля
    }
}
// Отключение полей
function disableNewOrderFields(){
    count.disabled = true;
    discount.disabled = true;
    $("#discount").val('');
    $("#count").val('');
    $("#cost").val('');
}
// Включение полей
function enableNewOrderFields() {
    count.disabled = false; // Включаем поле кол-ва
    discount.disabled = false; // Включаем поле кол-ва скидки
    $("#count").val(1); // По умолчанию поставим знач 1
    $("#discount").val(0); // По умолчанию поставим знач 0
}
// Вывод итога (общий)
function getOutput(){
    let totalCart  = 0;
    cart.forEach(item => {
        totalCart += item.cost;
    });
    $("#totalCart").html("Итоговая стоимость: "+totalCart);
}