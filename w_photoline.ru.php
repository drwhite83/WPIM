<?php $wps_script_time_start = microtime(true); $wps_script_time_show = true;
include_once $_SERVER['DOCUMENT_ROOT'].'/w_inc.php'; include_once 'wl.php';
include_once 'wlURL.php'; include_once 'wlDir.php'; include_once 'wlFile.php'; 
include_once 'wEnvParam.php'; 
include_once 'UI/wlUI.php'; include_once 'UI/wQSLinksList.php'; 
include_once 'app/php/wlPhotoline.php'; 
//include_once 'app/php/wlPhotolineDB.php';
$pages_count = 99999;
$GET_page_num = new wEnvParam(['name'=>'page_num','null_value'=>1]);
$page_url = wlPhotoline::getPageUrl($GET_page_num->value);
$Paginator = new wPaginator(['name'=>'page_num', 'c_page'=>$GET_page_num->value, 'max'=>$pages_count]); 
?>
<!DOCTYPE html>
<html><head><title><?php echo wlURL::fileName(); ?></title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width">
<link rel="shortcut icon" href="/favicon.ico" />
<link rel="stylesheet" href="/w/css/w_main.css?1010">
<link rel="stylesheet" href="app/css/main.css?1030">
<script type="text/javascript" src="/w/js/w/w.js?1010"></script>
<style type="text/css"></style>
</head><body>
<div id="content_block_over">

<div id="top_block">
<a id="logo_home_link" href="/"><img id="logo_stamp" src="/w/img/favicon_w1_x64_stamp.png" /></a> 
<h1 id="page_title_w"><?php echo wl::TitleFromFilename(); ?> <a target="_blank" href="<?=$page_url;?>">новые фото</a> <br> <?=$Paginator->getPages();?></h1>
</div>

<div id="photos"><?php
try {
$Photos_list = wlPhotoline::createPhotosObjsFromURL($page_url);
$prepared_show_photos_list = wlPhotoline::prepareShowPhotosObjs($Photos_list);
//$tableFromArray_params = ['array'=>$prepared_show_photos_list, 'table_tag_attrs'=>'id="main_show_table"', 'columns'=>4];
//echo wlUI::htmlTableFromArrayByColsNum($tableFromArray_params);
foreach ($prepared_show_photos_list as $photo_block) {echo $photo_block."\n";}
}
catch(Exception $E) {	wl::D($E);}?>
</div>

</div> <!-- content_block_over -->

<script type="text/javascript">
//пока так
<?php include_once('app/js/w_photoline.js');?>
try { wPhotoline(); }
catch (error) { l(error); }
</script>
<?php $wps_script_time = '<pre id="w_script_time">Script time: '.sprintf('%0.3f', (microtime(true)-$wps_script_time_start)).'</pre>';
echo $wps_script_time_show ? $wps_script_time : ''; ?>
</body>
</html>
