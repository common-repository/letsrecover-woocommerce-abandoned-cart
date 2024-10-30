jQuery(document).ready(function($) {
	toggleEnableDisableSettings();
	$("[data-link]").click(function(){
		toggleEnableDisableSettings();
	});

	function toggleEnableDisableSettings(){
		if($("[data-link]").is(":checked") == true){
			$( $("[data-link]").attr('data-link') + " *").removeAttr('disabled');
		}
		else{
			$( $("[data-link]").attr('data-link') + " *").attr('disabled','disabled');
		}
	}

	/** NOTIFICATION PREVIEW */
	$(document).on('DOMSubtreeModified', ".title .emojionearea-editor", function() {
		$(".wplrp-preview-title").html($(this).html())
	})

	$(document).on('DOMSubtreeModified', ".message .emojionearea-editor", function() {
		$(".wplrp-preview-msg").html($(this).html())
	})

	$(document).on('DOMSubtreeModified', ".button_1_text .emojionearea-editor", function() {
		$(".button1-preview").html($(this).html())
	})

	$(document).on('DOMSubtreeModified', ".button_2_text .emojionearea-editor", function() {
		$(".button2-preview").html($(this).html())
	})

	$(window).scroll(function (event) {
		if( $(".wplrp-notification-wrapper").length ){
			var scroll = $(window).scrollTop();
			if( scroll > ($(".wplrp-notification-wrapper").offset().top ) )
				$(".wplrp-push-preview").addClass('fixed')
			else
				$(".wplrp-push-preview").removeClass('fixed')
		}
	});
	
	$("#image").keyup(function(){
		$(".lp-preview-image").text($(this).val())
	});
	$("#icon").keyup(function(){
		$(".wplrp-preview-icon").text($(this).val())
	});
	/** NOTIFICATION PREVIEW */


	/** NOTIFICATION ACTION BUTTON */
	$('.add-action-button').on('click', function(e) {
		e.preventDefault();
		//if 1 is already shown, show 2
		if ($(".wplrp-action-button-1").is(":visible")) {
			$(".wplrp-action-button-2").show();
			$('.add-action-button-wrapper').hide(); //no more available so hide
			$('.add-action-button').parent('li').hide(); //no more available so hide
		} else {
			$(".wplrp-action-button-1").show();
		}
		$(".wplrp-action-button-windows-preview").show();
		$(".wplrp-mac-notification-buttons").show();
	});

	$('.wplrp-action-button-remove').on('click', function() {
		//find parent
		var parent = $(this).closest('.wplrp-action-button');
		//show add button
		$('.add-action-button-wrapper').show();
		$('.add-action-button').parent('li').show();
	
		if(  $(".wplrp-action-button").is(":hidden") )
			$(".wplrp-action-button-windows-preview").hide();


		//clear out the button contents
		if (parent.hasClass('wplrp-action-button-1')) {
			$(".wplrp-action-button-1").hide();
			// $(".a01-preview").hide().html("");
			$("#wplrp_button1_text").val('').next(".emojionearea").children(".emojionearea-editor").html("");
			$("#wplrp_button1_url").val('');
		} else {
			$(".wplrp-action-button-2").hide();
			// $(".a02-preview").hide().html("");
			$("#wplrp_button2_text").val('').next(".emojionearea").children(".emojionearea-editor").html("");
			$("#wplrp_button2_url").val('');
		}

	});
	/** NOTIFICATION ACTION BUTTON */

	/** UPLOAD BUTTON */
   var mediaUploader;
	$(".wplrp-btn-upload").on('click',function(){
        $upload_field = $(this).prev(".upload-field");
        $galary_title = $(this).data('label');
			
		mediaUploader = wp.media.frames.file_frame = wp.media({
			title : 'Choose ' + $galary_title,
			button:	{
				text : "Insert " + $galary_title,
			},
			multiple: false
		});
		
		mediaUploader.on('select',function(){
			attachment = mediaUploader.state().get('selection').first().toJSON();
			console.log($galary_title);
			if( $galary_title == 'Icon'){
				$(".wplrp-preview-icon").html("<img src='"+ attachment.url +"'>");     
				$upload_field.val(attachment.url);           
			}
			else if( $galary_title == 'Logo'){
				if( typeof(attachment.sizes.thumbnail) != 'undefined' )
					$url = attachment.sizes.thumbnail.url;	
				else if( typeof(attachment.sizes.medium ) != 'undefined' )
					$url = attachment.sizes.medium.url;	
				else
					$url = attachment.sizes.full.url;

				$(".wplrpPromptIcon").html("<img src='"+ $url +"'>");                
				$upload_field.val($url);
			}
			else{
				$(".lp-preview-image").html("<img src='"+ attachment.url +"'>");
				$(".wplrp-windows-preview").height('465px');
				$(".androidNotificationMessage").height('20px');
				$upload_field.val(attachment.url);
			}
		});
		
		mediaUploader.open();
		return false;
	});
	/** UPLOAD BUTTON */

	/** emojifield */
	if( $(".emojifield").length ){
		$(".emojifield").emojioneArea({
			pickerPosition: "right"
		});
	}

	/** Abandoned Carts Page */
	$(".wplrp-cart-total").on('click', function(){
		$(this).next('.wplrp-cart-info').slideToggle(300);
	})

	$(".wplrp-push-sent").on('click', function(){
		
		$id = $(this).data('cart-id')
		$push_info_wrapper = $(this).next('.wplrp-push-info');
		$push_info_wrapper.slideToggle(300, function(){

			if(  $push_info_wrapper.css('display') == 'block' ){
				$.ajax({
					'url'			: "/wp-admin/admin-ajax.php",
					'data'		: {'action' : 'get_push_info', 'cart_id' : $id },
					'success'	: function(data){
						$push_info_wrapper.html( data )
					}
				})
			}

		});
	})

	/** CUSTOM PROMPT */
	$("#title").keyup(function(){
		$(".wplrpPromptHeading").text($(this).val());
	});
	$("#message").keyup(function(){
		$(".wplrpPromptMessage").text($(this).val());
	})
	$("#allow_button_text").keyup(function(){
		$(".wplrppromptapprovebtn .wplrppromptbutton").text($(this).val());
	})
	$("#dismiss_button_text").keyup(function(){
		$(".wplrppromptdismissbtn .wplrppromptbutton").text($(this).val());
	})
	$(".logo").change(function(){
		$(".wplrpPromptIcon img").attr('src',$(this).val());
	})
	$("#prompt_title_color").change(function(){
		$(".wplrpPromptHeading").css('color',$(this).val());
	});
	$("#prompt_message_color").on('input',function(){
		$(".wplrpPromptMessage").css('color',$(this).val());
	});
	$("#allow_button_text_color").on('input',function(){
		$(".wplrppromptapprovebtn .wplrppromptbutton").css('color',$(this).val());
	});
	$("#dismiss_button_text_color").on('input',function(){
		$(".wplrppromptdismissbtn .wplrppromptbutton").css('color',$(this).val());
	});
	$("#allow_button_background_color").on('input',function(){
		$(".wplrppromptapprovebtn .wplrppromptbutton").css('background-color',$(this).val());
	});
	$("#dismiss_button_background_color").on('input',function(){
		$(".wplrppromptdismissbtn .wplrppromptbutton").css('background-color',$(this).val());
	});
});

function wplrp_confirm_delete(e){
	if( ! confirm("Are you sure to delete?") )
		e.preventDefault();
}

function prompt_type(ele){
	if( jQuery(ele).val() == 'native' ){
		jQuery(".wplrp_custom_prompt").hide();
		jQuery(".native-prompt").show();
	}else{
		jQuery(".wplrp_custom_prompt").show();
		jQuery(".native-prompt").hide();
	}

}
