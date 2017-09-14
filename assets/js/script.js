(function () {

    var websiteUrl = "http://localhost";
    var defaultContentType = "application/x-www-form-urlencoded";
    var $alertsContainer = $('.alerts-container');
    var $alerts = $('.alert');
    var alertMessages = {
        'selectImage': 'Please select an image to upload.',
        'uploadSuccess': 'Image has been uploaded successfully.'
    }

    getAllStoredImages();

    // Function which will fetches all stored images on page load
    function getAllStoredImages() {
        var message = "Fetching stored images..."
        var dataToSend = {
            'getAll': true
        };
        makeAjaxCall(dataToSend, true, defaultContentType, message, appendAllImages);
    }

    // Function which will appends all stored images to images container
    function appendAllImages(data) {
        if (data.success) {
            var imagesHtml = '';
            var images = data.images;
            var imagesCount = images.length;
            if(imagesCount > 0) {
                for (var i = 0; i < imagesCount; i++) {
                    imagesHtml += '<div class="col-sm-4 image-col"><image class="img img-responsive upload-image" src="' + websiteUrl + data.images[i].imagepath + '"></div>';
                    $('.images-row').html(imagesHtml);
                }
            } else {
                imagesHtml += "Sorry, there are no images uploaded yet to show...";
                $('.images-row').html(imagesHtml);
            }
        }
    }

    // Function to send ajax requests to server
    function makeAjaxCall(data, processData, contentType, message, callBack) {
        $.blockUI({message: '<h4>' + message + '</h4>'});
        $.ajax({
            url: websiteUrl + "/imageupload/uploadapi/uploadapi.php",
            type: "POST",
            dataType: 'json',
            processData: processData,
            contentType: contentType,
            data: data,
            success: function (response) {
                callBack(response);
            },
            complete: function () {
                $.unblockUI();
            }
        });
    }

    $(':file').change(function () {
        var image = this.files[0];
        var imageName = image.name;
        $('label').html(imageName);
    });

    $("#btnUpload").click(function () {
        var message = "Uploading image...";
        if($('#file').prop('files').length > 0) {
            var formData = new FormData();
            formData.append('file', $('#file').prop('files')[0]);
            makeAjaxCall(formData, false, false, message, appendNewImage);
        } else {
            $alerts.css({'display' : 'none'});
            $alertsContainer.find('.alert-warning').css({'display' : 'block'}).html(alertMessages.selectImage);
        }
    });

    // Funciton which will appends uploaded image with existing images after getting response from server
    function appendNewImage(data) {
        var $imagesRow = $('.images-row');
        if (data.success) {
            var imagesHtml = '';
            resetFileInput();
            imagesHtml += '<div class="col-sm-4 image-col"><image class="img img-responsive upload-image" src="' + websiteUrl + data.images.path + '"></div>';
            if($imagesRow.find('img').length > 0) {
                $('.images-row').append(imagesHtml);
            } else {
                $('.images-row').html(imagesHtml);
            }
            $alerts.css({'display' : 'none'});
            $alertsContainer.find('.alert-success').css({'display' : 'block'}).html(alertMessages.uploadSuccess);
            hideSuccessMessage();
        } else {
            $alerts.css({'display' : 'none'});
            $alertsContainer.find('.alert-danger').css({'display' : 'block'}).html(data.error);
        }
    }

    // Resetting file input
    function resetFileInput() {
        $(':file').val("");
        $('label').html("Choose an image");
    }
    
    // Function to hide success message
    function hideSuccessMessage() {
        setTimeout(function () {
            $alerts.css({'display' : 'none'});
        }, 4000);
    }

})();