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

        /**
         *
         * @param area  需要插入分页的dom对象
         * @param curPage 当前页码
         */
        PagePlug.prototype.normal = function(area,curPage) {
            var nav = $('<nav aria-label="Page navigation"></nav>');
            var ul = $('<ul class="pagination"></ul>');
            var disabledStyle = 'class="disabled"';

            var prePage = (curPage > 1)?(curPage - 1):1;
            var style = (curPage > 1)?'':disabledStyle;

            var prev = $('<li '+ style +'><a href="'+ this.url + prePage +'" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>');

            ul.append(prev);
            var firsPage = this.getGroupFirstPage(curPage);
            var endPage = this.getGroupEndPage(curPage);
            for(var i=firsPage;i<=endPage;i++) {
                style = (i == curPage)?'class = "active"':'';
                var li = $('<li '+ style +'><a href="'+ this.url + i +'">'+ i +'</a></li>');
                ul.append(li);
            }

            var nextPage = (curPage < this.totalPageCount)?(curPage + 1):curPage;
            style = (curPage < this.totalPageCount)?'':disabledStyle;

            var next = $('<li '+ style +'><a href="'+ this.url + nextPage +'" aria-label="Previous"><span aria-hidden="true">&raquo;</span></a></li>');

            ul.append(next);
            nav.append(ul);
            area.append(nav);
        };
    }
};