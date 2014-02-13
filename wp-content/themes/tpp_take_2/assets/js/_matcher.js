var tppResizer = function() {
    var items = document.getElementsByClassName('item-box');
    var sh = 0;
    if (items && items.length > 0) {
        var items_heights = [];
        for (var x = 0; x < items.length; x++) {
            items_heights.push(items[x].clientHeight);
            var ch = items[x].children;
            if (ch.length > 0) {
                ch = ch[0].children;
                if (ch.length > 0) {
                    if (ch[0].tagName == 'IMG') {
                        //sh = imgs[x].clientHeight;
                        sh = 250;
                        if (ch[0].clientHeight < sh) {
                            ch[0].style.marginTop = ((sh - ch[0].clientHeight)/2) + 'px';
                            ch[0].style.marginBottom = ((sh - ch[0].clientHeight)/2) + 'px';
                        }
                    }
                }
            }
        }

        var highest_height = items_heights.sort(function(a,b){return b-a;})[0];
        for (var x = 0; x < items.length; x++) {
            items[x].style.height = highest_height + 'px';
        }

    }
}


if (window.attachEvent) {window.attachEvent('onload', tppResizer);}
else if (window.addEventListener) {window.addEventListener('load', tppResizer, false);}
else {document.addEventListener('load', tppResizer, false);}