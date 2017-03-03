/* This module is copywrite to ozxmod
 * Author: ozxmod(ozxmod@gmail.com)
 * It is illegal to remove this comment without prior notice to ozxmod(ozxmod@gmail.com)
*/ 
$(document).ready(function(){
	$('#tabs-login a').tabs();
	
	$("#loginpopup").bind("click", function() {
		$('#ozxmod_login_modal').modal('show');
		$('#ajaxsignuptab').removeClass("selected");
		$('#ajaxlogintab').addClass("selected");  
		$('#tab-login').show();
		$('#tab-register').hide();
		
		$('.form-group').each(function(){ $(this).removeClass('has-error'); });
	});
	  
	$("#signuppopup").bind("click", function() {
	 	$('#ozxmod_login_modal').modal('show');
	 	$('#ajaxlogintab').removeClass("selected");
		$('#ajaxsignuptab').addClass("selected");  
		$('#tab-login').hide();
		$('#tab-register').show();
		
		$('.form-group').each(function(){ $(this).removeClass('has-error'); });
	});
	
	$("#footer_close").bind("click", function() {
		$('#ozxmod_login_modal').modal('hide');
	});
	
	$("#footer_twitter_close").bind("click", function() {
		$('#tab-login').show();
		$('#ozxmod_twitter_modal').modal('hide');
		$('#ozxmod_login_modal').modal('show');
		$('#ajaxsignuptab').removeClass("selected");
		$('#ajaxlogintab').addClass("selected");
		$('#tab-register').hide();
	});

	// Twitter popup
	$(document).delegate('.twit_link', 'click', function() {
		$('#ozxmod_login_modal').modal('hide');
  		$('#ozxmod_signup_modal').modal('hide');
  		
  		$('#ozxmod_twitter_modal').modal('show');
 	});
	// End Twitter Popup
	
	$(".signup_link").on("click", function(){
		$('#ozxmod_login_modal').modal('hide');
		$('#ozxmod_signup_modal').modal('show');
	});
	
	$(".login_link").on("click", function(){
		$('#ozxmod_login_modal').modal('show');
		$('#ozxmod_signup_modal').modal('hide');
	});
	    	  
	 
  // Login
$('#button-login-pop').bind('click', function() {
	$.ajax({
		url: 'index.php?route=account/awesome_social_login_ozxmod/login',
		type: 'post',
		data: $('#login_details :input'),
		
		dataType: 'json',
		beforeSend: function() {
			$('#button-login-pop').attr('disabled', true);
			$('#button-login-pop').after('<span class="waiting">&nbsp;<img src="catalog/view/theme/default/stylesheet/ozxmod/assets/img/loading.gif" alt="" /></span>');
		},	
		complete: function() {
			$('#button-login-pop').attr('disabled', false);
			$('.waiting').remove();
		},				
		success: function(json) {
			$('.alert-danger, .error, .warning-tr').remove();
			
			if (json['success'] != undefined) {
				location=json['redirect'];
			} else if (json['error'] != undefined) {
				$('#button-login-pop').parent().after('<div class="alert alert-danger warning-tr">' + json['error'] + '</div>');
				setTimeout(function(){$('.warning-tr').fadeOut();}, 4000); 
			}
			
		}
	});	

	
});
 
$('#button-register-pop').bind('click', function() {
	$.ajax({
		url: 'index.php?route=account/awesome_social_login_ozxmod/signup',
		type: 'post',
		data: $('#signup_details :input'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-register-pop').attr('disabled', true);
			$('#button-register-pop').after('<span class="waiting">&nbsp;<img src="catalog/view/theme/default/stylesheet/ozxmod/assets/img/loading.gif" alt="" /></span>');
		},	
		complete: function() {
			$('#button-register-pop').attr('disabled', false);
			$('.waiting').remove();
		},				
		success: function(json) {
			$('.alert-danger, .error, .warning-tr').remove();
			$('.form-group').each(function(){ $(this).removeClass('has-error'); });
			
			if (json['success'] != undefined) {
				location=json['redirect'];
			} else if (json['error']!= undefined) {
				if(json['error']['error_name'] != undefined) {
					$('[name=ajax_register_name]').after('<div class="alert alert-danger warning-tr">' + json['error']['error_name'] + '</div>');
					$('[name=ajax_register_name]').parent().addClass('has-error');
				}
				if(json['error']['error_email'] != undefined) {
					$('[name=ajax_register_email]').after('<div class="alert alert-danger warning-tr">' + json['error']['error_email'] + '</div>');
					$('[name=ajax_register_email]').parent().addClass('has-error');
				}
				if(json['error']['error_telephone'] != undefined) {
					$('[name=ajax_register_telephone]').after('<div class="alert alert-danger warning-tr">' + json['error']['error_telephone'] + '</div>');
					$('[name=ajax_register_telephone]').parent().addClass('has-error');
				}
				if(json['error']['error_password'] != undefined) {
					$('[name=ajax_register_password]').after('<div class="alert alert-danger warning-tr">' + json['error']['error_password'] + '</div>');
					$('[name=ajax_register_password]').parent().addClass('has-error');
				}
				if(json['error']['error_repassword'] != undefined) {
					$('[name=re_ajax_register_password]').after('<div class="alert alert-danger warning-tr">' + json['error']['error_repassword'] + '</div>');
					$('[name=re_ajax_register_password]').parent().addClass('has-error');
				}
				setTimeout(function(){$('.warning-tr').fadeOut();}, 4000);
			}
		}
	});	
});

// Twitter Login
$('#button-twit-login').bind('click', function() {
	$.ajax({
		url: 'index.php?route=account/awesome_social_login_ozxmod/login_twit',
		type: 'post',
		data: $('#twitter_login_details :input'),
		
		dataType: 'json',
		beforeSend: function() {
			$('#button-twit-login').attr('disabled', true);
			$('#button-twit-login').after('<span class="waiting">&nbsp;<img src="catalog/view/theme/default/stylesheet/ozxmod/assets/img/loading.gif" alt="" /></span>');
		},	
		complete: function() {
			$('#button-twit-login').attr('disabled', false);
			$('.waiting').remove();
		},				
		success: function(json) {
			$('.alert-danger, .error, .warning-tr').remove();
			
			if (json['success'] != undefined) {
				location=json['redirect'];
			} else if (json['error'] != undefined) {
				$('[name="ajax_twit_email"]').parent().after('<div class="alert alert-danger warning-tr">' + json['error'] + '</div>');
				setTimeout(function(){$('.warning-tr').fadeOut();}, 4000); 
			//	$('.alert-danger').show();
				
			}
			
		}
	});	

	
});

// Showing error if login email id and twitter email id are different
if(typeof ozxmod_twit_error != 'undefined') {
	if(ozxmod_twit_error != "") {
		$('#ozxmod_twitter_modal').modal('show');
		$('[name="ajax_twit_email"]').parent().after('<div class="alert alert-danger warning-tr">' + ozxmod_twit_error + '</div>');
		setTimeout(function(){$('.warning-tr').fadeOut();}, 7000);
	}
}
// End Showing error if login email id and twitter email id are different

// End Twitter Login


$('#forgot_password').click(function(){
	$('#forgot_password_div').slideToggle();
});

$('#button-forgot-password').click(function(){
	$.ajax({
		url: 'index.php?route=account/awesome_social_login_ozxmod/sendForgotPassword',
		type: 'post',
		data: $('input[name=\'ajax_forgot_email\']'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-forgot-password').attr('disabled', true);
			$('#button-forgot-password').after('<span class="waiting">&nbsp;<img src="catalog/view/theme/default/stylesheet/ozxmod/assets/img/loading.gif" alt="" /></span>');
		},	
		complete: function() {
			$('#button-forgot-password').attr('disabled', false);
			$('.waiting').remove();
		},				
		success: function(json) {
			$('.alert-danger, .error').remove();
			if (json['success'] != undefined) {
				$('#forgot_password_div').before('<div class="alert alert-success warning-tr">' + json['success'] + '</div>');
				setTimeout(function(){$('.alert-success').fadeOut();}, 4000); 
				$('#forgot_password_div').slideUp(1000); 
			} else if (json['error']!= undefined) {
				$('[name="ajax_forgot_email"]').after('<div class="alert alert-danger">' + json['error'] + '</div>');
				setTimeout(function(){$('.alert-danger').fadeOut();}, 4000);
			}
		}
	});	
});

});


/* This module is copywrite to ozxmod
 * Author: ozxmod(ozxmod@gmail.com)
 * It is illegal to remove this comment without prior notice to ozxmod(ozxmod@gmail.com)
*/ 	