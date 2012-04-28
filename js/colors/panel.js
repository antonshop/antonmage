jQuery(document).ready(function(){
    
    
    
    // OPEN/CLOSE
    
    var time_out = 300;
    var etheme_cp = jQuery('.etheme_cp');
    var btn_show = jQuery('.etheme_cp_btn_show');
    var btn_close = etheme_cp.find('.etheme_cp_head .etheme_cp_btn_close');
    
    if(jQuery.cookie('etheme_cp_closed') == 1){
        hide_cp(0);
    }else{
        show_cp(0);
    }
    
    function hide_cp(time){
        etheme_cp.animate({
            'left': '-200px' 
        },time, function() {
            btn_show.show().animate({
                'left':'0px'
            },time);
            jQuery.cookie('etheme_cp_closed', 1);
        }); 
    }    
    
    function show_cp(time){
        btn_show.animate({
            'left':'-60px'  
        },time, function() {
            etheme_cp.show().animate({
                'left':'0px'
            });
            jQuery.cookie('etheme_cp_closed', 0);
        });
    }
    
    btn_close.bind("click", function() {
        hide_cp(300);
    }); 
    
    btn_show.bind("click", function() {
        show_cp(300);
    });  
       
    // Color changes
    
    var pattern_select = jQuery('.pattern_select');
    var btn_clear_pattern = jQuery('.clear_patern');
    var pattern_selected = jQuery.cookie('pattern');
    var pattern_selected_id = jQuery.cookie('pattern_selected_id');
    
    if(pattern_selected_id){
        var selector1 = '#' + pattern_selected_id;
        jQuery(selector1).addClass('selected');
    }
    
    if(pattern_selected){
        jQuery('body').css({
            'backgroundImage' : pattern_selected
        });
    }
    
    function clearPattern(){
        jQuery.cookie('pattern1', null);
        jQuery.cookie('pattern_selected_id', null);
        jQuery('.pattern_select').each(function(){
            jQuery(this).removeClass('selected');
        });
        jQuery('body').css({
            'backgroundImage' : pattern_default
        });
    }
    

    function choose_pattern(pattern_block){
        var pattern_chosed = pattern_block.css('backgroundImage');
        jQuery('.pattern_select').each(function(){
            jQuery(this).removeClass('selected');
        });
        pattern_block.addClass('selected');
        jQuery('body').css({
            'backgroundImage' : pattern_chosed
        });
        jQuery.cookie('pattern', pattern_chosed);
        jQuery.cookie('pattern_selected_id', pattern_block.attr('id'));
    }
    
    btn_clear_pattern.bind("click", function() {
        clearPattern();
    });        
    pattern_select.bind("click", function() {
        choose_pattern(jQuery(this));
    });  
    
    // COLORPICKER
    var bg_color = jQuery.cookie('bg_color');
    var first_color = jQuery.cookie('first_color');
    var second_color = jQuery.cookie('second_color');
    var btn_clear_color = jQuery('.clear_color');
    var color_selector_div = jQuery('.colorSelector');
    var color_selector2_div = jQuery('.colorSelector_first');
    var color_selector3_div = jQuery('.colorSelector_second');
    
    
    // Bg color 
    function clearBgColor(){
        jQuery.cookie('bg_color', null);
        jQuery.cookie('first_color', null);
        jQuery.cookie('second_color', null);
        jQuery('body').css({
            'backgroundColor' : '#' + bg_default
        });
        jQuery(first_color_selector).css({
            'backgroundColor' : '#' + first_color_default
        });  
        jQuery(second_color_selector).css({
            'backgroundColor' : '#' + second_color_default
        });  
        jQuery(first_color_border_selector).css('borderColor', '#' + first_color_default);  
        color_selector2_div.find('div').css({
            'backgroundColor' : '#' + first_color_default
        });  
        color_selector3_div.find('div').css({
            'backgroundColor' : '#' + second_color_default
        });               
    }
    
    if(bg_color){
        color_picker = bg_color;
    }else{
        color_picker = bg_default;
    }
    
    color_selector_div.find('div').css({
        'backgroundColor' : '#' + color_picker
    });
    
    if(bg_color){
        jQuery('body').css({
            'backgroundColor' : '#' + bg_color
        });
    }
    
    jQuery('.colorSelector').ColorPicker({
    	color: '#' + color_picker,
        cssclass: 'picker1',
    	onShow: function (colpkr) {
    		jQuery(colpkr).fadeIn(500);
    		return false;
    	},
    	onHide: function (colpkr) {
    		jQuery(colpkr).fadeOut(500);
    		return false;
    	},
    	onChange: function (hsb, hex, rgb) {
    		jQuery('.colorSelector div').css('backgroundColor', '#' + hex);
    		jQuery('body').css('backgroundColor', '#' + hex);
            jQuery.cookie('bg_color', hex);
    	}
    })    .bind('keyup', function(){
    	jQuery(this).ColorPickerSetColor(this.value);
    });
    
    // First color 
        
    if(first_color){
        color_picker1 = first_color;
        jQuery(first_color_selector).css({
            'backgroundColor' : '#' + first_color
        });  
        jQuery(first_color_border_selector).css('borderColor', '#' + first_color);      
    }else{
        color_picker1 = first_color_default;
    }
    
    color_selector2_div.find('div').css({
        'backgroundColor' : '#' + color_picker1
    });

    jQuery('.colorSelector_first').ColorPicker({
    	color: '#' + color_picker1,
        cssclass: 'picker2',
    	onShow: function (colpkr) {
    		jQuery(colpkr).fadeIn(500);
    		return false;
    	},
    	onHide: function (colpkr) {
    		jQuery(colpkr).fadeOut(500);
    		return false;
    	},
    	onChange: function (hsb, hex, rgb) {
    		jQuery('.colorSelector_first div').css('backgroundColor', '#' + hex);
    		jQuery(first_color_selector).css('backgroundColor', '#' + hex);
    		jQuery(first_color_border_selector).css('borderColor', '#' + hex);
            jQuery.cookie('first_color', hex);
    	}
    })    .bind('keyup', function(){
    	jQuery(this).ColorPickerSetColor(this.value);
    });    
     
     
    // Second color
    
    if(second_color){
        color_picker2 = second_color;
        jQuery(second_color_selector).css({
            'backgroundColor' : '#' + second_color
        });    
    }else{
        color_picker2 = second_color_default;
    }
    
    color_selector3_div.find('div').css({
        'backgroundColor' : '#' + color_picker2
    });   
     
    jQuery('.colorSelector_second').ColorPicker({
    	color: '#' + color_picker2,
        cssclass: 'picker3',
    	onShow: function (colpkr) {
    		jQuery(colpkr).fadeIn(500);
    		return false;
    	},
    	onHide: function (colpkr) {
    		jQuery(colpkr).fadeOut(500);
    		return false;
    	},
    	onChange: function (hsb, hex, rgb) {
    		jQuery('.colorSelector_second div').css('backgroundColor', '#' + hex);
    		jQuery(second_color_selector).css('backgroundColor', '#' + hex);
            jQuery.cookie('second_color', hex);
    	}
    })    .bind('keyup', function(){
    	jQuery(this).ColorPickerSetColor(this.value);
    });    
    
    btn_clear_color.bind("click", function() {
        clearBgColor();
    });  
    
    function clearAll(){
        clearPattern();
        clearBgColor();
    }
    
    
});//End ready