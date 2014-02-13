var em = document.getElementById('dashboard_list_body');
if (em && em.children.length > 0) {
    for (var x = 0; x < em.children.length; x++) {
        em.children[x].onclick = function() {
            window.location.href = this.getAttribute('data-target');
        }
    }
}