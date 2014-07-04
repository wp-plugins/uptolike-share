<?php


class MySettingsPage
{

    private $options;
    private $settings_page_name = 'uptolike_settings';
    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
    }


    public function add_plugin_page()
    {
        add_options_page(
            'Settings Admin',
            'UpToLike',
            'manage_options',
            $this->settings_page_name, //'my-setting-admin',
            array($this, 'create_admin_page')
        );
    }

    /** creates url of iframe with statistics page from given params
     *
     * @param $projectId
     * @param $partnerId
     * @param $mail
     * @param $cryptKey
     * @return stringшfr
     */
    public function statIframe($projectId, $partnerId, $mail, $cryptKey)
    {
        $params = array(
            'mail' => $mail,
            'partner' => $partnerId,
            'projectId' => $projectId,

        );
        $paramsStr = 'mail=' . $mail . '&partner=' . $partnerId. '&projectId=' . $projectId;
        $signature = md5($paramsStr . $cryptKey);
        $params['signature'] = $signature;
        $finalUrl = 'http://uptolike.com/api/statistics.html?' . http_build_query($params);

        return $finalUrl;
    }

    /** create url of iframe with constructor from given params
     * @param $projectId
     * @param $partnerId
     * @param $mail
     * @param $cryptKey
     * @return string
     */
    public function constructorIframe($projectId, $partnerId, $mail, $cryptKey)
    {

        $params = array('mail' => $mail,
            'partner' => $partnerId,
            'projectId' => $projectId);

        $paramsStr = 'mail=' . $mail . '&partner=' . $partnerId . '&projectId=' . $projectId . $cryptKey;
        $signature = md5($paramsStr);
        $params['signature'] = $signature;
        if ('' !== $cryptKey) {
            $finalUrl = 'http://uptolike.com/api/constructor.html?' . http_build_query($params);
        } else $finalUrl = 'http://uptolike.com/api/constructor.html';


        return $finalUrl;
    }


    /** returns tabs html code. May be replace by proper html code
     * @param string $current
     */
    public function ilc_admin_tabs($current = 'construct')
    {
        $tabs = array('construct' => 'Конструктор',
            'stat' => 'Статистика',
            'settings' => 'Настройки');

        echo '<div id="icon-themes" class="icon32"><br></div>';
        echo '<h2 class="nav-tab-wrapper">';
        foreach ($tabs as $tab => $name) {
            $class = ($tab == $current) ? ' nav-tab-active' : '';
            echo "<a class='nav-tab$class' href='#' id=" . $tab . " ref='?page=" . $this->settings_page_name . "&tab=$tab'>$name</a>";

        }
        echo '</h2>';
    }

    /** render html page with code configuration settings
     *
     */
    public function create_admin_page()
    {
        $this->options = get_option('my_option_name');

        if ((isset($this->options['uptolike_email'])) && ('' !== $this->options['uptolike_email'])) {
            $email = $this->options['uptolike_email'];
        } else $email = get_settings('admin_email');
        $partnerId = 'cms';
        $projectId = 'cms' . preg_replace('/^www\./', '', $_SERVER['HTTP_HOST']);
        $projectId = str_replace('.','',$projectId);
        $options = get_option('my_option_name');
        if (is_array($options) && array_key_exists('id_number', $options)) {
            $cryptKey = $options['id_number'];
        } else $cryptKey = '';
        ?>
        <script type="text/javascript">
            <? include('main.js'); ?>
        </script>
        <style type="text/css">
            h2.placeholder {
                font-size: 1px;
                padding: 1px;
                margin: 0px;
                height: 2px;
            }

            div.wrapper-tab {
                display: none;
            }

            div.wrapper-tab.active {
                display: block;
                width: 100%;
            }

            input#id_number {
                width: 520px;
            }
        </style>
        <div class="wrap">
            <h2 class="placeholder">&nbsp;</h2>
            <div id="wrapper">
                <form id="settings_form" method="post" action="options.php">
                    <H1> UpToLike виджет</H1>
                    <h2 class="nav-tab-wrapper">
                        <a class="nav-tab nav-tab-active" href="#" id="construct">
                            Конструктор
                        </a>
                        <a class="nav-tab" href="#" id="stat">
                            Статистика
                        </a>

                        <a class="nav-tab" href="#" id="settings">
                            Настройки
                        </a>
                    </h2>
                    <div class="wrapper-tab active" id="con_construct">
                        <iframe id='cons_iframe' style='height: 445px;width: 100%;' data-src="<?php echo $this->constructorIframe($projectId, $partnerId, $email, $cryptKey); ?>"></iframe>
                        <br>
                        <a onclick="getCode();" href="#">
                            <button type="reset">Сохранить изменения</button>
                        </a>
                    </div>
                    <div class="wrapper-tab" id="con_stat">
                        <? if (('' == $partnerId) OR ('' == $email) OR ('' == $cryptKey)) {

                        ?>
                        <h2>Статистика</h2>
                        <p>Для просмотра статистики необходимо ввести ваш секретный ключ </p>
                        <? } else { ?>
                            <!-- <?php print_r(array($partnerId,$email, $cryptKey)); ?> -->
                            <iframe style="width: 100%;height: 380px;" id="stats_iframe" data-src="<?php echo $this->statIframe($projectId, $partnerId, $email, $cryptKey); ?>">
                        </iframe> <?
                        } ?>
                        <button class="reg_btn" type="button">Запросить секретный ключ</button><br/>
                        <div class="reg_block">
                            <label>Email<input type="text" class="uptolike_email"></label><br/>
                            <button type="button" class="button-primary">Отправить ключ на email</button><br/>
                        </div>
                        <button class="enter_btn" type="button">Авторизация</button><br/>
                        <div class="enter_block" >
                            <label>Email<input type="text" class="uptolike_email"></label><br/>
                            <label>Ключ<input type="text" class="id_number"></label><br/>
                            <button type="button" class="button-primary">Сохранить</button><br/>
                        </div>
                    </div>
                    <div class="wrapper-tab" id="con_settings">
                        <?php
                        settings_fields('my_option_group');
                        do_settings_sections($this->settings_page_name);
                        ?>
                        <input type="submit" name="submit_btn" value="Cохранить изменения">
                    </div>

                </form>
            </div>
        </div>
    <?php
    }

    public function page_init()
    {
        register_setting(
            'my_option_group', // Option group
            'my_option_name', // Option name
            array($this, 'sanitize') // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'Настройки виджета', // Title
            array($this, 'print_section_info'), // Callback
            $this->settings_page_name//'my-setting-admin' // Page
        );

        add_settings_field(
            'widget_code', // ID
            'код виджета', // Title
            array($this, 'widget_code_callback'), // Callback
            $this->settings_page_name, //'my-setting-admin', // Page
            'setting_section_id' // Section
        );

        add_settings_field(
            'data_pid', // ID
            'Ключ(CryptKey)', // Title
            array($this, 'id_number_callback'), // Callback
            $this->settings_page_name, //'my-setting-admin', // Page
            'setting_section_id' // Section           
        );

        add_settings_field(
            'email', //ID
            'email для регистрации',
            array($this, 'uptolike_email_callback'),
            $this->settings_page_name, //'my-setting-admin',
            'setting_section_id'
        );

        add_settings_field(
            'on_main', //ID
            'Располагать блок на главной странице',
            array($this, 'uptolike_on_main_callback'),
            $this->settings_page_name, //'my-setting-admin',
            'setting_section_id'
        );

        add_settings_field(
            'widget_position', //ID
            'Расположение виджета',
            array($this, 'uptolike_widget_position_callback'),
            $this->settings_page_name, //'my-setting-admin',
            'setting_section_id'
        );
        add_settings_field(
            'uptolike_json', //ID
            'настройки конструктора',
            array($this, 'uptolike_json_callback'),
            $this->settings_page_name, //'my-setting-admin',
            'setting_section_id'
        );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize($input)
    {
        $new_input = array();
        if (isset($input['id_number']))
            $new_input['id_number'] = $input['id_number'];

        if (isset($input['widget_code']))
            $new_input['widget_code'] = $input['widget_code'];

        if (isset($input['uptolike_email']))
            $new_input['uptolike_email'] = $input['uptolike_email'];

        if (isset($input['before_content']))
            $new_input['before_content'] = $input['before_content'];

        if (isset($input['on_main'])) {
                $new_input['on_main'] = true;

        } else  $new_input['on_main'] = false;

        if (isset($input['email']))
            $new_input['email'] = $input['email'];

        if (isset($input['after_content']))
            $new_input['after_content'] = $input['after_content'];

        if (isset($input['widget_position']))
            $new_input['widget_position'] = $input['widget_position'];

        if (isset($input['uptolike_json']))
            $new_input['uptolike_json'] = $input['uptolike_json'];

        return $new_input;
    }


    public function print_section_info()
    {
        //print 'Enter your settings below:';
    }

    public function widget_code_callback()
    {
        printf(
            '<textarea id="widget_code" name="my_option_name[widget_code]" >%s</textarea>',
            isset($this->options['widget_code']) ? esc_attr($this->options['widget_code']) : ''
        );
    }

    /** 12536473050877
     * Get the settings option array and print one of its values
     */
    public function id_number_callback()
    {
        printf(
            '<input type="text" class="id_number" name="my_option_name[id_number]" value="%s" />',
            isset($this->options['id_number']) ? esc_attr($this->options['id_number']) : ''
        );
    }

    public function uptolike_email_callback()
    {
        printf(
            '<input type="text" id="uptolike_email" name="my_option_name[uptolike_email]" value="%s" />',
            isset($this->options['uptolike_email']) ? esc_attr($this->options['uptolike_email']) : get_settings('admin_email')
        );
    }

    public function uptolike_json_callback()
    {
        printf(
            '<input type="hidden" id="uptolike_json" name="my_option_name[uptolike_json]" value="%s" />',
            isset($this->options['uptolike_json']) ? esc_attr($this->options['uptolike_json']) : ''
        );
    }

    public function uptolike_partner_id_callback()
    {
        printf(
            '<input type="text" id="uptolike_partner" name="my_option_name[uptolike_partner]" value="%s" />',
            isset($this->options['uptolike_partner']) ? esc_attr($this->options['uptolike_partner']) : ''
        );
    }

    public function uptolike_project_callback()
    {
        printf(
            '<input type="text" id="uptolike_project" name="my_option_name[uptolike_project]" value="%s" />',
            isset($this->options['uptolike_project']) ? esc_attr($this->options['uptolike_project']) : ''
        );
    }

    public function uptolike_on_main_callback()
    {
        echo '<input type="checkbox" id="on_main" name="my_option_name[on_main]"';
        echo ($this->options['on_main'] == true ? 'checked="checked"' : ''); echo '  />';

    }


    public function uptolike_widget_position_callback()
    {
        $top = $bottom = $both = $default = '';

        if (isset($this->options['widget_position'])) {
            if ('top' == $this->options['widget_position']) {
                $top = "selected='selected'";
            } elseif ('bottom' == $this->options['widget_position']) {
                $bottom = "selected='selected'";
            } elseif ('both' == $this->options['widget_position']) {
                $both = "selected='selected'";
            } else {
                $bottom = "selected='selected'";
            }
        } else {
            $my_options = get_option('my_option_name');
            $my_options['widget_position'] = 'bottom'; // cryptkey store
            update_option('my_option_name', $my_options);
        }
        $default = "selected='selected'";
        echo "<select id='widget_position' name='my_option_name[widget_position]'>
                            <option {$top} value='top'>Только сверху</option>
                            <option {$bottom} value='bottom'>Только снизу</option>
                            <option {$both} value='both'>Сверху и снизу</option>
                        </select>";

    }

}


function add_widget($content)
{

    $options = get_option('my_option_name');
    if (is_array($options) && array_key_exists('widget_code', $options)) {
        $widget_code = $options['widget_code'];
        $url = get_permalink();

        //fixing bad data-pid
        $domain = preg_replace('/^www\./', '', $_SERVER['HTTP_HOST']);
        $data_pid = 'cms' . str_replace('.', '', $domain);

        $widget_code = str_replace('data-pid="-1"','data-pid="' . $data_pid . '"',$widget_code);
        $widget_code = str_replace('data-pid=""','data-pid="' . $data_pid . '"',$widget_code);
        $widget_code = str_replace('div data', 'div data-url="' . $url . '" data', $widget_code);
        $widget_code_before = $widget_code_after = '';

        if ((!is_single() && array_key_exists('on_main', $options) && $options['on_main']) or is_single()) {
            switch ($options['widget_position']) {
                case 'both':
                    $widget_code_before = $widget_code_after = $widget_code;
                    break;
                case 'top':
                    $widget_code_before = $widget_code;
                    break;
                case 'bottom':
                    $widget_code_after = $widget_code;
                    break;
            }
            return $widget_code_before.$content.$widget_code_after;
        }
    } else {
        return $content;
    }
}

add_filter('the_content', 'add_widget', 100);


function uptolike_shortcode( $atts ){

    return add_widget("");
}
add_shortcode( 'uptolike', 'uptolike_shortcode' );

function my_widgetcode_notice()
{
    $options = get_option('my_option_name');
    if (is_array($options) && array_key_exists('widget_code', $options)) {
        $widget_code = $options['widget_code'];
        if ('' == $widget_code) {
            echo " <div class='updated'>
	                 <p>В настройках UpToLike 'Конструктор' выберите тип виджета и нажмите 'Сохранить'</p>
	          </div>";
        }
    };
}

function logger($str)
{
    file_put_contents(WP_PLUGIN_DIR . '/uptolike/log.txt', date(DATE_RFC822) . $str . PHP_EOL, FILE_APPEND | LOCK_EX);
}

function try_reg()
{
    include('api_functions.php');
    $domain = preg_replace('/^www\./', '', $_SERVER['HTTP_HOST']);
    $options = get_option('my_option_name');
    $email = $options['uptolike_email'];
    if ('' == $options['id_number']) {
        $reg_ans = userReg($email, 'cms', 'cms' . $domain);
        if (is_string($reg_ans)) {
            $my_options = get_option('my_option_name');
            $my_options['id_number'] = $reg_ans; // cryptkey store
            $my_options['choice'] = 'reg';
            update_option('my_option_name', $my_options);
        };
        update_option('reg_try', true);
    }
}

function my_choice_notice()
{
    $options = get_option('my_option_name');
    if (is_bool($options) or (('' == $options['id_number']) and ((!array_key_exists('choice', $options)) OR ('ignore' !== $options['choice'])))) {
        echo "<div class='updated'>
<div><img style='
    float: left;
    height: 38px;
    margin-left: -9px;
    margin-right: 5px;
' src='//uptolike.ru/img/logo.png'>
</div>
Кнопки успешно установлены! <br>Для просмотра статистики необходимо:
        <a  href='options-general.php?page=uptolike_settings#enter'>Ввести полученный ключ</a>
        | <a href='options-general.php?page=uptolike_settings#reg'>Запросить ключ</a>
        | <a href='options-general.php?page=uptolike_settings&choice=ignore'>Скрыть</a> </div>";


    };
}

function set_default_code()
{
    $options = get_option('my_option_name');
    if (is_bool($options)) {
        $options = array();
    }
    $data_url = 'cms' . $_SERVER['HTTP_HOST'];
    $data_pid = 'cms' . str_replace('.', '', preg_replace('/^www\./', '', $_SERVER['HTTP_HOST']));
    $code = <<<EOD
<script type="text/javascript">(function (w, doc) {
    if (!w.__utlWdgt) {
        w.__utlWdgt = true;
        var d = doc, s = d.createElement('script'), g = 'getElementsByTagName';
        s.type = 'text/javascript';
        s.charset = 'UTF-8';
        s.async = true;
        s.src = ('https:' == w.location.protocol ? 'https' : 'http') + '://w.uptolike.com/widgets/v1/uptolike.js';
        var h = d[g]('body')[0];
        h.appendChild(s);
    }
})(window, document);
</script>
<div data-url data-background-alpha="0.0" data-orientation="horizontal" data-text-color="000000" data-share-shape="round-rectangle" data-buttons-color="ff9300" data-sn-ids="fb.tw.ok.vk.gp.mr." data-counter-background-color="ffffff" data-share-counter-size="11" data-share-size="30" data-background-color="ededed" data-share-counter-type="common" data-pid data-counter-background-alpha="1.0" data-share-style="1" data-mode="share" data-following-enable="false" data-like-text-enable="false" data-selection-enable="true" data-icon-color="ffffff" class="uptolike-buttons">
</div>
EOD;

    $code = str_replace('data-pid', 'data-pid="' . $data_pid . '"', $code);

    $code = str_replace('data-url', 'data-url="' . $data_url . '"', $code);
    $options['widget_code'] = $code;
    $options['on_main'] = true;
    $options['widget_position'] = 'bottom';

    update_option('my_option_name', $options);
}

function choice_helper($choice)
{
    $options = get_option('my_option_name');
    $options['choice'] = $choice;
    if ('ignore' == $choice) {
        set_default_code();
    }
    update_option('my_option_name', $options);
}

add_action('admin_notices', 'my_choice_notice');
add_action('admin_notices', 'my_widgetcode_notice');

$options = get_option('my_option_name');

if (is_admin()) {
    $options = get_option('my_option_name');

    if (array_key_exists('regme', $_REQUEST)) {
        try_reg();
    }
    if (array_key_exists('choice', $_REQUEST)) {
        choice_helper($_REQUEST['choice']);
    }

    $my_settings_page = new MySettingsPage();
    if (is_bool($options) OR (!array_key_exists('widget_code', $options)) OR ('' == $options['widget_code'])) {
        set_default_code();
    }

}