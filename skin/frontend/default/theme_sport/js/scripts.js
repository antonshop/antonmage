jQuery(document).ready(function(){
	var fl=false, 
	fl2=false;
	jQuery('.block-cart-header .cart-content').hide();
	jQuery('.block-cart-header  .amount a, .block-cart-header .cart-content').hover(function(){
		jQuery('.block-cart-header .cart-content').stop(true, true).slideDown(400);
	},function(){
		jQuery('.block-cart-header .cart-content').stop(true, true).delay(400).slideUp(300);
	})
	
       jQuery("a[data-gal^='prettyPhoto']").prettyPhoto({
            animationSpeed: 'normal',
            padding: 40,
            opacity: 0.35,
            showTitle: true,
            allowresize: true,
            counter_separator_label: '/',          
            theme: 'facebook' 
        });
		
		jQuery('.header .links li a').append("<span></span>"); 	
		jQuery('.footer ul li a, .header .links li a, .list-top-1 li a').not('.active').hover(function(){
		   jQuery(this).stop(true,false).animate({color:"#000000"}, {duration: 300});
		  },function(){
		   jQuery(this).stop(true,false).animate({color:"#767575"}, {duration: 300});
		 });
		jQuery('.header .links li a').not('.active')
		.hover(function(){
			jQuery(this).find('span')
			.stop().animate({width:'100%', left:'0'}, {duration:200})
		}, function(){
			jQuery(this).find('span')
			.stop().animate({width:'0', left:'50%'}, {duration:200})
		});
		
		jQuery('#banners')
	   .cycle({fx:    'turnDown', 
    width: 730, 
    height: 400, 
    timeout: 8000 });
		
		 jQuery(document).ready(function(){
		  if(navigator.userAgent.indexOf('Chrome')!=-1){
			  jQuery('body').addClass('chrome-fix');
		  }
		});

});
	 jQuery(window).load(function() {
		jQuery('#slider').nivoSlider({
			effect:'fade', //Specify sets like: 'fold,fade,sliceDown'
			slices:10,
			animSpeed:500,
			pauseTime:6000,
			startSlide:0, //Set starting Slide (0 index)
			directionNav:false, //Next & Prev
			directionNavHide:false, //Only show on hover
			controlNav:true, //1,2,3...
			controlNavThumbs:false, //Use thumbnails for Control Nav
			controlNavThumbsFromRel:false, //Use image rel for thumbs
			controlNavThumbsSearch: '.jpg', //Replace this with...
			controlNavThumbsReplace: '_thumb.jpg', //...this in thumb Image src
			keyboardNav:true, //Use left & right arrows
			pauseOnHover:true, //Stop animation while hovering
			manualAdvance:false, //Force manual transitions
			captionOpacity:1, //Universal caption opacity
			beforeChange: function(){},
			afterChange: function(){},
			slideshowEnd: function(){} //Triggers after all slides have been shown
		});
});  