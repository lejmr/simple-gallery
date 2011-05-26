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
//   option('images_dir', $root_dir.'/images/');
  option('images_dir', '/media/Archiv/Fotky/');
  option('tmp_dir', $root_dir.'/tmp/');
  option('public_dir', $root_dir.'/public/');

}




function before()
{
  layout('default_layout.php');        
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


function handle_dir($path){
  $dest = file_path( option("images_dir"), $path );
  
  
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


dispatch('/**/thumb', 'handle_thumbnail');
function handle_thumbnail()
{
  $path = params(0);
  $dest = file_path( option("images_dir"), $path );
  $dest_cache = file_path( option("cache_dir"),'thumbs', $path );
  
  if( file_exists($dest) and !file_exists($dest_cache) ){
    
    //     Make a thumbnail
    try{
      $thumb = PhpThumbFactory::create($dest);
      $thumb->adaptiveResize(100, 100);
      if(! is_dir(dirname($dest_cache)) ) mkdir(dirname($dest_cache), 0777, true);
      $thumb->save($dest_cache);
    }
    catch (Exception $e)
    {
     // handle error here however you'd like
     $dest_tmp = file_path( option("root_dir"), "public", "img", "default.png" );
     $thumb = PhpThumbFactory::create($dest_tmp);
     $thumb->adaptiveResize(100, 100);
     $thumb->show();
    }   
  }
  
  if( !file_exists($dest) and file_exists($dest_cache) ){
    return "neco special";
  }
  
  
  //   Return thumbnail of a picture
  if( file_exists($dest) and file_exists($dest_cache) ){  
    $content_type = mime_type(file_extension($dest_cache));
    $header = 'Content-type: '.$content_type;
    if(file_is_text($dest_cache)) $header .= '; charset='.strtolower(option('encoding'));
    if(!headers_sent()) header($header);
    return file_read($dest_cache);
  }
  
  
  halt(NOT_FOUND, "Takovýto soubor neexistuje.");
}
  
dispatch('/**/preview', 'handle_preview');
function handle_preview()
{
  $path = params(0);
  $dest = file_path( option("images_dir"), $path );
  $dest_cache = file_path( option("cache_dir"),'preview', $path );
  
  if( file_exists($dest) and !file_exists($dest_cache) ){
    
    //     Make a thumbnail
    try{
      $thumb = PhpThumbFactory::create($dest);
      $thumb->Resize(800, 600);
      if(! is_dir(dirname($dest_cache)) ) mkdir(dirname($dest_cache), 0777, true);
      $thumb->save($dest_cache);
    }
    catch (Exception $e)
    {
     // handle error here however you'd like
     $dest_tmp = file_path( option("root_dir"), "public", "img", "default.png" );
     $thumb = PhpThumbFactory::create($dest_tmp);
     $thumb->adaptiveResize(100, 100);
     $thumb->show();
    }   
  }
  
  if( !file_exists($dest) and file_exists($dest_cache) ){
    return "neco special";
  }
  
  
  //   Return thumbnail of a picture
  if( file_exists($dest) and file_exists($dest_cache) ){  
    $content_type = mime_type(file_extension($dest_cache));
    $header = 'Content-type: '.$content_type;
    if(file_is_text($dest_cache)) $header .= '; charset='.strtolower(option('encoding'));
    if(!headers_sent()) header($header);
    return file_read($dest_cache);
  }
  
  
  halt(NOT_FOUND, "Takovýto soubor neexistuje.");
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