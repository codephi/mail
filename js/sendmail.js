(function ($) {
    $.fn.sendmail = function (custom) {
        var merge = function (object, target) {
            var newObject = {};
            for (var key in object)
                if (target[key] != undefined)
                    newObject[key] = target[key];
                else
                    newObject[key] = object[key];

            return newObject;
        };

        var optDefault = {
            url: '',
            success: function (response) {
                if ($('.return', target).length) {
                    var targetReturn = $('.return', this);

                    if (targetReturn.is('input'))
                        targetReturn.val(response.msg).addClass(response.success ? 'success' : 'error');
                    else
                        targetReturn.html(response.msg).addClass(response.success ? 'success' : 'error');
                }
                else if (response.msg)
                    alert(response.msg);
                else
                    console.log(response);

                if (response.captcha === true || response.error === true) {
                    if ($('.captcha', target).length) {
                        if ($('.captcha', target).is('img'))
                            $('.captcha', target).attr('src', options.url);
                        else
                            $('.captcha img', target).attr('src', options.url);

                        $('input[name=captcha]', target).val('');
                    }
                }
            },
            dataType: 'json',
            type: 'POST'
        };

        var options = (typeof custom == 'object') ? merge(optDefault, custom) : optDefault;
        var target = $(this);

        $(this).submit(function () {
            var data = $(this).serializeArray();
            $.ajax({
                type: options.type,
                url: options.url,
                data: data,
                success: options.success,
                dataType: options.dataType
            });
            return false;
        })
    }
})(jQuery);