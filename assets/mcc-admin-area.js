console.log("Loaded script");
(function ($) {
	console.log("Browser handled self-called function");
	// Submit data, uses callbacks (puke) for browser compatibility
	function submitData (data, successCallback, failCallback) {
		console.log("Attempting to send data");
		$.ajax({
			url: '/mccadminarea_post',
			type: 'POST',
			data: data,
			processData: false,
			contentType: false,
		}).complete(function (data) {
			var response = null;

			try {
				response = JSON.parse(data.responseText);
			} catch (e) {
				try {
					response = JSON.parse(data);
				} catch (e) {
					failCallback(data);
				}
			}

			console.log(response, data);

			if (response.error) {
				failCallback(response.msg)
			} else{
				successCallback();
			}
		});
	}

	$(document).ready(function () {
		console.log("Browser handled ready event");
		// post submission
		$("#MCCAdminArea-post-submit").click(function (e) {
			console.log("Browser handled click event");
			e.preventDefault();

			submitData(new FormData($('#MCCAdminArea-post-form')[0]), function () {
				// Success
				$("#MCCAdminArea-post-form").hide(100);
				$(".MCCAdminArea-post-success-message").show(100);
			}, function (error) {
				// Fail
				console.log(error);
			});
		});

		// post approval
		$(".MCCAdminArea-approve-post").click(function () {
			// change state of this post
			var postContainer = $(this).parent().parent(); // <li>
			var dynamicForm = postContainer.find(".MCCAdminArea-dynamic"); // <form>

			var form = new FormData();

			// remove prefix (mcc_)
			var postId = $(this).attr("name").slice(4);

			// if the form is being used when we click approve:
			if (dynamicForm.is(":visible")) {
				form = new FormData(dynamicForm[0]);

				var existingImages = dynamicForm.children(".existing-image-gallery-items");

				// If the gallery has any children add the id's to the form
				if (existingImages.length > 0) {
					var oldGallery = [];

					for (var existingImage of existingImages) {
						if ($(existingImage).is(":visible")) {
							var id = $(existingImage).attr("name");

							id = parseInt(id.slice(4));

							oldGallery.push(id);
						}
					}

					form.append("old_gallery", oldGallery);
				}
			}

			form.append("post_id", postId)

			submitData(form, function () {
				// Success
					postContainer.hide(100);
			}, function () {
				// Fail
				console.log(response.msg);
			});
		});

		// Misc
		$(".MCCAdminArea-image-gallery-remove").click(function () {
			$(this).parent().hide(0);
		});

		$(".MCCAdminArea-post-title").click(function () {
			$(this).next().toggle(100);
		});

		$(".MCCAdminArea-edit-post").click(function (e) {
			e.preventDefault();

			$(this).parent().parent().find(".MCCAdminArea-dynamic").toggle(100);
			$(this).parent().parent().find(".MCCAdminArea-static").toggle(100);
		});
	});
}(jQuery));
