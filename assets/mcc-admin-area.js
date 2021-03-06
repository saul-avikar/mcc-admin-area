(function ($) {
	var transitionSpeed = 200;
	// Submit data, uses callbacks (puke) for browser compatibility
	function submitData (data, successCallback, failCallback, progressCallback = null) {
		$.ajax({
			url: '/mccadminarea_post',
			type: 'POST',
			data: data,
			processData: false,
			contentType: false,
			// Progress report
			xhr: function () {
				var xhr = new window.XMLHttpRequest();

				//Upload progress, request sending to server
				xhr.upload.addEventListener("progress", function(e){
					if (e.lengthComputable) {
						percentComplete = parseInt( (e.loaded / e.total * 100), 10);

						if (progressCallback) {
							progressCallback(percentComplete);
						}
					}
				}, false);

				//Download progress, waiting for response from server
				xhr.addEventListener("progress", function(e){
					if (progressCallback) {
						progressCallback(100);
					}
				}, false);
				return xhr;
			},
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

	function loginFailure () {
		var get = {};
		var login;

		document.location.search.replace(/\??(?:([^=]+)=([^&]*)&?)/g, function () {
			function decode(s) {
				return decodeURIComponent(s.split("+").join(" "));
			}

			get[decode(arguments[1])] = decode(arguments[2]);
		});

		login = get["login"];

		if (!login) {
			return;
		}

		if (login.substr(0, 5) === "empty" || login.substr(0, 6) === "failed") {
			$(".MCCAdminArea-login-fail").show(transitionSpeed);
		}
	}

	$(document).ready(function () {
		// Check for login failure______________________________________________
		loginFailure();
		// galery image single upload___________________________________________
		$(".MCCAdminArea-upload-image").change(function (e) {
			var fileInput = $(this);
			var form = new FormData($(this).closest("form")[0]);
			var isSingular = $(this).attr("data-singular") === "true";
			var container = fileInput.closest(".MCCAdminArea-file-upload-container");
			var fileSize = e.currentTarget.files[0].size / 1024 / 1024;
			var progressElement = container.find(".MCCAdminArea-upload-image-upload-progress");
			var failureMessageElement = container.find(".MCCAdminArea-upload-image-failure");
			var sizeRestrictionElement = container.find(".MCCAdminArea-upload-image-size-failure");

			// Hide any errors as we will display them again later if needed
			failureMessageElement.hide(transitionSpeed);
			sizeRestrictionElement.hide(transitionSpeed);

			// 2MB restriction
			if (fileSize > 2) {
				// Image is too large
				sizeRestrictionElement.show(transitionSpeed);
				return;
			}

			fileInput.prop("disabled", true);

			// Show progress
			progressElement.show(transitionSpeed);

			submitData(form, function (imageData) {
				// Success
				var imagesList = container.find(".MCCAdminArea-upload-images");
				var imageElement = $("<div data-id='" + imageData.id + "' style='background-image: url(\"" + imageData.src + "\");'></div>");
				var removalElement = $("<div class='MCCAdminArea-image-gallery-remove'>X</div>");

				if (isSingular) {
					imagesList.text("");
				}

				removalElement.appendTo(imageElement);
				imageElement.append("<div class='MCCAdminArea-image-gallery-title'>" + imageData.title + "</div>");
				imageElement.appendTo(imagesList);

				// Add the event to remove the image
				removalElement.click(function () {
					hideAndDelete(this);
				});

				progressElement.hide(transitionSpeed);

				fileInput.prop("disabled", false);
			}, function (error) {
				// Fail
				console.error(error);

				failureMessageElement.show(transitionSpeed);

				progressElement.hide(transitionSpeed);

				fileInput.prop("disabled", false);
			}, function (progress) {
				// Progress callback
				progressElement.text(progress + "%");
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
