define(['jquery', 'bootstrap', 'backend', 'table', 'form','clipboard'], function ($, undefined, Backend, Table, Form, Clipboard) {

    var Controller = {
        index: function () {
            //让错误提示框居中
            Fast.config.toastr.positionClass = "toast-top-center";

            //本地验证未通过时提示
            $("form[role=form]").data("validator-options", {
                invalid: function (form, errors) {
                    $.each(errors, function (i, j) {
                        Toastr.error(j);
                    });
                },
                target: '#errtips'
            });

            Form.api.bindevent($("form[role=form]"), function (ret) {
                setTimeout(function () {
                    location.reload();
                }, 1500);
            });

            $(".btn-unlink").click(function () {
                Layer.confirm('账号安全性将大大降低，确认后如需绑定请再次扫码', {icon: 3, title: '确认解绑二次验证？'}, function () {
                    Fast.api.ajax({
                        url: 'setting/google_unlink',
                        data: {
                            
                        }
                    }, function (data, ret) {
                        if (ret && ret.code === 1) {
                            setTimeout(function () {
                                location.reload();
                            }, 1500);
                        }
                    });
                });
                return false;
            });

            $('.show_pass').click(function () {
                let pass_type = $('input.password').attr('type');
        
                if (pass_type === 'password' ){
                    $('input.password').attr('type', 'text');
                    $('.show_pass').find('i').removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    $('input.password').attr('type', 'password');
                    $('.show_pass').find('i').removeClass('fa-eye-slash').addClass('fa-eye');
                }
            })

            $(".btn-refresh").click(function () {
                let str = '';
                let chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                for (let i = 0; i < 18; i++) {
                    str += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                
                $(this).prev().attr('data-clipboard-text', str)
                $('input.password').val(str);
            });  

            var clipboard = new Clipboard('.btn-copy');
            clipboard.on('success', function(e) {
                console.log(e)
                Toastr.success('复制成功');
            });
            clipboard.on('error', function(e) {
                Toastr.error('复制失败');
            })

        },
        
    };
    return Controller;
});
