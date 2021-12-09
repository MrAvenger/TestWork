let storage = new Map(); //Хранилище
let currentProduct = undefined; //Текущий товар
$(document).ready(function () {
    checkSelects(); // Выбран ли товар для добавления
    $('#count').on('input', function () {
        this.value = parseInt(this.value);
        if(isNaN(this.value)){
            this.value = 1;
        }
        checkSelects();
    });

    $("#product").on('change',function(){
        checkSelects(); // Проверяем поле
    });

    $("#warehouse").on('change', function () {
        checkSelects(); // Проверяем поле
    });

    $("#add_product").on("click", function () {
        addToStorage();
    });

});
// Загрузка хранилища (если есть элементы)
function loadStorage() {
    if (savedProducts){
        // Пройдёмся по массиву
        savedProducts.forEach(function(product){
            let pivot = product.pivot; //Извлечём объект с нужными ключ-значениями
            delete product.pivot; //Удаляем ненужный ключ
            let newObject = Object.assign(product, pivot); //Объединяем объекты в один
            storage.set(newObject.id, newObject); //Добавляем в хранилище
            addRow("products_list", newObject, newObject.id); //Создаём строку в таблице
        });
    }
}
// Добавление в хранилище
function addToStorage() {
    if (checkSelectedWarehouse()){
        if (checkSelectedProduct()){
            // Если количество указано и больше 0
            if ($("#count").val() > 0) {
                let id = Number($("#product").val()); // Приводим к числу
                getProduct(id) //Получаем товар из сохранённых данных
                // Если товар есть
                if (currentProduct != undefined) {
                    // Если в хранилище нет такого товара
                    if (!storage.has(id)) {
                        // Создаём новый объект
                        let item = {
                            'id': id,
                            'name': currentProduct.name,
                            'count': Number($("#count").val()),
                        };
                        storage.set(Number(id), item); // Записываем его в хранилище
                        addRow("products_list", item, id); // Добавляем строку с данными в таблицу
                    }
                    // Если всё же в хранилище уже есть такой товар
                    else if (storage.has(id)) {
                        let current = storage.get(id); // Получаем элемент хранилища
                        let newCount = Number(current.count) + Number($("#count").val()); // Обновляем только количество
                        current.count = newCount; // Устанавливаем макс кол-во
                        updateStorageItem(current.id); // Обновляем элемент хранилища в таблице
                    }
                }
                else {
                    // Уведомление если невыбран товар
                    $.toast({
                        heading: 'Ошибка'
                        , text: 'Такого товара не существует!'
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
        else {
            // Если невыбран товар
            $.toast({
                heading: 'Ошибка'
                , text: 'Укажите товар!'
                , showHideTransition: 'fade'
                , icon: 'error'
            });
        }
        
    }
    else{
        // Если невыбран товар
        $.toast({
            heading: 'Ошибка'
            , text: 'Укажите склад!'
            , showHideTransition: 'fade'
            , icon: 'error'
        });
    }

}
// Удаление строки
function removeElement(row, id) {
    let rowElement = document.getElementById(row); // Получаем строку
    rowElement.parentNode.removeChild(rowElement); // Удаляем строку
    storage.delete(id); // Удаляем из хранилища
}
// Обновление элеммента хранилища
function updateStorageItem(id){
    let currentStorageItem = storage.get(id); // Текущий элемент хранилища
    $("#count-item-" + currentStorageItem.id).val(currentStorageItem.count); // Выводим значение кол-ва элемента хранилища
    storage.set(currentStorageItem.id, currentStorageItem); // Обновляем элемент хранилища
}
// Добавление строки
function addRow(id, item, key) {
    let rowPref = 'storage-item-' + key; // Формирование id для строки
    let t = document.getElementById(id); // Получаем tbody таблицы хранилища
    let newRow = t.insertRow(0); // Добавляем строку
    newRow.id = rowPref; //Установка id для строки
    let newCell = newRow.insertCell(0); // Добавление столбца (Наименование товара)
    newCell.innerHTML = item.name; // Устанавливаем значение в столбец (указываем номер строки используя размер хранилища)
    newCell = newRow.insertCell(1); // Добавление столбца (Количество)
    let inputCount = document.createElement('input'); // Поле ввода количества
    inputCount.type = 'number'; // Указываем тип поля
    inputCount.id = 'count-item-' + key; // Указываем id поля
    inputCount.setAttribute('class', 'form-control ignore'); //Устанавливаем аттрибут
    inputCount.setAttribute('min', 1); //Устанавливаем аттрибут
    // Устанавливаем событие oninput
    inputCount.oninput = function () {
        $(this).val(parseInt($(this).val()));   // Только int
        if (!$(this).val()){
            $(this).val(1);
        }
        setCurrentCountStorageItem(item.id, $(this).val()); //Обновляем кол-во объекта
        updateStorageItem(item.id) //Обновляем внешние данные
    }
    newCell.appendChild(inputCount); //Вставляем в столбец
    newCell = newRow.insertCell(2); // Добавление столбца (Действие)
    let deleteLink = document.createElement('a'); // Ссылка для удаления
    // Функция нажатия на ссылку
    deleteLink.onclick = function () { 
        removeElement(rowPref, key); // Обновляем строку
    };
    deleteLink.textContent = 'Удалить'; // Текст ссылки
    deleteLink.setAttribute('href', '#' + rowPref); // Атрибут адреса ссылки
    deleteLink.setAttribute('class', 'btn btn-danger'); // Устанавливаем класс
    newCell.appendChild(deleteLink); // Вставка ссылки в столбец
    updateStorageItem(item.id) // Обновляем элемент в хранилище (внеш.вид)
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
// Обновление элемента хранилища (обновление кол-ва)
function setCurrentCountStorageItem(id,count){
    let currentStorageItem = storage.get(id); // Получаем элемент хранилища
    currentStorageItem.count = count; // Устанавливаем значение
    storage.set(id, currentStorageItem); // Обновляем элемент хранилища
}
// Проверяем выбранный товар
function checkSelectedProduct(){
    let returned = false; // Возвращаемое значение
    // Проходим по циклу товаров
    products.forEach(function(product_item){
        if (product_item.id == $("#product").val()){
            returned = true;
        }
    });
    return returned;
}
// Проверяем выбранный склад
function checkSelectedWarehouse() {
    let returned = false; // Возвращаемое значение
    // Проходим по циклу товаров
    if (document.getElementById("warehouse")){
        warehouses.forEach(function (item) {
            if (item.id == $("#warehouse").val()) {
                returned = true;
                console.log(item.id + '|' + $("#warehouse").val());
            }
        });
    }
    else{
        return true;
    }
    return returned;
}
// Проверяем поля по изменению select
function checkSelects(){
    let selectedP = document.getElementById('product');
    let countInput = document.getElementById('count');
    if (!checkSelectedProduct()){
        countInput.disabled = true;
        $("#count").val('');
    }
    else{
        countInput.disabled = false;
    }
    if (!checkSelectedWarehouse()){
        selectedP.disabled = true;
    }
    else{
        selectedP.disabled = false;
    }
}