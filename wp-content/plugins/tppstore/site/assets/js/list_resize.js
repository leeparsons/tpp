var plist = document.getElementById('product_list').children;

if (plist.length > 1) {
    var i = 1, y = 1, heights = [], highest_height = 0, ch = [], mt=0;
    for (var x = 0; x < plist.length; x++) {

        heights.push(plist[x].clientHeight);

        if (i%4 === 0) {
            i = 0;

            highest_height = heights.sort(function(a,b){return b-a;})[0];

            for (y = x; y > x-4; y--) {
                plist[y].style.height = highest_height + 'px';
                ch = plist[y].children;
                if (ch.length == 1) {
                    mt = (highest_height - ch[0].clientHeight)/2;
                    ch[0].style.marginTop = mt + 'px';
                }
            }

            heights = [];
        }
        i++;
    }

}