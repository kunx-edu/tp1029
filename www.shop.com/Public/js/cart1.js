/*
 @功能：购物车页面js
 @作者：diamondwang
 @时间：2013年11月14日
 */

$(function () {

    //减少
    $(".reduce_num").click(function () {
        var amount = $(this).parent().find(".amount");
        if (parseInt($(amount).val()) <= 1) {
            alert("商品数量最少为1");
        } else {
            $(amount).val(parseInt($(amount).val()) - 1);
        }
        //小计
        var subtotal = parseFloat($(this).parent().parent().find(".col3 span").text()) * parseInt($(amount).val());
        $(this).parent().parent().find(".col5 span").text(subtotal.toFixed(2));
        //总计金额
        var total = 0;
        $(".col5 span").each(function () {
            total += parseFloat($(this).text());
        });

        $("#total").text(total.toFixed(2));
        
        //将商品的数量发送给后端
        var goods_id = $(amount).attr('goods_id');
        amount = $(amount).val();
        change_amount(goods_id,amount);
        
    });

    //增加
    $(".add_num").click(function () {
        var amount = $(this).parent().find(".amount");
        $(amount).val(parseInt($(amount).val()) + 1);
        //小计
        var subtotal = parseFloat($(this).parent().parent().find(".col3 span").text()) * parseInt($(amount).val());
        $(this).parent().parent().find(".col5 span").text(subtotal.toFixed(2));
        //总计金额
        calc_total_price()
        
        
        //将商品的数量发送给后端
        var goods_id = $(amount).attr('goods_id');
        amount = $(amount).val();
        change_amount(goods_id,amount);
    });

    //直接输入
    $(".amount").blur(function () {
        if (parseInt($(this).val()) < 1) {
            alert("商品数量最少为1");
            $(this).val(1);
        }
        //小计
        var subtotal = parseFloat($(this).parent().parent().find(".col3 span").text()) * parseInt($(this).val());
        $(this).parent().parent().find(".col5 span").text(subtotal.toFixed(2));
        //总计金额
        calc_total_price();
        
        
        //将商品的数量发送给后端
        var goods_id = $(amount).attr('goods_id');
        amount = $(amount).val();
        change_amount(goods_id,amount);

    });
    
    //删除
    $(".remove_goods").click(function () {
        //将商品的数量发送给后端
        var goods_id = $('.amount').attr('goods_id');
        var amount = 0;
        change_amount(goods_id,amount);
        $(this).parent().parent().remove();
        calc_total_price();
    });
    
    
    //总计金额
    function calc_total_price(){
        var total = 0;
        $(".col5 span").each(function () {
            total += parseFloat($(this).text());
        });

        $("#total").text(total.toFixed(2));
    }
});