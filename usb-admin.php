<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18.08.14
 * Time: 12:15
 */
function constructorIframe($projectId, $partnerId, $mail, $cryptKey)
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

function statIframe($projectId, $partnerId, $mail, $cryptKey)
{
    $params = array(
        'mail' => $mail,
        'partner' => $partnerId,
        'projectId' => $projectId,

    );
    $paramsStr = 'mail=' . $mail . '&partner=' . $partnerId . '&projectId=' . $projectId;
    $signature = md5($paramsStr . $cryptKey);
    $params['signature'] = $signature;
    $finalUrl = 'http://uptolike.com/api/statistics.html?' . http_build_query($params);

    return $finalUrl;
}

function usb_admin_page()
{

    $options = get_option('my_option_name');

    if ((isset($options['uptolike_email'])) && ('' !== $options['uptolike_email'])) {
        $email = $options['uptolike_email'];
    } else $email = get_settings('admin_email');
    $partnerId = 'cms';
    $projectId = 'cms' . preg_replace('/^www\./', '', $_SERVER['HTTP_HOST']);
    $projectId = str_replace('.', '', $projectId);
    $projectId = str_replace('-', '', $projectId);
    $options = get_option('my_option_name');
    if (is_array($options) && array_key_exists('id_number', $options)) {
        $cryptKey = $options['id_number'];
    } else $cryptKey = '';
    /*    $this->options = get_option('my_option_name');

        if ((isset($this->options['uptolike_email'])) && ('' !== $this->options['uptolike_email'])) {
            $email = $this->options['uptolike_email'];
        } else $email = get_settings('admin_email');
        $partnerId = 'cms';
        $projectId = 'cms' . preg_replace('/^www\./', '', $_SERVER['HTTP_HOST']);l
        $projectId = str_replace('.','',$projectId);
        $projectId = str_replace('-','',$projectId);
        $options = get_option('my_option_name');
        if (is_array($options) && array_key_exists('id_number', $options)) {
            $cryptKey = $options['id_number'];
        } else $cryptKey = '';
    */
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
                    <iframe id='cons_iframe' style='height: 445px;width: 100%;'
                            data-src="<?php echo constructorIframe($projectId, $partnerId, $email, $cryptKey); ?>"></iframe>
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
                        <!-- <?php print_r(array($partnerId, $email, $cryptKey)); ?> -->
                        <iframe style="width: 100%;height: 380px;" id="stats_iframe"
                                data-src="<?php echo statIframe($projectId, $partnerId, $email, $cryptKey); ?>">
                        </iframe> <?
                    } ?>
                    <button class="reg_btn" type="button">Запросить секретный ключ</button>
                    <br/>

                    <div class="reg_block">
                        <label>Email<input type="text" class="uptolike_email"></label><br/>
                        <button type="button" class="button-primary">Отправить ключ на email</button>
                        <br/>
                    </div>
                    <button class="enter_btn" type="button">Авторизация</button>
                    <br/>

                    <div class="enter_block">
                        <label>Email<input type="text" class="uptolike_email"></label><br/>
                        <label>Ключ<input type="text" class="id_number"></label><br/>
                        <button type="button" class="button-primary">Сохранить</button>
                        <br/>
                    </div>
                </div>

                <div class="wrapper-tab " id="con_settings">
                    <input type="hidden" name="option_page" value="my_option_group"><input type="hidden" name="action"
                                                                                           value="update"><input
                        type="hidden" id="_wpnonce" name="_wpnonce" value="cac4f73a65"><input type="hidden"
                                                                                              name="_wp_http_referer"
                                                                                              value="/wp-admin/options-general.php?page=uptolike_settings">

                    <h3>Настройки виджета</h3>
                    <table class="form-table">
                        <tbody>
                        <tr style="display:none">
                            <th scope="row">код виджета</th>
                            <td><textarea id="widget_code" name="my_option_name[widget_code]"></textarea></td>
                        </tr>
                        <tr style="display:none">
                            <th scope="row">Ключ(CryptKey)</th>
                            <td><input type="text" class="id_number" name="my_option_name[id_number]" value=""
                                       style="width: 520px;"></td>
                        </tr>
                        <tr style="display:none">
                            <th scope="row">email для регистрации</th>
                            <td><input type="text" id="uptolike_email" name="my_option_name[uptolike_email]"
                                       value="">
                                <button type="button" onclick="regMe();">Зарегистрироваться</button>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Располагать блок на главной странице</th>
                            <td><input type="checkbox" id="on_main" name="my_option_name[on_main]" checked="checked">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Расположение виджета</th>
                            <td><select id="widget_position" name="my_option_name[widget_position]">
                                    <option value="top">Только сверху</option>
                                    <option selected="selected" value="bottom">Только снизу</option>
                                    <option value="both">Сверху и снизу</option>
                                </select></td>
                        </tr>
                        <tr>
                            <th scope="row">Обратная связь</th>
                            <td><a href="mailto:support@uptolike.com" target="_top"> support@uptolike.com</a></td>
                        </tr>
                        <tr style="display:none">
                            <th scope="row">настройки конструктора</th>
                            <td><input type="hidden" id="uptolike_json" name="my_option_name[uptolike_json]" value="">
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <input type="submit" name="submit_btn" value="Cохранить изменения">
                </div>


            </form>
        </div>
    </div>
<?php
}

usb_admin_page();