<?php 
//Инструкция по ссылкам. 
//Создаём папку в том месте где должна быть ссылка.
// Создаём там же символическую ссылку на эту папку с помощью программы link shell extension.
// Заходим в свойства и меняем цель на сетевой путь. Всё.
$er=''; $dr=''; $drr=''; $inf=''; $pl='';
$pfil=''; $tpfil='';
if (isset ($_GET['file'])) {$er=$_GET['file'];}
if (isset ($_GET['dir'])) {$dr=$_GET['dir'];}
if (isset ($_GET['dirr'])) {$drr=$_GET['dirr'];}
if (isset ($_GET['info'])) {$inf=$_GET['info'];}
if (isset ($_POST['playlist'])) {$pl=$_POST['playlist'];}
if (isset ($_POST['files'])) {$pfil = $_POST['files'];}
if (isset ($_POST['tfiles'])) {$tpfil = $_POST['tfiles'];}
if (mb_detect_encoding ($er, null, true) !='UTF-8') {$er = iconv('cp1251', 'utf-8', $er);}
if (mb_detect_encoding ($dr, null, true) !='UTF-8') {$dr = iconv('cp1251', 'utf-8', $dr);}
if (mb_detect_encoding ($drr, null, true) !='UTF-8') {$drr = iconv('cp1251', 'utf-8', $drr);}
if ($pl!='') {
$pl = iconv('utf-8', 'cp1251', $pl);
$fsize=strlen ($pl);
$fname="playlist.m3u";
header("Content-Length: $fsize");
header("Content-Disposition: filename=\"$fname\"");
header("Content-Type: application/file");
echo $pl;} elseif ($tpfil!='') {
$fill=explode ('<|p>', $tpfil);
$fill = array_diff($fill, array(''));
$fill = str_replace ('R493', "'", $fill);
$filesiz=0;
foreach ($fill as $fill1)
{
$filesiz = $filesiz+ filesize('/OpenServer/domains/'.$_SERVER['HTTP_HOST'].iconv('utf-8', 'cp1251', $fill1));
if ($filesiz>300000000) {echo '0'; break;}
}
if ($filesiz<300000000) {echo '1';}
} elseif ($pfil!='') {
class createZip  {

    public $compressedData = array();
    public $centralDirectory = array(); // central directory
    public $endOfCentralDirectory = "\x50\x4b\x05\x06\x00\x00\x00\x00"; //end of Central directory record
    public $oldOffset = 0;
	
    public function addDirectory($directoryName) {
        $directoryName = str_replace("\\", "/", $directoryName);

        $feedArrayRow = "\x50\x4b\x03\x04";
        $feedArrayRow .= "\x0a\x00";
        $feedArrayRow .= "\x00\x00";
        $feedArrayRow .= "\x00\x00";
        $feedArrayRow .= "\x00\x00\x00\x00";

        $feedArrayRow .= pack("V",0);
        $feedArrayRow .= pack("V",0);
        $feedArrayRow .= pack("V",0);
        $feedArrayRow .= pack("v", strlen($directoryName) );
        $feedArrayRow .= pack("v", 0 );
        $feedArrayRow .= $directoryName;

        $feedArrayRow .= pack("V",0);
        $feedArrayRow .= pack("V",0);
        $feedArrayRow .= pack("V",0);

        $this -> compressedData[] = $feedArrayRow;

        $newOffset = strlen(implode("", $this->compressedData));

        $addCentralRecord = "\x50\x4b\x01\x02";
        $addCentralRecord .="\x00\x00";
        $addCentralRecord .="\x0a\x00";
        $addCentralRecord .="\x00\x00";
        $addCentralRecord .="\x00\x00";
        $addCentralRecord .="\x00\x00\x00\x00";
        $addCentralRecord .= pack("V",0);
        $addCentralRecord .= pack("V",0);
        $addCentralRecord .= pack("V",0);
        $addCentralRecord .= pack("v", strlen($directoryName) );
        $addCentralRecord .= pack("v", 0 );
        $addCentralRecord .= pack("v", 0 );
        $addCentralRecord .= pack("v", 0 );
        $addCentralRecord .= pack("v", 0 );
        $ext = "\x00\x00\x10\x00";
        $ext = "\xff\xff\xff\xff";
        $addCentralRecord .= pack("V", 16 );

        $addCentralRecord .= pack("V", $this -> oldOffset );
        $this -> oldOffset = $newOffset;

        $addCentralRecord .= $directoryName;

        $this -> centralDirectory[] = $addCentralRecord;
    }

    /**
     * Function to add file(s) to the specified directory in the archive
     *
     * @param $directoryName string
     *
     */

    public function addFile($data, $directoryName)   {

        $directoryName = str_replace("\\", "/", $directoryName);

        $feedArrayRow = "\x50\x4b\x03\x04";
        $feedArrayRow .= "\x14\x00";
        $feedArrayRow .= "\x00\x00";
        $feedArrayRow .= "\x08\x00";
        $feedArrayRow .= "\x00\x00\x00\x00";

        $uncompressedLength = strlen($data);
        $compression = crc32($data);
        $gzCompressedData = gzcompress($data);
        $gzCompressedData = substr( substr($gzCompressedData, 0, strlen($gzCompressedData) - 4), 2);
        $compressedLength = strlen($gzCompressedData);
        $feedArrayRow .= pack("V",$compression);
        $feedArrayRow .= pack("V",$compressedLength);
        $feedArrayRow .= pack("V",$uncompressedLength);
        $feedArrayRow .= pack("v", strlen($directoryName) );
        $feedArrayRow .= pack("v", 0 );
        $feedArrayRow .= $directoryName;

        $feedArrayRow .= $gzCompressedData;

        $feedArrayRow .= pack("V",$compression);
        $feedArrayRow .= pack("V",$compressedLength);
        $feedArrayRow .= pack("V",$uncompressedLength);

        $this -> compressedData[] = $feedArrayRow;

        $newOffset = strlen(implode("", $this->compressedData));

        $addCentralRecord = "\x50\x4b\x01\x02";
        $addCentralRecord .="\x00\x00";
        $addCentralRecord .="\x14\x00";
        $addCentralRecord .="\x00\x00";
        $addCentralRecord .="\x08\x00";
        $addCentralRecord .="\x00\x00\x00\x00";
        $addCentralRecord .= pack("V",$compression);
        $addCentralRecord .= pack("V",$compressedLength);
        $addCentralRecord .= pack("V",$uncompressedLength);
        $addCentralRecord .= pack("v", strlen($directoryName) );
        $addCentralRecord .= pack("v", 0 );
        $addCentralRecord .= pack("v", 0 );
        $addCentralRecord .= pack("v", 0 );
        $addCentralRecord .= pack("v", 0 );
        $addCentralRecord .= pack("V", 32 );

        $addCentralRecord .= pack("V", $this -> oldOffset );
        $this -> oldOffset = $newOffset;

        $addCentralRecord .= $directoryName;

        $this -> centralDirectory[] = $addCentralRecord;
    }

    /**
     * Fucntion to return the zip file
     *
     * @return zipfile (archive)
     */

    public function getZippedfile() {

        $data = implode("", $this -> compressedData);
        $controlDirectory = implode("", $this -> centralDirectory);

        return
            $data.
            $controlDirectory.
            $this -> endOfCentralDirectory.
            pack("v", sizeof($this -> centralDirectory)).
            pack("v", sizeof($this -> centralDirectory)).
            pack("V", strlen($controlDirectory)).
            pack("V", strlen($data)).
            "\x00\x00";
    }

    /**
     *
     * Function to force the download of the archive as soon as it is created
     *
     * @param archiveName string - name of the created archive file
     */

    public function forceDownload($archiveName) {
        $headerInfo = '';

        if(ini_get('zlib.output_compression')) {
            ini_set('zlib.output_compression', 'Off');
        }

        // Security checks
        if( $archiveName == "" ) {
            echo "<html><title>Public Photo Directory - Download </title><body><BR><B>ERROR:</B> The download file was NOT SPECIFIED.</body></html>";
            exit;
        }
        elseif ( ! file_exists( $archiveName ) ) {
            echo "<html><title>Public Photo Directory - Download </title><body><BR><B>ERROR:</B> File not found.</body></html>";
            exit;
        }

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private",false);
        header("Content-Type: application/zip");
        header("Content-Disposition: attachment; filename=".basename($archiveName).";" );
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: ".filesize($archiveName));
        readfile("$archiveName");

     }

}
$fill=explode ('<|p>', $pfil);
$fill = array_diff($fill, array(''));
$fill = str_replace ('R493', "'", $fill);
$createZip = new createZip;
$filesiz=0;
foreach ($fill as $fill1)
{
$filesiz = $filesiz+ filesize('/OpenServer/domains/'.$_SERVER['HTTP_HOST'].iconv('utf-8', 'cp1251', $fill1));
if ($filesiz>300000000) {echo '<!doctype html >
  <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru-ru" lang="ru-ru">
    <head>
      <meta http-equiv="content-type" content="text/html; charset=utf-8"></head><body>Слишком большой объём данных. Ограничение 300мб.</body></html>'; break;}
}
if ($filesiz<300000000) {
foreach ($fill as $fill1)
{
$fileContents = file_get_contents('/OpenServer/domains/'.$_SERVER['HTTP_HOST'].iconv('utf-8', 'cp1251', $fill1));
$createZip->addFile($fileContents, iconv('utf-8', 'CP866//TRANSLIT//IGNORE', substr ($fill1, strripos ($fill1, '/')+1)));
}
$fileName = 'archive.zip';
$fd = fopen ($fileName, 'wb');
$out = fwrite ($fd, $createZip->getZippedfile());
fclose ($fd);
$createZip->forceDownload($fileName);  
}} elseif ($inf!='') {
require_once('js/getid3/getid3.php');
$getID3 = new getID3;
$inf = iconv('utf-8', 'cp1251', $inf);
$fi = $getID3->analyze('/OpenServer/domains/'.$_SERVER['HTTP_HOST'].$inf);
getid3_lib::CopyTagsToComments($fi);

echo '<center>'.iconv('cp1251', 'utf-8', (iconv('utf-8', 'iso-8859-1', $fi[comments][artist][0]))).' - '.iconv('cp1251', 'utf-8', (iconv('utf-8', 'iso-8859-1', $fi[comments][title][0]))).'</center>Тип:'.$fi[audio][bitrate_mode].' '.round($fi[audio][bitrate]/1000).'к/бит '.$fi[audio][sample_rate].'кГц Каналы:'.$fi[audio][channelmode];
} else {
?>
<!doctype html >
  <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru-ru" lang="ru-ru">
    <head>
      <meta http-equiv="content-type" content="text/html; charset=utf-8">
      <meta name="keywords" content="Клипы, музыка, программы, игры, фильмы, картинки, klips, music, programs, games, pictures, films">
      <meta name="rights" content="Masters">
	  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
      <?php if ($er!='') { echo '<meta name="description" content="Контент http://'.$_SERVER['HTTP_HOST'].'/?file='.$er.'">';} else {
	  echo '<meta name="description" content="Сайт интересных файлов. Тут вы найдёте всё: фильмы, клипы, музыка, картинки, программы. Всё мультимедиа можно смотреть и слушать онлайн.">';
	  } ?>
      <title>ML Master collection</title>
      <link href="/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon">
	  <?php if ($er!='') { echo '
	  <meta property="og:title" content="http://'.$_SERVER['HTTP_HOST'].'/?file='.$er.'"/>
<meta property="og:url" content="http://'.$_SERVER['HTTP_HOST'].'/?file='.$er.'"/>
<meta property="og:image" content="http://'.$_SERVER['HTTP_HOST'].'/?file='.$er.'"/>
<meta property="og:description" content="Контент http://'.$_SERVER['HTTP_HOST'].'/?file='.$er.'"/>
<meta property="og:video" content="http://'.$_SERVER['HTTP_HOST'].'/?file='.$er.'"/>
<meta property="og:video:width" content="550"/>
<meta property="og:video:height" content="412"/>
<meta property="og:video:type" content="application/x-mplayer2"/>
	  ';} ?>
<link rel="stylesheet" href="/css/style.css" type="text/css"/>
<link rel="stylesheet" href="/css/jquery-ui.css" type="text/css"/>
<link rel="stylesheet" href="/skin/blue.monday/jplayer.blue.monday.css" type="text/css"/>
<link rel="stylesheet" href="/css/bootstrap.min.css">
<!-- Generic page styles -->
<!-- Bootstrap styles for responsive website layout, supporting different screen sizes -->
<link rel="stylesheet" href="/css/bootstrap-responsive.min.css">
<!-- Bootstrap CSS fixes for IE6 -->
<!--[if lt IE 7]>
<link rel="stylesheet" href="http://blueimp.github.io/cdn/css/bootstrap-ie6.min.css">
<![endif]-->
<!-- blueimp Gallery styles -->
<link rel="stylesheet" href="/css/blueimp-gallery.min.css">
<noscript><link rel="stylesheet" href="css/jquery.fileupload-ui-noscript.css"></noscript>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js" type="text/javascript"></script>
<script src="/js/jquery-ui.js" type="text/javascript"></script>
<script src="/js/jquery.jplayer.min.js" type="text/javascript"></script>
<script src="/js/jwplayer.js" type="text/javascript"></script>
<script src="/js/player.js" type="text/javascript"></script>
<script src="/js/auroraplayer.js" type="text/javascript"></script>
<script src="/js/aurora.js" type="text/javascript"></script>
<script src="/js/flac1.js" type="text/javascript"></script>

<script>
	var plugin2, player, waiter, elem, gh, fulls=0, stt=0;
    var url = '';
	var idd;
	var pll=-1;
	var date5 = 'Sat, 08 Mar 2114 06:46:40 GMT';
function prev (nam1) {
if ((nam1)>0) {
$("#playlist li").css ("color", "#555555"); 
$("#playlist" ).children('li').eq(nam1-1).css("color", "#F16161");
err1=document.getElementById('playlist').children[nam1-1].title;
run (err1, nam1-1);
}
}
function next (nam1) {
err=document.getElementById('playlist').getElementsByTagName('li').length;
if ($('#rand').attr('checked')=='checked') {n=Math.round(-0.5 + Math.random()*(err));} else {n=nam1+1;}
if ((((nam1+1)<err) && (stt==0)) || (($('#rand').attr('checked')=='checked') && (stt==0))) {
$("#playlist li").eq(nam1).css ("color", "#555555"); 
$("#playlist li").eq(n).css("color", "#F16161");
err1=document.getElementById('playlist').children[n].title;
run (err1, n);
}}
function run (nam, idd) {
	stt=0;
	window.idd=idd;
	nam=(nam.replace(/R493/g,"'")).toLowerCase();

	$('#prev').unbind();
	$('#prev').click(function(){
	stt=0;
	prev(idd);
	});
	$('#next').unbind();
	$('#next').click(function(){
	stt=0;
	next (idd);
	});
	

	if (nam.substr (nam.length-4, 4)==".mp3") {
	clse (1);
	$.ajax({
	type: "GET",
	url: ('/?info='+encodeURIComponent (nam)),
	cache: false,
	success: function (msg1){
	document.getElementById('titl').innerHTML=msg1;
	}});
	
	if (document.cookie.indexOf ('vol=')!=-1) {
		var volume1 =0.01*(parseInt((document.cookie.substring (document.cookie.indexOf ('vol=')+4, document.cookie.length))));} else { var volume1 = 0.5;}
		 $("#jquery_jplayer_1").jPlayer({
   ready: function () {
    $(this).jPlayer("setMedia", {
     mp3: encodeURI(nam)
    }).jPlayer("play");
   },
   solution: "flash, html",
	ended: function () {
	next (window.idd); },
		swfPath: "/js",
		supplied: "mp3",
		wmode: "window",
		volume: volume1,
		error: function () {next (window.idd);},
		volumechange: function (e) {document.cookie="vol="+parseInt(e.jPlayer.options.volume*100)+"; path=/; expires="+date5; }
  })
  } else if  ((nam.substr (nam.length-4, 4)==".flv") || (nam.substr (nam.length-4, 4)==".m4v")) {
		clse (2);
		if (document.cookie.indexOf ('vol=')!=-1) {
		var volume1 =parseInt((document.cookie.substring (document.cookie.indexOf ('vol=')+4, document.cookie.length)));} else { var volume1 = 50;}
jwplayer("mediaplayer").setup({
			flashplayer: "js/player.swf",
			file: encodeURI(nam),
			height: 414,
			width: 680,
			volume: volume1,
			'autostart': 'true',
			events: {
   onComplete: function() {
   next (window.idd);
   },
 onError: function() {
 next (window.idd);
 },
 onVolume: function() {
 document.cookie="vol="+parseInt(jwplayer().getVolume())+"; path=/; expires="+date5;
 }
  }
		});
		
		} else 
		if  ((nam.substr (nam.length-4, 4)==".jpg") || (nam.substr (nam.length-4, 4)==".gif")) {
		clse (3);
		document.getElementById('img1').style.display = "none";
		document.getElementById('img2').style.display = "block";

		document.getElementById('img1').src=encodeURI(nam);
		
		} else
		if  (nam.substr (nam.length-4, 4)=="flac") {
		clse (4);
		if (navigator.userAgent.toLowerCase().match('opera') || (navigator.userAgent.toLowerCase().match('trident'))) {next (window.idd);} else {
		if (player)
            {player.disconnect(); $(player).remove();}
		$('#player9').show();
 		(function(DGPlayer){
		if (document.cookie.indexOf ('vol=')!=-1) {
		DGPlayer.volume =parseInt((document.cookie.substring (document.cookie.indexOf ('vol=')+4, document.cookie.length)));} else { DGPlayer.volume = 50;}
		DGPlayer.songTitle='';
		DGPlayer.songArtist='';
		DGPlayer.coverArt = '/img/fallback_album_art.png';
		DGPlayer.duration=0;
		DGPlayer.bufferProgress=0;
		DGPlayer.seekTime=0;
		player = new DGAuroraPlayer(AV.Player.fromURL('http://'+document.domain+'/'+encodeURI(nam)), DGPlayer);
		player.player.on ('end', function() {
		player.disconnect(); $(player).remove(); next (window.idd);});
		player.player.on ('error', function(e) {player.disconnect(); $(player).remove(); next (window.idd);});
}(DGPlayer(document.getElementById('dgplayer'))));
		player.ui.on('volume', function() {document.cookie="vol="+parseInt(player.player.volume)+"; path=/; expires="+date5;});
		}}
		else
		if  ((nam.substr (nam.length-4, 4)==".wmv") || (nam.substr (nam.length-4, 4)==".mpg") || (nam.substr (nam.length-4, 4)==".avi") || (nam.substr (nam.length-4, 4)==".mp4") || (nam.substr (nam.length-4, 4)==".mkv")) {
		clse (5);
		if (navigator.userAgent.toLowerCase().match('chrome')&&(!navigator.userAgent.toLowerCase().match('opr'))) {next (window.idd);} else {
		if ($('#player11').text().length<20) {
		if (document.cookie.indexOf ('vol=')!=-1) {
		var volum1 =parseInt(document.cookie.substring (document.cookie.indexOf ('vol=')+4, document.cookie.length)); } else {var volum1= 50;}
		var plm='<object id="mediaPlayerwm" width="679" height="409" CLASSID="CLSID:6BF52A52-394A-11d3-B153-00C04F79FAA6" codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701" standby="Loading Microsoft Windows Media Player components..." type="video/x-ms-wmp"> <param name="animationatStart" value="true"/><param name="stretchToFit" value="true"/><param name="autoStart" value="true"/> <param name="showControls" value="true"/> <param name="loop" value="false"/> <param name="EnableFullScreenControls" value="1"/> <param name="SendPlayStateChangeEvents" value="true" /> <embed type="application/x-ms-wmp" pluginspage="http://microsoft.com/windows/mediaplayer/en/download/" id="mediaPlayerwm5" name="mediaPlayerwm5" displaysize="4" stretchToFit="true" autosize="1" bgcolor="darkblue" showcontrols="true" showtracker="-1" showdisplay="1" showstatusbar="1" videoborder3d="1" volume="'+volum1+'" width="679" height="409" autostart="true" designtimesp="5311" loop="false" sendplaystatechangeevents="true"> </embed> </object>';
		$('#player11').html (plm);
		if (($('#player11').is(':visible'))==false) {$('#player11').show();}
		
		if ((navigator.userAgent.toLowerCase().match('trident')) && (!(navigator.userAgent.toLowerCase().match('trident/7')))) {
		document.getElementById('mediaPlayerwm').attachEvent("PlayStateChange",function(newState) {
		if (newState==8) {
		if (plugin2) {plugin2.close (); $(plugin2).remove (); plugin2=false; $('#player11').html ('');}
		next (window.idd);}});
		}
		
		if (!navigator.userAgent.toLowerCase().match('trident')){

		plugin2 = document.getElementById('mediaPlayerwm5');
		} else {
		
		plugin2 = document.getElementById('mediaPlayerwm');
		}
		do {} while (plugin2.playState!=0);}
		plugin2.URL=('http://'+document.domain+'/'+encodeURI(nam));
		setTimeout(function () {
		if (plugin2)
		{plugin2.controls.play();}
		} , 700);
		setTimeout(function () {
		if (plugin2) {
		if (plugin2.playState==6) {
		plugin2.controls.currentPosition = 1}}
		} , 4000);
		plugin2.settings.volume = volum1;
		}} else { window.location.href = encodeURI(nam);}
		};
function OnDSPlayStateChangeEvt(NewState)
{
	if ((NewState>6)&&(NewState<12) || (NewState<6)){
	document.cookie="vol="+plugin2.settings.volume+"; path=/; expires="+date5;
	}
    if (NewState==8) {
	if (plugin2) {plugin2.close(); $(plugin2).remove (); plugin2=false; $('#player11').html (""); }
	next (window.idd);}
}
function OnDSErrorEvt()
{
if (plugin2) {plugin2.close(); $(plugin2).remove (); plugin2=false; $('#player11').html (""); }
	next (window.idd);
}

function clse (objc) //удаление плееров и всего чего хочешь. Главное в одном месте.
{
if (objc==0) {$('#playlist').empty(); $('#playlist-delete').empty(); $('#jquery_jplayer_1').jPlayer('destroy'); if (plugin2) {plugin2.close(); $(plugin2).remove (); plugin2=false; $('#player11').slideUp('slow'); $('#player11').html ('');} $('#player11').slideUp('slow'); $('#player7').slideUp('slow'); document.getElementById('mediaplayer').style.display = 'none'; document.getElementById('mediaplayer_wrapper').style.display = "none"; $('#slide').slideUp('slow'); $('#player9').hide(); if (player) player.disconnect();
if (jwplayer()) {jwplayer().stop();}
document.getElementById('img1').src="";
document.getElementById('img1').style.display = "none";
		document.getElementById('img2').style.display = "none";
		clearTimeout(waiter);
		stt=0;
		$('#gplay').slideDown('slow');
}
if (objc==1) {
		document.getElementById('mediaplayer').style.display = "none"; //mp3 player
		document.getElementById('mediaplayer_wrapper').style.display = "none";
		$("#jquery_jplayer_1").jPlayer("destroy");
		if (jwplayer()) {jwplayer().stop();}
		$('#player9').hide();
		if (plugin2) {plugin2.close(); $(plugin2).remove (); plugin2=false; $('#player11').html ('');}
		$('#player11').hide();
		$('#slide').hide();
		$('#gplay').hide();
		document.getElementById('img1').src="";
		document.getElementById('img1').style.display = "none";
		document.getElementById('img2').style.display = "none";
		clearTimeout(waiter);
		if (player)
            player.disconnect();
		$('#player7').show();}
if (objc==2) {
		$('#player9').hide(); //flv player
		$("#jquery_jplayer_1").jPlayer("destroy");
		if (plugin2) {plugin2.close(); $(plugin2).remove (); plugin2=false; $('#player11').html ('');}
		$('#player11').hide();
		$('#slide').hide();
		$('#gplay').hide();
		$('#player7').hide();
		document.getElementById('img1').src="";
		document.getElementById('img1').style.display = "none";
		document.getElementById('img2').style.display = "none";
		clearTimeout(waiter);
		if (player)
            player.disconnect();
		$('#mediaplayer').show();
}
if (objc==3) {
		document.getElementById('mediaplayer').style.display = "none"; //Image
		document.getElementById('mediaplayer_wrapper').style.display = "none";
		$("#jquery_jplayer_1").jPlayer("destroy");
		if (jwplayer()) {jwplayer().stop();}
	    $('#player7').hide();
		$('#gplay').hide();
		$('#player9').hide();
		if (plugin2) {plugin2.close(); $(plugin2).remove (); plugin2=false; $('#player11').html ('');}
		$('#player11').hide();
		document.getElementById('img1').src="";
		clearTimeout(waiter);
		if (player)
            player.disconnect();}
		$('#slide').show();
if (objc==4) {
		document.getElementById('mediaplayer').style.display = "none"; // Flac
		document.getElementById('mediaplayer_wrapper').style.display = "none";
		$("#jquery_jplayer_1").jPlayer("destroy");
		if (jwplayer()) {jwplayer().stop();}
	    $('#player7').hide();
		$('#gplay').hide();
		if (plugin2) {plugin2.close(); $(plugin2).remove (); plugin2=false; $('#player11').html ('');}
		$('#player11').hide();
		$('#slide').hide();
		document.getElementById('img1').src="";
		document.getElementById('img1').style.display = "none";
		document.getElementById('img2').style.display = "none";
		clearTimeout(waiter);
		}
if (objc==5) {
		document.getElementById('mediaplayer').style.display = "none"; // windows media
		document.getElementById('mediaplayer_wrapper').style.display = "none";
		$("#jquery_jplayer_1").jPlayer("destroy");
		if (jwplayer()) {jwplayer().stop();}
	    $('#player7').hide();
		$('#player9').hide();
		$('#gplay').hide();
		$('#slide').hide();
		document.getElementById('img1').src="";
		document.getElementById('img1').style.display = "none";
		document.getElementById('img2').style.display = "none";
		clearTimeout(waiter);
		if (player)
            player.disconnect();
		if (plugin2) {plugin2.close();}
}
}

function appen (ffil, pod, m3)
{
	if ($('#playlist > li').size()<=0) {window.elem=1; window.gh=1;};
		window.gh=window.elem;
		
function plpr (msg){
ff=0;
ff1=0;
extinf=false;
err=false;
musicDir2="/Music/";
musicDir="res/Музыка";
klipDir2="/Клипы/";
klipDir="res/Клипы";
namepls = "", pth = "", ext = "";
msg=msg.replace(/\r\n|\r|\n/g, "<|>");
if (msg.indexOf("#EXTINF") != -1)
{
	extinf = true;
} else
{
	extinf = false;
}
do {
try {
if (extinf == true) {
namepls=msg.substring (msg.indexOf(",", msg.indexOf("#EXTINF:", ff)+8)+1 ,msg.indexOf("<|>", msg.indexOf(",", msg.indexOf("#EXTINF:", ff)+8)+1));
ff=msg.indexOf("<|>", msg.indexOf(",", msg.indexOf("#EXTINF:", ff)+8)+1);
pth=msg.substring (msg.indexOf("<|>", ff)+3, msg.indexOf("<|>", msg.indexOf("<|>", ff)+3));
pth = pth.replace(/\\/g, "/");
} else {
pth = msg.substring(ff1, (msg.indexOf("<|>", ff1)==-1)?msg.length:msg.indexOf("<|>", ff1));
pth = pth.replace(/\\/g, "/");
namepls = pth.substring((pth.lastIndexOf("/") != -1) ? pth.lastIndexOf("/") + 1 : 0,pth.length);
}
} catch (e)
{
err = true;
}
if (err == false)
{
if (namepls.indexOf("/res/")<0) {
ext = (pth.substring(pth.lastIndexOf('.') + 1, pth.length)).toLowerCase();
if ((ext=='flac') || (ext=='mp3') || (ext=='aac') || (ext=='ogg') || (ext=='m4a')) {
if (pth.indexOf(musicDir2) != -1)
										{
											pth = pth.substring(
													pth.indexOf(musicDir2)
															+ musicDir2
																	.length,
													pth.length);
										}
										pth = musicDir + "/" + pth;
$('#playlist').append( "<li id = 'id"+window.elem+"' class='ui-state-default' onclick='$(\"#playlist li\").css (\"color\", \"#555555\"); $(this).css(\"color\", \"#F16161\"); run(\""+pth.replace(/'/g,"R493")+"\", $(this).index());' title=\""+pth.replace(/'/g,"R493")+"\">"+namepls+"</li>");
} 


else if (
(ext=='avi') || (ext=='mpeg') || (ext=='flv') || (ext=='mpg') || (ext=='wmv') 
|| (ext=='mp4') || (ext=='mkv')
){
if (pth.indexOf(klipDir2) != -1)
										{
											pth = pth.substring(
													pth.indexOf(klipDir2)
															+ klipDir2
																	.length,
													pth.length);
										}
										pth = klipDir + "/" + pth;
$('#playlist').append( "<li id = 'id"+window.elem+"' class='ui-state-default' onclick='$(\"#playlist li\").css (\"color\", \"#555555\"); $(this).css(\"color\", \"#F16161\"); run(\""+pth.replace(/'/g,"R493")+"\", $(this).index());' title=\""+pth.replace(/'/g,"R493")+"\">"+namepls+"</li>");}
}
 else {
$('#playlist').append( "<li id = 'id"+window.elem+"' class='ui-state-default' onclick='$(\"#playlist li\").css (\"color\", \"#555555\"); $(this).css(\"color\", \"#F16161\"); run(\""+pth.replace(/'/g,"R493")+"\", $(this).index());' title=\""+pth.replace(/'/g,"R493")+"\">"+namepls+"</li>");}

window.elem=window.elem+1;
window.gh=window.gh+1;

if (extinf == true) {ff1=msg.indexOf("<|>", msg.indexOf("<|>", msg.indexOf("<|>", ff)+3)+3);} else {
ff1 = ((msg.indexOf("<|>", ff1 + 3))!=-1)?(msg.indexOf("<|>", ff1 + 3))+3:-1;
}

}} while (ff1>0);
$('.ui-state-default').mousedown(function(eventObject){window.gh=$(this).index();});
        $('.ui-state-default').contextPopup({
          items: [
            {label:'Скачать', icon:'img/icons/shopping-basket.png', action:function() {
			fd=document.getElementById('playlist').children[window.gh].title;
			window.location.href = encodeURI(fd);
			 } },
            {label:'Скопировать ссылку', icon:'img/icons/receipt-text.png', action:function() { 
			fd=(document.getElementById('playlist').children[window.gh].title).replace (/&/g, "R494");
			prompt("Ссылка на файл:","http://"+document.domain+"/?file="+fd); } }
          ]
        });
}
if ((m3!='') && (m3!=undefined) && (m3!=0)) {plpr (m3);} else {
if (((ffil.substr (ffil.length-4, 4)).toLowerCase())==".m3u") {
$.ajax({
type: "GET",
url: encodeURI (ffil),
cache: false,
success: function (msg1){
plpr (msg1);
}});

} else if ((ffil.lastIndexOf ("/")+1)==(ffil.length)) {   //добавление файлов папками и подпапками
		$.get(ffil, {}, function(data1) {
		var i=0, i1=0;
		do {
			i=(data1.indexOf ('" alt="[', i));
			if (i!=-1) {
			var edf=data1.substring ((i+8), (data1.indexOf ('"', i+8)-1));
			if ((edf!='DIR') && (edf!='PARENTDIR') && (edf!='ICO')) {
			var fill=decodeURI(data1.substring (i+32, data1.indexOf ('"', i+32)));
			var extt=(fill.substring (fill.lastIndexOf ('.')+1, fill.length)).toLowerCase();
			//Типы файлов для отображения	
			if ((extt=='mpg') || (extt=='iso') || (extt=='mdf') || (extt=='mds') || (extt=='exe') || (extt=='wmv') || (extt=='mpeg') || (extt=='rar') || (extt=='zip') || (extt=='gif') || (extt=='jpeg') || (extt=='jpg') || (extt=='mp4') || (extt=='mp3') || (extt=='flv') || (extt=='mkv') || (extt=='flac') || (extt=='avi')) {
			
			$('#playlist').append( "<li id = 'id"+window.elem+"' class='ui-state-default' onclick='$(\"#playlist li\").css (\"color\", \"#555555\"); $(this).css(\"color\", \"#F16161\"); run(\""+ffil.replace(/'/g,"R493")+fill.replace(/'/g,"R493")+"\", $(this).index());' title=\'"+ffil.replace(/'/g,"R493")+fill.replace(/'/g,"R493")+"\'>"+ffil+fill+"</li>");
		$('.ui-state-default').mousedown(function(eventObject){window.gh=$(this).index();});
        $('.ui-state-default').contextPopup({
          items: [
            {label:'Скачать', icon:'img/icons/shopping-basket.png', action:function() {
			fd=document.getElementById('playlist').children[window.gh].title;
			window.location.href = encodeURI(fd);
			 } },
            {label:'Скопировать ссылку', icon:'img/icons/receipt-text.png', action:function() { 
			fd=(document.getElementById('playlist').children[window.gh].title).replace (/&/g, "R494");
			prompt("Ссылка на файл:","http://"+document.domain+"/?file="+fd); } }
          ]
        });
		
	  window.elem=window.elem+1;
	  window.gh=window.gh+1;
			}
			
			}
			i=i+32;}}
		while (i!=-1);
		
		if (pod==1) {i=0;
		do {
			i=data1.indexOf ('alt="[DIR]"></td><td><a href=\"', i);
			if (i!=-1) {
			var dirr=decodeURI(data1.substring (i+30, data1.indexOf ('"', i+30)-1));
			appen (ffil+dirr+'/', 1);
			i=i+30;
			}
			}
		while (i!=-1);
		}
		});
		
		} else {
		$('#playlist').append( "<li id = 'id"+window.elem+"' class='ui-state-default' onclick='$(\"#playlist li\").css (\"color\", \"#555555\"); $(this).css(\"color\", \"#F16161\"); run(\""+ffil.replace(/'/g,"R493")+"\", $(this).index());' title=\'"+ffil.replace(/'/g,"R493")+"\'>"+ffil+"</li>");

		$('.ui-state-default').mousedown(function(eventObject){window.gh=$(this).index();});
        $('.ui-state-default').contextPopup({
          items: [
            {label:'Скачать', icon:'img/icons/shopping-basket.png', action:function() {
			fd=document.getElementById('playlist').children[window.gh].title;
			window.location.href = encodeURI(fd);
			 } },
            {label:'Скопировать ссылку', icon:'img/icons/receipt-text.png', action:function() { 
			fd=(document.getElementById('playlist').children[window.gh].title).replace (/&/g, "R494");
			prompt("Ссылка на файл:","http://"+document.domain+"/?file="+fd); } }
          ]
        });
		
	  window.elem=window.elem+1;
	  window.gh=window.gh+1;
	  }}
}

function clrf(Id)
        {
		if (Id!='') {
            document.getElementById(Id).innerHTML = document.getElementById(Id).innerHTML;}
			var fileInput = document.getElementById('fllp');
		$(fileInput).change ( function() {
		if (window.File && window.FileReader && window.FileList && window.Blob) {
			var file = fileInput.files[0];
			if ((file.name.substring (file.name.lastIndexOf ('.')+1, file.name.length)).toLowerCase()=='m3u') {
				var reader = new FileReader();
				reader.onload = function() {
					appen ('',0, (reader.result));
				}
				reader.readAsText(file, 'windows-1251');	
			} else {
			alert ('Только в формате m3u');
			}
		} else { alert ('Этот браузер не поддерживает загрузку плейлистов. Обновите его или используйте другой');}});
        }
////////////////////////////////////////////ДОКУМЕНТ РЕДИ\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$(document).ready( function() {

		var  put;
		window.elem=1;
		window.gh=1;
		scroll1=0;
		clrf('');
		$("#back-top").hide();
		$(function () {
			$(window).scroll(function () {
				if ($(this).scrollTop() > 50) {
					$('#back-top').fadeIn();
				} else {
					$('#back-top').fadeOut();
				}
			});
			$('#back-top a').click(function () {
				scroll1=$(window).scrollTop();
				$('body,html').animate({
					scrollTop: 0
				}, 300);
				return false;
			});
		});
		$('#back-down a').click(function () {
				scroll1=$(window).scrollTop();
				$('body,html').animate({
					scrollTop: $('body,html').height()
				}, 300);
				return false;
			});
		$('#back-forw a').click(function () {
				var scroll2=$(window).scrollTop();
				$('body,html').animate({
					scrollTop: scroll1
				}, 300);
				scroll1=scroll2;
				return false;
			});
		$(document).bind('keydown', function(e) {if (e.keyCode==32) {
		if (window.idd>0) { if ($("#playlist li").eq(window.idd).offset().top>$(window).height()) {
		scroll1=$(window).scrollTop();
		$('body,html').animate({scrollTop:$("#playlist li").eq(window.idd).offset().top}, 100);}} return false;};});
		
		
////////////////////////////////////////////////Модальное окно\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
(function($){

	// Defining our jQuery plugin

	$.fn.paulund_modal_box = function(prop){
		// Default parameters
		var options = $.extend({
			height : "250",
			width : "500",
			title:"",
			description: "",
			top: "20%",
			left: "30%",
		},prop);

		return this.ready(function(e){
			add_block_page();
			add_popup_box();
			add_styles();
			
			$('.paulund_modal_box').fadeIn();
		});
		
		/**
		 * Add styles to the html markup
		 */
		 function add_styles(){			
			$('.paulund_modal_box').css({ 
				'position':'absolute', 
				'left':options.left,
				'top':options.top,
				'display':'none',
				'height': options.height + 'px',
				'width': options.width + 'px',
				'border':'1px solid #fff',
				'box-shadow': '0px 2px 7px #292929',
				'-moz-box-shadow': '0px 2px 7px #292929',
				'-webkit-box-shadow': '0px 2px 7px #292929',
				'border-radius':'10px',
				'-moz-border-radius':'10px',
				'-webkit-border-radius':'10px',
				'background': '#f2f2f2', 
				'z-index':'50',
			});
			$('.paulund_modal_close').css({
				'position':'relative',
				'top':'-25px',
				'left':'20px',
				'float':'right',
				'display':'block',
				'height':'50px',
				'width':'50px',
				'background': 'url(img/close.png) no-repeat',
			});
			$('.paulund_block_page').css({
				'position':'absolute',
				'top':'0',
				'left':'0',
				'background-color':'rgba(0,0,0,0.6)',
				'height':'100%',
				'width':'100%',
				'z-index':'10'
			});
			$('.paulund_inner_modal_box').css({
				'background-color':'#fff',
				'height':(options.height - 50) + 'px',
				'width':(options.width - 50) + 'px',
				'padding':'10px',
				'margin':'15px',
				'border-radius':'10px',
				'-moz-border-radius':'10px',
				'-webkit-border-radius':'10px'
			});
		}
		
		 /**
		  * Create the block page div
		  */
		 function add_block_page(){
			var block_page = $('<div class="paulund_block_page"></div>');
			$(block_page).appendTo('body');
		}
		 	
		 /**
		  * Creates the modal box
		  */
		 function add_popup_box(){
			 var pop_up = $('<div class="paulund_modal_box"><a href="#" class="paulund_modal_close"></a><div class="paulund_inner_modal_box"><h2>' + options.title + '</h2><p>' + options.description + '</p></div></div>');
			 $(pop_up).appendTo('.paulund_block_page');
			 			 
			 $('.paulund_modal_close').click(function(){
				$(this).parent().fadeOut().remove();
				$('.paulund_block_page').fadeOut().remove();				 
			 });
		}

		return this;
	};
	
})(jQuery);
<?php if (!(($er!='') or ($dr!='') or ($drr!='') or ($inf!='') or ($pl!='') or ($pfil!='') or ($tpfil!=''))) {
?>
if (document.cookie.indexOf ('first=1')==-1) {
document.cookie="first=1; path=/; expires="+date5;
$(document).paulund_modal_box({
		title:'Welcome. Привет!',
		description:'Этот сайт - онлайн библиотека мультимедиа файлов. <br/> В отличие от всех аналогичных сайтов тут собрано только лучшее из лучших (хиты в своём жанре) и в максимальном качестве.<br/>Файлы можно не только открыть онлайн, но и скачать. Всё регулярно обновляется так что следите за новинками и плейлистами.<br/>Внизу страницы есть инструкции, как управлять сайтом. Почитайте их обязательно!<br/>С этого сайта вы можете начать создавать свою коллекцию! Вперёд!',
		height: '400',
		width: '500'
	});
	}
<?php }?>
//////////////////////////////////////////////////загрузка файлов\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
		$(function () {
    'use strict';

    // Initialize the jQuery File Upload widget:
    $('#fileupload').fileupload({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: 'up/'
    });

    // Enable iframe cross-domain access via redirect option:
    $('#fileupload').fileupload(
        'option',
        'redirect',
        window.location.href.replace(
            /\/[^\/]*$/,
            '/cors/result.html?%s'
        )
    );


        // Load existing files:
        $('#fileupload').addClass('fileupload-processing');
        $.ajax({
            // Uncomment the following to send cross-domain cookies:
            //xhrFields: {withCredentials: true},
            url: $('#fileupload').fileupload('option', 'url'),
            dataType: 'json',
            context: $('#fileupload')[0]
        }).always(function () {
            $(this).removeClass('fileupload-processing');
        }).done(function (result) {
            $(this).fileupload('option', 'done')
                .call(this, null, {result: result});
        });

});
		//////////////////////////////////////Контекстное меню\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$.fn.contextPopup = function(menuData) {
	// Define default settings
	var settings = {
		contextMenuClass: 'contextMenuPlugin',
		gutterLineClass: 'gutterLine',
		headerClass: 'header',
		seperatorClass: 'divider',
		title: '',
		items: []
	};
	
	// merge them
	$.extend(settings, menuData);

  // Build popup menu HTML
  function createMenu(e) {
    var menu = $('<ul class="' + settings.contextMenuClass + '"><div class="' + settings.gutterLineClass + '"></div></ul>')
      .appendTo(document.body);
    if (settings.title) {
      $('<li class="' + settings.headerClass + '"></li>').text(settings.title).appendTo(menu);
    }
    settings.items.forEach(function(item) {
      if (item) {
        var rowCode = '<li><input type="button" class="butt2"  value=""/></li>';
        // if(item.icon)
        //   rowCode += '<img>';
        // rowCode +=  '<span></span></a></li>';
        var row = $(rowCode).appendTo(menu);
        if(item.icon){
          var icon = $('<img>');
          icon.attr('src', item.icon);
          icon.insertBefore(row.find(':button'));
        }
        row.find(':button').val(item.label);
          
        if (item.isEnabled != undefined && !item.isEnabled()) {
            row.addClass('disabled');
        } else if (item.action) {
            row.find(':button').click(function () { item.action(e); });
        }

      } else {
        $('<li class="' + settings.seperatorClass + '"></li>').appendTo(menu);
      }
    });
    menu.find('.' + settings.headerClass ).text(settings.title);
    return menu;
  }

  // On contextmenu event (right click)
  this.bind('contextmenu', function(e) {	
    var menu = createMenu(e)
      .show();
    
    var left = e.pageX + 5, /* nudge to the right, so the pointer is covering the title */
        top = e.pageY;
    if (top + menu.height() >= $(window).height()) {
        top -= menu.height();
    }
    if (left + menu.width() >= $(window).width()) {
        left -= menu.width();
    }

    // Create and show menu
    menu.css({zIndex:1000001, left:left, top:top})
      .bind('contextmenu', function() { return false; });

    // Cover rest of page with invisible div that when clicked will cancel the popup.
    var bg = $('<div></div>')
      .css({left:0, top:0, width:'100%', height:'100%', position:'absolute', zIndex:1000000})
      .appendTo(document.body)
      .bind('contextmenu click', function() {
        // If click or right click anywhere else on page: remove clean up.
        bg.remove();
        menu.remove();
        return false;
      });
	 $('#page').click (function() {
      bg.remove();
      menu.remove();
    });
	$(document.body).click (function() {
      bg.remove();
      menu.remove();
    });
	setTimeout (function() {
      bg.remove();
      menu.remove();
    }, 5000);
    // When clicking on a link in menu: clean up (in addition to handlers on link already)
    menu.find(':button').click(function() {
      bg.remove();
      menu.remove();
    });

    // Cancel event, so real browser popup doesn't appear.
    return false;
  });

  return this;
};

		///////////////////////////////////////////Дерево файлов\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
	$.extend($.fn, {
		fileTree: function(o, h) {
			// Defaults
			if( !o ) var o = {};
			if( o.root == undefined ) o.root = '/';
			if( o.folderEvent == undefined ) o.folderEvent = 'click';
			if( o.expandSpeed == undefined ) o.expandSpeed= 500;
			if( o.collapseSpeed == undefined ) o.collapseSpeed= 500;
			if( o.expandEasing == undefined ) o.expandEasing = null;
			if( o.collapseEasing == undefined ) o.collapseEasing = null;
			if( o.multiFolder == undefined ) o.multiFolder = true;
			if( o.loadMessage == undefined ) o.loadMessage = 'Loading...';
			
			$(this).each( function() {
				
				function showTree(c, t) {
					$(c).addClass('wait');
					$(".jqueryFileTree.start").remove();
				
					$.ajax({url:encodeURI(t), type: 'GET', cache: false, success:function(data1) {
					
						var data='<ul class="jqueryFileTree" style="display: none;">';
						var i=0, i1=0; var fill="" , fill1="";
						do {
						i=(data1.indexOf ('" alt="[', i));
						if (i!=-1) {
						var edf=data1.substring ((i+8), (data1.indexOf ('"', i+8)-1));
						if ((edf!='DIR') && (edf!='PARENTDIR') && (edf!='ICO')) {
						fill1=data1.substring (i+32, data1.indexOf ('"', i+32));
						fill=decodeURI(fill1);
						
						var extt=(fill.substring (fill.lastIndexOf ('.')+1, fill.length)).toLowerCase();
						//Типы файлов для отображения	
						if ((extt=='mpg') || (extt=='iso') || (extt=='mdf') || (extt=='mds') || (extt=='exe') || (extt=='wmv') || (extt=='mpeg') || (extt=='rar') || (extt=='zip') || (extt=='gif') || (extt=='jpeg') || (extt=='jpg') || (extt=='mp4') || (extt=='mp3') || (extt=='flv') || (extt=='mkv') || (extt=='m3u') || (extt=='flac') || (extt=='avi')) {
						data=data+'<li class="file ext_'+extt+'"><a href="#" rel="'+t+fill+'">'+fill+'</a></li>';
						i1=i1+1;}
						}
						i=i+32;}}
						while (i!=-1);
						i=0; var dirr="", dirr1="";
						do {
						i=data1.indexOf ('alt="[DIR]"></td><td><a href=\"', i);
						if (i!=-1) {
						dirr1=data1.substring (i+30, data1.indexOf ('"', i+30)-1);
						dirr=decodeURI(dirr1);
						data=data+'<li class="directory collapsed"><a href="#" rel="'+t+dirr+'/">'+dirr+'</a></li>';
						i=i+30;
						i1=i1+1;
						}
						}
						while (i!=-1);
						data=data+'</ul>';
						
						$(c).find('.start').html('');
						$(c).removeClass('wait').append(data);
						if( o.root == t ) $(c).find('UL:hidden').show(); else $(c).find('UL:hidden').slideDown({ duration: o.expandSpeed, easing: o.expandEasing, complete: function () {
						
						if (i1>15) {scroll1=$(window).scrollTop(); $('body,html').animate({scrollTop: $(c).offset().top}, 100);} else if ((i1>5) && (($(c).offset().top - $(window).scrollTop())>($(window).height()/2))) {scroll1=$(window).scrollTop(); $('body,html').animate({scrollTop: ($(window).scrollTop()+50)}, 100);}} });
						bindTree(c);
					}, error: function (a1,b1,c1) {$(c).removeClass('expanded'); $(c).removeClass('wait').addClass('collapsed');}});
				}
				
				function bindTree(t) {
					$(t).find('LI A').bind(o.folderEvent, function() {
						if( $(this).parent().hasClass('directory') ) {
							if($(this).parent().hasClass('collapsed')) {
								// Expand
								if( !o.multiFolder ) {
									$(this).parent().parent().find('UL').slideUp({ duration: o.collapseSpeed, easing: o.collapseEasing });
									$(this).parent().parent().find('LI.directory').removeClass('expanded').addClass('collapsed');
								}
								$(this).parent().find('UL').remove(); // cleanup
								showTree( $(this).parent(), $(this).attr('rel').match( /.*\// ) );
								$(this).parent().removeClass('collapsed').addClass('expanded');
								
							} else {
								// Collapse
								$(this).parent().find('UL').slideUp({ duration: o.collapseSpeed, easing: o.collapseEasing });
								$(this).parent().removeClass('expanded').addClass('collapsed');
							}
						} else {
							h($(this).attr('rel'));
						}
						return false;
					});
	
	var ii;
	$('.jqueryFileTree LI.ext_m3u').unbind ('mousedown');
	$('.jqueryFileTree LI.ext_m3u').mousedown(function(eventObject){ii=$(this).children('A').attr('rel');
	ii=ii.substring (ii.indexOf('/res/'), ii.length);
	});
	$('.jqueryFileTree LI.ext_m3u').contextPopup({
          items: [
            {label:'Скопировать ссылку', icon:'img/icons/receipt-text.png', action:function() { 
			prompt("Ссылка на файл:","http://"+document.domain+"/?file="+(ii).replace (/&/g, "R494")); } }
          ]
        });
	var if1;
	$('.directory').children ('A').unbind('mousedown');
	$('.directory').children ('A').mousedown(function(eventObject){
	if1=$(this); });
	
	$('.directory').children ('A').contextPopup({
          items: [{label:'Добавить в плейлист c подпапками', icon:'img/icons/bin-metal.png', action:function() {
			appen (if1.attr('rel'), 1);
			if1.css ('color','#CF2323');
			 } },
            {label:'Добавить в плейлист', icon:'img/icons/bin-metal.png', action:function() {
			appen (if1.attr('rel'), 0);
			if1.css ('color','#CF2323');
			 } },
			 {label:'Скопировать ссылку', icon:'img/icons/bin-metal.png', action:function() {
				prompt("Ссылка на папку:","http://"+document.domain+"/?dir="+(if1.attr('rel')).replace (/&/g, "R494"));
			 } },
			 {label:'Скопировать ссылку с подпапками', icon:'img/icons/bin-metal.png', action:function() {
				prompt("Ссылка на папку:","http://"+document.domain+"/?dirr="+(if1.attr('rel')).replace (/&/g, "R494"));
			 } }
          ]
        });
					// Prevent A from triggering the # on non-click events
					if( o.folderEvent.toLowerCase != 'click' ) $(t).find('LI A').bind('click', function() { return false; });
				}
				// Loading message
				$(this).html('<ul class="jqueryFileTree start"><li class="wait">' + o.loadMessage + '<li></ul>');
				// Get the initial file list
				
				showTree( $(this), escape(o.root) );
			});
		}
	});
		///////////////////////////////////////Слайд шоу\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
		var prfx = ["webkit", "moz", "ms", "o", ""]; function RunPrefixMethod(obj, method) { var p = 0, m, t; while (p < prfx.length && !obj[m]) { m = method; if (prfx[p] == "") { m = m.substr(0,1).toLowerCase() + m.substr(1); } m = prfx[p] + m; t = typeof obj[m]; if (t != "undefined") { prfx = [prfx[p]]; return (t == "function" ? obj[m]() : obj[m]); } p++; } }
		var elm = document.getElementById("img1");
		var elm1 = document.getElementById("img2");
		function frl() { if ((RunPrefixMethod(document, "FullScreen")==true) || (RunPrefixMethod(document, "IsFullScreen")==true)) { RunPrefixMethod(document, "CancelFullScreen"); fulls=0; $(elm).height(414); $(elm).width('auto');} else if ((RunPrefixMethod(document, "FullScreen")==false) || (RunPrefixMethod(document, "IsFullScreen")==false)) {fulls=1; RunPrefixMethod(elm, "RequestFullScreen"); $(elm).height('100%'); $(elm).width(($(elm).height()*(elm.naturalWidth/elm.naturalHeight))); } else if (((navigator.userAgent.toLowerCase().match('opera')) || (navigator.userAgent.toLowerCase().match('trident'))) && (fulls==0)) {fulls=1; $(elm).css({'position': 'fixed', 'width': 'auto', 'height': $(window).height()}); $(elm).css({'left':(($(window).width()/2)-($(elm).width()/2)), 'top':(($(window).height()/2)-($(elm).height()/2))});} else if (((navigator.userAgent.toLowerCase().match('opera')) || (navigator.userAgent.toLowerCase().match('trident'))) && (fulls==1)) {fulls=0; $(elm).css ({'position': 'relative', 'width': 'auto', 'height': 414, 'left':0, 'top':0})} }
		
		
		
		elm.oncontextmenu = function() {return false;};
		elm1.oncontextmenu = function() {return false;};
		$(elm).mousedown(function(e){
		if( e.button == 2 ) {
		prev (window.idd);
		return false; 
		}
		if( e.button == 1 ) {
		frl();
		return false; 
		}
		if( e.button == 0 ) {
		next (window.idd);
		return false; 
		}
		return true; 
		}); 
		
		$(elm).bind("load",function(){
		if (((RunPrefixMethod(document, "FullScreen")==true) || (RunPrefixMethod(document, "IsFullScreen")==true)) || ((fulls==1) && ((RunPrefixMethod(document, "IsFullScreen")==undefined)&&(RunPrefixMethod(document, "FullScreen")==undefined)))) {if ((navigator.userAgent.toLowerCase().match('opera')) || (navigator.userAgent.toLowerCase().match('trident'))) {
		$(elm).css({'position': 'fixed', 'width': 'auto', 'height': $(window).height()}); $(elm).css({'left':(($(window).width()/2)-($(elm).width()/2)), 'top':(($(window).height()/2)-($(elm).height()/2))});
		} else {
		
		$(elm).height('100%'); $(elm).width(($(elm).height()*(elm.naturalWidth/elm.naturalHeight))); console.log ($(elm).height()); console.log (elm.naturalWidth/elm.naturalHeight);}} else {$(elm).height(414); $(elm).width('auto');}
		if ($(elm).is(':visible') == false) {
		$('#slide').show();
		elm.style.display = "block";
		elm1.style.display = "none";
		waiter=setTimeout(function(){
		if ($(elm).is(':visible') == true) {
		if (stt==0) {
		$('#slide').slideUp('slow', function() {
		if ($(elm).is(':visible') == false) {
		next (window.idd);}
		});}}
		}, 6000); }});
		
		$( "#instr" ).click(function() {
		$('#instr1').slideToggle('slow');
		});
		$( "#instr2" ).click(function() {
		$('#instr3').slideToggle('slow');
		});
		$( "#zagr3" ).click(function() {
		$('#zagr4').slideToggle('slow');
		});
		$( "#down" ).click(function() {
		var coli=document.getElementById('playlist').getElementsByTagName('li').length;
		if (coli>0) {
		var dat='';
		for(var w=0; w<coli; w++)
		{
		dat=dat+'<|p>'+document.getElementById('playlist').children[w].title;
		}
		dat=dat+'<|p>';
		$.ajax({url:'/', type: 'POST', data: {'tfiles': dat}, cache: false, success:function(data1) {
		
		if (data1=='1') {$('#fi').val(dat); $("#form1").submit();} else {alert ('Слишком большой объём данных. Ограничение 300мб.'); };}});
		
		} });
        $( "#playlist" ).sortable();
		$( "#playlist-delete" ).sortable();
        $( "#playlist" ).disableSelection();
		$('#playlist').sortable({
        tolerance: 'pointer',
        cursor: 'pointer',
		scroll:true,
        dropOnEmpty: true,
        connectWith: '#playlist-delete',
        update: function(event, ui) {
            if(this.id == 'playlist-delete') {//плейлист, удаление и перемещение элементов
                jQuery('#'+ui.item.attr('id')).remove();
            } else {
			var kor=$('#playlist li').size();
			var kor1=0;
			do {
			if (($('#playlist li:eq('+kor1+')').css('color')=='rgb(241, 97, 97)') || (($('#playlist li:eq('+kor1+')').css('color')=='rgb(241,97,97)'))) {
			window.idd=kor1;
			idd=kor1;
			$('#prev').unbind();
			$('#prev').click(function(){
			prev(idd);
			});
			$('#next').unbind();
			$('#next').click(function(){
			next (idd);
			});
			break;}
			kor1=kor1+1;
			} 
			while (kor1<=kor)}   
        }            
    });
	$('#sohr').click (function () {  // сохранение плейлиста
	if ($('ul#playlist li').length==0) return false;
	var k1=0,re;
	re='#EXTM3U \n';
	do {
	var ddf=$('#playlist li:eq('+k1+')').text();
	ddf=ddf.substring(0, ddf.lastIndexOf("."));
	re=re+'#EXTINF:0,'+ddf+'\n'+'http://'+document.domain+$('#playlist li:eq('+k1+')').attr('title')+'\n';
	k1=k1+1;
	}
	while (k1<$('ul#playlist li').length);
	$('#pl1').val(re);
	});
    $('.td3').fileTree({ root: '/res/' },  function(file) { //клик по файлу

appen (file, 0);
		
    });

 
<?php 
if ($dr!='') {
$dr = str_replace ('R494', "&", $dr);
echo "appen ('".$dr."');  setTimeout (function () {next(-1);}, 2000);";
}
if ($drr!='') {
$drr = str_replace ('R494', "&", $drr);
echo "appen ('".$drr."', 1);  setTimeout (function () {next(-1);}, 2000);";
}
if ($er !='') {
$er = str_replace ('R494', "&", $er);
if (substr ($er, strripos($er, ".")+1)=='m3u') {
echo "appen ('".$er."');  setTimeout (function () {next(-1);}, 2000);";

} else {
echo "
$('#playlist').append(\"<li class='ui-state-default' style='color:#F16161' onclick='$("."\\"."\"#playlist li"."\\"."\").css ("."\\"."\"color"."\\"."\", "."\\"."\"#555555"."\\"."\"); $(this).css("."\\"."\"color"."\\"."\", "."\\"."\"#F16161"."\\"."\"); run("."\\"."\"\"+'".$er."'.replace(/'/g,\"R493\")+\""."\\"."\", $(this).index());' title='".$er."'>1. \"+'".$er."'+\"</li>\");
run ('".$er."',0);
$(function () {
window.elem=window.elem+1;
        $('.ui-state-default').contextPopup({
          items: [
            {label:'Скачать', icon:'img/icons/shopping-basket.png', action:function() { window.location.href ='".$er."'; } },
            {label:'Скопировать ссылку', icon:'img/icons/receipt-text.png', action:function() { prompt('Ссылка на файл:','http://".$_SERVER['HTTP_HOST']."/?file='+('".$er."').replace (/&/g, 'R494')); } }
          ]
        });
      });
";}}
?>
});
</script>
</head>
<body class="body1">
<div id="page">
<div id="top"><a href="/">Media Library - Master collection (HQ)</a></div>
<br/>
<div class="cont">
<div class="table1">
  <div class="tr">
   <div class="td1"><a href="\res">Ресурсы</a></div>
   <div class="td2">Плейлист</div>
  </div>

 </div>
<div class="td3"></div>
<div class="td6">
    <div class="td4">
	<div id="gplay"   align="center"><a href="https://play.google.com/store/apps/details?id=com.medialibrary.mycollection"><img id = "gplay"  height="297" src="img/gplay.jpg"/></a></div>
	<div id="slide" style="display: none" align="center"><img id = "img1"  height="390" src=""/><img id = "img2" width="535" height="390" src="img/loading.gif"/></div>
<div id="player7" style="display: none" align="center" >
		<div id="jquery_jplayer_1" class="jp-jplayer" ></div>

		<div id="jp_container_1" class="jp-audio" >
			<div class="jp-type-single">
				<div class="jp-gui jp-interface">
					<ul class="jp-controls">
						<li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li>
						<li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li>
						<li><a href="javascript:;" class="jp-stop" tabindex="1">stop</a></li>
						<li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a></li>
						<li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li>
						<li><a href="javascript:;" class="jp-volume-max" tabindex="1" title="max volume">max volume</a></li>
					</ul>
					<div class="jp-progress" align="left">
						<div class="jp-seek-bar">
							<div class="jp-play-bar"></div>
						</div>
					</div>
					<div class="jp-volume-bar" align="left">
						<div class="jp-volume-bar-value"></div>
					</div>
					<div class="jp-time-holder">
						<div class="jp-current-time"></div>
						<div class="jp-duration"></div>

						<ul class="jp-toggles">
							<li><a href="javascript:;" class="jp-repeat" tabindex="1" title="repeat">repeat</a></li>
							<li><a href="javascript:;" class="jp-repeat-off" tabindex="1" title="repeat off">repeat off</a></li>
						</ul>
					</div>
				</div>
				<div class="jp-title">
					<ul>
						<li id="titl"></li>
					</ul>
				</div>
				<div class="jp-no-solution">
					<span>Update Required</span>
					To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
				</div>
			</div>
		</div>

</div>

<div id="player9" style="display: none" align="center">
<div class="player" id="dgplayer" tabindex="0">
    <div class="avatar">
        <img src="/img/fallback_album_art.png">
    </div>

    <span class="title"></span>
    <span class="artist"></span>

    <div class="button"></div>

    <div class="volume">
        <img src="/img/volume_high.png">
        <div class="track">
            <div class="progress2"></div>
            <div class="handle"></div>
        </div>
        <img src="/img/volume_low.png">
    </div>

    <div class="seek">
        <span>0:00</span>
        <div class="track">
            <div class="loaded"></div>
            <div class="progress2"></div>
        </div>
        <span>-0:00</span>
    </div>
    <span class="file_description">Формат fLaC. Вы слушаете трек в максимальном качестве.</span>
</div>
</div>

<div id="player11" style="display: none" align="center"></div>



<center><div id="mediaplayer" style="display: none" align="center"><div id="mediaplayer_wrapper" style="display: none" align="center"></div></div></center>
	</div>
    <div class="td5">
<form method="post" id="form1">
<input type="button"  class="button" value="Загрузить плейлист" id="zagrr" onClick="clrf ('fllp1'); $('#fllp').click(); "/>
<div id="fllp1" class="fllp2">
<input type="file"  style="font-size: 50px; width: 1px; opacity: 0; filter:alpha(opacity: 0); z-index: -100;  position: relative; top: -40px; left: -200px;" name="pllist" id="fllp"></div>
<input type="button"  class="button" value="Стоп" onClick="stt=1"/>
<input type="button"  value="<<" id="prev"/>
<input type="button" name="sub" class="button" value="Очистить" onClick="clse(0)"/>
<input type="hidden" id="pl1" name="playlist" value=""/>
<input type="button"  value=">>" id="next"/>
<input type="submit"  value="Сохранить плейлист" id="sohr"/>
<input type="hidden" id="fi" name="files" value=""/>
<input type="button"   value="Скачать" id="down"/>
<input type="checkbox" id="rand">рандом
</form>

</div>
<div class="td7">
    <div class="tr">
    <div class="td8">
        <ul id="playlist" align="center" class="playlist"></ul>
</div>
        <div id="playlist-delete" class="td9"></div></div></div></div></div>
<div style="clear: both"></div>
<p id="back-down">
	<a href="#down"><span></span>Вниз</a>
</p>
<p id="back-forw">
	<a href="#backf"><span></span>Обратно</a>
</p>
<p id="back-top">
	<a href="#top"><span></span>Вверх</a>
</p>


<div id="instr" align="center" class="butt1">Инструкция. Если не работает.</div>
<div id="instr1" align="center" style="display: none">Если не воспроизводит видео значит в браузере не установлен плагин Windows media. Для его установки необходимо нажать на ссылки соответствующие вашему браузеру и выполнить инструкции:
<a target="blank" href="http://www.interoperabilitybridges.com/wmp-extension-for-chrome">Opera 13+</a>
<a target="blank" href="http://www.opera.com/docs/plugins/installation/#wmp">Opera 12.2-</a>
<a target="blank" href="http://www.interoperabilitybridges.com/windows-media-player-firefox-plugin-download">FireFox, Safari</a>
После скачивания плагинов их необходимо установить.
<br/>Видео в Chrome не работает.
<br/>В браузере Internet explorer плагин установлен по умолчанию, если в системе установлен компонент Windows media player 10+. 
<br/>В браузере Firefox последней версии нужно <a target="blank" href="http://www.interoperabilitybridges.com/windows-media-player-firefox-plugin-download">скачать и установить плагин</a>. После установки в браузере перейти на адрес (скопировать и вставить в адрес):"about:config". 
<br/>Нажать "я буду остарожен". Найти пункт "plugins.load_appdir_plugins" и два раза кликнуть чтобы изменить его значение на true.
<br/>Закрыть фаерфокс. Скопировать папку C:\PFiles\Plugins в C:\Program Files (x86)\Mozilla Firefox, если она есть. Зайти в Дополнения -> плагины. Поставить "Всегда включать" на против плагина windows media player. Всё, плагин установлен.
<br/>Интструкция от медиацентр: <a href="http://www.earthmediacenter.com/ru/windows_media_player_plugin_installation_guide.html" target="blank">Откроется в новом окне</a>

<br/>Установив этот плагин 1 раз видео будет играть всегда!
<br/>Кроме плагина нужно что бы у вас на компьютере были установлены следующие кодеки и только они! Остальные удалить, если операционная система windows7+:
<a target="blank" href="http://www.ac3filter.net/wiki/Download_AC3Filter">AC3Filter</a>
<a target="blank" href="http://www.ac3filter.net/wiki/AC3File">AC3File</a>
<a target="blank" href="http://haali.su/mkv/">Haali Media Splitter</a>
<a target="blank" href="http://bmproductions.fixnum.org/index.htm?http://bmproductions.fixnum.org/wmptagplus/">WMP Tag Plus</a>
<br/>Проверено при установке вышеуказанного софта 99% видео с этого сайта воспроизводится нормально.
</div>
<div id="instr2" align="center" class="butt1">Инструкция. Если работает.</div>
<div id="instr3" align="center" style="display: none">
<center><b>Это коллекция мультимедиа, программ и игр. (Разработка сайта временно приостановлена. Сейчас идёт разработка android приложения к сайту)</b></center>
Слева находится дерево файлов, справа - плеер и плейлист. Из дерева можно набросать плейлист и включить его на проигрывание.
<br/>Есть готовые плейлисты. На всё (файл, папка, плейлист) можно получить ссылку и поделиться с кем-нибудь.
<br/>Нажимая правой кнопкой по файлам и папкам в дереве и плейлисте с ними можно производить различные действия.
<br/>Все программы проверены на вирусы и работоспособность. Они постоянно обновляются, как и музыка. 
<br/>Программы обновляются по необходимости. Музыка и клипы по возможности. 
<br/>Мультимедиа лучше всего проигрывать в браузере Opera. 
<br/>Это единственный в мире веб плеер flac - и он немного недоработан. На сегодня некоторые треки не перематываются.
<br/>Windows Media плеер на весь экран переходит по двойному щелчку по картинке. 
<br/>Слайдшоу фотографий или картинок: Левая кнопка мыши по изображению - следующий слайд, Правая - перейти на предыдущий слайд, <br/>Средняя кнопка мыши или при нажатии колёсиком - увеличить\сделать на весь экран (Работает не во всех браузерах).
<br/>В самом низу есть кнопки по загрузке файлов. С помощью них можно отправить мне любые файлы.
<br/>Свой плейлист можно сохранить у себя на компе и открыть windows media player и он будет играть его почти так же, как сайт. Не будет проигрываться flac и ещё некоторые форматы. Обратно загрузить на сайт тоже можно.
<br/>Пробел - перелёт к проигрываемому треку.
</div>
<div id="zagr3" align="center" class="butt1">Загрузить файлы</div>
<div style="display:none" id="zagr4">
   <form id="fileupload" action="//jquery-file-upload.appspot.com/" method="POST" enctype="multipart/form-data">

        <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
        <div class="row fileupload-buttonbar">
            <div class="span7">
                <!-- The fileinput-button span is used to style the file input field as button -->
                <span class="btn btn-success fileinput-button">
                    <i class="icon-plus icon-white"></i>
                    <span>Загрузить файлы...</span>
                    <input type="file" name="files[]" multiple>
                </span>
                <button type="submit" class="btn btn-primary start">
                    <i class="icon-upload icon-white"></i>
                    <span>Начать загрузку</span>
                </button>
                <button type="reset" class="btn btn-warning cancel">
                    <i class="icon-ban-circle icon-white"></i>
                    <span>Отменить</span>
                </button>
                <input type="checkbox" class="toggle">
                <!-- The loading indicator is shown during file processing -->
                <span class="fileupload-loading"></span>
            </div>
            <!-- The global progress information -->
            <div class="span5 fileupload-progress fade">
                <!-- The global progress bar -->
                <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                    <div class="bar" style="width:0%;"></div>
                </div>
                <!-- The extended global progress information -->
                <div class="progress-extended">&nbsp;</div>
            </div>
        </div>
        <!-- The table listing the files available for upload/download -->
        <table role="presentation" class="table table-striped"><tbody class="files"></tbody></table>
    </form></div>
	<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls" data-filter=":even">
    <div class="slides"></div>
    <h3 class="title"></h3>
    <a class="prev">‹</a>
    <a class="next">›</a>
    <a class="close">?</a>
    <a class="play-pause"></a>
    <ol class="indicator"></ol>
</div>
<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td>
            <span class="preview"></span>
        </td>
        <td>
            <p class="name">{%=file.name%}</p>
            {% if (file.error) { %}
                <div><span class="label label-important">Error</span> {%=file.error%}</div>
            {% } %}
        </td>
        <td>
            <p class="size">{%=o.formatFileSize(file.size)%}</p>
            {% if (!o.files.error) { %}
                <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="bar" style="width:0%;"></div></div>
            {% } %}
        </td>
        <td>
            {% if (!o.files.error && !i && !o.options.autoUpload) { %}
                <button class="btn btn-primary start">
                    <i class="icon-upload icon-white"></i>
                    <span>Start</span>
                </button>
            {% } %}
            {% if (!i) { %}
                <button class="btn btn-warning cancel">
                    <i class="icon-ban-circle icon-white"></i>
                    <span>Cancel</span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
        <td>
            <span class="preview">
                {% if (file.thumbnailUrl) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
                {% } %}
            </span>
        </td>
        <td>
            <p class="name">
                <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
            </p>
            {% if (file.error) { %}
                <div><span class="label label-important">Error</span> {%=file.error%}</div>
            {% } %}
        </td>
        <td>
            <span class="size">{%=o.formatFileSize(file.size)%}</span>
        </td>
        <td>
            <button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                <i class="icon-trash icon-white"></i>
                <span>Delete</span>
            </button>
            <input type="checkbox" name="delete" value="1" class="toggle">
        </td>
    </tr>
{% } %}
</script>
<!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included <script src="js/vendor/jquery.ui.widget.js"></script>-->
<!-- The Templates plugin is included to render the upload/download listings -->
<script src="js/tmpl.min.js"></script>
<!-- The Load Image plugin is included for the preview images and image resizing functionality -->
<script src="js/load-image.min.js"></script>
<!-- The Canvas to Blob plugin is included for image resizing functionality -->
<script src="js/canvas-to-blob.min.js"></script>
<!-- Bootstrap JS is not required, but included for the responsive demo navigation <script src="js/bootstrap.min.js"></script>-->

<!-- blueimp Gallery script -->
<script src="js/jquery.blueimp-gallery.min.js"></script>
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="js/jquery.iframe-transport.js"></script>
<!-- The basic File Upload plugin -->
<script src="js/jquery.fileupload.js"></script>
<!-- The File Upload processing plugin -->
<script src="js/jquery.fileupload-process.js"></script>
<!-- The File Upload image preview & resize plugin -->
<script src="js/jquery.fileupload-image.js"></script>
<!-- The File Upload audio preview plugin -->
<script src="js/jquery.fileupload-audio.js"></script>
<!-- The File Upload video preview plugin -->
<script src="js/jquery.fileupload-video.js"></script>
<!-- The File Upload validation plugin -->
<script src="js/jquery.fileupload-validate.js"></script>
<!-- The File Upload user interface plugin -->
<script src="js/jquery.fileupload-ui.js"></script>
<!-- The main application script -->
<!-- The XDomainRequest Transport is included for cross-domain file deletion for IE 8 and IE 9 -->
<!--[if (gte IE 8)&(lt IE 10)]>
<script src="js/cors/jquery.xdr-transport.js"></script>
<![endif]-->
<div id="bottom" ><a href="mailto:masters@inbox.ru">Masters ©copyright</a></div>
<div align="center"><script type="text/javascript" src="http://jj.revolvermaps.com/2/2.js?i=9n3z9c9mhpq&amp;m=8&amp;s=178&amp;c=00ff6c&amp;t=1" async="async"></script></div>
</body>
</html><?php } ?>