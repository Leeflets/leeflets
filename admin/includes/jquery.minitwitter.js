// jquery.minitwitter.js - http://minitwitter.webdevdesigner.com/
// Copyright © 2012 Olivier Bréchet
(function( $, window, document, undefined ) {

	$.fn.miniTwitter = function( options ) {

		var o = $.extend({
			username: ['webdevdesigner'],          
	      	list: null,              
	      	favorite: false,             
	      	query: null,                             
	      	limit: 5, 
	      	max: null,                                               
	      	page: 1,                 
	      	retweet: true,
	      	refresh: null,
	      	linkColor: null,
	      	nofollow: true,
	      	blank: true,
	      	tweetId: 0
		}, options);


		function fetch () {
			return $.ajax({
				url: url(),
				dataType: 'jsonp'
			});
		};

		function url() {
		    var p = ('https:' == document.location.protocol ? 'https:' : 'http:');
		    var limit = (o.max === null) ? o.limit : o.max;
		    if (o.favorite) {
		        return p+'//api.twitter.com/favorites/'+o.username[0]+'.json?page='+o.page+'&count='+limit+'&include_entities=1&callback=?';
		    } else if (o.list) {
		        return p+'//api.twitter.com/1/'+o.username[0]+'/lists/'+o.list+'/statuses.json?page='+o.page+'&per_page='+limit+'&include_entities=1&callback=?';
		    } else  if (o.query === null && o.username.length == 1) {
		        return p+'//api.twitter.com/1/statuses/user_timeline.json?screen_name='+o.username[0]+'&count='+limit+(o.retweet ? '&include_rts=1' : '')+'&page='+o.page+'&include_entities=1&callback=?';
		    } else {
		        var query = (o.query || 'from:'+o.username.join(' OR from:'));
		        return p+'//search.twitter.com/search.json?&q='+encodeURIComponent(query)+'&rpp='+limit+'&page='+o.page+'&include_entities=1&callback=?';
		    }
	    };

	    function parse_date (date_str) {
	      	return Date.parse(date_str.replace(/^([a-z]{3})( [a-z]{3} \d\d?)(.*)( \d{4})$/i, '$1,$2$4$3'));
	    }

	    function createdAt (date) {
	    	var d = (new Date).getTime() - parse_date(date);
	      	var thedate = new Date(parse_date(date));
	      	var month = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
	      	if (d>86400000) {
	      		d = thedate.getDate()+' '+month[thedate.getMonth()];
	      	} else if (d>3600000) {
	      		d = parseInt(d/3600000)+'h';
	      	} else if (d>60000) {
	      		d = parseInt(d/60000)+'m';
	      	}else if (d>1000) {
	      		d = parseInt(d/1000)+'s';
	      	}
	      	return d;
	    }

	    function replacer (regex, replacement) {
	      	return function() {
		        var res = [];
		        this.each(function() {
		          	res.push(this.replace(regex, replacement));
		        });
		        return $(res);
	    	};
    	}


	    function escapeHTML(s) {
	      return s.replace(/</g,"&lt;").replace(/>/g,"^&gt;");
	    }

	    $.fn.extend({
		    linkUser: replacer(/(^|[\W])@(\w+)/gi, '$1<a '+rel()+' '+target()+' href="http://twitter.com/$2">@$2</a>'),
		    linkHash: replacer(/(?:^| )[\#]+([\w\u00c0-\u00d6\u00d8-\u00f6\u00f8-\u00ff\u0600-\u06ff]+)/gi,
		    ' <a '+rel()+' '+target()+' href="http://search.twitter.com/search?q=&tag=$1&lang=all">#$1</a>')
		});

		function rel() {
			return ( o.nofollow ? 'rel="nofollow"' : '');
		}

		function target() {
			return ( o.blank ? 'target="_blank"' : '');
		}

		function linkURLs(text, entities) {
			var regexToken = /(((ftp|https?):\/\/)[\-\w@:%_\+.~#?,&\/\/=]+)|((mailto:)?[_.\w-]+@([\w][\w\-]+\.)+[a-zA-Z]{2,3})/g;
	      	return text.replace(regexToken, function(match) {
	        var url = (/^[a-z]+:/i).test(match) ? match : 'http://'+match;
	        var text = match;
	        for(var i = 0; i < entities.length; ++i) {
	          	var entity = entities[i];
	          	if (entity.url == url && entity.expanded_url) {
	            	url = entity.expanded_url;
	            	text = entity.display_url;
	            	break;
	          	}
	        }
	        return '<a '+rel()+' '+target()+' href="'+escapeHTML(url)+'">'+escapeHTML(text)+'</a>';
	      	});
	    }

		function fetchTweetsData ( object ) {
	      	var obj = {};
	      	obj.text = object.text;
	      	obj.tweetId = object.id_str;
	      	obj.tweetUrl = obj.userUrl+"/status/"+obj.tweetId;
	      	obj.retweet = typeof(object.retweeted_status) != 'undefined';
	      	obj.screenName = obj.retweet ? object.retweeted_status.user.screen_name : (object.from_user || object.user.screen_name ) ;
	      	obj.realName = obj.retweet ? object.retweeted_status.user.name : (object.from_user_name || object.user.name);
	      	obj.userUrl = obj.retweet ? "http://twitter.com/"+object.retweeted_status.user.screen_name : "http://twitter.com/"+obj.screenName;
		    obj.tweetTime = createdAt(object.created_at);
		    obj.image = obj.retweet ? object.retweeted_status.user.profile_image_url : object.profile_image_url || object.user.profile_image_url ;
			obj.replyUrl = "http://twitter.com/intent/tweet?in_reply_to="+o.tweetId;		    
		    obj.retweetUrl = "http://twitter.com/intent/retweet?tweet_id="+o.tweetId;
		    obj.favoriteUrl = "http://twitter.com/intent/favorite?tweet_id="+o.tweetId;
		    obj.entities = object.entities ? (object.entities.urls || []).concat(object.entities.media || []) : [];
		    obj.retweetScreenName = obj.retweet && object.retweeted_status.user.screen_name;
		    obj.retweetName = obj.retweet && object.retweeted_status.user.name;
		    obj.writeTweet = obj.retweet ? object.retweeted_status.text : object.text;
		    obj.finalText = $([linkURLs(obj.writeTweet, obj.entities)]).linkUser().linkHash()[0];
		    obj.header = '<div class="mt_header"> <a '+rel()+' '+target()+' class="mt_user" href="'+obj.userUrl+'">'+obj.realName+'</a> <span class="mt_screen_name">@'+obj.screenName+'</span> <div class="time">'+obj.tweetTime+'</div></div>';
		    obj.avatar = '<div class="tweet"><div class="avatar"><a '+rel()+' '+target()+' class="mt_avatar" href="'+obj.userUrl+'"><img src="'+obj.image+'" alt="'+obj.realName+'\'s avatar" border="0"/></a></div>';
		    obj.textTweet = '<div class="mt_text">'+obj.finalText+'</div>';
		    obj.footer = obj.retweet ? '<div class="mt_footer"><span class="image_r"></span>Retweeted by <a '+rel()+' '+target()+' class="mt_retweet" href="http://twitter.com/'+object.user.screen_name+'">'+object.user.name+'</a></div><div style="clear:both;"></div></div>' : '<div class="mt_footer"></div><div style="clear:both;"></div></div>';

		    //obj.replyName = (object.in_reply_to_screen_name != 'undefined') ? object.in_reply_to_screen_name : '';
		    //linkcolors
		    obj.linkColor = (o.linkColor == null) ? ( object.from_user_id || object.user.profile_link_color) : o.linkColor;

		    return obj;
	    }

		function display ( widget ) {
			fetch().done(function( res ) {
				//o.tweetId = results[0].id;
				tweets = $.map( res.results || res , function( obj, i) {
					return fetchTweetsData ( obj );
				});
				for(var i=0; i<tweets.length; i++) {
					$(widget).append(tweets[i].avatar+tweets[i].header+tweets[i].textTweet+tweets[i].footer);
				}
				$( "."+$(widget).attr('class')+" .mt_text a").css('color', '#'+tweets[0].linkColor);
				hover ( "."+$(widget).attr('class')+" .mt_header a", tweets[0].linkColor, "333" );
				hover ( "."+$(widget).attr('class')+" .mt_footer a", tweets[0].linkColor, "999" );
			}); 
		};

		function hover ( element, newcolor, initcolor ) {
			$(element).hover(function(){
					$(this).css('color', '#'+newcolor);
				}, function () {
					$(this).css('color',"#"+initcolor);
			});
		}

		return this.each(function(i, widget) {

			if(typeof(options) == "string"){
			    o.username = [options];
			}
			if(o.username && typeof(o.username) == "string"){
		        o.username = [o.username];
		    }
		
			display( widget );

			if( o.refresh != null ) {
				//to do
			}

		});
	};

})( jQuery, window, document );