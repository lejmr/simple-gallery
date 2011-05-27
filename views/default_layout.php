<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Foto.lejmr.Com<? if(!empty($title)) echo " | $title";?></title>
	<link rel="stylesheet" href="<?php echo url_for('/_lim_css/screen.css');?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?php echo url_for('/_public_css/lightbox.css');?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?php echo url_for('/_public_css/content.css');?>" type="text/css" media="screen">
	
	<script type="text/javascript" src="/public/js/prototype.js"></script>
	<script type="text/javascript" src="/public/js/scriptaculous.js?load=effects,builder"></script>
	<script type="text/javascript" src="/public/js/lightbox.js"></script>

</head>
<body>
  <div id="header">
    <h1>Limonade</h1>
  </div>
  
  <div id="content">
    <?php echo error_notices_render(); ?>
    
    <!-- Path Line -->
    <b>Path: <a href="/">foto.lejmr.com</a>
      <? $last="?/";
      foreach( explode("/",$path) as $item ){ ?>
	<a href="<?= file_path($last,$item) ?>"><? echo $item?></a>/
	<? $last=file_path($last,$item);
      } ?>    
    </b>
    <!-- / Path Line -->


    <div id="main">
    
      <?php  $content;?>
      
      
      <div class="folders">
	<ul>
	<? foreach( $dirs as $item){ ?>
	  <li><a href="?/<?= file_path($path,$item) ?>"><?=$item?></a></li>
	<?}?>
	</ul>
      </div>
      
      
      
      <div class="images_files">
	
	<? foreach( $files as $item){ 
	  $type=explode("/",mime_type(file_extension($item)));
	  $image= $type[0]!="video"; ?>
	  
	  <a href="?<? echo file_path($path,$item); 
			if($image) echo "/preview";
		?>" <? if($image) { ?> rel="lightbox[roadtrip]" <? } ?> title="<?=$item?> <a href='?<?=file_path($path,$item)?>'>Plné rozlišení</a>">
	      <img src="?<?= file_path($path,$item) ?>/thumb" alt="<?=$item?>" class="<?=$type[0]?>" />
	  </a>
	<?}?>
	
      </div>
      
      
      <hr class="space">
    </div>
  </div>

</body>
</html>
