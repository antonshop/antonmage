/*!
 * artDialog 4.1.6
 * Date: 2012-07-16 22:57
 * http://code.google.com/p/artdialog/
 * (c) 2009-2012 TangBin, http://www.planeArt.cn
 *
 * This is licensed under the GNU LGPL, version 2.1 or later.
 * For details, see: http://creativecommons.org/licenses/LGPL/2.1/
 */(function(h,e,l){h.noop=h.noop||function(){};var o,p,s,v,z=0,q=h(e),j=h(document),m=h("html");s=document.documentElement;var r=e.VBArray&&!e.XMLHttpRequest,t="createTouch"in document&&!("onmousemove"in s)||/(iPhone|iPad|iPod)/i.test(navigator.userAgent),w="artDialog"+ +new Date,g=function(a,b,c){a=a||{};if("string"===typeof a||1===a.nodeType)a={content:a,fixed:!t};var d;d=g.defaults;var f=a.follow=1===this.nodeType&&this||a.follow,i;for(i in d)a[i]===l&&(a[i]=d[i]);h.each({ok:"yesFn",cancel:"noFn",close:"closeFn",init:"initFn",okVal:"yesText",cancelVal:"noText"},function(b,d){a[b]=a[b]!==l?a[b]:a[d]});"string"===typeof f&&(f=h(f)[0]);a.id=f&&f[w+"follow"]||a.id||w+z;d=g.list[a.id];if(f&&d)return d.follow(f).zIndex().focus();if(d)return d.zIndex().focus();if(t)a.fixed=!1;if(!h.isArray(a.button))a.button=a.button?[a.button]:[];if(b!==l)a.ok=b;if(c!==l)a.cancel=c;a.ok&&a.button.push({name:a.okVal,callback:a.ok,focus:!0});a.cancel&&a.button.push({name:a.cancelVal,callback:a.cancel});g.defaults.zIndex=a.zIndex;z++;return g.list[a.id]=o?o._init(a):new g.fn._init(a)};g.fn=g.prototype={version:"4.1.6",closed:!0,_init:function(a){var b,c=a.icon,d=c&&(r?{png:"icons/"+c+".png"}:{backgroundImage:"url('"+a.path+"/skins/icons/"+c+".png')"});this.closed=!1;this.config=a;this.DOM=b=this.DOM||this._getDOM();b.wrap.addClass(a.skin);b.close[!1===a.cancel?"hide":"show"]();b.icon[0].style.display=c?"":"none";b.iconBg.css(d||{background:"none"});b.se.css("cursor",a.resize?"se-resize":"auto");b.title.css("cursor",a.drag?"move":"auto");b.content.css("padding",a.padding);this[a.show?"show":"hide"](!0);this.button(a.button).title(a.title).content(a.content,!0).size(a.width,a.height).time(a.time);a.follow?this.follow(a.follow):this.position(a.left,a.top);this.zIndex().focus();a.lock&&this.lock();this._addEvent();this._ie6PngFix();o=null;a.init&&a.init.call(this,e);return this},content:function(a,b){var c,d,f,i,k=this,u=k.DOM,n=u.wrap[0],g=n.offsetWidth,j=n.offsetHeight,h=parseInt(n.style.left),e=parseInt(n.style.top),r=n.style.width,u=u.content,m=u[0];k._elemBack&&k._elemBack();n.style.width="auto";if(a===l)return m;if("string"===typeof a)u.html(a);else if(a&&1===a.nodeType)i=a.style.display,c=a.previousSibling,d=a.nextSibling,f=a.parentNode,k._elemBack=function(){c&&c.parentNode?c.parentNode.insertBefore(a,c.nextSibling):d&&d.parentNode?d.parentNode.insertBefore(a,d):f&&f.appendChild(a);a.style.display=i;k._elemBack=null},u.html(""),m.appendChild(a),a.style.display="block";if(!b){k.config.follow?k.follow(k.config.follow):(g=n.offsetWidth-g,j=n.offsetHeight-j,e-=j/2,n.style.left=Math.max(h-g/2,0)+"px",n.style.top=Math.max(e,0)+"px");if(r&&"auto"!==r)n.style.width=n.offsetWidth+"px";k._autoPositionType()}k._ie6SelectFix();k._runScript(m);return k},title:function(a){var b=this.DOM,c=b.wrap,b=b.title;if(a===l)return b[0];!1===a?(b.hide().html(""),c.addClass("aui_state_noTitle")):(b.show().html(a||""),c.removeClass("aui_state_noTitle"));return this},position:function(a,b){var c=this.config,d=this.DOM.wrap[0],f=r?!1:c.fixed,c=r&&this.config.fixed,i=j.scrollLeft(),k=j.scrollTop(),u=f?0:i,f=f?0:k,n=q.width(),g=q.height(),h=d.offsetWidth,e=d.offsetHeight,d=d.style;if(a||0===a)if(this._left=-1!==a.toString().indexOf("%")?a:null,a=this._toNumber(a,n-h),"number"===typeof a)a=c?a+=i:a+u,d.left=Math.max(a,u)+"px";else if("string"===typeof a)d.left=a;if(b||0===b)if(this._top=-1!==b.toString().indexOf("%")?b:null,b=this._toNumber(b,g-e),"number"===typeof b)b=c?b+=k:b+f,d.top=Math.max(b,f)+"px";else if("string"===typeof b)d.top=b;if(a!==l&&b!==l)this._follow=null,this._autoPositionType();return this},size:function(a,b){var c,d,f=this.DOM;d=f.wrap;var i=f.main,k=d[0].style,f=i[0].style;if(a)if(this._width=-1!==a.toString().indexOf("%")?a:null,c=q.width()-d[0].offsetWidth+i[0].offsetWidth,a=c=this._toNumber(a,c),"number"===typeof a)k.width="auto",f.width=Math.max(this.config.minWidth,a)+"px",k.width=d[0].offsetWidth+"px";else if("string"===typeof a)f.width=a,"auto"===a&&d.css("width","auto");if(b)if(this._height=-1!==b.toString().indexOf("%")?b:null,d=q.height()-d[0].offsetHeight+i[0].offsetHeight,b=d=this._toNumber(b,d),"number"===typeof b)f.height=Math.max(this.config.minHeight,b)+"px";else if("string"===typeof b)f.height=b;this._ie6SelectFix();return this},follow:function(a){var b,c=this.config;if("string"===typeof a||a&&1===a.nodeType)b=h(a),a=b[0];if(!a||!a.offsetWidth&&!a.offsetHeight)return this.position(this._left,this._top);var d=w+"follow",f=q.width(),i=q.height(),k=j.scrollLeft(),g=j.scrollTop(),n=b.offset();b=a.offsetWidth;var e=r?!1:c.fixed,m=e?n.left-k:n.left,n=e?n.top-g:n.top,l=this.DOM.wrap[0],p=l.style,o=l.offsetWidth,l=l.offsetHeight,t=m-(o-b)/2,s=n+a.offsetHeight,k=e?0:k,g=e?0:g;p.left=(t<k?m:t+o>f&&m-o>k?m-o+b:t)+"px";p.top=(s+l>i+g&&n-l>g?n-l:s)+"px";this._follow&&this._follow.removeAttribute(d);this._follow=a;a[d]=c.id;this._autoPositionType();return this},button:function(){var a=this,b=arguments,c=a.DOM.buttons,d=c[0],f=a._listeners=a._listeners||{},i=h.isArray(b[0])?b[0]:[].slice.call(b);if(b[0]===l)return d;h.each(i,function(b,c){var i=c.name,g=!f[i],j=!g?f[i].elem:document.createElement("button");f[i]||(f[i]={});if(c.callback)f[i].callback=c.callback;if(c.className)j.className=c.className;if(c.focus)a._focus&&a._focus.removeClass("aui_state_highlight"),a._focus=h(j).addClass("aui_state_highlight"),a.focus();j.setAttribute("type","button");j[w+"callback"]=i;j.disabled=!!c.disabled;if(g)j.innerHTML=i,f[i].elem=j,d.appendChild(j)});c[0].style.display=i.length?"":"none";a._ie6SelectFix();return a},show:function(a){this.DOM.wrap.show();!a&&this._lockMaskWrap&&this._lockMaskWrap.show();return this},hide:function(a){this.DOM.wrap.hide();!a&&this._lockMaskWrap&&this._lockMaskWrap.hide();return this},close:function(){if(this.closed)return this;var a=this.DOM,b=a.wrap,c=g.list,d=this.config.close,f=this.config.follow;this.time();if("function"===typeof d&&!1===d.call(this,e))return this;this.unlock();this._elemBack&&this._elemBack();b[0].className=b[0].style.cssText="";a.title.html("");a.content.html("");a.buttons.html("");if(g.focus===this)g.focus=null;f&&f.removeAttribute(w+"follow");delete c[this.config.id];this._removeEvent();this.hide(!0)._setAbsolute();for(var i in this)this.hasOwnProperty(i)&&"DOM"!==i&&delete this[i];o?b.remove():o=this;return this},time:function(a){var b=this,c=b.config.cancelVal,d=b._timer;d&&clearTimeout(d);if(a)b._timer=setTimeout(function(){b._click(c)},1E3*a);return b},focus:function(){try{var a=this._focus&&this._focus[0]||this.DOM.close[0];a&&a.focus()}catch(b){}return this},zIndex:function(){var a=this.DOM.wrap,b=g.focus,c=g.defaults.zIndex++;a.css("zIndex",c);this._lockMask&&this._lockMask.css("zIndex",c-1);b&&b.DOM.wrap.removeClass("aui_state_focus");g.focus=this;a.addClass("aui_state_focus");return this},lock:function(){if(this._lock)return this;var a=this,b=g.defaults.zIndex-1,c=a.DOM.wrap,d=a.config,f=j.width(),i=j.height(),k=a._lockMaskWrap||h(document.body.appendChild(document.createElement("div"))),e=a._lockMask||h(k[0].appendChild(document.createElement("div"))),f=t?"width:"+f+"px;height:"+i+"px":"width:100%;height:100%",i=r?"position:absolute;left:expression((document).documentElement.scrollLeft);top:expression((document).documentElement.scrollTop);width:expression((document).documentElement.clientWidth);height:expression((document).documentElement.clientHeight)":"";a.zIndex();c.addClass("aui_state_lock");k[0].style.cssText=f+";position:fixed;z-index:"+b+";top:0;left:0;overflow:hidden;"+i;e[0].style.cssText="height:100%;background:"+d.background+";filter:alpha(opacity=0);opacity:0";r&&e.html('<iframe src="about:blank" style="width:100%;height:100%;position:absolute;top:0;left:0;z-index:-1;filter:alpha(opacity=0)"></iframe>');e.stop();e.bind("click",function(){a._click(a.config.cancelVal)/*a._reset()*/}).bind("dblclick",function(){a._click(a.config.cancelVal)});0===d.duration?e.css({opacity:d.opacity}):e.animate({opacity:d.opacity},d.duration);a._lockMaskWrap=k;a._lockMask=e;a._lock=!0;return a},unlock:function(){var a=this._lockMaskWrap,b=this._lockMask;if(!this._lock)return this;var c=a[0].style,d=function(){r&&(c.removeExpression("width"),c.removeExpression("height"),c.removeExpression("left"),c.removeExpression("top"));c.cssText="display:none";o&&a.remove()};b.stop().unbind();this.DOM.wrap.removeClass("aui_state_lock");this.config.duration?b.animate({opacity:0},this.config.duration,d):d();this._lock=!1;return this},_getDOM:function(){var a=document.createElement("div"),b=document.body;a.style.cssText="position:absolute;left:0;top:0";a.innerHTML=g._templates;b.insertBefore(a,b.firstChild);for(var b=0,c={wrap:h(a)},d=a.getElementsByTagName("*"),f=d.length;b<f;b++)(a=d[b].className.split("aui_")[1])&&(c[a]=h(d[b]));return c},_toNumber:function(a,b){if(!a&&0!==a||"number"===typeof a)return a;var c=a.length-1;a.lastIndexOf("px")===c?a=parseInt(a):a.lastIndexOf("%")===c&&(a=parseInt(b*a.split("%")[0]/100));return a},_ie6PngFix:r?function(){for(var a=0,b,c,d=g.defaults.path+"/skins/",f=this.DOM.wrap[0].getElementsByTagName("*");a<f.length;a++)if(b=f[a],c=b.currentStyle.png)c=d+c,b=b.runtimeStyle,b.backgroundImage="none",b.filter="progid:DXImageTransform.Microsoft.AlphaImageLoader(src='"+c+"',sizingMethod='crop')"}:h.noop,_ie6SelectFix:r?function(){var a=this.DOM.wrap,b=a[0],c=w+"iframeMask",d=a[c],f=b.offsetWidth,i=b.offsetHeight,f=f+"px",i=i+"px";d?(d.style.width=f,d.style.height=i):(d=b.appendChild(document.createElement("iframe")),a[c]=d,d.src="about:blank",d.style.cssText="position:absolute;z-index:-1;left:0;top:0;filter:alpha(opacity=0);width:"+f+";height:"+i)}:h.noop,_runScript:function(a){for(var b,c=b=0,a=a.getElementsByTagName("script"),d=a.length,f=[];b<d;b++)if("text/dialog"===a[b].type)f[c]=a[b].innerHTML,c++;f.length&&(f=f.join(""),b=new Function(f),b.call(this))},_autoPositionType:function(){this[this.config.fixed?"_setFixed":"_setAbsolute"]()},_setFixed:function(){r&&h(function(){"fixed"!==m.css("backgroundAttachment")&&"fixed"!==h("body").css("backgroundAttachment")&&m.css({zoom:1,backgroundImage:"url(about:blank)",backgroundAttachment:"fixed"})});return function(){var a=this.DOM.wrap,b=a[0].style;if(r){var c=parseInt(a.css("left")),a=parseInt(a.css("top")),d=j.scrollLeft(),f=j.scrollTop();this._setAbsolute();b.setExpression("left","eval((document.documentElement).scrollLeft + "+(c-d)+') + "px"');b.setExpression("top","eval((document.documentElement).scrollTop + "+(a-f)+') + "px"')}else b.position="fixed"}}(),_setAbsolute:function(){var a=this.DOM.wrap[0].style;r&&(a.removeExpression("left"),a.removeExpression("top"));a.position="absolute"},_click:function(a){a=this._listeners[a]&&this._listeners[a].callback;return"function"!==typeof a||!1!==a.call(this,e)?this.close():this},_reset:function(a){var b=this._winSize||q.width()*q.height(),c=this._follow,d=this._width,f=this._height,i=this._left,g=this._top;if(a&&(a=this._winSize=q.width()*q.height(),b===a))return;(d||f)&&this.size(d,f);c?this.follow(c):(i||g)&&this.position(i,g)},_addEvent:function(){var a,b=this,c=b.config,d="CollectGarbage"in e,f=b.DOM;b._winResize=function(){a&&clearTimeout(a);a=setTimeout(function(){b._reset(d)},40)};q.bind("resize",b._winResize);f.wrap.bind("click",function(a){a=a.target;if(a.disabled)return!1;if(a===f.close[0])return b._click(c.cancelVal),!1;(a=a[w+"callback"])&&b._click(a);b._ie6SelectFix()}).bind("mousedown",function(){b.zIndex()})},_removeEvent:function(){this.DOM.wrap.unbind();q.unbind("resize",this._winResize)}};g.fn._init.prototype=g.fn;h.fn.dialog=h.fn.artDialog=function(){var a=arguments;this[this.live?"live":"bind"]("click",function(){g.apply(this,a);return!1});return this};g.focus=null;g.get=function(a){return a===l?g.list:g.list[a]};g.list={};j.bind("keydown",function(a){var b=a.target.nodeName,c=/^INPUT|TEXTAREA$/,d=g.focus,a=a.keyCode;d&&d.config.esc&&!c.test(b)&&27===a&&d._click(d.config.cancelVal)});v=e._artDialog_path||function(a,b,c){for(b in a)a[b].src&&-1!==a[b].src.indexOf("artDialog")&&(c=a[b]);p=c||a[a.length-1];c=p.src.replace(/\\/g,"/");return 0>c.lastIndexOf("/")?".":c.substring(0,c.lastIndexOf("/"))}(document.getElementsByTagName("script"));if(s=p.src.split("skin=")[1]){var x=document.createElement("link");x.rel="stylesheet";x.href=v+"/skins/"+s+".css?"+g.fn.version;p.parentNode.insertBefore(x,p)}q.bind("load",function(){setTimeout(function(){z||g({left:"-9999em",time:9,fixed:!1,lock:!1,focus:!1})},150)});try{document.execCommand("BackgroundImageCache",!1,!0)}catch(y){}g._templates='<div class="aui_outer"><table class="aui_border"><tbody><tr><td class="aui_nw"></td><td class="aui_n"></td><td class="aui_ne"></td></tr><tr><td class="aui_w"></td><td class="aui_c"><div class="aui_inner"><table class="aui_dialog"><tbody><tr><td colspan="2" class="aui_header"><div class="aui_titleBar"><div class="aui_title"></div><a class="aui_close" href="javascript:/*artDialog*/;">\u00d7</a></div></td></tr><tr><td class="aui_icon"><div class="aui_iconBg"></div></td><td class="aui_main"><div class="aui_content"></div></td></tr><tr><td colspan="2" class="aui_footer"><div class="aui_buttons"></div></td></tr></tbody></table></div></td><td class="aui_e"></td></tr><tr><td class="aui_sw"></td><td class="aui_s"></td><td class="aui_se"></td></tr></tbody></table></div>';g.defaults={content:'<div class="aui_loading"><span>loading..</span></div>',title:"\u6d88\u606f",button:null,ok:null,cancel:null,init:null,close:null,okVal:"\u786e\u5b9a",cancelVal:"\u53d6\u6d88",width:"auto",height:"auto",minWidth:96,minHeight:32,padding:"20px 25px",skin:"",icon:null,time:null,esc:!0,focus:!0,show:!0,follow:null,path:v,lock:!1,background:"#000",opacity:0.7,duration:300,fixed:!1,left:"50%",top:"38.2%",zIndex:1987,resize:!0,drag:!0};e.artDialog=h.dialog=h.artDialog=g})(this.art||this.jQuery&&(this.art=jQuery),this);(function(h){var e,l,o=h(window),p=h(document),s=document.documentElement,v=!("minWidth"in s.style),z="onlosecapture"in s,q="setCapture"in s;artDialog.dragEvent=function(){var j=this,e=function(e){var h=j[e];j[e]=function(){return h.apply(j,arguments)}};e("start");e("move");e("end")};artDialog.dragEvent.prototype={onstart:h.noop,start:function(e){p.bind("mousemove",this.move).bind("mouseup",this.end);this._sClientX=e.clientX;this._sClientY=e.clientY;this.onstart(e.clientX,e.clientY);return!1},onmove:h.noop,move:function(e){this._mClientX=e.clientX;this._mClientY=e.clientY;this.onmove(e.clientX-this._sClientX,e.clientY-this._sClientY);return!1},onend:h.noop,end:function(e){p.unbind("mousemove",this.move).unbind("mouseup",this.end);this.onend(e.clientX,e.clientY);return!1}};l=function(h){var m,l,t,s,g,x,y=artDialog.focus,a=y.DOM,b=a.wrap,c=a.title,d=a.main,f="getSelection"in window?function(){window.getSelection().removeAllRanges()}:function(){try{document.selection.empty()}catch(a){}};e.onstart=function(){x?(l=d[0].offsetWidth,t=d[0].offsetHeight):(s=b[0].offsetLeft,g=b[0].offsetTop);p.bind("dblclick",e.end);!v&&z?c.bind("losecapture",e.end):o.bind("blur",e.end);q&&c[0].setCapture();b.addClass("aui_state_drag");y.focus()};e.onmove=function(a,c){if(x){var e=b[0].style,h=d[0].style,j=a+l,o=c+t;e.width="auto";h.width=Math.max(0,j)+"px";e.width=b[0].offsetWidth+"px";h.height=Math.max(0,o)+"px"}else h=b[0].style,e=Math.max(m.minX,Math.min(m.maxX,a+s)),j=Math.max(m.minY,Math.min(m.maxY,c+g)),h.left=e+"px",h.top=j+"px";f();y._ie6SelectFix()};e.onend=function(){p.unbind("dblclick",e.end);!v&&z?c.unbind("losecapture",e.end):o.unbind("blur",e.end);q&&c[0].releaseCapture();v&&!y.closed&&y._autoPositionType();b.removeClass("aui_state_drag")};x=h.target===a.se[0]?!0:!1;m=function(){var a=y.DOM.wrap[0],b="fixed"===a.style.position,c=a.offsetWidth,a=a.offsetHeight,d=o.width(),f=o.height(),e=b?0:p.scrollLeft(),b=b?0:p.scrollTop();return{minX:e,minY:b,maxX:d-c+e,maxY:f-a+b}}();e.start(h)};p.bind("mousedown",function(h){var m=artDialog.focus;if(m){var o=h.target,p=m.config,m=m.DOM;if(!1!==p.drag&&o===m.title[0]||!1!==p.resize&&o===m.se[0])return e=e||new artDialog.dragEvent,l(h),!1}})})(this.art||this.jQuery&&(this.art=jQuery));