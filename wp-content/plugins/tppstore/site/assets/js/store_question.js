//ask the store owner a question functionality
jQuery(function($) {

    var ask_question = {
        init:function() {
            if ($('#ask_question').length > 0) {
                $('#ask_question').on('click', 'a', function() {
alert('s');
                });
            }
        }
    }

    ask_question.init();

});