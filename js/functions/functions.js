jQuery(document).ready(function() {
    
}); // END READY

function	getTweets(el, utils){    			
    if( !utils.username ){
        el.twitterList.find("li").eq(0).html("You need to specify a username");
    }else{
        jQuery.ajax({
            url			: "http://twitter.com/statuses/user_timeline/"+utils.username+".json?callback=?",
            dataType	: "json",
            timeout		: 15000,
            success		: function(data){
                var li = '';
                for( i=0; i<utils.tweetNum; i++ ){
                    
                    var text = data[i].text;
                    text = text.replace(/((https?|s?ftp|ssh)\:\/\/[^"\s\<\>]*[^.,;'">\:\s\<\>\)\]\!])/g, function(url){return '<a href="'+url+'" target="_blank">'+url+'</a>'});
                    text = text.replace(/@(\w+)/g, function(url){return '<a href="http://www.twitter.com/'+url.substring(1)+'" target="_blank">'+url+'</a>'});
                    text = text.replace(/#(\w+)/g, function(url){return '<a href="http://twitter.com/search?q=%23'+url.substring(1)+'" target="_blank">'+url+'</a>'});
                    li += '<li>'+text+'</li>';
                    							
                }
                el.twitterList.html( li );
            },
            error : function(){
                el.twitterList.find("li").eq(0).html("There was an error connecting to your Twitter account");
            }
        });
    }
}

