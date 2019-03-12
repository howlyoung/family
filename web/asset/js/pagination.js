/**
 * Created by yanghao on 2019/3/12.
 */

var PagePlug = function(url,size) {
    this.url = url;
    this.size = size;

    if(typeof this.normal != "function") {
        PagePlug.prototype.normal = function(area,curPage,pageCount) {
            var url = this.url;
            var nav = $('<nav aria-label="Page navigation"></nav>');
            var ul = $('<ul class="pagination"></ul>');
            var style = '';
            ul.append('<li><a href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>');
            for(var i=1;i<pageCount;i++) {
                if(i == curPage) {
                    style = 'class = "active"';
                } else {
                    style = '';
                }
                var li = $('<li '+ style +' data-id="'+ i +'"><a href="#">'+ i +'</a></li>');
                li.on('click',function(){
                    var p =  $(this).attr('data-id');
                    $(location).attr('href',url + p);
                });
                ul.append(li);
            }
            ul.append('<li><a href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>');
            nav.append(ul);
            area.append(nav);
        };
    }
};