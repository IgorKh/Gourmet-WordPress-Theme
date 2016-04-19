<?php

    //Загружаем скрипты и стили в админку
    add_action('admin_enqueue_scripts', 'registerScripts');
    function registerScripts()
    {
        wp_enqueue_media();

        wp_enqueue_style('KHT_backEnd_fancyBox_style', get_template_directory_uri().'/admin/fancyBox/jquery.fancybox.css');
        wp_enqueue_script('KHT_backEnd_fancyBox_script', get_template_directory_uri().'/admin/fancyBox/jquery.fancybox.pack.js', array(), false, true);
        wp_enqueue_style('KHT_backEnd_style', get_template_directory_uri().'/admin/css/styles.css');
        wp_enqueue_script('KHT_backEnd_script', get_template_directory_uri().'/admin/js/app.js', array('jquery'), false, true);
    }


    //Даем разрешение загружать SVG
    add_filter('upload_mimes', 'allow_svg');
    function allow_svg($mimes)
    {
        $mimes['svg'] = 'image/svg+xml';

        return $mimes;
    }

    //AJAX хэндлер: обновляет данные из БД для дебага
    add_action('wp_ajax_debug_DB', 'AJAX_admin_debug_DB');
    function AJAX_admin_debug_DB()
    {
        if(!isset($_POST['key_debug'])){
            echo 'no key debug';
            return;
        }

        $keys = explode(KHT_BackEnd::$delimiter, $_POST['key_debug']);

        $prefix = array_shift($keys);
        echo'<pre>';
        print_r(KHT_BackEnd::db_get($prefix));
        echo'</pre>';
        wp_die();
    }

    ////AJAX хэндлер: добавить настройку
    //add_action('wp_ajax_add_settings', 'AJAX_admin_add_settings');
    //function AJAX_admin_add_settings()
    //{
    //    if (!isset($_POST['settings'])) {
    //        echo KHT_BackEnd::$message['error']['!isset'] . ' ' . __FUNCTION__;
    //    }
    //    foreach ($_POST['settings'] as $id) {
    //
    //        KHT_BackEnd::option($id, 'add');
    //
    //        echo KHT_BackEnd::$message['setting']['add'] . ' ' . $id;
    //    }
    //    wp_die();
    //}

    //AJAX хэндлер: удалить настройку
    add_action('wp_ajax_remove_settings', 'AJAX_admin_remove_settings');
    function AJAX_admin_remove_settings()
    {
        //if (isset($_REQUEST['KHT_updating_DB'])) {
        //    echo 'WARNING!!! DB updating. Exit';
        //    wp_die();
        //
        //    return;
        //} else {
        //    echo 'AJAX_admin_remove_settings. DB is not updating?' . PHP_EOL;
        //}

        if (!isset($_POST['settings'])) {
            echo KHT_BackEnd::$message['error']['!isset'] . ' ' . __FUNCTION__;
        }

        //echo 'AJAX_admin_remove_settings. !!!START!!!' . PHP_EOL;
        KHT_BackEnd::option($_POST['settings'], 'remove');
        //echo 'AJAX_admin_remove_settings. !!!STOP!!!' . PHP_EOL;

        //echo KHT_BackEnd::$message['setting']['remove'] . ' ' . $_POST['settings'];
        wp_die();
    }

    //AJAX хэндлер: сохранить настройку
    add_action('wp_ajax_save_setting', 'AJAX_admin_save_setting');
    function AJAX_admin_save_setting()
    {

        //if (isset($_REQUEST['KHT_updating_DB'])) {
        //    echo 'WARNING!!! DB updating. Exit';
        //    wp_die();
        //
        //    return;
        //} else {
        //    echo 'AJAX_admin_save_setting. DB is not updating?' . PHP_EOL;
        //}
        if (!isset($_POST['name']) || !isset($_POST['value'])) {
            echo KHT_BackEnd::$message['error']['!isset'] . ' ' . __FUNCTION__;;
        }

        echo 'AJAX_admin_save_setting. !!!START!!!' . PHP_EOL;
        KHT_BackEnd::option($_POST['name'], 'value', $_POST['value']);
        echo 'AJAX_admin_save_setting. !!!STOP!!!' . PHP_EOL;

        //echo KHT_BackEnd::$message['setting']['saved'] . ' ' . $_POST['name'];
        wp_die();
    }


    //todo:есть смысл разделить класс, на логику и постройку тегов
    class KHT_BackEnd
        //Магия постройки админки
    {
        //название плагина или шаблона, должно передоваться в конструктор
        private $prefix = null;

        //массив, сюда выгружаются настройки из баззы данных и отсюда же загружаются обратно в базу
        private $options = array();

        //содержит данные о текущем плагине, эти данные первые заносятся в базу
        private $plugin = array();

        //разделитель, используется в названии настройки и используется для идентификации индекса массива настроек, где содержится сама настройка
        static $delimiter = '-';

        private $uri = null;

        //Сообщения при различный событиях
        static $message = array(
            'setting' => array(
                'add' => 'add setting success',
                'remove' => 'remove setting success',
                'saved' => 'setting saved'
            ),
            'error' => array(
                '!isset' => 'Required setting is not set',
                'fields_empty' => 'Array "fields" in settings, but empty',
                'fields_invalid' => 'Field in "fields" must be array! Now it is not!',
                'construct' => '__construct($parts, $prefix) error. Wrong arguments(hole) fool.',
                'construct_string' => '$prefix is not string!',
                'construct_array' => '$parts is not array!',
                'section_id' => '$name is not string',
                'section_count' => 'Count and max cant be set at same time',
                'option_id' => '$id is not string',
                'option_option' => '$option is not array',
                'empty_uri' => 'uri of template or plugin called by, not set',
                'no-js' => 'Манипуляции (сохранение, удаление) невозможны без включенного JavaScript. Пожалуйста, установите кошерный браузер или включите JavaScript'
            ),
        );

        //указываем значения по умолчанию
        static $default = array(
            'submit_button' => array(
                'id' => 'KHT_submit',
                'label' => 'Применить'
            ),
            'block' => array(
                'size' => 12,
                'title' => '',
                'type' => 'input',
                'class' => ''
            ),
            'max_button' => array(
                'add' => 'Добавить строку',
                'remove' => 'Удалить строку'
            )
        );

        //Конструктор принимает массив с настройками и префикс, который не может быть пустым, также берем адрес шаблона для корректного отображения скриншота
        function __construct($parts, $prefix, $uri = null)
        {

            //если массив с настройками или префикс не указаны, то выходим, сообщая об ошибке
            if ($parts === false || count($parts) === 0 || !$prefix) {
                wp_die(self::$message['error']['construct']);

                return;
            }
            if (!is_array($parts)) {
                wp_die(self::$message['error']['construct_array']);

                return;
            }
            if (!is_string($prefix)) {
                wp_die(self::$message['error']['construct_string']);

                return;
            }

            //указываем префикс. см. описание свойства
            $this->prefix = $prefix;
            //указываем данные плагины. см. описание свойства
            $this->plugin = ['Name'=>'KHT_backEnd','Version'=>'1.0'];
            //указываем адрес шаблона или плагина, который вызывает этот класс
            $this->uri = $uri !== null ? $uri : null;

            //проверяем, есть ли в базе данных массив настроек, см. описание функции
            $this->is_exist_options();

            //строим страницу админки
            $this->build_wrap($parts);

            ////обновляем настройки в базе данных
            update_option($prefix, $this->options);
            //echo '<div class="db_debug">';
            //echo '<pre>';
            //print_r($this->options);
            //echo '</pre>';
            //echo '</div>';
        }

        //todo: потом! сечас: если страниц несколько, то на каждую страницу своя запись в БД с префиксом этой страницы, надо!: весь сайт в одной записи в БД, с префиксом этого сайта
        //проверяем, существуют ли настройки в базе данных с префиксом, который передавался конструктору
        private function is_exist_options()
        {

            //берем название настройки, которое является префиксом
            $option_name = $this->prefix;

            //Проверяем, если в базе данных настройка с таким именем, если нет, то добавляем
            if (get_option($option_name) === false) {
                add_option($option_name);
            }

            //теперь берем содержимое настройки
            $options = get_option($option_name);

            //если настройка в БД пустая, то добавляем первичную информацию туда
            if (empty($options)) {
                $options = array(
                    'Plugin Name' => $this->plugin['Name'],
                    'Version' => $this->plugin['Version']
                );
            }

            //настройки делаем свойством текущего класса
            $this->options = $options;
        }

        //Проверяем, если отключен js и пытались изменить настройки, то проверяем их, старым добрым методом проверки $_POST
        //private function check_post_data($id, $type)
        //{
        //    //Проверяем. Если была нажата кнопка сохранения, то продолжаем, если нет, то выходим
        //    $button = self::$default['submit_button']['id'];
        //    if (!isset($_POST[$button]) || !isset($id)) {
        //        return;
        //    }
        //
        //    //особые указания для проверки чекбокса, так как там не все так просто,
        //    //в остальном, берем новое значени поля, сравниваем со старым, если изменились, обнавляем
        //    switch ($type) {
        //        case'checkbox':
        //            if (isset($_POST[$id])) {
        //                $this->value($id, '1');
        //            } else {
        //                $this->value($id, '0');
        //            }
        //            break;
        //
        //        default:
        //            $value_old = $this->get_option($id);
        //            $value_new = $_POST[$id];
        //
        //            if ($value_new !== $value_old) {
        //                $this->value($id, $value_new);
        //            }
        //
        //            break;
        //    }
        //
        //}

        //главная прелесть этого класса, строит страницу настроек, но отвечает за обертку страницы
        private function build_wrap($parts)
        {
            //определяем, указан ли заголовок в настройках, если нет, то делаем его пустым
            if (isset($parts['title'])) {
                $title = $parts['title'];
                unset($parts['title']);
            } else {
                $title = '';
            }
            ?>

            <script>
                //указываем дерикторию плагина, для манипуляции с файлами
                var pluginURL = '<?= get_template_directory_uri().'/admin' ?>';
            </script>

            <div class="wrap KHT_backEnd <?= $this->prefix ?>">
                <form action="" method="post">
                    <h1 class="page-header"><?= $title ?></h1>

                    <div class="alert alert-danger no-js-error" role="alert">
                        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                        <span class="sr-only">Error:</span>
                        <?= self::$message['error']['no-js'] ?>
                    </div>
                    <?php
                        //определяем префикс
                        $prefix = $this->prefix;

                        //Вызов функции, которая строит саму страницу
                        $this->build_section($parts, $prefix);
                    ?>
                    <!--<input type="submit" name="--><?//= self::$default['submit_button']['id']
                    ?><!--" class="button button-primary" id="--><?//= self::$default['submit_button']['id']
                    ?><!--" value="--><?//= self::$default['submit_button']['label']
                    ?><!--"/>-->
                </form>
            </div>
        <?php
        }

        //функция стрительства секции настроек. отвечает за конкретный секцию(больше блока) настроек
        private function build_section($part, $prefix, $level = 0)
        {
            //Переменная, которая показывает уровень блока в настройках. нужен для корректного отображения (так как оформление первого блока и вложенных блоков отличаются)
            $level++;

            //перебираем массив с описанием полей и настроек
            foreach ($part as $name => $block) {

                //определяем значения
                $settings = array(
                    //идентификатор блока, который состоит из префикса, который передан в функцию, разделителя, и имени блока настроек
                    'id' => $prefix . self::$delimiter . $name,
                    //Ширина блока в админки (в единицах bootstrap)
                    'size' => isset($block['size']) ? $block['size'] : self::$default['block']['size'],
                    //Заголовок блока
                    'title' => isset($block['title']) ? $block['title'] : self::$default['block']['title'],
                    //тип поля (текстовый, картинка или прочее)
                    'type' => isset($block['type']) ? $block['type'] : self::$default['block']['type'],
                    //слектор блока
                    'class' => isset($block['class']) ? $block['class'] : self::$default['block']['class'],
                    //Текст для кнопки "добавить"
                    'text_add_button' => isset($block['text_add_button']) ? $block['text_add_button'] : self::$default['max_button']['add'],
                    //Текст для кнопки "удалить"
                    'text_remove_button' => isset($block['text_remove_button']) ? $block['text_remove_button'] : self::$default['max_button']['remove'],
                    //если поле неконечное (содержит fields) то говорим об этом
                    'fields' => isset($block['fields']) ? $block['fields'] : false,
                    //todo:проверять, max это число или что. и соответственно чтото с этим делать
                    //проверяем, указан ли max
                    'max' => isset($block['max']) ? $block['max'] : false,
                    //проверяем, указан ли count
                    'number' => isset($block['number']) ? ($block['number'] - 1) : false,
                    //уровень блоков
                    'level' => $level,
                    //определяем, первый ли уровень блоков
                    'first' => $level === 1 ? true : false,
                    //Определяем скриншот для секции
                    'screen_shot' => (isset($block['screen_shot']) && !empty($block['screen_shot'])) ? $block['screen_shot'] : false,
                );

                $settings['count'] = $settings['max'] ? $settings['max'] : ($settings['number'] ? $settings['number'] : 0);

                if ($settings['type'] === 'select') {

                    if(!isset($block['selectData'])){
                        wp_die('No data for select tag');
                    }

                    $settings['selectData']=$block['selectData'];
                }

                //если индекс блока в масиве настроек не строка, а числовой, то ругаемся и выходим
                if (!is_string($name)) {
                    wp_die(self::$message['error']['section_id'] . '' . __FUNCTION__);

                    return;
                }

                //ошибка, если указаны и max и count
                if ($settings['number'] && $settings['max']) {
                    wp_die(self::$message['error']['section_count'] . '' . __FUNCTION__);

                    return;
                }

                //если в настройках указано, что есть вложенные блоки (подмассив fields), но он пустой, ругаемся и выходим
                //проверяем содержание fields, если значение внутри не массив, выходим, ругаемся и выходим
                if ($settings['fields'] !== false) {
                    if (count($settings['fields']) === 0) {

                        wp_die(self::$message['error']['fields_empty'] . '' . __FUNCTION__);

                        continue;
                    }

                    foreach ($settings['fields'] as $field_name => $field_value) {
                        if (!is_array($field_value)) {
                            wp_die('<h5 style="color:red">' . self::$message['error']['fields_invalid'] . ' Field is: ' . $field_name . '; Function: ' . __FUNCTION__ . '()</h5>');

                            continue;
                        }
                    }

                    if(!is_array($this->get_option($settings['id']))){
                        $this->value($settings['id'],array());
                    }
                }


                //вызываем построение блока
                $this->build_block($settings);
            }

        }

        //строит блок
        private function build_block($settings)
        {
            //определяем значения
            $size = $settings['size'];
            $type = $settings['type'];
            $class = $settings['class'];
            $fields = $settings['fields'];
            $max = $settings['max'];
            $number = $settings['number'];
            $count = $settings['count'];
            $first = $settings['first'];

            //дополнительные действия, если указан в блоке max
            if ($max) {
                //заголовоки и прочие прелести, которые нужны для работы блоков с переменным количеством
                $count = $this->pre_max($settings);
            }

            //соответственно по количеству блоков/полей строим блок
            for ($i = 0; $i <= $count; $i++) {

                //оберточный тег блока
                echo '<div class="' . $class . ' ' . ($first ? 'panel panel-info' : 'col-xs-12 col-sm-' . $size) . ' ' . ($fields ? '' : $type) . '" ' . (($max || $number) ? 'data-index="' . $i . '"' : '') . '>';

                //отправляем блок на рендер тегов
                $this->render_block($settings, $i);

                //закрываем оберточный тег
                echo '</div>';
            }

            //если указан max, то добавляем кнопки удаления, удаления + закрываем теги
            if ($max) {
                $this->post_max($settings, $count);
            }
        }

        //непосредственно строительство тегов
        private function render_block($settings, $index)
        {
            $id = $settings['id'];
            $fields = $settings['fields'];
            $max = $settings['max'];
            $number = $settings['number'];
            $level = $settings['level'];
            $first = $settings['first'];
            $title=$settings['title'];
            $front_title = $title . (($max || $number) ? $this->header_with_count($id, ($index + 1)) : '');

            //если уровень блока первый, то выводим необходимые теги
            if ($first) {
                echo '<div class="panel-heading">';

                echo '<h3 class="panel-title">';
                echo $front_title;
                echo '</h3>';

                echo '</div>';
                echo '<div class="panel-body">';

            }

            //если поле не конечное, то выводим заголовок секции и опять же строительство секции
            if ($fields) {

                //если уровень блоков не первый, то выводим необходимые блоки
                if (!$first) {
                    echo '<h4 class="section-header">';
                    echo $front_title;
                    echo '</h4>';
                }

                //если в настройках указан скриншот, то выводим его
                if ($settings['screen_shot']) {
                    $this->get_screen_shot($settings['screen_shot']);
                }

                //опять вызываем строительство секции
                $this->build_section($fields, $id . (($max || $number) ? (self::$delimiter . $index) : ''), $level);
            } else {
                //вызываем рендер непосредственного тега
                $this->render_tag($settings,$title, $front_title, $index);
            }

            //закрывает тег, если уровень блока первый
            echo $first ? '</div>' : '';
        }

        //рендер тега
        private function render_tag($settings,$title, $front_title, $index)
        {
            $id = $settings['id'];
            $type = $settings['type'];
            $max = $settings['max'];
            $number = $settings['number'];
            $selectData=$type==='select'?$settings['selectData']:false;

            //если поле конечное, то проверяем, не изменилось ли оно
            //$this->check_post_data($id, $type);

            //полный идентификатор поля, содержит индекс поля
            $full_id = $id . (($max || $number) ? (self::$delimiter . $index) : '');

            echo '<label title="' . $title . '" for="' . $full_id . '">' . $front_title . '</label>';

            //если в настройках указан скриншот, то выводим его
            if ($settings['screen_shot']) {
                $this->get_screen_shot($settings['screen_shot']);
            }
            //вызов обработчика конкретного тега, указанного в type

            if($type==='select'){
                $this->select($full_id,$selectData);
            }elseif (method_exists($this, $type)) {
                $this->$type($full_id);
            } else {
                return null;
            }
        }

        //обрабатывает первую часть логики, если в блоке указан макс
        private function pre_max($settings)
        {
            $id = $settings['id'];
            $max = $settings['max'];

            //определяем количество блоков, которые уже есть
            $count = $this->count($id);

            //если количество меньше нуля(если поле одно, то выдаст -1), то равняем на нуль
            $count = $count < 0 ? 0 : $count;
            //если больше указанного максимума, то приравниваем к максимуму
            $count = $count >= ($max - 1) ? $max - 1 : $count;

            ////добавляем блок, если нажали кнопку
            //if (isset($_POST[$id . self::$delimiter . 'add_row']) && $count < $max) {
            //    //просто увеличиваем воличество блоков, новый блок зарегистрирует автоматически
            //    $count++;
            //}
            //
            ////удаляем, если нажали кнопку
            //if (isset($_POST[$id . self::$delimiter . 'remove_row']) && $count !== 0) {
            //
            //    //функция удаления настройки
            //    $this->remove($id);
            //
            //    //обновляем количество блоков
            //    $count = $this->count($id);
            //}

            //и добавляем теги,
            //clearfix - для правильного отображения рамки вокруг блока
            //parent - для родительского блока, в котором переменное число блоков
            //max-wrap - непосредственный родитель блоков, которые удаляются или добавляются
            //data-max - это максимум из  массива настроек
            //data-name - идентификатор блока (нужен для скрипта на js)
            echo '<div class="clearfix"></div>';
            echo '<div class="parent">';
            echo '<div class="max-wrap clearfix" data-max="' . $max . '" data-name="' . $id . self::$delimiter . '">';

            return $count;
        }

        //обрабатывает вторую часть логики, если в блоке указан max
        private function post_max($settings, $count)
        {
            $id = $settings['id'];
            $max = $settings['max'];
            $text_remove = $settings['text_remove_button'];
            $text_add = $settings['text_add_button'];

            echo '</div>';
            echo '<div class="clearfix button-wrap">';

            if ($count !== 0) {
                $this->get_remove_button($id, $text_remove);
            } else {
                $this->get_remove_button($id, $text_remove, true);
            }
            if ($count < $max && $count !== ($max - 1)) {

                $this->get_add_button($id, $text_add);
            } else {
                $this->get_add_button($id, $text_add, true);
            }

            echo '</div>';
            echo '</div>';
        }

        //функция для вывода заголовка с индексом конкретного блока (блок №1, блок №2, и т. д.)
        private function header_with_count($id, $index)
        {
            return '<span class="count-header ' . $id . self::$delimiter . 'count-header">' . $index . '</span>';
        }

        //обработчик текстового поля, которое используется по умолчанию
        function input($id)
        {
            echo '<input type="text" class="form-control field text-fields" name="' . $id . '" id="' . $id . '" value="' . stripslashes(htmlentities($this->get_option($id))) . '"/>';
        }

        //оброботчик многострочного текстового поля
        function textarea($id)
        {
            echo '<textarea class="form-control field text-fields" name="' . $id . '" id="' . $id . '">' . stripslashes($this->get_option($id)) . '</textarea>';
        }

        //обработчик выпадающего списка сос страницами сайта
        function wpPages_dropDown($id)
        {
            wp_dropdown_pages(
                array(
                    'name' => $id,
                    'id' => $id,
                    'echo' => 1,
                    'show_option_none' => 'Choose',
                    'option_none_value' => '0',
                    'selected' => $this->get_option($id),
                )
            );
        }

        //обработчик загрузки изображения
        function image($id)
        {
            $src = $this->get_option($id) == '' ? get_template_directory_uri() . '/admin/img/min.jpg' : $this->get_option($id);
            echo '<a class="fancybox" rel="group" href="' . $src . '">';
            echo '<img class="preview-upload" src="' . $src . '" alt=""/>';
            echo '</a>';
            echo '<input class="upload_image_field form-control upload-field" type="hidden" aria-describedby="basic-addon3" name="' . $id . '" id="' . $id . '" value="' . $this->get_option($id) . '"/>';
            echo '<input class="upload_image_button btn btn-default" type="button" value="Upload"/>';
        }

        //обработчик чекбокса
        function checkbox($id)
        {
            if ($this->get_option($id) === '') {
                $this->value($id, 0);
            }

            if ($this->get_option($id) == '1') {
                $checked = 1;
                $status = 'checked';
            } else {
                $checked = 0;
                $status = '';
            }

            echo '<input type="checkbox" class="checkbox " name="' . $id . '" id="' . $id . '" value="' . $checked . '" ' . $status . '/>';
        }

        //обрабочик цифрового поля
        function number($id)
        {
            echo '<input class="form-control field text-fields" type="number" name="' . $id . '" id="' . $id . '" min="0" max="500" step="5" value="' . stripslashes($this->get_option($id)) . '">';
        }

        function select($id,$data)
        {
	        $current = $this->get_option( $id );
	        $current = $current === false || $current === '' ? false : $current;

	        $selected= ' selected="selected" ';
	        echo '<select name="' . $id . '" class="form-control">';

	        echo '<option ' . ( ! $current ? $selected : '' ) . ' value="" >' . 'Выберите' . '</option>';
	        foreach ( $data as $name => $value ) {
		        echo '<option ' . ( $current && $current === $name ? $selected : '' ) . ' value="' . $name . '" >' . $value . '</option>';
	        }
	        echo '</select>';
        }

        //Выдает скриншот секции
        private function get_screen_shot($src)
        {
            //если вызывается скриншот, но адрес шаблона недоступен, то ругаемся и выходим
            if ($this->uri === null) {
                wp_die(self::$message['error']['empty_uri']);

                return;
            }
            echo '<div class="screenShot"><img src="' . $this->uri . $src . '" alt=""/></div>';
        }

        //обработчки кнопок добавления и удаления блока
        private function get_remove_button($id, $text, $hide = false)
        {
            $class = 'btn btn-danger remove-row-button' . ($hide ? ' disable' : '');
            $id = $id . self::$delimiter . 'remove_row';

            $this->button($id, $text, $class, $hide);
        }

        private function get_add_button($id, $text, $hide = false)
        {
            $class = 'btn btn-warning add-row-button' . ($hide ? ' disable' : '');
            $id = $id . self::$delimiter . 'add_row';

            $this->button($id, $text, $class, $hide);
        }

        private function button($id, $text, $class, $hide)
        {
            echo '<div class="col-xs-6">';
            echo '<button type="submit" id="' . $id . '" name="' . $id . '" class="' . $class . '" aria-label="' . $text . '" ' . ($hide ? ' disabled' : '') . '>' . $text . '</button>';
            echo '</div>';
        }

        //эти функции (до статичной функции) просто помошники
        //помогают добавлять, удалять, подсчитывать, указывать значения используя для этого статичную функцию
        private function count($id)
        {

            return $this->option($id, 'count', '', $this->options);
        }

        private function value($id, $value)
        {
            $this->options = $this->option($id, 'value', $value, $this->options);
        }

        private function get_option($id)
        {
            if ($this->option($id, '', '', $this->options) === false) {
                $this->options = $this->option($id, 'value', '', $this->options);
            }

            return $this->option($id, '', '', $this->options);
        }

        //главная функции по операции над настройкам
        static function option($id, $operation = '', $value = '', $option = array())
        {

            //ругаемся, если $id не стоока и $options не архив
            if (!is_string($id)) {
                echo self::$message['error']['option_id'] . ' ' . __FUNCTION__;
                wp_die();
            }
            if (!is_array($option)) {
                echo self::$message['error']['option_option'] . ' ' . __FUNCTION__;
                wp_die();
            }


            //проеверяем, пришел ли запрос из аякса
            if (
                !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
                && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
            ) {
                $ajax = true;
            } else {
                $ajax = false;
            }

            //разбиваем $id на составляющие, чтобы понять индекс в массиве настроек, где хранится эта настройка
            $keys = explode(self::$delimiter, $id);
            //первая часть - это префикс, берем ее и удаляем из $keys
            $prefix = array_shift($keys);

            //по умолчанию возвращаем массив измененных настроек,
            // если $update - true , находим по префиксу настройку в БД и обнавляем ее
            $update = false;
            if (count($option) === 0) {

                if ($ajax) {
                    $option = self::db_get($prefix);
                } else {
                    $option = get_option($prefix);
                }
                $update = true;
            }

            //теперь смотрим, то нужно сделать с насиройкой
            switch ($operation) {
                //добавляем
                //case 'add':
                //$head = '';
                //break;
                //удаляет
                case 'remove':
                    $index = array_pop($keys);
                    $head =& $option;

                    if (!is_numeric($index)) {
                        echo 'REMOVE! last el in not number' . $index;

                        return $index;
                    } else {
                        $index = intval($index);
                    }

                    foreach ($keys as $key) {

                        if ($key === '') {
                            continue;
                        }

                        $head = &$head[$key];
                    }
                    echo '$index is ' . $index . PHP_EOL;
                    //echo 'array removed BEFORE: ' . PHP_EOL;
                    //print_r($head);
                    if (!isset($head[$index])) {
                        echo 'no element to remove' . PHP_EOL;
                    } elseif (count($head) >= $index) {
                        ksort($head);
                        array_splice($head, $index);
                        echo 'last element removed';
                    }
                    //echo 'array removed AFTER: ' . PHP_EOL;
                    //print_r($head);
                    break;
                //присваивает значение
                case 'value':
                    $head = &$option;
                    for ($i = 0; $i < count($keys); $i++) {
                        if ($keys[$i] === '') {
                            continue;
                        }
                        $head = &$head[$keys[$i]];
                        $next_index = isset($keys[$i + 1]) && is_numeric($keys[$i + 1]) ? intval($keys[$i + 1]) : false;
                        if ($next_index !== false && (count($head) - 1) < $next_index) {
                            for ($j = 0; $j <= $next_index; $j++) {
                                $head[$j] = isset($head[$j]) ? $head[$j] : '';
                            }
                        }

                    }

                    $head = $value;
                    break;
                //подсчитывает количество блоков
                case 'count':
                    //todo: сделать подсчет только цифровых значений
                    $head =& $option;
                    foreach ($keys as $key) {

                        if ($key === '') {
                            continue;
                        }

                        $head = &$head[$key];

                    }

                    if (!isset($head)) {
                        return 0;
                    }

                    $count = 0;

                    foreach (
                        $head as
                        $key =>
                        $value
                    ) {
                        if (is_numeric($key)) {
                            $count++;
                        }
                    }
                    return ($count - 1);
                    break;
                //по умолчанию, просто возвращаем значения настройки
                default:
                    $head =& $option;
                    foreach ($keys as $key) {

                        if ($key === '') {
                            continue;
                        }

                        $head = &$head[$key];

                    }
                    if (!isset($head)) {
                        return false;
                    } else {
                        return $head;
                    }
                    break;
            }

            //если надо обновить настройки, то обновляем
            if ($update) {

                if ($ajax) {
                    self::db_set($prefix, $option);
                } else {
                    update_option($prefix, $option);
                }

            }

            //наконец просто возвращаем массив
            return $option;
        }

        static function db_get($prefix)
        {
            global $wpdb;
            $options = $wpdb->get_var("SELECT option_value FROM $wpdb->options WHERE option_name = '$prefix'");

            return maybe_unserialize($options);
        }

        static function db_set($prefix, $options)
        {
            global $wpdb;
            $options = serialize($options);

            return $wpdb->query("UPDATE $wpdb->options SET option_value = '$options' WHERE option_name = '$prefix'");
        }
    }