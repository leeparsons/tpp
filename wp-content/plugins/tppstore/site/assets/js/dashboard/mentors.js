var dropdowns = document.getElementsByClassName('dropdown');
if (dropdowns.length > 0) {
    var s = null;
    var options = null;
    for (var x = 0; x < dropdowns.length; x++) {
        options = dropdowns[x].children;
        if (options.length > 1) {
            for (var y = 0; y < options.length; y++) {
                if (y > 0) {
                    options[y].style.display = 'none';
                }
            }

            s = document.createElement('span');

            s.setAttribute('data-x', x);

            dropdowns[x].insertBefore(s, options[0]);
            s.onmouseover = function() {
                var c = dropdowns[this.getAttribute('data-x')].getElementsByTagName('A');
                for (var x = 0; x < c.length; x++) {
                    c[x].style.display = 'block';
                }
            }

            s.onmouseout = function() {
                var c = dropdowns[this.getAttribute('data-x')].getElementsByTagName('A');

                for (var x = 0; x < c.length; x++) {
                    if (x > 0) {
                        c[x].style.display = 'none';
                    }
                }
            }
        }
    }
}