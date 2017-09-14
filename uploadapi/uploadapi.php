<?php

require_once 'config.php';
require_once 'libraries/Database.php';

$objDatabase = new Database();

// Checking for database connection
if($objDatabase->dbConnection === NULL){
    $response = array(
        'error' => "There was an error occurred while connecting to database.",
        'success' => false
    );
    echo json_encode($response);
    die();
}

if (array_key_exists('getAll', $_REQUEST)) { // Fetching all stored images on page load
    $selectQuery = "SELECT CONCAT('/imageupload/imageuploads/', renamed_image_name) as imagepath FROM images";
    $result = $objDatabase->fetchAll($selectQuery);
    if ($result instanceof Exception) {
        $response = array(
            'error' => "There was an error occurred while fetching images from database.",
            'success' => false
        );
    } else {
        $response = array(
            'images' => $result,
            'success' => true
        );
    }
} else { // Inserting uploaded file into database table
    $error = "";
    $response = array();
    $allowedExtensions = array('jpg', 'jpeg', 'png');
    $imageName = $_FILES['file']['name'];

    // Checking for errors in uploaded file
    if ($_FILES['file']['error'] === UPLOAD_ERR_NO_FILE) {
        $error = "Please select an image to upload.";
    } else if ($_FILES['file']['error'] === UPLOAD_ERR_INI_SIZE) {
        $error = "Uploaded image size should be less than or equal to 2MB.";
    }

    // Validating uploaded image file for image extension
    $imageExtension = pathinfo($imageName, PATHINFO_EXTENSION);
    if (!in_array($imageExtension, $allowedExtensions)) {
        $error = "Uploaded file should be an image.";
    }

    if (empty($error)) {
        // Renaming image name to resolve conflicts with same image names
        $imageParts = explode('.', $imageName);
        $newImageName = current($imageParts) . "_" . time() . "." . end($imageParts);

        // Moving uploaded file to imageuploads folder
        $targetDir = '../imageuploads';
        if (!file_exists($targetDir)) {
            mkdir($targetDir, FILE_PERMISSIONS);
        }
        
        move_uploaded_file($_FILES["file"]["tmp_name"], $targetDir . '/' . $newImageName);

        // Inserting image data to database table
        $insertQuery = "INSERT INTO images (image_original_name, renamed_image_name, created_at) VALUES (:originalname, :duplicatename, NOW())";
        $arrParams = array(
            ':originalname' => $imageName,
            ':duplicatename' => $newImageName
        );
        $result = $objDatabase->insert($insertQuery, $arrParams);
        if ($result instanceof Exception) {
            $response = array(
                'error' => "There was an error occurred while inserting uploaded image into database.",
                'success' => false
            );
        } else {
            $response = array(
                'images' => array(
                    'path' => IMAGE_DIR_PATH . $newImageName
                ),
                'success' => true
            );
        }
    } else {
        $response = array(
            'success' => false,
            'error' => $error
        );
    }
}

// Sending response back to client
echo json_encode($response);
