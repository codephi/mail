/**
 * Created by philippe on 04/04/15.
 */
(function(a){a.fn.sendmail=function(f){var h=function(b,a){var d={},c;for(c in b)d[c]=void 0!=a[c]?a[c]:b[c];return d},g={url:"",success:function(b){if(a(".return",d).length){var e=a(".return",this);e.is("input")?e.val(b.msg).addClass(b.success?"success":"error"):e.html(b.msg).addClass(b.success?"success":"error")}else b.msg?alert(b.msg):console.log(b);!0!==b.captcha&&!0!==b.error||!a(".captcha",d).length||(a(".captcha",d).is("img")?a(".captcha",d).attr("src",c.url):a(".captcha img",d).attr("src",
    c.url),a("input[name=captcha]",d).val(""))},dataType:"json",type:"POST"},c="object"==typeof f?h(g,f):g,d=a(this);a(this).submit(function(){var b=a(this).serializeArray();a.ajax({type:c.type,url:c.url,data:b,success:c.success,dataType:c.dataType});return!1})}})(jQuery);
