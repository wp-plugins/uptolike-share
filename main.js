var onmessage = function (e) {
    
    if (e.data !== null && typeof e.data === 'object') {
        if ('ready' == e.data.action ){
            json = jQuery('input#uptolike_json').val();
            initConstr(json);
        }
        if (('json' in e.data) && ('code' in e.data)) {
            $('input#uptolike_json').val(e.data.json);
            $('#widget_code').val(e.data.code);
            jQuery('#settings_form').submit();
        }
        if (e.data.url.indexOf('constructor.html', 0) != -1) {
            document.getElementById("cons_iframe").style.height = e.data.size + 'px';
        }
        if (e.data.url.indexOf('statistics.html', 0) != -1) {
            document.getElementById("stats_iframe").style.height = e.data.size + 'px';
        }
            
    }
};

if (typeof window.addEventListener != 'undefined') {
    window.addEventListener('message', onmessage, false);
} else if (typeof window.attachEvent != 'undefined') {
    window.attachEvent('onmessage', onmessage);
}
var getCode = function () {
    var win = document.getElementById("cons_iframe").contentWindow;
    win.postMessage({action: 'getCode'}, "*");
};
function initConstr(jsonStr) {
    var win = document.getElementById("cons_iframe").contentWindow;
    if ('' !== jsonStr) {
        win.postMessage({action: 'initialize', json: jsonStr}, "*");
    }

}


function regMe(my_mail) {
    str = jQuery.param({ email: my_mail,
                        partner: 'cms',
                        projectId: 'cms' + document.location.host.replace( new RegExp("^www.","gim"),"").replace(/\-/g, '').replace(/\./g, ''),
                        url:document.location.host.replace( new RegExp("^www.","gim"),"")})
    dataURL = "http://uptolike.com/api/getCryptKeyWithUserReg.json";
    jQuery.getJSON(dataURL + "?" + str + "&callback=?", {}, function (result) {
        var jsonString = JSON.stringify(result);
        var result = JSON.parse(jsonString);
        if ('ALREADY_EXISTS' == result.statusCode) {
            alert('Пользователь с таким email уже зарегистрирован, обратитесь в службу поддержки.');
        } else if ('MAIL_SENDED' == result.statusCode) {
            alert('Ключ отправлен вам на email. Теперь необходимо ввести его в поле ниже.');
            $('.reg_block').toggle('fast');
            $('.reg_btn').toggle('fast');
            $('.enter_btn').toggle('fast');
            $('.enter_block').toggle('fast');

        } else if ('ILLEGAL_ARGUMENTS' == result.statusCode) {
            alert('Email указан неверно.')
        }
    });
}

function hashChange(){
    var hsh = document.location.hash
  //  if ('#settings' == hsh) {
  //      $('.nav-tab-wrapper a').removeClass('nav-tab-active');
  //      $('a.nav-tab#settings').addClass('nav-tab-active');
  //      $('.wrapper-tab').removeClass('active');
  //      $('#con_settings').addClass('active');
  //  }

   // else
    if (('#reg' == hsh) || ('#enter' == hsh)) {

        $('.nav-tab-wrapper a').removeClass('nav-tab-active');
        $('a.nav-tab#stat').addClass('nav-tab-active');
        $('.wrapper-tab').removeClass('active');
        $('#con_stat').addClass('active');

        if ('#reg' == hsh) {
            $('.reg_btn').show();
            $('.reg_block').show();
            $('.enter_btn').hide();
            $('.enter_block').hide();
        }
        if ('#enter' == hsh) {
            $('.reg_btn').hide();
            $('.reg_block').hide();
            $('.enter_btn').show();
            $('.enter_block').show();
        }
    }
}

window.onhashchange = function() {
    hashChange();
}

jQuery(document).ready(function () {
    $ = jQuery;

    $('input.id_number').css('width','520px');//fix
    $('.uptolike_email').val($('#uptolike_email').val())//init fields with hidden value (server email)
    $('.enter_block input.id_number').attr('value', $('table input.id_number').val());

    $('div.enter_block').hide();
    $('div.reg_block').hide();

    $('.reg_btn').click(function(){
        $('.reg_block').toggle('fast');
        $('.enter_btn').toggle('fast');
    })

    $('.enter_btn').click(function(){
        $('.enter_block').toggle('fast');
        $('.reg_btn').toggle('fast');
    })

    $('.reg_block button').click(function(){
        my_email = $('.reg_block .uptolike_email').val();
        regMe(my_email);
    })

    $('.enter_block button').click(function(){
        my_email = $('.enter_block input.uptolike_email').val();
        my_key = $('.enter_block input.id_number').val();
        $('table input.id_number').attr('value',my_key);
        $('table input#uptolike_email').attr('value',my_email);
        $('#settings_form').submit();
    })

    //if unregged user
    if ($('.id_number').val() == '') {
        $('#uptolike_email').after('<button type="button" onclick="regMe();">Зарегистрироваться</button>');
        json = $('input#uptolike_json').val();
        initConstr(json);
    }
    $('#widget_code').parent().parent().attr('style', 'display:none');
    $('#uptolike_json').parent().parent().attr('style', 'display:none')
    $('table .id_number').parent().parent().attr('style', 'display:none')
    $('#uptolike_email').parent().parent().attr('style', 'display:none')

    $('.nav-tab-wrapper a').click(function (e) {
        e.preventDefault();
        var click_id = $(this).attr('id');
        if (click_id != $('.nav-tab-wrapper a.nav-tab-active').attr('id')) {
            $('.nav-tab-wrapper a').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            $('.wrapper-tab').removeClass('active');
            $('#con_' + click_id).addClass('active');
        }
    });

    hashChange();
    $.getScript( "http://uptolike.com/api/getsession.json" )
        .done(function( script, textStatus ) {
            $('iframe#cons_iframe').attr('src',$('iframe#cons_iframe').attr('data-src'));
            $('iframe#stats_iframe').attr('src',$('iframe#stats_iframe').attr('data-src'));
        });

});

