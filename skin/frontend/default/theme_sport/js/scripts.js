jQuery(document).ready(function(){
	
       jQuery("a[data-gal^='prettyPhoto']").prettyPhoto({
            animationSpeed: 'normal',
            padding: 40,
            opacity: 0.35,
            showTitle: true,
            allowresize: true,
            counter_separator_label: '/',          
            theme: 'facebook' 
        });
        jQuery('#custommenu .menu:first-child').addClass ('menu-item');
		jQuery('.cms-home .products-grid:first li:first-child').append ('<em></em>');
		
		jQuery('div.wp-custom-menu-popup a ').hover(function(){
		   jQuery(this).stop(true,false).animate({paddingLeft:"7px", color:"#cc450f"}, {duration: 300});
		  },function(){
		   jQuery(this).stop(true,false).animate({paddingLeft:"0px", color:"#8e8c8c"}, {duration: 300});
		 });
		
		 jQuery('.products-grid .product-name a').hover(function(){
		   jQuery(this).stop(true,false).animate({color:"#434343"}, {duration: 300});
		  },function(){
		   jQuery(this).stop(true,false).animate({color:"#DF4B11"}, {duration: 300});
		 });
		 
		  jQuery('.cms-home .products-grid .product-name a').hover(function(){
		   jQuery(this).stop(true,false).animate({color:"#DF4B11"}, {duration: 300});
		  },function(){
		   jQuery(this).stop(true,false).animate({color:"#434343"}, {duration: 300});
		 });
		  
		  jQuery('.footer .col-1 ul li a, .footer .col-2 ul li a, .footer .col-3 ul li a ').hover(function(){
		   jQuery(this).stop(true,false).animate({paddingLeft:"10px"}, {duration: 300});
		  },function(){
		   jQuery(this).stop(true,false).animate({paddingLeft:"0px"}, {duration: 300});
		 });
		  
		   jQuery('.list-icon li a').hover(function(){
		   jQuery(this).stop(true,false).animate({paddingTop:"10px", marginTop:"4px" }, {duration: 300});
		  },function(){
		   jQuery(this).stop(true,false).animate({paddingTop:"0px", marginTop:"0px"}, {duration: 300});
		 });
		
		jQuery('.col-main .product-image').hover(function(){
		   jQuery(this).stop(true,false).animate({borderTopColor: '#000', borderLeftColor: '#000', borderRightColor: '#000', borderBottomColor: '#000'}, {duration: 450});
		  },function(){
		   jQuery(this).stop(true,false).animate({borderTopColor: '#EAEAE6', borderLeftColor: '#EAEAE6', borderRightColor: '#EAEAE6', borderBottomColor: '#EAEAE6'}, {duration: 450});
		 });
		
		jQuery('.mini-products-list .product-image').hover(function(){
		   jQuery(this).stop(true,false).animate({borderTopColor: '#000', borderLeftColor: '#000', borderRightColor: '#000', borderBottomColor: '#000'}, {duration: 450});
		  },function(){
		   jQuery(this).stop(true,false).animate({borderTopColor: '#fff', borderLeftColor: '#fff', borderRightColor: '#fff', borderBottomColor: '#fff'}, {duration: 450});
		 });
         

		 jQuery(document).ready(function(){
		  if(navigator.userAgent.indexOf('Chrome')!=-1){
			  jQuery('body').addClass('chrome-fix');
		  }
		});
        
});
jQuery(document).ready(function(){
	var fl=false, 
	fl2=false;
	jQuery('.block-cart-header .cart-content').hide();
	jQuery('.block-cart-header  .amount a, .block-cart-header .cart-content').hover(function(){
		jQuery('.block-cart-header .cart-content').stop(true, true).slideDown(400);
	},function(){
		jQuery('.block-cart-header .cart-content').stop(true, true).delay(400).slideUp(300);
	});
});  
