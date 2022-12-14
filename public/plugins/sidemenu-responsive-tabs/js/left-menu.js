(function($) {
	"use strict";
	
	// ______________Active Class
	$(".app-sidebar li a").each(function() {
	  var pageUrl = window.location.href.split(/[?#]/)[0];
		if (this.href == pageUrl) { 
			$(this).addClass("active");
			$(this).parent().addClass("active"); // add active to li of the current link
			$(this).parent().parent().prev().addClass("active"); // add active class to an anchor
			$(this).parent().parent().prev().click(); // click the item to make it drop
		}
	}); 
	
	
	//Active Class
	$(".app-sidebar li a").each(function() {
		var pageUrl = window.location.href.split(/[?#]/)[0];
		if (this.href == pageUrl) {
			$(this).addClass("active");
			$(this).parent().addClass("active"); // add active to li of the current link
			$(this).parent().addClass("resp-tab-content-active"); // add active to li of the current link
			$(this).parent().parent().parent().prev().addClass("active"); // add active class to an anchor
			$(this).parent().parent().parent().prev().click(); // click the item to make it drop
		}
	});
	
	$(".submenu-list li a").each(function() {
		var pageUrl = window.location.href.split(/[?#]/)[0];
		if (this.href == pageUrl) {
			$(this).addClass("active");
			$(this).parent().parent().parent().parent().parent().addClass("active"); // add active to li of the current link
			$(this).parent().parent().parent().parent().parent().addClass("resp-tab-content-active"); // add active to li of the current link
			$(this).parent().parent().parent().prev().addClass("active"); // add active class to an anchor
			$(this).parent().parent().parent().prev().click(); // click the item to make it drop
		}
	});
	
	$(document).ready(function(){		
		
		if ($('.dashboard-admintro.active').hasClass('active'))
        $('li.dashboard-admintro').addClass('active');
	
		if ($('.app-admintro.active').hasClass('active'))
        $('li.app-admintro').addClass('active');
	
		if ($('.widget-admintro.active').hasClass('active'))
        $('li.widget-admintro').addClass('active');
	
		if ($('.forms-admintro.active').hasClass('active'))
        $('li.forms-admintro').addClass('active');
		
		if ($('.chart-admintro.active').hasClass('active'))
        $('li.chart-admintro').addClass('active');
	
		if ($('.map-admintro.active').hasClass('active'))
        $('li.map-admintro').addClass('active');
	
		if ($('.element-admintro.active').hasClass('active'))
        $('li.element-admintro').addClass('active');
	
		if ($('.icons-admintro.active').hasClass('active'))
        $('li.icons-admintro').addClass('active');
	
		if ($('.pages-admintro.active').hasClass('active'))
        $('li.pages-admintro').addClass('active');
		
		if ($('.ecommerce-admintro.active').hasClass('active'))
        $('li.ecommerce-admintro').addClass('active');
		
		if ($('.basic-admintro.active').hasClass('active'))
        $('li.basic-admintro').addClass('active');
	
		if ($('.account-admintro.active').hasClass('active'))
        $('li.account-admintro').addClass('active');
	
		if ($('.error-admintro.active').hasClass('active'))
        $('li.error-admintro').addClass('active');
	
	});
	
	
	// VerticalTab
	$('#sidemenu-Tab').easyResponsiveTabs({
		type: 'vertical',
		width: 'auto', 
		fit: true, 
		closed: 'accordion',
		tabidentify: 'hor_1',
		activate: function(event) {
			var $tab = $(this);
			var $info = $('#nested-tabInfo2');
			var $name = $('span', $info);
			$name.text($tab.text());
			$info.show();
		}
	});
	
	const ps11 = new PerfectScrollbar('.first-sidemenu', {
	  useBothWheelAxes:true,
	  suppressScrollX:true,
	});
	const ps12 = new PerfectScrollbar('.second-sidemenu', {
	  useBothWheelAxes:true,
	  suppressScrollX:true,
	});
	
})(jQuery);