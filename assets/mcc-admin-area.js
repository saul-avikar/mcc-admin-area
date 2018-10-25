(function ($) {
	var transitionSpeed = 200;
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

	function hideAndDelete (el) {
		var parent = $(el).parent();
		parent.hide(transitionSpeed);

		window.setTimeout(function () {
			parent.remove();
		}, transitionSpeed);
	}

	$(document).ready(function () {
		// galery image single upload___________________________________________
		$(".MCCAdminArea-upload-image").change(function () {
			var fileInput = $(this);
			var form = new FormData($(this).closest("form")[0]);
			var isSingular = $(this).attr("data-singular") === "true";
			var container = fileInput.closest(".MCCAdminArea-file-upload-container");

			// Hide any errors as we will display them again later if needed
			container.find(".MCCAdminArea-upload-image-failure").hide(transitionSpeed);

			submitData(form, function (imageData) {
				// Success
				var imagesList = container.find(".MCCAdminArea-upload-images");
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
					hideAndDelete(this);
				});
			}, function (error) {
				// Fail
				console.error(error);

				container.find(".MCCAdminArea-upload-image-failure").show(transitionSpeed);
			});
		});

		// Post sumbission or approval__________________________________________
		$(".MCCAdminArea-post-submit").click(function (e) {
			e.preventDefault();

			var container = $(this).parent();
			var postId = $(this).attr("data-id");
			var form = new FormData(container.find("form")[0]);


			// Disable to stop button spammers
			$(this).prop("disabled", true);

			// Hide any errors as we will display them again later if needed
			container.find(".MCCAdminArea-failure-message").hide(transitionSpeed);

			// Only for post approval
			if (postId) {
				// remove prefix (mcc_)
				form.append("post_id", postId);
			}

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
				if (postId) {
					container.parent().hide(transitionSpeed);
				} else {
					container.find(".MCCAdminArea-success-message").show(transitionSpeed);
					container.find(":not(.MCCAdminArea-success-message)").hide(transitionSpeed);
				}
			}, function (error) {
				// Fail
				console.error(error);
				container.find(".MCCAdminArea-failure-message").show(transitionSpeed);

				// Re-enable the button to try again.
				$(".MCCAdminArea-post-submit").prop("disabled", false);
			});
		});

		// Misc
		$(".MCCAdminArea-image-gallery-remove").click(function () {
			hideAndDelete(this);
		});

		$(".MCCAdminArea-post-title").click(function () {
			$(this).next().toggle(transitionSpeed);
		});

		$(".MCCAdminArea-edit-post").click(function (e) {
			e.preventDefault();

			$(this).parent().find(".MCCAdminArea-post-preview").toggle(transitionSpeed);
			$(this).parent().find(".MCCAdminArea-post-form-container").toggle(transitionSpeed);

			$(this).text( $(this).text() === "Edit" ? "Cancel" : "Edit");
		});
	});
}(jQuery));
