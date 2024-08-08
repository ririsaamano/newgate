<?php
include "includes/_process_include.php";
include "includes/_header.php";
include "includes/_login_check.php";

$galleryObj = new Gallery();

// Get search query
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
$result = $galleryObj->getAllGallery($searchQuery);
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Galleries</h1>
        <!-- Search Field -->
        <div class="input-group">
            <input type="text" id="searchGallery" class="form-control" placeholder="Search Galleries" value="<?= htmlspecialchars($searchQuery) ?>">
            <span class="input-group-btn">
                <button class="btn btn-default" type="button" id="searchGalleryBtn">Search</button>
            </span>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <?php 
        if ($_SESSION['type'] == "admin") {
        ?>
        <div class="row">  
            <div class="col-lg-4">
                <div class="input-group">
                    <input type="text" id="galleryName" class="form-control" placeholder="Add Gallery">
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="button" id="addGallerybtn">Add</button>
                    </span>
                    <div id="loader"></div>
                </div><!-- /input-group -->
            </div><!-- /.col-lg-6 -->
        </div><!-- /.row -->
        <?php } ?>
    </div>
</div>

<!-- Gallery List -->
<div id="galleriesList" class="row">
<?php
    if (!empty($result)) {
        foreach ($result as $gallery) {
            $latestImageUrl = $galleryObj->getLatestImageUrl($gallery);
?>
            <div class="col-lg-3 col-md-4 col-xs-6 thumb">
                <?php if ($_SESSION['type'] == "admin") { ?>
                <!-- Dropdown action -->
                <div class="btn-group">
                    <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="#" data-name="<?= $gallery ?>" class="editGallery" data-toggle="modal" data-target="#editGalleryModal">Edit Name</a></li>
                        <li><a href="#" data-gallery="<?= $gallery ?>" class="deleteGallery">Delete Gallery</a></li>
                    </ul>
                </div> <!-- End Dropdown action -->
                <?php } ?>
                <a class="thumbnail" href="<?= Photos_Page_Link ?>?gallery=<?= $gallery ?>">
                    <img class="img-responsive" src="<?= $latestImageUrl ?>" alt="">
                    <p style="font-size: 20px; text-align: center;"><?= $gallery ?></p>
                </a>
            </div>
<?php
        }
    } else {
        echo '<div class="col-lg-3"><p class="alert alert-info">No Galleries Found</p></div>';
    }
?>
</div> <!-- End row -->

<!-- Edit Gallery Modal -->
<div id="editGalleryModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Edit Gallery Name</h4>
      </div>
      <div class="modal-body">
        <div class="row">  
            <div class="col-lg-8">
                <div class="input-group">
                    <input type="text" id="newGalleryName" class="form-control" placeholder="New Gallery Name">
                    <input type="hidden" id="currentGalleryName">
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="button" id="editGalleryBtn">Edit</button>
                    </span>
                    <div id="loader"></div>
                </div><!-- /input-group -->
            </div><!-- /.col-lg-6 -->
        </div><!-- /.row -->
      </div>      
    </div>
  </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmationModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Confirm Deletion</h4>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this gallery?</p>
        <input type="hidden" id="galleryToDelete">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
      </div>
    </div>
  </div>
</div>

<?php
include "includes/_footer.php";
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchField = document.getElementById('searchGallery');
    const searchButton = document.getElementById('searchGalleryBtn');

    searchButton.addEventListener('click', function() {
        const query = searchField.value.trim();
        if (query) {
            window.location.href = `?search=${encodeURIComponent(query)}`;
        } else {
            window.location.href = '?'; // Reload without search query
        }
    });

    searchField.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchButton.click();
        }
    });

    // Handle edit gallery button click
    document.querySelectorAll('.editGallery').forEach(button => {
        button.addEventListener('click', function() {
            const galleryName = this.getAttribute('data-name');
            document.getElementById('currentGalleryName').value = galleryName;
        });
    });

    document.getElementById('editGalleryBtn').addEventListener('click', function() {
        const newName = document.getElementById('newGalleryName').value.trim();
        const currentName = document.getElementById('currentGalleryName').value.trim();
        
        if (newName && currentName && newName !== currentName) {
            fetch('edit_gallery.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'edit',
                    currentName: currentName,
                    newName: newName
                })
            }).then(response => response.text())
              .then(result => {
                  if (result === 'success') {
                      location.reload();
                  } else {
                      alert('Failed to edit gallery name.');
                  }
              });
        } else {
            alert('Please enter a new name or ensure it is different from the current name.');
        }
    });

    // Handle delete gallery button click
    document.querySelectorAll('.deleteGallery').forEach(button => {
        button.addEventListener('click', function() {
            const galleryName = this.getAttribute('data-gallery');
            document.getElementById('galleryToDelete').value = galleryName;
            $('#confirmationModal').modal('show');
        });
    });

    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        const galleryName = document.getElementById('galleryToDelete').value;
        
        fetch('edit_gallery.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'delete',
                galleryName: galleryName
            })
        }).then(response => response.text())
          .then(result => {
              if (result === 'success') {
                  location.reload();
              } else {
                  alert('Failed to delete gallery.');
              }
          });
    });
});
</script>
