<?php
require_once 'vendors/limonade/lib/limonade.php';
require_once 'vendors/phpthumb/src/ThumbLib.inc.php';
// echo $root_dir;

/* Konfigurace */
function configure()
{

  $root_dir="/home/milos/Projekty/foto/htdocs";

  option('debug',              true);     
  option('cache_dir', $root_dir.'/cache/');
  option('images_dir', $root_dir.'/images/');
//   option('images_dir', '/media/Archiv/Fotky/');
  option('tmp_dir', $root_dir.'/tmp/');
  option('public_dir', $root_dir.'/public/');

}




function before()
{
  layout('default_layout.php');       
  set("title","");
}


dispatch(array("/_public_css/*.css", array('_lim_css_filename')), 'render_public_css');
function render_public_css()
  {
    option('views_dir', file_path(option('public_dir'), 'css'));
    $fpath = file_path(params('_lim_css_filename').".css");
    return css($fpath, null); // with no layout
  }


dispatch(array("/_public/**", array('_lim_public_file')), 'render_public_file');
  /**
   * Internal controller that responds to route /_lim_public/**
   *
   * @access private
   * @return void
   */
  function render_public_file()
  {echo "milos";
    $fpath = file_path(option('public_dir'), params('_lim_public_file'));
    return render_file($fpath, true);
  }




  /**
   * Internal controller that handles all HTTP requests, decides whether 
   * handle a dir or show a picture, thumbnail or fullsize iamge.
   *
   * @access public
   * @return void
   */
function handle_dir($path){
  $dest = file_path( option("images_dir"), $path );
  
  $parhArray= explode("/",$path);
  set("title", $parhArray[count($parhArray)-1]);
  
  $dirs= array();
  $files= array();
  foreach( file_list_dir($dest) as $item  ){
      $tmp_dest= file_path($dest, $item );
      
      if( is_dir( $tmp_dest ) ){
	  $dirs[ count($dirs)+1 ]= $item;
      }
      
      if( is_file( $tmp_dest ) ){
	  $files[ count($files)+1 ]= $item;
      }
  }
  
  set("dirs", $dirs );
  set("files", $files );
  
  return html($dest);
}



function handle_file($path){
  $dest = file_path( option("images_dir"), $path );
  if( ! file_exists($dest) )  halt(NOT_FOUND, "Takovýto soubor neexistuje.");
  
  $content_type = mime_type(file_extension($dest));
  $header = 'Content-type: '.$content_type;
  if(file_is_text($dest)) $header .= '; charset='.strtolower(option('encoding'));
  if(!headers_sent()) header($header);
  
  return file_read($dest);
}



function handle_thumbnail_preview($w,$h,$dir,$adaptive=True)
{
  $path = params(0);
  $dest = file_path( option("images_dir"), $path );
  $dest_cache = file_path( option("cache_dir"),$dir, $path );
  
  if( file_exists($dest) and !file_exists($dest_cache) ){
    
    //     Make a thumbnail
    try{
      $thumb = PhpThumbFactory::create($dest);
      $exif = exif_read_data($dest);
     
     
     
      $ort = $exif['Orientation'];
      switch($ort)
      {
	  case 2: // horizontal flip
  //             $thumb->flipImage($public,1);
	  break;
				
	  case 3: // 180 rotate left
	    $thumb->rotateImageNDegrees(180);
	  break;
		    
	  case 4: // vertical flip
  //             $thumb->flipImage(2);
	  break;
		
	  case 5: // vertical flip + 90 rotate right
  //             $thumb->flipImage($public, 2);
	      $thumb->rotateImageNDegrees(-90);
	  break;
		
	  case 6: // 90 rotate right
	      $thumb->rotateImageNDegrees(-90);
	  break;
		
	  case 7: // horizontal flip + 90 rotate right
  //             $thumb->flipImage($public,1);   
	      $thumb->rotateImageNDegrees(-90);
	  break;
		
	  case 8:    // 90 rotate left
	      $thumb->rotateImageNDegrees(90);
	  break;
      }
      
      
      ($adaptive)?$thumb->adaptiveResize($w, $h):$thumb->Resize($w, $h);
      if(! is_dir(dirname($dest_cache)) ) mkdir(dirname($dest_cache), 0777, true);
      $thumb->save($dest_cache);
    }
    catch (Exception $e)
    {
     // handle error here however you'd like
     $dest_tmp = file_path( option("root_dir"), "public", "img", "default.png" );
     $thumb = PhpThumbFactory::create($dest_tmp);
     ($adaptive)?$thumb->adaptiveResize($w, $h):$thumb->Resize($w, $h);
     $thumb->show();
    }   
  }
  
  if( !file_exists($dest) and file_exists($dest_cache) ){
    return "neco special";
  }
  
  
  //   Return thumbnail of a picture
  if( file_exists($dest) and file_exists($dest_cache) ){  
//     $content_type = mime_type(file_extension($dest_cache));
//     $header = 'Content-type: '.$content_type;
//     if(file_is_text($dest_cache)) $header .= '; charset='.strtolower(option('encoding'));
//     if(!headers_sent()) header($header);
    $thumb = PhpThumbFactory::create($dest_cache);
    $thumb->show();
//     return file_read($dest_cache);
  }
  
  
  halt(NOT_FOUND, "Takovýto soubor neexistuje.");
}

dispatch('/**/thumb', 'handle_thumbnail');
function handle_thumbnail()
{
  return handle_thumbnail_preview(100,100,'thumbs');
}

dispatch('/**/preview', 'handle_preview');
function handle_preview()
{
  return handle_thumbnail_preview(800,600,'preview',false);
}
  
  
dispatch('/**', 'my_dir');
function my_dir()
{
  # Matches /writing/an_email/to/joe
  $path = params(0);
  $dest = file_path( option("images_dir"), $path );
  
  set('path', $path);  
  return (is_dir($dest))?handle_dir($path):handle_file($path);

}  

 
run();
?>