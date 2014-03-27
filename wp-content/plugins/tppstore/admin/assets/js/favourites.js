var ems = document.getElementsByClassName('toggle');

function toggleFavourite() {

    var id = this.getAttribute('data-id');
    var fav = this.getAttribute('data-on');

    if (parseInt(fav) == 1) {
        this.setAttribute('src', '/assets/images/cross.png');
        this.setAttribute('data-on', 0);
        document.getElementById('product_' + id).checked = false;
    } else {
        var checked_items = 0;

        for ( var x = 0; x < ems.length; x++) {
            if (ems[x].getAttribute('data-on') == '1') {
                checked_items++;
            }
            if (checked_items == 8) {
                alert('You have selected 8 items already. You can not select any more.');
                return false;
            }
        }
        this.setAttribute('src', '/assets/images/tick.png');
        this.setAttribute('data-on', 1);
        document.getElementById('product_' + id).checked = true;
    }
}

if (ems.length > 0) {
    for (var x = 0; x < ems.length; x++) {
        ems[x].onclick = toggleFavourite;
    }
}

jQuery(function($) {
    $("#sort tbody").sortable().disableSelection();
});