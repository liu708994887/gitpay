$(function() {
    $('#reset_amount').on('click', function() {
        $('#total_amount').val('').focus();  
    });  
    $('#m_zl01').on('click', function(e) {
        e.preventDefault();
        set('m_zl0',1,3);
        $('#bar_code').focus();
    }); 
    $('body').on('click', '#barcode_submit', function(e) {
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
        var pay_res = barCodePay(o);
    });
    function barCodePay(data) {
        $.ajax({
            url : 'alipay_1.0/core.php?action=brpay', 
            type : 'POST',
            data : data,
            dataType : 'json',
            success : afterBarCodePay
        }).done(function() {
            $('#barcode_submit').remove(); 
            $('#barcode_tip').text('下单处理中'); 
        });
    }
    function afterBarCodePay(resp) {
        $('#barcode_tip').text(resp.message);
        if (resp.status == 'success') {
            if (resp.data.query == 1) {
                var data = {order_no : resp.data.order_no, order_ts : resp.data.order_ts};
                var bar_code_query = setInterval(function() {
                    $.ajax({
                        url : 'alipay_1.0/core.php?action=query', 
                        type : 'GET',
                        data : data,
                        dataType : 'json',
                        success : function(res) {
                            $('#barcode_tip').text(res.message);  
                            if (res.status == 'failed' || res.data.query == 0) {
                                clearInterval(bar_code_query);
                                $('#bar_code').val('');
                                $('#total_amount').val('').focus();
                                $('#barcode_tip').before('<a href="#" id="barcode_submit"><img src="images/zf_30.jpg" width="97" height="36"/></a>');
                            }
                        } 
                    });
                }, 5000);                 
            }
        } 
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
