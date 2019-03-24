<?php
 
 /* source: https://stackoverflow.com/questions/1334613/zip-a-directory-in-php/1334949#1334949 */
 function create_zip_archive($source, $destination)
 {
   if (!extension_loaded('zip')) {
     die('No ZIP extension. Change PHP configuration.');
   }

   if (!file_exists($source)) {
     die('No source file. Abandon.');
   }

   $zip = new ZipArchive();
   if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
     die('Error. Can\'t open ZIP file.');
   }

   $source = str_replace('\\', '/', realpath($source));
   if (is_dir($source) === true) {
     $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
     foreach ($files as $file) {
       $file = str_replace('\\', '/', $file);
       if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) ) {
         continue;
       }

       $file = realpath($file);
       if (is_dir($file) === true) {
         $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
       } else if (is_file($file) === true) {
         $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
       }
     }
   } else if (is_file($source) === true) {
     $zip->addFromString(basename($source), file_get_contents($source));
   }
   return $zip->close();
 }
 
 $pass = $_POST['pass'];
 if (md5($pass) != '827ccb0eea8a706c4c34a16891f84e7b') {
   die('Error. Wrong pass'); 
 }
 
 $compressed_directory = $_POST['dirname'];
 $zip_filename = $_POST['filename'];
 create_zip_archive($compressed_directory, $zip_filename); 

 header("Content-Disposition: attachment; filename=\"".$zip_filename."\"");
 header("Content-Transfer-Encoding: binary");
 header("Content-type: application/octet-stream");
 header("Content-Length: ".filesize($zip_filename));
 readfile($zip_filename);
 
?>
