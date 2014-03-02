var tppMenu = {
    open: false,
    menu: {},
    toggler: {},
    init: function() {
        if (document.getElementById('main_menu')) {
            tppMenu.menu = document.getElementById('main_menu');
        }
        if (document.getElementById('menu_toggle')) {
            tppMenu.toggler = document.getElementById('menu_toggle');
            tppMenu.toggler.onclick = tppMenu.toggle;
        }
    },
    toggle: function() {
        if (tppMenu.open === false) {
            tppMenu.menu.style.height = '330px';
            tppMenu.toggler.innerHTML = 'Close menu';
        } else {
            tppMenu.menu.style.height = '0px';
            tppMenu.toggler.innerHTML = 'Expand menu';
        }
        tppMenu.open = tppMenu.open === false;
    }
}

window.onload = function() {
    tppMenu.init();
}

