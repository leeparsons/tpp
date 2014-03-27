jQuery(function($) {

    var blog2p_ajax = null;

    $('#selected_products').on('click', '.remove-product', function(e) {
        e.preventDefault();
        $('#product_' + $(this).data('val')).removeClass('remove').addClass('add').html('+');
        $(this).parent().remove();
    });

        $('#blog2product_find').on('click', function(e) {
            e.preventDefault();

            $(this).next('img').show();

            if (blog2p_ajax != null) {
                blog2p_ajax.abort();
            }

            $('#related_product_list').hide().html('');

            blog2p_ajax = $.ajax({
                url:  ajaxurl,
                type: 'post',
                data:   {
                    s:  $('#blog2product_search').val(),
                    action: 'find_b2p_products'
                },
                dataType:   'json',
                success:    function(response) {



                    if (response.data && response.data.length > 0) {

                        $('#related_product_list').show();

                        var li;
                        var inpt;
                        var lbl;
                        var img;
                        var checked = 0;
                        for (var x in response.data) {

                            li = document.createElement('li');
                            inpt = document.createElement('a');

                            if ($('#selected_products').find('#product_wrap' + response.data[x].product_id).length == 1) {
                                checked = 1;
                            } else {
                                checked = 0;
                            }

                            img = document.createElement('img');


                            if (response.data[x].image !== false) {
                                $(img).attr('src', response.data[x].image).css('width', '100px');
                            }

                            $(inpt).prop({
                                id:     'product_' + response.data[x].product_id
                            }).data({
                                    val: response.data[x].product_id,
                                    checked: checked,
                                    display_title: response.data[x].product_title + ' -- ' + response.data[x].store_name
                                }).on('click', function(e) {

                                    if ($(this).data('checked') == '0' && $('#selected_products').find('span').length >= 4) {
                                        alert('You have selected the maximum number of products');
                                        return false;
                                    }

                                    e.preventDefault();

                                    if ($(this).data('checked') == '0') {
                                        $(this).html('-').removeClass('add').addClass('remove');
                                        $(this).data('checked', 1);
                                        if ($('#selected_products').find('#product_wrap' + $(this).data('val')).length == 0) {
                                            $('#selected_products').append('<span style="display:block;" id="product_wrap' + $(this).data('val') + '"><input type="hidden" name="product[' + $(this).data('val') + ']" value="' + $(this).data('val') + '">' + $(this).data('display_title') + '<a href="#" data-val="' + $(this).data('val') + '" class="remove-product"><img src="/assets/images/cross.png"></a></span>');
                                        }
                                    } else {
                                        $(this).html('+').removeClass('remove').addClass('add');
                                        $(this).data('checked', 0);
                                        if ($('#selected_products').find('#product_wrap' + $(this).data('val')).length == 1) {
                                            $('#selected_products').find('#product_wrap' + $(this).data('val')).remove();
                                        }
                                    }
                                }).html(checked == 1?'-':'+').addClass(checked == 1?'remove':'add');

                            lbl = document.createElement('label');

                            $(lbl).attr('for', 'product_' + response.data[x].product_id);
                            $(lbl).append(inpt, img).append(response.data[x].product_title);

                            $(li).append(lbl);

                            $(li).data({
                                id:response.data[x].product_id,
                                title:response.data[x].product_title,
                                store:response.data[x].store_title
                            });
                            $('#related_product_list').append(li);
                        }



                    } else {
                        $('#related_product_list').hide();
                    }
                    $('#blog2product_find').next('img').hide();
                }
            });

        });

});