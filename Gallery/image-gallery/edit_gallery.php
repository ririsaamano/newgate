<?php
include "includes/_process_include.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $galleryObj = new Gallery();

    switch ($action) {
        case 'edit':
            $currentName = $_POST['currentName'] ?? '';
            $newName = $_POST['newName'] ?? '';
            if ($galleryObj->editGalleryName($currentName, $newName)) {
                echo 'success';
            } else {
                echo 'error';
            }
            break;

        case 'delete':
            $galleryName = $_POST['galleryName'] ?? '';
            if ($galleryObj->deleteGallery($galleryName)) {
                echo 'success';
            } else {
                echo 'error';
            }
            break;

        default:
            echo 'invalid_action';
            break;
    }
}
