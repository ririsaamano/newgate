<?php

class Gallery {

    public function __construct() {}

public function getAllGallery($searchQuery = '') {
    $final = [];
    $galleries = glob(Gallery_Folder . "*");

    foreach ($galleries as $gallery) {
        if ($gallery === '.' or $gallery === '..') continue;

        if (is_dir($gallery)) {
            $galleryName = explode("/", $gallery)[1];
            if ($searchQuery === '' || stripos($galleryName, $searchQuery) !== false) {
                $final[] = $galleryName;
            }
        }
    }

    return $final;
}



    public function getLatestImageUrl($galleryName) {
        $galleryPath = Gallery_Folder . $galleryName;
        $latestImage = null;
        $latestTime = 0;

        if (is_dir($galleryPath)) {
            foreach (glob($galleryPath . "/*.{jpg,jpeg,png,gif}", GLOB_BRACE) as $file) {
                $fileTime = filemtime($file);
                if ($fileTime > $latestTime) {
                    $latestTime = $fileTime;
                    $latestImage = $file;
                }
            }
        }

        return $latestImage ? str_replace($_SERVER['DOCUMENT_ROOT'], '', $latestImage) : 'path/to/default-thumbnail.jpg';
    }

    /**
    * Create folder using the name argument
    * @param
    *   $name (string)
    *
    * @return 
    *   Boolean
    **/
    public function addGallery($name) {
        if(!file_exists(Gallery_Folder . $name)) {
            if(mkdir(Gallery_Folder . $name)){
                return true;
            }
        }
        return false;
    }

    /**
    * Delete folder using the name argument
    * @param
    *   $name (string)
    *
    * @return 
    *   Boolean
    **/
    public function deleteGallery($name) {
        $dir = Gallery_Folder . $name;

        if(file_exists($dir)) {
            foreach(scandir($dir) as $file) {
                if ('.' === $file || '..' === $file) continue;
                if (is_dir("$dir/$file")) $this->rmdir_recursive("$dir/$file");
                else unlink("$dir/$file");
            }
            
            if(rmdir($dir))
                return true;
        }
        return false;
    }

    /**
    * Rename a gallery folder name
    * @param
    *   $currentName (string)
    *   $newName (string)
    *
    * @return 
    *   Boolean
    **/
    public function editGalleryName($currentName, $newName){
        if(file_exists(Gallery_Folder . $currentName)) {
            if(rename(Gallery_Folder . $currentName, Gallery_Folder . $newName)){
                return true;
            }
        }
        return false;
    }

    /**
    * Recursively delete a directory
    * @param
    *   $dir (string)
    *
    * @return
    *   Void
    **/
    private function rmdir_recursive($dir) {
        foreach (scandir($dir) as $file) {
            if ('.' === $file || '..' === $file) continue;
            $path = "$dir/$file";
            if (is_dir($path)) $this->rmdir_recursive($path);
            else unlink($path);
        }
        rmdir($dir);
    }

    public function show($data) {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
        exit;
    }
} // End Gallery Class
?>
