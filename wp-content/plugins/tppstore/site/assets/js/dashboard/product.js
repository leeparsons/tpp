jQuery(function($) {





    $('fieldset').each(function(k) {

        $('.aside-25').find('li').eq(k).data('ind', k);

        $(this).data('ind', k);
        if (k > 0 && !$(this).hasClass('notoggle')) {
            $(this).slideUp();
        } else {
            $(this).addClass('open');
        }
    });

    $('a.step').on('click', function(e) {
        e.preventDefault();
        var self = $(this);
        $('.aside-25').find('li').eq(self.data('step')).click();
    });

    $('.aside-25').on('click', 'li', function() {
        generateShortDescription();
        //fulfill the seo meta box
        var self = $(this);
        $('fieldset.open').removeClass('open').slideUp('normal', function() {
            $('.aside-25').find('.active').removeClass('active');
            self.addClass('active');
            $('fieldset').eq(self.data('ind')).addClass('open').slideDown('normal', function() {
                $('html, body').animate({
                    scrollTop: $('.aside-25').offset().top
                }, 100);
            });
        });





    });

    $('[name="price_includes_tax"]').on('change', function() {

        if (document.getElementById('include_tax_na').checked === true) {
            document.getElementById('tax_rate').value = '';
            document.getElementById('tax_group').style.display = 'none';
        } else {
            document.getElementById('tax_group').style.display = 'block';
        }
    });


    var preview_ajax = false;
    var preview_w = false;
    var preview_interval = 0;

    $('#product_form').on('click', 'input[type="submit"]', function(e) {
        $('#product_form').data('e', $(this));
    });

    $('#product_form').on('submit', function(e) {


        if (preview_ajax !== false) {
            preview_ajax.abort();
        }




        if (true === dropper.uploading) {
            var c = confirm('Some images have not finished uploading. If you choose to continue, you may lose these images. Continue?');

            if (false === c) {
                return false;
            }

        }

        if ($(this).data('e').hasClass('publish')) {
            document.getElementById('enabled_yes').checked = true;
        } else if ($(this).data('e').hasClass('unpublish')) {
            document.getElementById('enabled_no').checked = true;
        }

        //var publish = $('[name="product_enabled"]:checked').val();



//        if ($('#senabled').val() == 0 && publish == 1) {
//            var c = confirm('You can not publish yet because your store is not published. Continue if you wish to save anyway, but this item will not be published');
//            if (false === c) {
//                return false;
//            }
//
//            $('[name="product_enabled"]').eq(1).click();
//        }

        //validate all the required fields.

        if ($('#message').length == 1) {
            $('#message').html('');
        }

        var errors = [];

        var include_tax_yes = document.getElementById('include_tax_yes').checked;
        var include_tax_no = document.getElementById('include_tax_no').checked;

        if (include_tax_yes === true || include_tax_no === true) {
            var tax_rate = document.getElementById('tax_rate').value;
            if (tax_rate == '' || (
                (!isFinite(tax_rate) || isNaN(parseFloat(tax_rate)) || parseFloat(tax_rate) < 0)
                ))
            {
                errors.push('Please enter a tax rate so we can determine the tax to display on invoices');
            }
        }


        if (document.getElementById('product_type').value == 4) {
            if (document.getElementById('mentor_name').value.replace(/\s+/g, '') == '') {
                errors.push('Please enter the mentor\'s name');
            }

            if (document.getElementById('mentor_company').value.replace(/\s+/g, '') == '') {
                errors.push('Please enter the mentor\'s company/ business name');
            }

//            var s1, s2, s3;
//
//            s1 = document.getElementById('specialism_one').value.replace(/\s+/g, '');
//            s2 = document.getElementById('specialism_two').value.replace(/\s+/g, '');
//            s3 = document.getElementById('specialism_three').value.replace(/\s+/g, '');
//
//            if (s1 == '' && s2 == '' && s3 == '') {
//                errors.push('Please enter a specialism');
//            }
        }


        if ($('#product_title').val().replace(/\s+/g, '') == '') {
            errors.push('Please enter a title');
        }

        if (false === $('#unlimited').is(':checked')) {
            var quantity = $('#product_quantity').val();

            if (quantity.replace(/\s+/g, '') == '' || !isFinite(quantity) || isNaN(parseInt(quantity)) || quantity.indexOf('.') > -1 || quantity.indexOf(',') > -1) {
                errors.push('Please enter the quantity available as a number (without "." or ",")');
            }
        }

        var price = $('#product_price').val();

        if (!isFinite(price) || isNaN(parseFloat(price))) {
            errors.push('Please enter a price (no ",")');
        }

        if ($('.preview').find('img').length === 0) {
            errors.push('Please upload at least one image.');
        }




        if ($('#discount_type').is(':checked')) {

            var discount = $('#discount_value').val();

            if  (discount.replace(/\s+/g, '') == '' || !isFinite(discount) || isNaN(parseInt(discount)) || discount.indexOf('.') > -1 || discount.indexOf(',') > -1) {
                errors.push('Please enter a discount value');
            }
        }


        if (errors.length > 0) {

            e.preventDefault();

            if ($('#message').length == 0) {
                var message = $('<div id="message"></div>');
                $('.page-article-part').find('form').eq(0).prepend(message);
            } else {
                var message = $('#message');
            }

            for (var x in errors) {
                message.append('<p class="wp-error">' + errors[x] + '</p>');
            }

            $('html, body').animate({
                scrollTop: message.offset().top
            }, 750);


            overlay.setHeader('Oops, sorry there were errors');

            message = [];

            for (var x in errors) {
                message.push(errors[x]);
            }

            overlay.setBody('Please see the error messages and fill in the required fields:<br><br>' + message.join('<br>'));
            overlay.populateInner();


            return false;
        }



        if ($(this).data('e').hasClass('preview')) {


//            if ($('#lee').length === 0) {
//                alert('sorry, preview is currently under maintenance. It will be back soon!');
//                return false;
//
//            }
            overlay.setHeader('Crunching images and saving...');
            overlay.setBody('Please wait while we save your product and upload your images...')
            overlay.populateInner();

            if ($(this).find('#ajax').length == 0) {
                $(this).append('<input type="hidden" id="ajax" name="preview" value="1">');
            } else {
                $(this).find('#ajax').val('1');
            }


            var data = $(this).serialize();

//            var editor = tinyMCE.get('full_description');
//
//            if (editor) {
//                data += '&full_description=' + editor.getContent();
//            }

            preview_ajax = $.ajax(
                {
                    url:    $(this).prop('action'),
                    type:   'post',
                    data:   data,
                    success: function(response) {
                        console.log(response);
                        overlay.setHeader('Saved!');
                        overlay.setBody('Sending you to the preview... (please note if you do not see the preview open then you may have popups blocked (this is default in most browsers)<br><br>Please enable popups for this website to view your preview.');
                        overlay.populateInner();

                        console.log(response);
                        if (response.error === false && response.data && response.data.location) {
                            preview_w = window.open(response.data.location, '_blank');
                            setTimeout(function() {
                                preview_interval = setInterval(function() {
                                    if (preview_w == null || preview_w.closed) {
                                        overlay.close();
                                        clearInterval(preview_interval);
                                        $.ajax('/shop/dashboard/preview_close/');
                                    }
                                }, 100);
                            }, 15000);

                        }
return;
                        if (response.error === false && response.data.product) {
                            if ($('#product_form').find('[name="saved_preview"]').length == 0) {
                                $('#product_form').append('<input type="hidden" name="saved_preview" value="' + response.data.product + '">');
                            }

                            $('#p').val(response.data.product);
                            $('#p').after('<input type="hidden" name="saved_already" value="1">');
                            preview_w = window.open(response.data.location, '_blank');
                            preview_interval = setInterval(function() {
                                if (preview_w == null || preview_w.closed) {
                                    overlay.close();
                                    clearInterval(preview_interval);
                                }
                            }, 100);
//                            w.onbeforeunload = function() {
//                                overlay.close();
//                            }
                        } else if (response.data.errors) {

                            var str = '';
                            for (var x in response.data.errors) {
                                str += response.data.errors[x] + '<br>';
                            }
                            overlay.setHeader('Oops!');

                            overlay.setBody(str);



                            if ($('#message').length == 0) {
                                var m = document.createElement('div');
                                m = $(m);
                                m.prop('id', 'message')
                                    .addClass('error');
                                $('.page-article-part').find('.wrap').eq(0).prepend(m);
                            } else {
                                var m = $('#message');
                                m.find('p').each(function() {$(this).remove();});
                            }



                            for (var x in response.data.errors) {
                                m.append('<p class="wp-error">' + response.data.errors[x] + '</p>');
                            }

                            $('html, body').animate({
                                scrollTop: m.offset().top
                            }, 750);

                        } else {
                            overlay.setHeader('Oops!');
                            overlay.setBody('there was a problem saving your product. Please make sure you have filled out all the required fields');
                            overlay.populateInner();

                        }

                    },

                    error:  function(response) {


                        if (response.status > 0) {
                            overlay.setHeader('Oops!');
                            overlay.setBody('there was a problem saving your product. Please make sure you have filled out all the required fields');
                            overlay.populateInner();
                        } else {
                            overlay.close();
                        }

                    }

                }
            );

            e.preventDefault();

            return false;
        } else {
            $(this).find('#ajax').val('0').remove();
        }


    });



});



var product_options_count = 0;

var sizer = function() {

    this.obj = [];

    this.setObj = function(obj) {
        this.obj = obj;
    }

    this.getSize = function() {
        var count = 0;
        for (var key in this.obj) {
            if (this.obj.hasOwnProperty(key))
                count++;
        }
        return count;
    }

    this.getElementAt = function(ind) {
        if (ind != undefined) {
            var count = 0;
            for (var key in this.obj) {
                if (this.obj.hasOwnProperty(key)) {
                    if (count == ind) {
                        return this.obj[key];
                    } else {
                        count++;
                    }
                }
            }

        }
        return false;
    }

};

document.getElementById('unlimited').onclick = function() {
    var checked = this.checked;
    if (checked === true) {
        document.getElementById('product_quantity').style.visibility = 'hidden';
    } else {
        document.getElementById('product_quantity').style.visibility = 'visible';
    }
}

if (document.getElementById('add_product_variation')) {
    document.getElementById('add_product_variation').onclick = function() {

        var price = document.getElementById('variation_cost').value;
        var name = document.getElementById('variation_name').value;
        var availability = document.getElementById('variation_availability').value;

        switch (currency) {
            default:
                price = price.replace('£', '').replace(currency, '');
                break;

            case '&dollar;':
                price = price.replace('$', '').replace(currency, '');
                break;


        }



        document.getElementById('option_cost_error').setAttribute('class', 'hidden wp-error');
        document.getElementById('option_availability_error').setAttribute('class', 'hidden wp-error');
        document.getElementById('option_name_error').setAttribute('class', 'hidden wp-error');
        error = false;
        if (price.replace(/\s/g, '') == '' || isNaN(parseFloat(price))) {
            document.getElementById('option_cost_error').innerHTML = 'Please enter a price';
            document.getElementById('option_cost_error').setAttribute('class', 'wp-error');
            error = true;
        }

        if (name.replace(/\s/g, '') == '') {
            document.getElementById('option_name_error').innerHTML = 'Please enter a name';
            document.getElementById('option_name_error').setAttribute('class', 'wp-error');
            error = true;
        }

        if (isNaN(parseInt(availability))) {
            document.getElementById('option_availability_error').innerHTML = 'Please enter the quantity available';
            document.getElementById('option_availability_error').setAttribute('class', 'wp-error');
            error = true;
        }

        if (error === false) {

            var li = document.createElement('li');

            var close = document.createElement('span');
            close.setAttribute('class', 'close');
            close.innerHTML = 'x';


            li.innerHTML = '<span class="option-name">' + name +  '</span><span class="option-price"> ' + currency + parseFloat(price) + '</span> <span class="option-availability">' + parseInt(availability) + ' available</span>' +
                '<input type="hidden" name="product_option_new[' + product_options_count + '][name]" value="' + encodeURI(name) + '">' +
                '<input type="hidden" name="product_option_new[' + product_options_count + '][price]" value="' + parseFloat(price) + '">' +
                '<input type="hidden" name="product_option_new[' + product_options_count + '][availability]" value="' + parseInt(availability)+ '">';

            product_options_count++;

            li.appendChild(close);

            close.onclick = function() {
                document.getElementById('product_options').removeChild(this.parentNode);
            }

            document.getElementById('product_options').appendChild(li);

            this.disabled = true;
            this.value = 'Option Added!';
            var self = this;
            setTimeout(function() {

                document.getElementById('variation_cost').value = '';
                document.getElementById('variation_name').value = '';
                document.getElementById('variation_availability').value = '';


                self.disabled = false;
                self.value = 'Add Option';

            }, 500);
        }

        return false;

    }
}

if (document.getElementsByClassName('close').length > 0) {
    for (var x in document.getElementsByClassName('close')) {
        document.getElementsByClassName('close')[x].onclick = function() {
            document.getElementById('product_options').removeChild(this.parentNode);
        }
    }
}

document.getElementById('product_type').onchange = function() {

    var ems = document.getElementsByClassName('product-type-group');

    if (this.value == 2) {
        document.getElementById('product_type_label').style.display = 'block';
    } else {
        document.getElementById('product_type_label').style.display = 'none';
    }

    for (var x = 0; x < ems.length; x++) {
        if (x+1 != this.value) {
            ems[x].setAttribute('class', 'product-type-group form-group hidden');
        } else {
            ems[x].setAttribute('class', 'product-type-group form-group');
        }
    }

}

function generateShortDescription() {
    var txt = '';

    //var editor = tinyMCE.get('full_description');
    //var content = editor.getContent();

    var content = document.getElementById('full_description').value;

    if (this == document.getElementById('full_description')) {
        var l = mb_strlen(content);
        document.getElementById('description_count').innerHTML = (21000 - l) + ' characters remaining';
        if (l > 21000) {
            document.getElementById('description_count').setAttribute('class', 'wp-error');
        } else {
            document.getElementById('description_count').setAttribute('class', 'wp-message');
        }

    }

    var tmp = document.createElement("DIV");

    tmp.innerHTML = content;
    if (tmp.textContent) {
        content = tmp.textContent;
    } else if (tmp.innerText) {
        content = tmp.innerText;
    }

    if (content && document.getElementById('short_description').value == '') {
        txt = content.replace(/(\r\n|\n|\r)/gm, ' ').replace(/\s+/g, ' ').substr(0, 150);
        //determine if this.value contains line breaks?
        document.getElementById('short_description').value = txt;
    }

    var v = 150 - document.getElementById('short_description').value.length;
    document.getElementById('short_character_count').innerHTML = v;
    document.getElementById('short_character_term').innerHTML = v == 1?'character':'characters';

    if (v < 0) {
        document.getElementById('short_character_count').parentNode.setAttribute('class', 'alert-danger')
    }

}

function mb_strlen(str) {
    var len = 0;
    for(var i = 0; i < str.length; i++) {
        len += str.charCodeAt(i) < 0 || str.charCodeAt(i) > 255 ? 2 : 1;
    }
    return len;
}



//document.getElementById('wp-full_description-editor-container').onkeyup = generateShortDescription;
document.getElementById('full_description').onkeyup = generateShortDescription;
document.getElementById('short_description').onkeyup = generateShortDescription;





function catSwitcher(em, id, levels)
{
    var optn = '';
    var obj = category_options;


    var s = new sizer();
    var children_size = 0;

    for (var x = 1; x <= id; x++) {
        if (x > 1) {


//                if (obj != undefined && obj.children[document.getElementById('category_' + x).value] != undefined && obj.children[document.getElementById('category_' + x).value].children != undefined) {
//                    obj = obj.children[document.getElementById('category_' + x).value];
//                } else {
//                    obj = undefined;
//                }


            if (obj != undefined && obj.children[document.getElementById('category_' + x).value] != undefined && obj.children[document.getElementById('category_' + x).value].children != undefined) {
                obj = obj.children[document.getElementById('category_' + x).value];
                s.setObj(obj.children);
            } else {
                //obj = undefined;
                s.setObj([]);
            }


        } else {


            if (obj != undefined && obj[document.getElementById('category_' + x).value] != undefined && obj[document.getElementById('category_' + x).value].children != undefined ) {
                s.setObj(obj[document.getElementById('category_' + x).value].children);
            } else {
                s.setObj([]);
            }

            children_size = s.getSize();

            if (children_size > 0) {
                obj = obj[document.getElementById('category_' + x).value];
            } else {
                obj = undefined;
            }
        }
    }

    children_size = s.getSize();


    if (children_size > 0) {

        var em;

        document.getElementById('category_' + (id+1)).innerHTML = '';

        optn = document.createElement('option');
        optn.setAttribute('value', '');
        optn.innerHTML = '-- select --';

        document.getElementById('category_' + (id+1)).appendChild(optn);

        for (var x = 0; x < children_size; x++) {
            em = s.getElementAt(x);

            optn = document.createElement('option');
            optn.setAttribute('value', em.category_id);
            //optn.setAttribute('value', obj.children[x].category_id);

            //optn.innerHTML = obj.children[x].category_name;

            optn.innerHTML = em.category_name;


            document.getElementById('category_' + (id+1)).appendChild(optn);
        }
        document.getElementById('category_' + (id+1)).parentNode.setAttribute('class', 'control-group');

    } else {

        var ems = document.getElementsByClassName('category-list');

        for (var x = id; x < ems.length; x++) {

            ems[x].innerHTML = '<option value="">-- Select category -- </option>';

            ems[x].parentNode.setAttribute('class', 'control-group hidden');
        }

    }
}

if (document.getElementById('category_1')) {
    document.getElementById('category_1').onchange = function() {
        if (this.value == 3) {
            //hide the product type selection
            document.getElementById('product_type').value = 2;
            document.getElementById('product_type').style.display = 'none';
            document.getElementById('product_type_label').style.display = 'none';
            document.getElementById('download_group').setAttribute('class', 'product-type-group form-group hidden');

        } else {
            //show the product type selection
            document.getElementById('product_type').style.display = 'block';
            document.getElementById('product_type_label').style.display = 'block';
            document.getElementById('product_type').value = 2;
            document.getElementById('download_group').setAttribute('class', 'product-type-group form-group hidden');
        }
        catSwitcher(this, 1);
    }



    document.getElementById('category_2').onchange = function() {
        catSwitcher(this, 2, [document.getElementById('category_1').value]);
    }
}

//
//function makeShortDescription(e) {
//
//    if (e.which == 8 || document.activeElement == document.getElementById('short_description')) {
//        //delete
//        return;
//    }
//    if (tinyMCE.activeEditor != null && tinyMCE.activeEditor != undefined) {
//        if (document.getElementById('short_description').value.length == 0) {
//            var tmp = tinyMCE.activeEditor.getContent().replace(/(<([^>]+)>)/ig, " ").replace(/\s{2,}/g, ' ').trim();
//            var v = '';
//            if (tmp.length > 150) {
//                for (var x in tmp) {
//                    v += tmp[x];
//                    if (v.length == 150) {
//                        break;
//                    }
//                }
//            } else {
//                v = tmp;
//            }
//
//            var l = 150 - v.length;
//            document.getElementById('short_character_count').innerHTML = l;
//            document.getElementById('short_character_term').innerHTML = l == 1?'character':'characters';
//            document.getElementById('short_description').value = v;
//        }
//    }
//}
//
//document.onkeyup = makeShortDescription;
//document.onclick = makeShortDescription;


document.getElementById('discount_value').onkeyup = function() {

    this.value = this.value.replace(/[^0-9.]/g, '');

}