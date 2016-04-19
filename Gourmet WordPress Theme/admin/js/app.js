/**
 * Created by Igor on 3/23/15.
 */
function KHT_backEnd($, templateUrl) {

//todo: корректное клонирование чекбоксов
//todo: сброс чекбоксов при клонировании
    //Определяем селекторы, названия полей и прочее

    //текстовое поле
    var field = $('.field');
    //выпадающий список
    var select = $('select');
    //чекбокс
    var checkbox = $('.checkbox');
    //загрузка картинки
    var upload_button = $('.' + 'upload_image_button');

    //кнопка добавления
    var add_row_selector = 'add-row-button';
    var add_row_button = $('.' + add_row_selector);
    var add_row_postfix = 'add_row';

    //кнопка удаления
    var remove_row_selector = 'remove-row-button';
    var remove_row_button = $('.' + remove_row_selector);
    var remove_row_postfix = 'remove_row';

    var attr_prev = 'data-prev';


    //Запоминаем предыдущие настройки

    //собственно хэндлер
    function set_previous_value(el) {
        var value = el.val();

        el.attr(attr_prev, value);
    }

    //события

    //текстареа и инпут
    field.focus(function () {
        set_previous_value($(this));
    });

    //выпадающий список
    select.focus(function () {
        set_previous_value($(this));
    });

    //картинка
    upload_button.focus(function () {
        var field = $(this).siblings('input');
        set_previous_value(field);
    });


    //Проверяем предыдущие настройки и, если отличаются, то сохраняем

    //собственно хэндлеры
    function check_previous_value(el) {
        var attr = el.attr(attr_prev);

        var name = el.attr('name');
        var value = el.val();

        if (attr !== value) {
            save(name, value, el);
        } else {
            return; //('Element - ' + name + ': no changes');
        }
    }

    function save(name, value, el) {
        SavingBlock.action('start');

        var data = {
            action: 'save_setting',
            name: name,
            value: value
        };

        $.post(
            ajaxurl,
            data,
            function (response) {
                console.log(response);
                SavingBlock.action('stop');
                saved(el);
                //db_debug(name);
            }
        );
    }

    //события

    //селект
    select.change(function () {
        check_previous_value($(this));
    });


    //текстовые поля
    field.blur(function () {
        check_previous_value($(this));
    });

    //чекбокс
    checkbox.change(function () {
        var el = $(this);
        var value = el.val();
        if (value == '1' || value == '0') {
            switch (value) {
                case '0':
                    el.val(1);
                    el.attr('checked', true);
                    break;
                case '1':
                    el.val(0);
                    el.attr('checked', false);
                    break;
            }
            save(el.attr('name'), el.val(), el);
        }
    });

    //картинка
    upload_button.click(function (event) {
        //определяем поля для использования
        var element = $(this);
        var field = element.siblings('input');
        var link = element.siblings('a');
        var img = link.find('img');

        upload(event, field, img, link);
    });


    //загружает картинку
    function upload(event, field, image, link) {

        //объявляем загрузчик картинки
        var custom_uploader;

        //текст кнопки
        var text = 'Загрузить';

        var black_hole = templateUrl + '/img/min.jpg';

        event.preventDefault();

        //если уже открыт загрузчик, уходим
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }

        //Определяем загрузчик картинки
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: text,
            button: {
                text: text
            },
            multiple: false
        });

        //определяем действия после выбора картинки
        custom_uploader.on('select', function () {

            //хз, но по ходу берем выбранную картинку и после используем адрес этой картинки
            var attachment = custom_uploader.state().get('selection').first().toJSON();

            //меняем у поля, ссылки и картинки предосмотра аттрибуты и значения на загруженну картинку

            var url = attachment.url.trim() !== '' ? attachment.url.trim() : black_hole;

            field.val(url);
            link.attr('href', url);
            image.attr('src', url);


            //ну и собственно проверяем, изменилась ли картинка и сохраняем ее
            check_previous_value(field);
        });
        custom_uploader.open();
    }

    //блок сохранения
    var SavingBlock = {

        //необходимые селекторы
        selector: {
            wrap: 'KHT_save',
            text: 'text',
            icon: 'icon',
            whereAppend: '#wpadminbar'
        },

        //необходимый текст
        text: {
            init: 'Все ок :)',
            action: 'Сохраняю...'
        },

        //разметка, которую ьудем добавлять в шапку
        html: function (selectors, text) {
            return '<p id="' + selectors.wrap + '">' +
                '<span class="' + selectors.text + '">' + text + '</span>' +
                '<img class="icon" src="' + templateUrl + '/img/cube.svg" >' +
                '</p>';
        },

        //как раз изменяет состояния, с с"сохраняется", до "сохранено"
        action: function (status) {

            var sel = this.selector;
            var txt = this.text;

            switch (status) {
                case 'start':
                    sel.wrap.addClass('saving');
                    sel.text.text(txt.action);
                    sel.icon.fadeIn('fast');

                    break;

                case 'stop':
                    sel.text.text(txt.init);
                    sel.icon.fadeOut('slow');

                    setTimeout(function () {
                        sel.wrap.removeClass('saving');
                    }, 4000);
                    break;
                default :
                    //return console.log('saving block. action. no status');
                    break;
            }
        },

        init: function () {

            var sel = this.selector;
            var txt = this.text;

            $(this.html(sel, txt.init)).appendTo(sel.whereAppend);

            sel.wrap = $('#' + sel.wrap);
            sel.text = $('.' + sel.text);
            sel.icon = $('.' + sel.icon);
        }
    };
    SavingBlock.init();


    function saved(el) {
        //console.log('saved');
        var class_name = 'saved';
        el.addClass(class_name);
        setTimeout(function () {
            el.removeClass(class_name);
        }, 1000)
    }

    function change_settings(settings, operation) {
        //console.log('change_settings(settings - ' + settings + '; operation - ' + operation + ')');
        var remove = 'remove_settings';
        var add = 'add_settings';

        switch (operation) {
            case 'add':
                operation = add;
                break;
            case 'remove':
                operation = remove;
                break;
            default :
                return; //console.log('change_settings wrong operation for settings');
                break;
        }

        var data = {
            action: operation,
            settings: settings
        };

        console.log(data);

        $.post(
            ajaxurl,
            data,
            function (response) {
                //try {
                //    response = JSON.parse(response);
                //    console.log(response);
                //    //callback(response);
                //} catch (error) {
                //    console.warn('JSON parse error ' + error); //error in the above string(in this case,yes)!
                //}
                console.log(response);
                SavingBlock.action('stop');
                db_debug(settings);
            }
        );
    }

    function db_debug(key){
        var data = {
            action: 'debug_DB',
            key_debug: key
        };

        console.log(data);

        $.post(
            ajaxurl,
            data,
            function (response) {
                //try {
                //    response = JSON.parse(response);
                //    console.log(response);
                //    //callback(response);
                //} catch (error) {
                //    console.warn('JSON parse error ' + error); //error in the above string(in this case,yes)!
                //}
                var deb=$('.db_debug');
                deb.html(response);
                deb.addClass('active');
                setTimeout(function(){
                    deb.removeClass('active');
                },3000);
                //console.log(response);
            }
        );
    }

    function operation_button(id, button, operation) {

        //определяем какая кнопка, удаления или добавления
        switch (button) {
            case 'add':
                button = add_row_postfix;
                break;
            case 'remove':
                button = remove_row_postfix;
                break;
            default :
                return; //console.log('operation_button invalid button role');
        }

        //находим кнопку
        //console.log('operation_button( id+button: ' + id + button + '; operation: ' + operation + ')');
        button = $('#' + id + button);
        //Если указано скрыть кнопку, скрываем и собственно наоборот
        switch (operation) {
            case 'hide':
                //если кнопка еще не скрыта, скрываем
                    button.addClass('disable');
                    button.prop("disabled", true);
                break;
            case 'show':

                //если кнопка скрыта, показываем
                    button.removeClass('disable');
                    button.prop("disabled", false);
                break;
            default :
                console.warn('button fail! name: '+button+'; operation: '+operation);
                return;
                break;
        }

    }

    add_row_button.click(function (event) {
        event.preventDefault();

        //картинка для заглушки поля загрузки картинки
        var black_hole = templateUrl + '/img/min.jpg';

        //Показываем блок сохранения настроек
        SavingBlock.action('start');

        //берем родителя в котором надо добавить элемент
        var wrap = $(this).parent().parent().siblings('.max-wrap');

        //берем последний элемент, после которого надо добавить новый
        var element = wrap.children('div').last();

        //берем число максимума блоков, которое можно добавить
        var max = parseInt(wrap.attr('data-max'));

        //берем имя настроек, которое будет использоваться в качестве шаблона для добавления
        var name = wrap.attr('data-name');

        //берем индекс последнего элемента
        var index = parseInt(element.attr('data-index'));

        //создаем регулярное выражение для замены данных настроек в новом блоке
        var regex_find = '(' + name + index + ')';
        var regex = new RegExp(regex_find, 'g');

        //массив, в который будут добавляться элементы для регситрации настроек
        var to_register = [];


        //если индекс последнего элемента равен максимуму, то выходим, сообщив об этом
        if (index >= (max - 1)) {
            //console.log('past_index>=max');
            SavingBlock.action('stop');
            return;
        }

        //самое вкусное

        //делаем клон последнего элемента и добавляем его в конец
        element.clone(true).appendTo(wrap);

        //берем только что созданный последний элемент
        element = wrap.children('div').last();

        //увеличиваем индекс
        index = index + 1;

        //меняем индекс в атрибуте индекса, будем его использовать для замены заголовков и прочего
        element.attr('data-index', index);


        //очищаем дочерние блоки, в которых можно тоже добавлять блоки.
        //оставляем только первый блок в данном случае
        element.find('.max-wrap').each(function () {
            $(this).children('*:not(:first-child)').remove();
        });


        //ищем поля настроек по имени
        element.find('*[name*=' + name + ']').each(function () {
            var el = $(this);
            var el_name = el.attr('name');

            //заменяем меняем старое имя на новое ( по сути, меняем только индекс)
            el_name = el_name.replace(regex, name + index);

            //Заменяем аттрибут name и id на новый
            el.attr('name', el_name);
            el.attr('id', el_name);

            //Если был аттрибут disable убираем
            el.attr('disabled', false);

            //очищаем значение поля
            el.val('');


            if (el.is('select')) {
                var option = $(this).find('option');
                option.each(function (index, element) {
                    element = $(element);
                    if (index === 0) {
                        element.attr('selected', true);
                    } else {
                        element.attr('selected', false);
                    }
                });
            }

            //вставляем в массив эту настройку для регистрации настройки если это не кнопка, вашу мать! Запарился этот баг искать
            if (!el.hasClass(add_row_selector) && !el.hasClass(remove_row_selector)) {
                to_register.push(el_name);
            }

            if(el.hasClass('checkbox')){
                el.attr('checked',false);
                el.val('0');
            }

            if (el.hasClass(add_row_selector)){
                el.removeClass('disable');
                el.prop("disabled", false);
            }
            if (el.hasClass(remove_row_selector)) {
                el.addClass('disable');
                el.prop("disabled", true);
            }
        });

        //Заголовок, который содержит индекс блока
        var count_header = 'count-header';

        //ищем
        element.find('.' + count_header).each(function () {
            $(this).attr('class', $(this).attr('class').replace(regex, name + index));
        });

        element.find('*[for*=' + name + ']').each(function () {
            var el = $(this);
            el.attr('for', el.attr('for').replace(regex, name + index));
        });

        //меняем цифру в заголовке блока и в подписи к полю
        element.find('.' + name + count_header).text(index + 1);

        element.find('*[data-name*=' + name + ']').each(function () {
            $(this).attr('data-name', $(this).attr('data-name').replace(regex, name + index))
        });

        //если добавляем поле загрузки картинки, то очищаем ссылку и src картинки и убераем активный класс
        element.find('.fancybox').each(function () {
            var element = $(this);
            var img = element.find('img');

            element.attr('href', black_hole);
            img.attr('src', black_hole);
        });

        //если индекс блока достиг максимума, то прячем кнопку
        if (index >= (max - 1)) {
            operation_button(name, 'add', 'hide');
        }

        //регистрируем настройки
        //UPD: не надо регистрировать, так как при венесении изменениы в настройку, она сама регистрируется
        //UPD: это надо еще посмотреть
        //change_settings(to_register, 'add');

        //если блоков больше одного, то показываем кнопку удаления блока
        if (index !== 0) {
            operation_button(name, 'remove', 'show');
        }
        SavingBlock.action('stop');

    });

    remove_row_button.click(function (event) {
        event.preventDefault();

        //показываем анимацию сохранения
        SavingBlock.action('start');

        //ищем блок, в котором надо удалить дочерний блок
        var wrap = $(this).parent().parent().siblings('.max-wrap');

        //console.log(wrap);
        //определяем имя (идентификатор) поля и максимальное число блоков
        var name = wrap.attr('data-name');
        var max = parseInt(wrap.attr('data-max'));

        //находим последний элемент и его индекс
        var element = wrap.children('div').last();
        var index = parseInt(element.attr('data-index'));

        //берем название блока, чтобы удалить его и все дочерние настройки из БД
        var to_remove = name+index;

        //если элемент первый, то выходим из функции
        if (index === 0) {
            //console.log('index === 0');
            SavingBlock.action('stop');
            return;
        }

        element.remove();

        //если элемент остался один в списке, то скрываем кнопку удаления
        if (index === 1) {
            //console.log('index === 1; index = ' + index);
            operation_button(name, 'remove', 'hide');
        }

        //console.log(index);

        //берем новый "последний" элемент и его индекс после удаления предыдущего "последнего"
        element = wrap.children('div').last();
        index = parseInt(element.attr('data-index'));


        //если последний индекс меньше максимума, то выводим кнопку добавления строки
        if (index <= (max - 1)) {
            operation_button(name, 'add', 'show');
        }

        //console.log(to_remove);

        //функция удаление настроек
        change_settings(to_remove, 'remove');
    });

    //добавляем модальное окно для предосмотра картинок
    $(".fancybox").fancybox({
        arrows: false
    });

    //кнопку сохранения настроек прячем
    $('#' + 'submit').hide();

    //Первую ссылку в меню не даем заполнять, ибо это главная УРЛА
    $('.menu_url').first().find('input[type=text]').attr('disabled', true);
}

if (typeof pluginURL !== typeof undefined) {
    KHT_backEnd(jQuery, pluginURL)
}