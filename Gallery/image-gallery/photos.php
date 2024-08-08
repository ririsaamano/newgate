<?php
include "includes/_process_include.php";
include "includes/_header.php";
include "includes/_login_check.php";

// Get and sanitize the gallery name
$gallery = htmlspecialchars(trim($_GET['gallery']));

// Create an instance of the Photos class
$photosObj = new Photos();
$result    = $photosObj->getAllGalleryPhotos($gallery);

// Handle delete request
if (isset($_POST['delete'])) {
    $photoToDelete = htmlspecialchars(trim($_POST['photo']));
    $galleryName = htmlspecialchars(trim($_POST['gallery']));
    $photosObj->deleteGalleryPhotos($galleryName, $photoToDelete);
    // Redirect to reflect changes
    header("Location: ".$_SERVER['PHP_SELF']."?gallery=".urlencode($galleryName));
    exit;
}

// Handle download request
if (isset($_GET['download'])) {
    $photoToDownload = htmlspecialchars(trim($_GET['download']));
    $filePath = Gallery_Folder . $gallery . "/" . $photoToDownload;
    
    if (file_exists($filePath)) {
        // Determine MIME type
        $mimeType = mime_content_type($filePath);
        
        // If MIME type cannot be determined, set to a default
        if (!$mimeType) {
            $mimeType = 'application/octet-stream';
        }
        
        // Clean the output buffer
        if (ob_get_length()) {
            ob_clean();
        }
        
        // Set headers
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: attachment; filename="'.basename($filePath).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        
        // Read the file
        readfile($filePath);
        exit;
    } else {
        echo 'File does not exist.';
        exit;
    }
}
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Photos</h1>
    </div>

    <?php 
    if ($_SESSION['type'] == "admin") {
    ?>
    <form action="image_upload.php" method="post" enctype="multipart/form-data"> 
    <div><strong>Upload a Photo or Image</strong></div>   
    <div class="row">  
        <div class="col-lg-6">
            <div class="input-group">            
                <input type="file" name="image" class="form-control" placeholder="Upload photo" accept="image/jpeg, image/png, image/gif">
                <input type="hidden" name="galleryName" value="<?= $gallery ?>">
                <span class="input-group-btn">                   
                    <button class="btn btn-default" type="submit">Upload</button>
                </span>
            </div><!-- /input-group -->
        </div><!-- /.col-lg-6 -->
    </div><!-- /.row -->
    </form>
    <?php } ?>
    <hr >

    <?php
        if (!empty($result)) {
            foreach ($result as $photo) {
    ?>
            <div class="col-lg-3 col-md-4 col-xs-6 thumb">
                <a class="thumbnail" href="javascript:void(0)" id="popImage" data-imgsrc="<?=Gallery_Folder.$gallery."/".$photo?>">
                    <img class="img-responsive" src="<?=Gallery_Folder.$gallery."/".$photo?>" alt="">
                </a>
                <?php if ($_SESSION['type'] == "admin") { ?>
                <form action="" method="post" style="display:inline;">
                    <input type="hidden" name="photo" value="<?= htmlspecialchars($photo) ?>">
                    <input type="hidden" name="gallery" value="<?= htmlspecialchars($gallery) ?>">
                    <button type="submit" name="delete" class="btn btn-danger btn-xs">Delete Photo</button>
                </form>
                <?php } ?>
                <a href="?gallery=<?= urlencode($gallery) ?>&download=<?= urlencode($photo) ?>" class="btn btn-primary btn-xs">Download Image</a>
            </div>
    <?php
            }
        } else {
            echo '<div class="col-lg-3"><p class="alert alert-info">No Photos Found</p></div>';
        }
    ?>
</div> <!-- End row -->

<div id="imagemodal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">      
      <div class="modal-body">
        <img src="" id="imagepreview" class="img-responsive" >
      </div>      
    </div>
  </div>
</div>

<?php
include "includes/_footer.php";
?>
