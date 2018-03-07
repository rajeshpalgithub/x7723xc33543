// JavaScript Document
$( document ).ready(function() {
$('#myModal').on('hide.bs.modal', function (e) {
  				$("#myModal .modal-body").html('');
			});
			
			
			
			$('#myModal').on('show.bs.modal', function(e) {
				var $modal = $(this);
				var $invoker = $(e.relatedTarget);
				
				var target = $invoker.attr("data-url");
				var mTitle= $invoker.attr("data-title");
				//console.log(target);
					/*target = e.relatedTarget.data-url,
					mTitle = e.relatedTarget.data-title;*/
				//$(this).attr("data-url")
				$modal.find('.modal-body').html('<span class="label label-danger" id="loading" >loading...</span>');
		
				$.ajax({
					cache: false,
					type: 'GET',
					url: target,
					success: function(data) {
						$modal.find('.modal-title').html(mTitle);
						$modal.find('.modal-body').html(data);
					}
				});
    		})
			
			
			
			/*jQuery("a[data-target=#myModal]").click(function(ev) {
				
				ev.preventDefault();
				var target = jQuery(this).attr("data-url");
				var mTitle= jQuery(this).attr("data-title");
				
				
				// load the url and show modal on success
				
				
				jQuery("#myModal .modal-body ").append('<div id="loading" >loading...</div>');
				jQuery(".modal-title").html(mTitle);
				
				
				
				var response;
				jQuery.ajax({ type: "GET",   
					 url: target,
					 cache: false,
					 success : function(text)
					 {
						
						 response= text;
					 }
				});
				jQuery('#myModal .modal-body ').html(response);
				
			});*/
 });