$('.scrollable').pullToRefresh({
    callback: function() {
        var def = $.Deferred();

        setTimeout(function() {
            def.resolve();      
        }, 3000); 

        return def.promise();
    }
});