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

window.onresize = function() {

    if (document.getElementById('main_menu')) {
        if (window.innerWidth * 1 > 700) {
            tppMenu.open = true;
            document.getElementById('main_menu').style.height = '55px';
            document.getElementById('menu_toggle').style.display = 'none';
        } else {
            tppMenu.open = false;
            document.getElementById('menu_toggle').style.display = 'block';
            document.getElementById('menu_toggle').innerHTML = 'Expand menu';
            document.getElementById('main_menu').style.height = '0px';
        }
    }
}

