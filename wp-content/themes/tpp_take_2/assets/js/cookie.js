var cd = document.createElement('div');
cd.setAttribute('class', 'cookiebar');
document.body.appendChild(cd);
var cda = document.createElement('a');
cda.setAttribute('href', '/cookie-policy');
cda.innerHTML = 'View Cookie Policy';
var cda2 = document.createElement('a');
cda2.setAttribute('href', '#');
cda2.setAttribute('class', 'btn btn-default');
cda2.innerHTML = 'Accept Cookies';
cda2.onclick = function() {
    cd.style.height = 0;
    var expiration_date = new Date();var cookie_string = '';expiration_date.setFullYear(expiration_date.getFullYear() + 1);cookie_string = "accept_cookies=true; path=/; expires=" + expiration_date.toGMTString();document.cookie = cookie_string;
    return false;
}
cd.appendChild(cda2);
cd.appendChild(cda);
var p = document.createElement('p');
p.innerHTML = 'By browsing this website you accept our cookie policy.';
cd.appendChild(p);
(function(d, t) {
    var g = d.createElement(t),
        s = d.getElementsByTagName(t)[0];
    g.href = '/assets/css/cookies.css';
    g.rel='stylesheet';
    s.parentNode.insertBefore(g, s);
}(document, 'link'));