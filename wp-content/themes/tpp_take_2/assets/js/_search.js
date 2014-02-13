var sf = {
    filters:[],
    filter_wrap: {},
    to: 0,
    search:{},
    init: function() {
        sf.filter_wrap = document.getElementById('search_filters');

//        var tmp = sf.filter_wrap.getElementsByTagName('INPUT');
//        for (var x = 0; x < tmp.length; x++) {
//            if (typeof tmp[x] === 'object' && tmp[x].tagName === 'INPUT') {
//                tmp[x].onclick = sf.click;
//                sf.filters.push(tmp[x]);
//            }
//        }
        sf.search = document.getElementById('search_all');
        sf.search.onfocus = sf.focus;
        sf.search.onfocus = sf.showButton;
        sf.search.onclick = sf.focus;
        sf.search.onkeyup = sf.focus;
        sf.search.onblur = sf.blur;
        sf.filter_wrap.onmouseover = sf.focus;
        sf.filter_wrap.onclick = sf.focus;
        sf.search_button = document.getElementById('search_button');
        sf.filter_wrap.onmouseout = sf.blur;
    },
//    click: function() {
//        if (this.value === -1) {
//            for (var x in sf.filters) {
//                sf.filters[x].checked = this.checked;
//            }
//        } else {
//            //determine if any of the filters are not checked?
//            var chck = true;
//            for (var y = 1; y < sf.filters.length; y++) {
//                if (sf.filters[y].checked === false) {
//                    chck = false;
//                    break;
//                }
//            }
//
//            sf.filters[0].checked = chck;
//        }
//    },
    showButton: function() {
        sf.search_button.style.color = '#bebab1';
    },
    hideButton: function() {
        sf.search_button.style.color = '#FFFFFF';
    },
    blur: function() {
        sf.to = setTimeout(sf.hide, 1500);
    },
    focus: function() {
        clearTimeout(sf.to);
        sf.filter_wrap.style.display = 'block';
    },
    hide: function() {
        var e = document.activeElement;
        if (e != 'undefined') {
            if (e === sf.search) {
                return true;
            }
        }
        sf.filter_wrap.style.display = 'none';
        sf.hideButton();
    }
};
sf.init();