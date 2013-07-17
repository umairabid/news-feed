(function( $ ){
    $.fn.scrollFeedPagination = function(options) {
        var opts = $.extend($.fn.scrollFeedPagination.defaults, options);  
        var target = opts.scrollTarget;
        if (target == null){
            target = obj; 
        }
        opts.scrollTarget = target;
	 
        return this.each(function() {
            $.fn.scrollFeedPagination.init($(this), opts);
        });
    };
    $.fn.stopScrollPagination = function(){
        return this.each(function() {
            $(this).attr('scrollPagination', 'disabled');
        });
    };
    $.fn.scrollFeedPagination.loadContent = function(obj, opts){
        var target = opts.scrollTarget;
        var mayLoadContent = ($(target)[0].scrollHeight - $(target).scrollTop()) == $(target).height();
        if (mayLoadContent){
            if (opts.beforeLoad != null){
                opts.beforeLoad(); 
            }
            $(obj).children().attr('rel', 'loaded');
            $.ajax({
                type: 'POST',
                url: opts.contentPage,
                data: opts.contentData,
                success: function(data){
                    var selector = target.selector;
                    var response = $(data).find(selector).children();
                    $(obj).append(response);
                    var objectsRendered = $(obj).children('[rel!=loaded]');
                    if (opts.afterLoad != null){
                        opts.afterLoad(objectsRendered);	
                    }
                },
                dataType: 'html'
            });
        }
    };
  
    $.fn.scrollFeedPagination.init = function(obj, opts){
        var target = opts.scrollTarget;
         
        $(obj).attr('scrollPagination', 'enabled');
        $(target).scroll(function(event){
            if ($(obj).attr('scrollPagination') == 'enabled'){
                $.fn.scrollFeedPagination.loadContent(obj, opts);		
            }
            else {
                event.stopPropagation();	
            }
        });
        $.fn.scrollFeedPagination.loadContent(obj, opts);
    };
    $.fn.scrollFeedPagination.defaults = {
        'contentPage' : null,
        'contentData' : {},
        'beforeLoad': null,
        'afterLoad': null	,
        'scrollTarget': null,
        'heightOffset': 0		  
    };	
})( jQuery );