<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Limonade, the fizzy PHP micro-framework</title>
	<link rel="stylesheet" href="<?php echo url_for('/_lim_css/screen.css');?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?php echo url_for('/_public_css/lightbox.css');?>" type="text/css" media="screen">
	
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
    <b>Path: <a href="/">ROOT</a>
      <? $last="?/";
      foreach( explode("/",$path) as $item ){ ?>
	<a href="<?= file_path($last,$item) ?>"><? echo $item?></a>
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
	
	<? foreach( $files as $item){ ?>
	  <a href="?<?= file_path($path,$item) ?>/preview" rel="lightbox[roadtrip]">
	    <img src="?<?= file_path($path,$item) ?>/thumb" alt="<?=$item?>" width="100" class="<?$type=explode("/",mime_type(file_extension($item)));echo $type[0]?>"/>
	  </a>
	<?}?>
	
      </div>
      
      
      <hr class="space">
    </div>
  </div>

</body>
</html>
