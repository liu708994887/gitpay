$(function() {
    $('#reset_amount').on('click', function() {
        $('#total_amount').val('').focus();  
    });  
    $('#m_zl01').on('click', function(e) {
        e.preventDefault();
        set('m_zl0',1,3);
        $('#bar_code').focus();
    }); 
    $('#barcode_submit').on('click', function(e) {
        e.preventDefault(); 
        var o = {};
        o.pay_type = $("input[name='pay_type']:checked").val(); 
        if (isEmpty(o.pay_type)) {
            alert('请选择支付方式'); 
            return false;
        }
        o.total_amount = $('#total_amount').val();
        if (isEmpty(o.total_amount)) {
            alert('请填写支付金额');
            return false;
        }
        o.bar_code = $('#bar_code').val();
        if (isEmpty(o.bar_code)) {
            alert('请填写条形码数据');
            return false;
        }
        o.action = 'brpay';
        var pay_res = barCodePay(o);
    });
    function barCodePay(data) {
        var url = data.pay_type == 'alipay' ? 'alipay_1.0/core.php' : '';
        $.ajax({
            url : url, 
            type : 'POST',
            data : data,
            dataType : 'json',
            success : 'afterBarCodePay'
        }).always(function() {
            $('#bar_code').html('处理中...').next().text('下单处理中'); 
        });
    }
    function afterBarCodePay(resp) {
        console.debug(resp); 
        return false;
    }
    function isEmpty(value) {
        if(value == null || value == "" || value == "undefined" || value == undefined || value == "null") {
            return true;
        } else {
            value = (value+"").replace(/\s/g,'');
            if (value == '') {
                return true; 
            }
            return false;
        }
    }
});
