(function ($) {
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

			if (response.error) {
				failCallback(response.msg)
			} else{
				successCallback(response);
			}
		});
	}

	function retrieveImageIdsFromChildren (container) {
		var images = [];
		var children = $(container).children();

		for (var i = 0; i < children.length; i++) {
			images.push( parseInt(
				$(children[i]).attr("data-id")
			) );
		}

		return images;
	}

	$(document).ready(function () {
		// galery image single upload___________________________________________
		$(".MCCAdminArea-upload-image").change(function () {
			var fileInput = $(this);
			var form = new FormData($(this).closest("form")[0]);
			var isSingular = $(this).attr("data-singular") === "true";

			console.log(isSingular);

			submitData(form, function (imageData) {
				// Success
				var imagesList = fileInput.closest(".MCCAdminArea-file-upload-container").find(".MCCAdminArea-upload-images");
				var imageElement = $("<div data-id='" + imageData.id + "' style='background-image: url(\"" + imageData.src + "\");'></div>");
				var removalElement = $("<span>(X)</span>");

				if (isSingular) {
					imagesList.text("");
				}

				imageElement.append(imageData.title);
				removalElement.appendTo(imageElement);
				imageElement.appendTo(imagesList);

				// Add the event to remove the image
				removalElement.click(function () {
					$(this).parent().remove();
				});
			}, function (error) {
				// Fail
				console.log(error);
			});
		});

		// Post sumbission or approval__________________________________________
		$(".MCCAdminArea-post-submit").click(function (e) {
			e.preventDefault();

			var container = $(this).parent();
			//var postId = $(this).attr("name");
			var form = new FormData(container.find("form")[0]);
/*
			// Only for post approval
			if (postId) {
				// remove prefix (mcc_)
				postId = postId.slice(4);
			} else {

			}*/
			// Gallery and feature image
			var imageFieldContainers = container.find(".MCCAdminArea-upload-images");
			var galleryImages = undefined;
			var featureImage = undefined;

			for (var i = 0; i < imageFieldContainers.length; i++) {
				var imageFieldContainer = $(imageFieldContainers[i]);
				var isSingular = $(imageFieldContainers[i]).attr("data-singular") === "true";

				if (isSingular) {
					// Feature image
					featureImage = retrieveImageIdsFromChildren (
						imageFieldContainers[i]
					)[0];
				} {
					// Gallery images
					galleryImages = retrieveImageIdsFromChildren (
						imageFieldContainers[i]
					);
				}
			}

			if (featureImage) {
				form.append("feature", featureImage);
			}

			form.append("gallery", galleryImages);

			// Send it!
			submitData(form, function (message) {
				// Success
				console.log(message);
			}, function (error) {
				// Fail
				console.log(error);
			});
		});
		// MCCAdminArea-post-form

		// post submission
		$("#MCCAdminArea-post-submit").click(function (e) {
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
			}, function (error) {
				// Fail
				console.log(error);
			});
		});

		// Misc
		$(".MCCAdminArea-image-gallery-remove").click(function () {
			$(this).parent().remove();
		});

		$(".MCCAdminArea-post-title").click(function () {
			$(this).next().toggle(100);
		});

		$(".MCCAdminArea-edit-post").click(function (e) {
			e.preventDefault();

			$(this).parent().find(".MCCAdminArea-post-preview").toggle(100);
			$(this).parent().find(".MCCAdminArea-post-form-container").toggle(100);
		});
	});
}(jQuery));
