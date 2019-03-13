/**
 * Created by yanghao on 2019/3/12.
 */

var PagePlug = function(url,totalCount,size) {
    this.url = url;                     //分页跳转的url
    this.pageSize = size;               //每页显示的条数
    this.pageGroupSize = 6;             //一组显示的页数
    this.swapLimit = 3;                 //换页值
    this.totalCount = totalCount;       //总条数
    this.totalPageCount = Math.ceil(this.totalCount/this.pageSize);  //总页数

    if(typeof this.normal != "function") {
        //获取当前页面所在组的首页
        PagePlug.prototype.getGroupFirstPage = function(curPage) {
            if((curPage - this.swapLimit) > 0) {
                return curPage - this.swapLimit + 1;
            } else {
                return 1;
            }
        };

        //获取当前页面所在组的末页
        PagePlug.prototype.getGroupEndPage = function(curPage) {
            var page = this.getGroupFirstPage(curPage) + this.pageGroupSize - 1;
            if(page >= this.totalPageCount) {
                return this.totalPageCount;
            } else {
                return this.getGroupFirstPage(curPage) + this.pageGroupSize - 1;
            }
        };

        PagePlug.prototype.processMethodNormal = function() {
            var p =  parseInt($(this).attr('data-id'));
            $(location).attr('href',url + p);
        };

        /**
         *
         * @param area  需要插入分页的dom对象
         * @param curPage 当前页码
         */
        PagePlug.prototype.normal = function(area,curPage) {
            var url = this.url;
            var nav = $('<nav aria-label="Page navigation"></nav>');
            var ul = $('<ul class="pagination"></ul>');
            var style = '';
            if(curPage > 1) {
                var prev = $('<li data-id="'+ (curPage - 1) +'"><a href="#"  aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>');
                prev.on('click',this.processMethodNormal);
            } else {
                var prev = $('<li class="disabled" data-id="'+ curPage +'"><a href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>');

            }
            ul.append(prev);
            var firsPage = this.getGroupFirstPage(curPage);
            var endPage = this.getGroupEndPage(curPage);
            for(var i=firsPage;i<=endPage;i++) {
                if(i == curPage) {
                    style = 'class = "active"';
                } else {
                    style = '';
                }
                var li = $('<li '+ style +' data-id="'+ i +'"><a href="#">'+ i +'</a></li>');
                li.on('click',this.processMethodNormal);
                ul.append(li);
            }
            if(curPage < this.totalPageCount) {
                var next = $('<li data-id="'+ (curPage+1) +'"><a href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>');
                next.on('click',this.processMethodNormal);
            } else {
                var next = $('<li class="disabled" data-id="'+ curPage +'"><a href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>');
            }
            ul.append(next);
            nav.append(ul);
            area.append(nav);
        };
    }
};