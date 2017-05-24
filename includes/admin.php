<?php
require_once( dirname(__FILE__).'/form.lib.php' );

define( 'PHPFMG_USER', "AceInTheHole@gmail.com" ); // must be a email address. for sending password to you.
define( 'PHPFMG_PW', "750e9d" );

?>
<?php
/**
 * GNU Library or Lesser General Public License version 2.0 (LGPLv2)
*/

# main
# ------------------------------------------------------
error_reporting( E_ERROR ) ;
phpfmg_admin_main();
# ------------------------------------------------------




function phpfmg_admin_main(){
    $mod  = isset($_REQUEST['mod'])  ? $_REQUEST['mod']  : '';
    $func = isset($_REQUEST['func']) ? $_REQUEST['func'] : '';
    $function = "phpfmg_{$mod}_{$func}";
    if( !function_exists($function) ){
        phpfmg_admin_default();
        exit;
    };

    // no login required modules
    $public_modules   = false !== strpos('|captcha||ajax|', "|{$mod}|");
    $public_functions = false !== strpos('|phpfmg_ajax_submit||phpfmg_mail_request_password||phpfmg_filman_download||phpfmg_image_processing||phpfmg_dd_lookup|', "|{$function}|") ;   
    if( $public_modules || $public_functions ) { 
        $function();
        exit;
    };
    
    return phpfmg_user_isLogin() ? $function() : phpfmg_admin_default();
}

function phpfmg_ajax_submit(){
    $phpfmg_send = phpfmg_sendmail( $GLOBALS['form_mail'] );
    $isHideForm  = isset($phpfmg_send['isHideForm']) ? $phpfmg_send['isHideForm'] : false;

    $response = array(
        'ok' => $isHideForm,
        'error_fields' => isset($phpfmg_send['error']) ? $phpfmg_send['error']['fields'] : '',
        'OneEntry' => isset($GLOBALS['OneEntry']) ? $GLOBALS['OneEntry'] : '',
    );
    
    @header("Content-Type:text/html; charset=$charset");
    echo "<html><body><script>
    var response = " . json_encode( $response ) . ";
    try{
        parent.fmgHandler.onResponse( response );
    }catch(E){};
    \n\n";
    echo "\n\n</script></body></html>";

}


function phpfmg_admin_default(){
    if( phpfmg_user_login() ){
        phpfmg_admin_panel();
    };
}



function phpfmg_admin_panel()
{    
    if( !phpfmg_user_isLogin() ){
        exit;
    };

    phpfmg_admin_header();
    phpfmg_writable_check();
?>    
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td valign=top style="padding-left:280px;">

<style type="text/css">
    .fmg_title{
        font-size: 16px;
        font-weight: bold;
        padding: 10px;
    }
    
    .fmg_sep{
        width:32px;
    }
    
    .fmg_text{
        line-height: 150%;
        vertical-align: top;
        padding-left:28px;
    }

</style>

<script type="text/javascript">
    function deleteAll(n){
        if( confirm("Are you sure you want to delete?" ) ){
            location.href = "admin.php?mod=log&func=delete&file=" + n ;
        };
        return false ;
    }
</script>


<div class="fmg_title">
    1. Email Traffics
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=1">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=1">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_EMAILS_LOGFILE) ){
            echo '<a href="#" onclick="return deleteAll(1);">delete all</a>';
        };
    ?>
</div>


<div class="fmg_title">
    2. Form Data
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=2">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=2">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_SAVE_FILE) ){
            echo '<a href="#" onclick="return deleteAll(2);">delete all</a>';
        };
    ?>
</div>

<div class="fmg_title">
    3. Form Generator
</div>
<div class="fmg_text">
    <a href="http://www.formmail-maker.com/generator.php" onclick="document.frmFormMail.submit(); return false;" title="<?php echo htmlspecialchars(PHPFMG_SUBJECT);?>">Edit Form</a> &nbsp;&nbsp;
    <a href="http://www.formmail-maker.com/generator.php" >New Form</a>
</div>
    <form name="frmFormMail" action='http://www.formmail-maker.com/generator.php' method='post' enctype='multipart/form-data'>
    <input type="hidden" name="uuid" value="<?php echo PHPFMG_ID; ?>">
    <input type="hidden" name="external_ini" value="<?php echo function_exists('phpfmg_formini') ?  phpfmg_formini() : ""; ?>">
    </form>

		</td>
	</tr>
</table>

<?php
    phpfmg_admin_footer();
}



function phpfmg_admin_header( $title = '' ){
    header( "Content-Type: text/html; charset=" . PHPFMG_CHARSET );
?>
<html>
<head>
    <title><?php echo '' == $title ? '' : $title . ' | ' ; ?>PHP FormMail Admin Panel </title>
    <meta name="keywords" content="PHP FormMail Generator, PHP HTML form, send html email with attachment, PHP web form,  Free Form, Form Builder, Form Creator, phpFormMailGen, Customized Web Forms, phpFormMailGenerator,formmail.php, formmail.pl, formMail Generator, ASP Formmail, ASP form, PHP Form, Generator, phpFormGen, phpFormGenerator, anti-spam, web hosting">
    <meta name="description" content="PHP formMail Generator - A tool to ceate ready-to-use web forms in a flash. Validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. ">
    <meta name="generator" content="PHP Mail Form Generator, phpfmg.sourceforge.net">

    <style type='text/css'>
    body, td, label, div, span{
        font-family : Verdana, Arial, Helvetica, sans-serif;
        font-size : 12px;
    }
    </style>
</head>
<body  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">

<table cellspacing=0 cellpadding=0 border=0 width="100%">
    <td nowrap align=center style="background-color:#024e7b;padding:10px;font-size:18px;color:#ffffff;font-weight:bold;width:250px;" >
        Form Admin Panel
    </td>
    <td style="padding-left:30px;background-color:#86BC1B;width:100%;font-weight:bold;" >
        &nbsp;
<?php
    if( phpfmg_user_isLogin() ){
        echo '<a href="admin.php" style="color:#ffffff;">Main Menu</a> &nbsp;&nbsp;' ;
        echo '<a href="admin.php?mod=user&func=logout" style="color:#ffffff;">Logout</a>' ;
    }; 
?>
    </td>
</table>

<div style="padding-top:28px;">

<?php
    
}


function phpfmg_admin_footer(){
?>

</div>

<div style="color:#cccccc;text-decoration:none;padding:18px;font-weight:bold;">
	:: <a href="http://phpfmg.sourceforge.net" target="_blank" title="Free Mailform Maker: Create read-to-use Web Forms in a flash. Including validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. " style="color:#cccccc;font-weight:bold;text-decoration:none;">PHP FormMail Generator</a> ::
</div>

</body>
</html>
<?php
}


function phpfmg_image_processing(){
    $img = new phpfmgImage();
    $img->out_processing_gif();
}


# phpfmg module : captcha
# ------------------------------------------------------
function phpfmg_captcha_get(){
    $img = new phpfmgImage();
    $img->out();
    //$_SESSION[PHPFMG_ID.'fmgCaptchCode'] = $img->text ;
    $_SESSION[ phpfmg_captcha_name() ] = $img->text ;
}



function phpfmg_captcha_generate_images(){
    for( $i = 0; $i < 50; $i ++ ){
        $file = "$i.png";
        $img = new phpfmgImage();
        $img->out($file);
        $data = base64_encode( file_get_contents($file) );
        echo "'{$img->text}' => '{$data}',\n" ;
        unlink( $file );
    };
}


function phpfmg_dd_lookup(){
    $paraOk = ( isset($_REQUEST['n']) && isset($_REQUEST['lookup']) && isset($_REQUEST['field_name']) );
    if( !$paraOk )
        return;
        
    $base64 = phpfmg_dependent_dropdown_data();
    $data = @unserialize( base64_decode($base64) );
    if( !is_array($data) ){
        return ;
    };
    
    
    foreach( $data as $field ){
        if( $field['name'] == $_REQUEST['field_name'] ){
            $nColumn = intval($_REQUEST['n']);
            $lookup  = $_REQUEST['lookup']; // $lookup is an array
            $dd      = new DependantDropdown(); 
            echo $dd->lookupFieldColumn( $field, $nColumn, $lookup );
            return;
        };
    };
    
    return;
}


function phpfmg_filman_download(){
    if( !isset($_REQUEST['filelink']) )
        return ;
        
    $filelink =  base64_decode($_REQUEST['filelink']);
    $file = PHPFMG_SAVE_ATTACHMENTS_DIR . basename($filelink);

    // 2016-12-05:  to prevent *LFD/LFI* attack. patch provided by Pouya Darabi, a security researcher in cert.org
    $real_basePath = realpath(PHPFMG_SAVE_ATTACHMENTS_DIR); 
    $real_requestPath = realpath($file);
    if ($real_requestPath === false || strpos($real_requestPath, $real_basePath) !== 0) { 
        return; 
    }; 

    if( !file_exists($file) ){
        return ;
    };
    
    phpfmg_util_download( $file, $filelink );
}


class phpfmgDataManager
{
    var $dataFile = '';
    var $columns = '';
    var $records = '';
    
    function __construct(){
        $this->dataFile = PHPFMG_SAVE_FILE; 
    }

    function phpfmgDataManager(){
        $this->dataFile = PHPFMG_SAVE_FILE; 
    }
    
    function parseFile(){
        $fp = @fopen($this->dataFile, 'rb');
        if( !$fp ) return false;
        
        $i = 0 ;
        $phpExitLine = 1; // first line is php code
        $colsLine = 2 ; // second line is column headers
        $this->columns = array();
        $this->records = array();
        $sep = chr(0x09);
        while( !feof($fp) ) { 
            $line = fgets($fp);
            $line = trim($line);
            if( empty($line) ) continue;
            $line = $this->line2display($line);
            $i ++ ;
            switch( $i ){
                case $phpExitLine:
                    continue;
                    break;
                case $colsLine :
                    $this->columns = explode($sep,$line);
                    break;
                default:
                    $this->records[] = explode( $sep, phpfmg_data2record( $line, false ) );
            };
        }; 
        fclose ($fp);
    }
    
    function displayRecords(){
        $this->parseFile();
        echo "<table border=1 style='width=95%;border-collapse: collapse;border-color:#cccccc;' >";
        echo "<tr><td>&nbsp;</td><td><b>" . join( "</b></td><td>&nbsp;<b>", $this->columns ) . "</b></td></tr>\n";
        $i = 1;
        foreach( $this->records as $r ){
            echo "<tr><td align=right>{$i}&nbsp;</td><td>" . join( "</td><td>&nbsp;", $r ) . "</td></tr>\n";
            $i++;
        };
        echo "</table>\n";
    }
    
    function line2display( $line ){
        $line = str_replace( array('"' . chr(0x09) . '"', '""'),  array(chr(0x09),'"'),  $line );
        $line = substr( $line, 1, -1 ); // chop first " and last "
        return $line;
    }
    
}
# end of class



# ------------------------------------------------------
class phpfmgImage
{
    var $im = null;
    var $width = 73 ;
    var $height = 33 ;
    var $text = '' ; 
    var $line_distance = 8;
    var $text_len = 4 ;

    function __construct( $text = '', $len = 4 ){
        $this->phpfmgImage( $text, $len );
    }

    function phpfmgImage( $text = '', $len = 4 ){
        $this->text_len = $len ;
        $this->text = '' == $text ? $this->uniqid( $this->text_len ) : $text ;
        $this->text = strtoupper( substr( $this->text, 0, $this->text_len ) );
    }
    
    function create(){
        $this->im = imagecreate( $this->width, $this->height );
        $bgcolor   = imagecolorallocate($this->im, 255, 255, 255);
        $textcolor = imagecolorallocate($this->im, 0, 0, 0);
        $this->drawLines();
        imagestring($this->im, 5, 20, 9, $this->text, $textcolor);
    }
    
    function drawLines(){
        $linecolor = imagecolorallocate($this->im, 210, 210, 210);
    
        //vertical lines
        for($x = 0; $x < $this->width; $x += $this->line_distance) {
          imageline($this->im, $x, 0, $x, $this->height, $linecolor);
        };
    
        //horizontal lines
        for($y = 0; $y < $this->height; $y += $this->line_distance) {
          imageline($this->im, 0, $y, $this->width, $y, $linecolor);
        };
    }
    
    function out( $filename = '' ){
        if( function_exists('imageline') ){
            $this->create();
            if( '' == $filename ) header("Content-type: image/png");
            ( '' == $filename ) ? imagepng( $this->im ) : imagepng( $this->im, $filename );
            imagedestroy( $this->im ); 
        }else{
            $this->out_predefined_image(); 
        };
    }

    function uniqid( $len = 0 ){
        $md5 = md5( uniqid(rand()) );
        return $len > 0 ? substr($md5,0,$len) : $md5 ;
    }
    
    function out_predefined_image(){
        header("Content-type: image/png");
        $data = $this->getImage(); 
        echo base64_decode($data);
    }
    
    // Use predefined captcha random images if web server doens't have GD graphics library installed  
    function getImage(){
        $images = array(
			'EDB7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QkNEQ1hDGUNDkMQCGkRaWRsdGkRQxRpdQSS6GFBdAJL7QqOmrUwNXbUyC8l9UHWtDJjmTcEiFsCA4RZHByxuRhEbqPCjIsTiPgCenM63LM1ObwAAAABJRU5ErkJggg==',
			'4376' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpI37prCGsIYGTHVAFgsRaWVoCAgIQBJjDGFodGgIdBBAEmOdwtDK0OjogOy+adNWha1aujI1C8l9ASB1UxhRzAsNBZoXwOggguIWkGnoYiKtrA0MKHrBbm5gQHXzQIUf9SAW9wEAglTLkMfDFcYAAAAASUVORK5CYII=',
			'EA09' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkMYAhimMEx1QBILaGAMYQhlCAhAEWNtZXR0dBBBERNpdG0IhImBnRQaNW1l6qqoqDAk90HUBUxF1Ssa6gqSQTMPaAWGHQ5obgkNAYqhuXmgwo+KEIv7AMcbzdBWoDkwAAAAAElFTkSuQmCC',
			'01D3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7GB0YAlhDGUIdkMRYAxgDWBsdHQKQxESmsAawNgQ0iCCJBbQygMUCkNwXtRSCspDch6YORUwExQ5MMdYABgy3MDqwhqK7eaDCj4oQi/sAlWXK8m1K2L8AAAAASUVORK5CYII=',
			'48B8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpI37pjCGsIYyTHVAFgthbWVtdAgIQBJjDBFpdG0IdBBBEmOdgqIO7KRp01aGLQ1dNTULyX0BUzDNCw3FNI9hCjYxTL1Y3TxQ4Uc9iMV9AALmzP74acqDAAAAAElFTkSuQmCC',
			'EF18' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QkNEQx2mMEx1QBILaBBpYAhhCAhAE2MMYXQQQVc3Ba4O7KTQqKlhq6atmpqF5D40dUhi2MzDawfUzUC3hDqguHmgwo+KEIv7ABqLzPo2xq8sAAAAAElFTkSuQmCC',
			'6BF4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WANEQ1hDAxoCkMREpoi0sjYwNCKLBbSINLo2MLSiiDWA1U0JQHJfZNTUsKWhq6KikNwXAjaP0QFFbyvIPMbQEAwxBmxuQREDuxlNbKDCj4oQi/sAa1PN/elCpX0AAAAASUVORK5CYII=',
			'38DF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAV0lEQVR4nGNYhQEaGAYTpIn7RAMYQ1hDGUNDkMQCprC2sjY6OqCobBVpdG0IRBUDqUOIgZ20Mmpl2NJVkaFZyO5DVYfbPCxi2NwCdTOq3gEKPypCLO4DABRLymnfDDcpAAAAAElFTkSuQmCC',
			'20EE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7WAMYAlhDHUMDkMREpjCGsDYwOiCrC2hlbUUXY2gVaXRFiEHcNG3aytTQlaFZyO4LQFEHhowOmGKsDZh2iDRguiU0FNPNAxV+VIRY3AcAbLfIXtEYRLAAAAAASUVORK5CYII=',
			'59A3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeElEQVR4nGNYhQEaGAYTpIn7QkMYQximMIQ6IIkFNLC2MoQyOgSgiIk0Ojo6NIggiQUGiDS6AmUCkNwXNm3p0tRVUUuzkN3XyhiIpA4qxtDoGhqAYl5AKwvYPGQxkSmsrawNgShuYQ1gDGFtCEBx80CFHxUhFvcBAIdyziXbNI6SAAAAAElFTkSuQmCC',
			'A2BB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDGUMdkMRYA1hbWRsdHQKQxESmiDS6NgQ6iCCJBbQyNLoi1IGdFLV01dKloStDs5DcB1Q3Bd280FCGAFYM8xgdMMVYG9D1BrSKhrqiuXmgwo+KEIv7AFdnzI/wmMaGAAAAAElFTkSuQmCC',
			'448E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpI37pjC0MoQyhgYgi4UwTGV0dHRAVscYwhDK2hCIIsY6hdEVSR3YSdOmLV26KnRlaBaS+wKmiLSimxcaKhrqimYeyC3odoDE0PVidfNAhR/1IBb3AQATxMkTp/rKiwAAAABJRU5ErkJggg==',
			'A25E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDHUMDkMRYA1hbWYEyyOpEpog0uqKJBbQyNLpOhYuBnRS1dNXSpZmZoVlI7gOqm8LQEIiiNzSUIQBdLKCV0YEVQwzoEkdHNDHRUIdQRhQ3D1T4URFicR8AgeLKRo93JxMAAAAASUVORK5CYII=',
			'3C46' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7RAMYQxkaHaY6IIkFTGFtdGh1CAhAVtkq0uAw1dFBAFlsikgDQ6CjA7L7VkZNW7UyMzM1C9l9QHWsjY4Y5rGGBjqIoNvR6IgiBnZLI6pbsLl5oMKPihCL+wBVvM0jBp9HxQAAAABJRU5ErkJggg==',
			'260E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WAMYQximMIYGIImJTGFtZQhldEBWF9Aq0sjo6IgixtAq0sDaEAgTg7hp2rSwpasiQ7OQ3Rcg2oqkDgwZHUQaXdHEWBtEGh3R7ADagOGW0FBMNw9U+FERYnEfAEPvyRxBc4OIAAAAAElFTkSuQmCC',
			'BCA9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QgMYQxmmMEx1QBILmMLa6BDKEBCALNYq0uDo6OgggqJOpIG1IRAmBnZSaNS0VUtXRUWFIbkPoi5gqgiaeayhAQ3oYq4NAWh2sDYCxVDcAnIzyDxkNw9U+FERYnEfAETlzuvZTeYRAAAAAElFTkSuQmCC',
			'23CC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WANYQxhCHaYGIImJTBFpZXQICBBBEgtoZWh0bRB0YEHW3crQytrA6IDivmmrwpauWpmF4r4AFHVgCOQBzUMVY23AtEOkAdMtoaGYbh6o8KMixOI+AOHCylfN4vDbAAAAAElFTkSuQmCC',
			'ABC5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7GB1EQxhCHUMDkMRYA0RaGR0CHZDViUwRaXRtEEQRC2gVaWVtYHR1QHJf1NKpYUtXrYyKQnIfRB3QDCS9oaEg81DFgOrAdqCJAd0SEBCAIgZys8NUh0EQflSEWNwHAL2PzEx3MsmqAAAAAElFTkSuQmCC',
			'1FE4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7GB1EQ11DHRoCkMRYHUQaWBsYGpHFRCFirQEoesFiUwKQ3Lcya2rY0tBVUVFI7oOoY3TA1MsYGoJpXgMWO1DEREOAYmhuHqjwoyLE4j4Ay4PKUMw8SxgAAAAASUVORK5CYII=',
			'2779' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WANEQ11DA6Y6IImJTGFodGgICAhAEgtoBYkFOogg624FwkZHmBjETdOAcOmqqDBk9wUA4RSGqch6GR0YHYCiDchirEAIFEWxQwQIWYEmILslNBQshuLmgQo/KkIs7gMAqInLbRlFsGkAAAAASUVORK5CYII=',
			'B61A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QgMYQximMLQiiwVMYW1lCGGY6oAs1irSCFQZEICiTqSBYQqjgwiS+0KjpoWtmrYyaxqS+wKmiLYiqYOb5zCFMTQEUwxVHcgtaGIgNzOGOqKIDVT4URFicR8AaeDMVzz/ifUAAAAASUVORK5CYII=',
			'416F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpI37pjAEMIQyhoYgi4UwBjA6Ojogq2MMYQ1gbUAVYwXqZW1ghImBnTRt2qqopVNXhmYhuS8ApA7NvNBQkN5AB3S3YBNDdwvDFNZQoJtRxQYq/KgHsbgPAKM8xvfJc670AAAAAElFTkSuQmCC',
			'509B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkMYAhhCGUMdkMQCGhhDGB0dHQJQxFhbWRsCHUSQxAIDRBpdgWIBSO4LmzZtZWZmZGgWsvtaRRodQgJRzAOLoZkX0MrayogmJjIF0y2sAZhuHqjwoyLE4j4A7cDLD7ULdZQAAAAASUVORK5CYII=',
			'1C57' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7GB0YQ1lDHUNDkMRYHVgbXYG0CJKYqINIA7oYI1CMdSpDQwCS+1ZmTVu1NDNrZRaS+0DqgKpaGdD0AsWmoIu5NgQEoIqxNjo6Ojogi4mGMIYyhDKiiA1U+FERYnEfAPJvyWXwrvMLAAAAAElFTkSuQmCC',
			'FB37' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QkNFQxhDGUNDkMQCGkRaWRsdGkRQxYAiAehirQxgUYT7QqOmhq2aumplFpL7oOpaGTDNm4JFLIABwy2ODqhiYDejiA1U+FERYnEfAD6Szneg5DGWAAAAAElFTkSuQmCC',
			'0E8C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7GB1EQxlCGaYGIImxBog0MDo6AEmEmMgUkQbWhkAHFiSxgFaQOkcHZPdFLZ0atip0ZRay+9DUwcVA5jEQsAObW7C5eaDCj4oQi/sAOl/J3Cc4zeQAAAAASUVORK5CYII=',
			'F316' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QkNZQximMEx1QBILaBBpZQhhCAhAEWNodAxhdBBAFWtlmMLogOy+0KhVYaumrUzNQnIfVB2GeQ5AvSIExYBumYLuFtYQxlAHFDcPVPhREWJxHwBotcyLjhHPMQAAAABJRU5ErkJggg==',
			'B7DB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QgNEQ11DGUMdkMQCpjA0ujY6OgQgi7UCxRoCHURQ1bWyAsUCkNwXGrVq2tJVkaFZSO4DqgtAUgc1j9GBFd08oGkYYlNEGljR3BIaABRDc/NAhR8VIRb3AQBQUc3BzIpZUgAAAABJRU5ErkJggg==',
			'2AB5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nM2QMQ6AMAhF6cAN8D506I6JLO7egw69gXoHPaXViUZHTeRvL5/wAuy3MfhTPvFDAUENKo7RHAbMkX1PCha0vmFQKKccE3u/dd0m3cbR+8nZYyO3G7jTZNIwtNqrNzwju3bF+6lWprDwD/73Yh78DvPTzHmijka3AAAAAElFTkSuQmCC',
			'0E7B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7GB1EQ1lDA0MdkMRYA0SAZKBDAJKYyBSImAiSWEArkNfoCFMHdlLU0qlhq5auDM1Cch9Y3RRGFPPAYgGMKOaB7GB0QBUDuYW1AVUv2M0NjChuHqjwoyLE4j4ASaXKcNrCNzAAAAAASUVORK5CYII=',
			'79D1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDGVpRRFtZW1kbHaaiiok0ujYEhKKITQGLwfRC3BS1dGkqkEB2H6MDYyCSOjBkbWBoRBcTaWDBEAtoALsFTQzs5tCAQRB+VIRY3AcAN1XNKWW+41AAAAAASUVORK5CYII=',
			'FB65' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QkNFQxhCGUMDkMQCGkRaGR0dHRhQxRpdGzDEWlkbGF0dkNwXGjU1bOnUlVFRSO4Dq3N0aBDBMC8Ai1iggwiGWxwCUN0HcjPDVIdBEH5UhFjcBwAKTc0jat/oFQAAAABJRU5ErkJggg==',
			'2916' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WAMYQximMEx1QBITmcLayhDCEBCAJBbQKtLoGMLoIICsGyjmMIXRAcV905YuzZq2MjUL2X0BjIFAdSjmAXWB9Yogu6WBBUNMpAHolimobgkNZQxhDHVAcfNAhR8VIRb3AQCKlsr2QEeMwwAAAABJRU5ErkJggg==',
			'599B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkMYQxhCGUMdkMQCGlhbGR0dHQJQxEQaXRsCHUSQxAIDIGIBSO4Lm7Z0aWZmZGgWsvtaGQMdQgJRzGNoZWh0QDMvoJWl0RFNTGQKpltYAzDdPFDhR0WIxX0A0AHLvTWoeeIAAAAASUVORK5CYII=',
			'F153' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QkMZAlhDHUIdkMQCGhgDWBsYHQJQxFiBYgwNIihiQL1TwTTcfaFRq6KWZmYtzUJyH0gdiAxA0wsiMczDIsbo6IjullAGIER280CFHxUhFvcBAJIoy8EG6hUdAAAAAElFTkSuQmCC',
			'53B5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QkNYQ1hDGUMDkMQCGkRaWRsdHRhQxBgaXRsCUcQCAxhA6lwdkNwXNm1V2NLQlVFRyO5rBalzaBBBtrkVZF4AilhAK8QOZDGRKSC3OAQgu481AORmhqkOgyD8qAixuA8AaK/McKvpvgwAAAAASUVORK5CYII=',
			'56AC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7QkMYQximMEwNQBILaGBtZQhlCBBBERNpZHR0dGBBEgsEqmBtCHRAdl/YtGlhS1dFZqG4r1W0FUkdVEyk0TUUVSwAJAZUh2yHyBRWoN4AFLewBjCGAMVQ3DxQ4UdFiMV9AKtcy+2188VLAAAAAElFTkSuQmCC',
			'4FCA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpI37poiGOoQ6tKKIhYg0MDoETHVAEmMEirE2CAQEIImxTgGJMTqIILlv2rSpYUtXrcyahuS+AFR1YBgaChYLDUFxC0hMEEUdSIzRIRBDjCHUEVVsoMKPehCL+wDvxssW/Run6AAAAABJRU5ErkJggg==',
			'F4BF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QkMZWllDGUNDkMQCGhimsjY6OjCgioWyNgSiiTG6IqkDOyk0aunSpaErQ7OQ3BfQINKKaZ5oqCuGeUC3YBPDdAvIzShiAxV+VIRY3AcAJI7LVDTkW0YAAAAASUVORK5CYII=',
			'D9D6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDGaY6IIkFTGFtZW10CAhAFmsVaXRtCHQQwCKG7L6opUuXpq6KTM1Ccl9AK2MgUB2aeQxgvSIoYiyYYljcgs3NAxV+VIRY3AcAUznOtinrbHgAAAAASUVORK5CYII=',
			'2D26' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7WANEQxhCGaY6IImJTBFpZXR0CAhAEgtoFWl0bQh0EEDWDRRzAIqhuG/atJVZKzNTs5DdFwBU18qIYh6jA1BsCpBEdksDUCwAVUykAegWBwYUvaGhoiGsoQEobh6o8KMixOI+AGZby4RlhDjfAAAAAElFTkSuQmCC',
			'3471' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7RAMYWllDA1qRxQKmMEwFklNRVLYyhALFQlHEpjC6MjQ6wPSCnbQyaunSVSCI7L4pIq0MUxhaUc0TDXUIQBdjaGV0YEB3SytrA6oY2M0NDKEBgyD8qAixuA8An7rLl7APHmUAAAAASUVORK5CYII=',
			'8D6B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7WANEQxhCGUMdkMREpoi0Mjo6OgQgiQW0ijS6Njg6iKCqA4oxwtSBnbQ0atrK1KkrQ7OQ3AdWh9W8QBTzsIlhcws2Nw9U+FERYnEfAHoBzHg8hUfwAAAAAElFTkSuQmCC',
			'D457' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QgMYWllDHUNDkMQCpjBMZQXSIshirQyhmGKMrqxTgTSS+6KWAkFm1sosJPcFtIq0gk1A0Ssa6gCyCdWOVtaGgAAGVLe0Mjo6OqC7mSGUEUVsoMKPihCL+wBsIsz42sHRTwAAAABJRU5ErkJggg==',
			'6EE2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WANEQ1lDHaY6IImJTBFpYG1gCAhAEgtoAYkxOoggizWA1TWIILkvMmpq2NLQVauikNwXAjGvEdmOgFawWCsDptgUBixuwXSzY2jIIAg/KkIs7gMAlwvLqt8AyLYAAAAASUVORK5CYII=',
			'CD08' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7WENEQximMEx1QBITaRVpZQhlCAhAEgtoFGl0dHR0EEEWaxBpdG0IgKkDOylq1bSVqauipmYhuQ9NHZJYIKp5WOzA5hZsbh6o8KMixOI+AO3bzZeeUXg7AAAAAElFTkSuQmCC',
			'26DF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDGUNDkMREprC2sjY6OiCrC2gVaWRtCEQRY2gVaUASg7hp2rSwpasiQ7OQ3Rcg2oqul9FBpNEVTYy1AVMMaAOGW0JDwW5GdcsAhR8VIRb3AQBIf8nPz96S0gAAAABJRU5ErkJggg==',
			'DC1A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QgMYQxmmMLQiiwVMYW10CGGY6oAs1irS4BjCEBCAJsYwhdFBBMl9UUunrVo1bWXWNCT3oalDFgsNQRNzQFcHcguaGMjNjKGOKGIDFX5UhFjcBwC5JM1Ok95jzwAAAABJRU5ErkJggg==',
			'8E63' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7WANEQxmA0AFJTGSKSAOjo6NDAJJYQKtIA2uDQ4MImjpWkByS+5ZGTQ1bOnXV0iwk94HVOTo0YJoXgGIeNjFsbsHm5oEKPypCLO4DAKqxzKFA5435AAAAAElFTkSuQmCC',
			'7D47' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7QkNFQxgaHUNDkEVbRVoZWh0aRFDFGh2moolNAYoFOjQEILsvatrKzMyslVlI7mN0EGl0bXRoRbaXtQEoFhowBVlMBCjm0OgQgCwW0AB0S6OjA6oY2M0oYgMVflSEWNwHAPG+zXhYP4/LAAAAAElFTkSuQmCC',
			'3D58' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7RANEQ1hDHaY6IIkFTBFpZW1gCAhAVtkq0ujawOgggiw2BSg2Fa4O7KSVUdNWpmZmTc1Cdh9QnUNDAIZ5Dg2BqOaB7UAVA7mF0dEBRS/IzQyhDChuHqjwoyLE4j4AHBXM7RErhAEAAAAASUVORK5CYII=',
			'3B27' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7RANEQxhCGUNDkMQCpoi0Mjo6NIggq2wVaXRtCEAVA6oDqgZChPtWRk0NW7Uya2UWsvtA6kAQzTyHKQxTMMQCGAIY0N3iwOiA7mbW0EAUsYEKPypCLO4DAI7Ey2RhvGDBAAAAAElFTkSuQmCC',
			'A9CF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7GB0YQxhCHUNDkMRYA1hbGR0CHZDViUwRaXRtEEQRC2gFiTHCxMBOilq6dGnqqpWhWUjuC2hlDERSB4ahoQyN6GIBrSxY7MB0C9A8kJtRxAYq/KgIsbgPALKxylNKBFAyAAAAAElFTkSuQmCC',
			'7335' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkNZQxhDGUMDkEVbRVpZGx0dUFS2MjQ6NASiik0BiTq6OiC7L2pV2KqpK6OikNzH6ADRLYKkl7UBJBKAIibSALEDWQyoAugWh4AAFDGQmxmmOgyC8KMixOI+AMV8zChSsp1ZAAAAAElFTkSuQmCC',
			'0F58' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7GB1EQ11DHaY6IImxBog0sDYwBAQgiYlMAYkxOoggiQW0AsWmwtWBnRS1dGrY0sysqVlI7gOpA5Io5kHEAlHMg9iBKgZyC6OjA4pesCtCGVDcPFDhR0WIxX0AhJ3LpHB1nNUAAAAASUVORK5CYII=',
			'F5C0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkNFQxlCHVqRxQIaRBoYHQKmOqCJsTYIBASgioWwAlWKILkvNGrq0qWrVmZNQ3IfUE+jK0IdHjERoBi6HaytmG5hDEF380CFHxUhFvcBALhBzX4UbfOdAAAAAElFTkSuQmCC',
			'41B1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpI37pjAEsIYytKKIhTAGsDY6TEUWYwxhDWBtCAhFFmMF6W10gOkFO2natFVRS0NXLUV2XwCqOjAMDWUAmdeK4RZsYmh6GaawhgLdHBowGMKPehCL+wC4xcpmoHzgWQAAAABJRU5ErkJggg==',
			'35EE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7RANEQ1lDHUMDkMQCpog0sDYwOqCobMUiNkUkBEkM7KSVUVOXLg1dGZqF7L4pDI2uGOZhExPBEAuYwtqKbq9oAGMIupsHKvyoCLG4DwCMqMlNOuIyYgAAAABJRU5ErkJggg==',
			'A139' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nM2Quw2AMAwFnSIbZCCzwaMwRaZxCjYgI9B4ShAIyUooQeDXnfw5mawrpT/lFb/AhCBU2bGIgFgYcCwtEaQjJ8cwE6gMFzuU8mrZquXJ+Z19XP2syM4U2u1TdDdaF8xRWuev/vdgbvw2dP3LJ5QgSMUAAAAASUVORK5CYII=',
			'B9A8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QgMYQximMEx1QBILmMLayhDKEBCALNYq0ujo6OgggqJOpNG1IQCmDuyk0KilS1NXRU3NQnJfwBTGQCR1UPMYGl1DA1HNa2UBmocmBnQLK5pekJuBYihuHqjwoyLE4j4AWwHO7P/AIPQAAAAASUVORK5CYII=',
			'3DDD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7RANEQ1hDGUMdkMQCpoi0sjY6OgQgq2wVaXRtCHQQQRabgiIGdtLKqGkrU1dFZk1Ddt8ULHqxmYdFDJtbsLl5oMKPihCL+wDsnMzLuv/BAAAAAABJRU5ErkJggg==',
			'A74A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7GB1EQx0aHVqRxVgDGIAiDlMdkMREpgDFpjoEBCCJBbQytDIEOjqIILkvaumqaSszM7OmIbkPqC6AtRGuDgxDQxkdWEMDQ0NQzGNtYEBTF9AqQpTYQIUfFSEW9wEAJrDM8UkFToAAAAAASUVORK5CYII=',
			'0209' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7GB0YQximMEx1QBJjDWBtZQhlCAhAEhOZItLo6OjoIIIkFtDK0OjaEAgTAzspaumqpUtXRUWFIbkPqG4Ka0PAVDS9AUCxBhEUO4CucXRAsQPolgZ0tzA6iIY6oLl5oMKPihCL+wDpEcswHdqEtwAAAABJRU5ErkJggg==',
			'E65B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDHUMdkMQCGlhbWRsYHQJQxEQaQWIiqGINrFPh6sBOCo2aFrY0MzM0C8l9AQ2irQwNgRjmOQDF0MxrdMUQY21ldHRE0QtyM0MoI4qbByr8qAixuA8AMKfMQruGQR0AAAAASUVORK5CYII=',
			'AD9C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7GB1EQxhCGaYGIImxBoi0Mjo6BIggiYlMEWl0bQh0YEESC2iFiCG7L2rptJWZmZFZyO4DqXMIgasDw9BQoFgDqhhInSOmHRhuCWjFdPNAhR8VIRb3AQA5XcyCRbf3FAAAAABJRU5ErkJggg==',
			'AAC0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7GB0YAhhCHVqRxVgDGEMYHQKmOiCJiUxhbWVtEAgIQBILaBVpdAWaIILkvqil01amrlqZNQ3JfWjqwDA0VDQUXQyiDtMORzS3gMQc0Nw8UOFHRYjFfQDaws1MCjc3FQAAAABJRU5ErkJggg==',
			'140E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7GB0YWhmmMIYGIImxOjBMZQgFyiCJiToARRwdHVD1MrqyNgTCxMBOWpm1dOnSVZGhWUjuY3QQaUVSBxUTDXXFEGNoxbQD6D50t4Rgunmgwo+KEIv7AP9Wxoe9HFeIAAAAAElFTkSuQmCC',
			'73CF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QkNZQxhCHUNDkEVbRVoZHQIdUFS2MjS6Ngiiik1haGVtYISJQdwUtSps6aqVoVlI7gOqQFYHhqwNIPNQxUQaMO0IaMB0S0AD2M2obhmg8KMixOI+AHUfyTAeJbrdAAAAAElFTkSuQmCC',
			'717B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QkMZAlhDA0MdkEVbGQMYGgIdAlDEWMFiIshiUxgCGBodYeogbooCwqUrQ7OQ3MfoAFQ3hRHFPNYGoFgAI4p5QDZQBFUMqCeAtQFVb0ADayhQDNXNAxR+VIRY3AcAKrfI1cPmFPYAAAAASUVORK5CYII=',
			'9246' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeklEQVR4nGNYhQEaGAYTpIn7WAMYQxgaHaY6IImJTGFtZWh1CAhAEgtoFQGqcnQQQBED6gx0dEB237Spq5auzMxMzUJyH6srwxTWRkcU8xhaGQJYQwMdRJDEBFoZHRgaHVHEgG5pANqCopc1QDTUAc3NAxV+VIRY3AcAZ0vMRmeg0OsAAAAASUVORK5CYII=',
			'478B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpI37poiGOoQyhjogi4UwNDo6OjoEIIkxAsVcGwIdRJDEWKcwtDIi1IGdNG3aqmmrQleGZiG5L2AKQwAjmnmhoYwOrGjmMUxhbcAUE2lA1wsSY0B380CFH/UgFvcBAHW+ys1FY4RjAAAAAElFTkSuQmCC',
			'84C3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WAMYWhlCHUIdkMREpjBMZXQIdAhAEgsAqmJtEGgQQVHH6MoKkkNy39KopUuXAqksJPeJTBFpRVIHNU801BUkh2pHK6YdDK3obsHm5oEKPypCLO4DAKCRzJloCVbXAAAAAElFTkSuQmCC',
			'3ED9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7RANEQ1lDGaY6IIkFTBFpYG10CAhAVtkKFGsIdBBBFpuCIgZ20sqoqWFLV0VFhSG7D6wuYKoIhnkBDVjEUOzA5hZsbh6o8KMixOI+ADuLzEkLsm47AAAAAElFTkSuQmCC',
			'A503' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7GB1EQxmmMIQ6IImxBog0MIQyOgQgiYlMEWlgdHRoEEESC2gVCWFtCGgIQHJf1NKpS5cCySwk9wW0MjS6ItSBYWgoRAzNvEZHDDtYW9HdEtDKGILu5oEKPypCLO4DAF+EzXqLORjfAAAAAElFTkSuQmCC',
			'AA55' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7GB0YAlhDHUMDkMRYAxhDWEEySGIiU1hb0cUCWkUaXacyujoguS9q6bSVqZmZUVFI7gOpc2gIaBBB0hsaKhqKLgY2ryHQAV3M0dEhIABNzCGUYarDIAg/KkIs7gMA4MfMsXcnnf0AAAAASUVORK5CYII=',
			'0A1F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7GB0YAhimMIaGIImxBjCGMIQAZZDERKawtjKiiQW0ijQ6TIGLgZ0UtXTayqxpK0OzkNyHpg4qJhqKLiYyBVMdawCmGKODSKNjqCOK2ECFHxUhFvcBAEx0yVdbGyQeAAAAAElFTkSuQmCC',
			'0825' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUMDkMRYA1hbGR0dHZDViUwRaXRtCEQRC2hlbWVoCHR1QHJf1NKVYatWZkZFIbkPrA6oUgRFr0ijwxRUMZAdDgGMDiLobnFgCEB2H8jNrKEBUx0GQfhREWJxHwDZncp1kAtjRgAAAABJRU5ErkJggg==',
			'4681' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpI37pjCGMIQytKKIhbC2Mjo6TEUWYwwRaWRtCAhFFmOdItIAVAfTC3bStGnTwlaFrlqK7L6AKaKtSOrAMDRUpNG1IQDV3inYxFgx9ELdHBowGMKPehCL+wAGnMuESxM4lgAAAABJRU5ErkJggg==',
			'A1C0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7GB0YAhhCHVqRxVgDGAMYHQKmOiCJiUxhDWBtEAgIQBILaGUAijE6iCC5L2opCK3MmobkPjR1YBgaiikGUYdpB7pbAlpZQ9HdPFDhR0WIxX0AUpbKL4ONQxkAAAAASUVORK5CYII=',
			'957C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7WANEQ1lDA6YGIImJTBEBkgEBIkhiAa0gXqADC6pYCEOjowOy+6ZNnbp01dKVWcjuY3VlaHSYwuiAYnMrUCwAVUygVQRoGiOKHSJTWFtZGxhQ3MIawBgCFENx80CFHxUhFvcBACCNyw2ddiK1AAAAAElFTkSuQmCC',
			'B6E9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDHaY6IIkFTGFtZW1gCAhAFmsVaWRtYHQQQVEn0oAkBnZSaNS0sKWhq6LCkNwXMEUUZN5UETTzXIE0FjE0OzDdgs3NAxV+VIRY3AcAulDMurWfGfcAAAAASUVORK5CYII=',
			'B234' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QgMYQxhDGRoCkMQCprC2sjY6NKKItYo0OgBJVHUMQFUOUwKQ3BcatWrpqqmroqKQ3AdUB1Tp6IBqHkMAQ0NgaAiKGKMDyCVobmlgBdmM4mbRUEc0Nw9U+FERYnEfAEEW0Dfrv0WnAAAAAElFTkSuQmCC',
			'3031' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7RAMYAhhDGVqRxQKmMIawNjpMRVHZygpUExCKIjZFpNGh0QGmF+yklVHTVmZNXbUUxX2o6qDmAcUaAlqx2IHNLShiUDeHBgyC8KMixOI+AIBdzII96O9xAAAAAElFTkSuQmCC',
			'A7FC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7GB1EQ11DA6YGIImxBjA0ujYwBIggiYlMAYkxOrAgiQW0MrSyAsWQ3Re1dNW0paErs5DdB1QXgKQODENDGR3QxQKAprFi2CECFEN1C1QMxc0DFX5UhFjcBwCEwsryzb+nyAAAAABJRU5ErkJggg==',
			'5D59' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7QkNEQ1hDHaY6IIkFNIi0sjYwBASgijW6NjA6iCCJBQYAxabCxcBOCps2bWVqZlZUGLL7WkUaHRoCpiLrhYo1IIsFtILsCECxQ2SKSCujowOKW1gDREMYQhlQ3DxQ4UdFiMV9AExnzQCITtUfAAAAAElFTkSuQmCC',
			'FA27' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkMZAhhCGUNDkMQCGhhDGB0dGkRQxFhbWYEkqphIowOQDEByX2jUtJVZIIjkPrC6VoZWBhS9oqEOUximMKCbFwB0D5qYowOjA7qYa2ggithAhR8VIRb3AQCTx81SbNAaewAAAABJRU5ErkJggg==',
			'821B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WAMYQximMIY6IImJTGFtZQhhdAhAEgtoFWl0BIqJoKhjaHSYAlcHdtLSqFVLV01bGZqF5D6gOiBEN48hACQmgiLG6IAuBnRLA7pe1gDRUEcgRHbzQIUfFSEW9wEAnJPK+Zb/QIkAAAAASUVORK5CYII=',
			'9837' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WAMYQxhDGUNDkMREprC2sjY6NIggiQW0igBFAtDEWFsZwKII902bujJs1dRVK7OQ3MfqClbXimIzxLwpyGICELEABgy3ODpgcTOK2ECFHxUhFvcBAPCSzIFBX8Q8AAAAAElFTkSuQmCC',
			'7592' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nM3QvRGAMAiGYVJkA9wHN8C70GQDt/hSZAMdwUKnVDv8KfUu0L0Nz0HbY0At7S8+s87IaBZfKyP0onprEYOwbxOnCAV7X56Xdcxbdr4gVCRp8Tcijgat3sLg0kMn3xSxnpZrC4ksWGrgfx/ui28HID/MOhrzdK0AAAAASUVORK5CYII=',
			'5ED7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QkNEQ1lDGUNDkMQCGkQaWBsdgCSaGJhEiAUGQMQCkNwXNm1q2NJVUSuzkN3XClbXimIzRGwKslgARCwAWUxkCsgtjg7IYqwBYDejiA1U+FERYnEfAAD4zHoO1gKgAAAAAElFTkSuQmCC',
			'CD4F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7WENEQxgaHUNDkMREWkVaGVodHZDVBTSKNDpMRRNrAIoFwsXATopaNW1lZmZmaBaS+0DqXBsx9bqGBmLagaYO7BY0MaibUcQGKvyoCLG4DwASA8v8qVeANAAAAABJRU5ErkJggg==',
			'F910' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkMZQximMLQiiwU0sLYyhDBMdUARE2l0DGEICEATc5jC6CCC5L7QqKVLs6atzJqG5L6ABsZAJHVQMYZGTDEWoBi6HUC3TEF3C2MIY6gDipsHKvyoCLG4DwBfys1BlMtxiQAAAABJRU5ErkJggg==',
			'F805' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QkMZQximMIYGIIkFNLC2MoQyOjCgiIk0Ojo6oomxtrI2BLo6ILkvNGpl2NJVkVFRSO6DqAOagGaeKxYxkB0iGG5hCEB1H8jNDFMdBkH4URFicR8Am1rMxaUvgCsAAAAASUVORK5CYII=',
			'386C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7RAMYQxhCGaYGIIkFTGFtZXR0CBBBVtkq0uja4OjAgiwGVMfawOiA7L6VUSvDlk5dmYXiPpA6R0cHBgzzArGKIduBzS3Y3DxQ4UdFiMV9AIJlyuWfV0RLAAAAAElFTkSuQmCC',
			'C4B4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WEMYWllDGRoCkMREWhmmsjY6NCKLBTQyhLI2BLSiiDUwugLVTQlAcl/UqqVLl4auiopCcl8A0ETWRkcHVL2ioa4NgaEhqHa0Au1AdwtQrwOKGDY3D1T4URFicR8ARprOvs02vlsAAAAASUVORK5CYII=',
			'523E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkMYQxhDGUMDkMQCGlhbWRsdHRhQxEQaHRoCUcQCAxgaHRDqwE4Km7Zq6aqpK0OzkN3XyjCFAc08oFgAA5p5Aa2MDuhiIlNYG9DdwhogGuqI5uaBCj8qQizuAwDmd8r5I/55tAAAAABJRU5ErkJggg==',
			'A48B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7GB0YWhlCGUMdkMRYAximMjo6OgQgiYlMYQhlbQh0EEESC2hldEVSB3ZS1NKlS1eFrgzNQnJfQKtIK7p5oaGioa4Y5jG0YtrBgKEXJIbu5oEKPypCLO4DAIGWyzP5lBE1AAAAAElFTkSuQmCC',
			'B834' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QgMYQxhDGRoCkMQCprC2sjY6NKKItYo0OgBJdHUMjQ5TApDcFxq1MmzV1FVRUUjug6hzdMA0LzA0BNMObG5BEcPm5oEKPypCLO4DAG5C0G9QJ/jMAAAAAElFTkSuQmCC',
			'8D4B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WANEQxgaHUMdkMREpoi0MrQ6OgQgiQW0ijQ6THV0EEFV1+gQCFcHdtLSqGkrMzMzQ7OQ3AdS59qIaZ5raCCKeWA7GjHsaGVA04vNzQMVflSEWNwHANNGzYcyLToQAAAAAElFTkSuQmCC',
			'B0A6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QgMYAhimMEx1QBILmMIYwhDKEBCALNbK2sro6OgggKJOpNG1IdAB2X2hUdNWpq6KTM1Cch9UHZp5QLHQQAcRNDtYG9DEgG5hbQhA0QtyM1AMxc0DFX5UhFjcBwC1Ic2rUW0uvAAAAABJRU5ErkJggg=='        
        );
        $this->text = array_rand( $images );
        return $images[ $this->text ] ;    
    }
    
    function out_processing_gif(){
        $image = dirname(__FILE__) . '/processing.gif';
        $base64_image = "R0lGODlhFAAUALMIAPh2AP+TMsZiALlcAKNOAOp4ANVqAP+PFv///wAAAAAAAAAAAAAAAAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh+QQFCgAIACwAAAAAFAAUAAAEUxDJSau9iBDMtebTMEjehgTBJYqkiaLWOlZvGs8WDO6UIPCHw8TnAwWDEuKPcxQml0Ynj2cwYACAS7VqwWItWyuiUJB4s2AxmWxGg9bl6YQtl0cAACH5BAUKAAgALAEAAQASABIAAAROEMkpx6A4W5upENUmEQT2feFIltMJYivbvhnZ3Z1h4FMQIDodz+cL7nDEn5CH8DGZhcLtcMBEoxkqlXKVIgAAibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkphaA4W5upMdUmDQP2feFIltMJYivbvhnZ3V1R4BNBIDodz+cL7nDEn5CH8DGZAMAtEMBEoxkqlXKVIg4HibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpjaE4W5tpKdUmCQL2feFIltMJYivbvhnZ3R0A4NMwIDodz+cL7nDEn5CH8DGZh8ONQMBEoxkqlXKVIgIBibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpS6E4W5spANUmGQb2feFIltMJYivbvhnZ3d1x4JMgIDodz+cL7nDEn5CH8DGZgcBtMMBEoxkqlXKVIggEibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpAaA4W5vpOdUmFQX2feFIltMJYivbvhnZ3V0Q4JNhIDodz+cL7nDEn5CH8DGZBMJNIMBEoxkqlXKVIgYDibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpz6E4W5tpCNUmAQD2feFIltMJYivbvhnZ3R1B4FNRIDodz+cL7nDEn5CH8DGZg8HNYMBEoxkqlXKVIgQCibbK9YLBYvLtHH5K0J0IACH5BAkKAAgALAEAAQASABIAAAROEMkpQ6A4W5spIdUmHQf2feFIltMJYivbvhnZ3d0w4BMAIDodz+cL7nDEn5CH8DGZAsGtUMBEoxkqlXKVIgwGibbK9YLBYvLtHH5K0J0IADs=";
        $binary = is_file($image) ? join("",file($image)) : base64_decode($base64_image); 
        header("Cache-Control: post-check=0, pre-check=0, max-age=0, no-store, no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: image/gif");
        echo $binary;
    }

}
# end of class phpfmgImage
# ------------------------------------------------------
# end of module : captcha


# module user
# ------------------------------------------------------
function phpfmg_user_isLogin(){
    return ( isset($_SESSION['authenticated']) && true === $_SESSION['authenticated'] );
}


function phpfmg_user_logout(){
    session_destroy();
    header("Location: admin.php");
}

function phpfmg_user_login()
{
    if( phpfmg_user_isLogin() ){
        return true ;
    };
    
    $sErr = "" ;
    if( 'Y' == $_POST['formmail_submit'] ){
        if(
            defined( 'PHPFMG_USER' ) && strtolower(PHPFMG_USER) == strtolower($_POST['Username']) &&
            defined( 'PHPFMG_PW' )   && strtolower(PHPFMG_PW) == strtolower($_POST['Password']) 
        ){
             $_SESSION['authenticated'] = true ;
             return true ;
             
        }else{
            $sErr = 'Login failed. Please try again.';
        }
    };
    
    // show login form 
    phpfmg_admin_header();
?>
<form name="frmFormMail" action="" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:380px;height:260px;">
<fieldset style="padding:18px;" >
<table cellspacing='3' cellpadding='3' border='0' >
	<tr>
		<td class="form_field" valign='top' align='right'>Email :</td>
		<td class="form_text">
            <input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" class='text_box' >
		</td>
	</tr>

	<tr>
		<td class="form_field" valign='top' align='right'>Password :</td>
		<td class="form_text">
            <input type="password" name="Password"  value="" class='text_box'>
		</td>
	</tr>

	<tr><td colspan=3 align='center'>
        <input type='submit' value='Login'><br><br>
        <?php if( $sErr ) echo "<span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
        <a href="admin.php?mod=mail&func=request_password">I forgot my password</a>   
    </td></tr>
</table>
</fieldset>
</div>
<script type="text/javascript">
    document.frmFormMail.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();
}


function phpfmg_mail_request_password(){
    $sErr = '';
    if( $_POST['formmail_submit'] == 'Y' ){
        if( strtoupper(trim($_POST['Username'])) == strtoupper(trim(PHPFMG_USER)) ){
            phpfmg_mail_password();
            exit;
        }else{
            $sErr = "Failed to verify your email.";
        };
    };
    
    $n1 = strpos(PHPFMG_USER,'@');
    $n2 = strrpos(PHPFMG_USER,'.');
    $email = substr(PHPFMG_USER,0,1) . str_repeat('*',$n1-1) . 
            '@' . substr(PHPFMG_USER,$n1+1,1) . str_repeat('*',$n2-$n1-2) . 
            '.' . substr(PHPFMG_USER,$n2+1,1) . str_repeat('*',strlen(PHPFMG_USER)-$n2-2) ;


    phpfmg_admin_header("Request Password of Email Form Admin Panel");
?>
<form name="frmRequestPassword" action="admin.php?mod=mail&func=request_password" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:580px;height:260px;text-align:left;">
<fieldset style="padding:18px;" >
<legend>Request Password</legend>
Enter Email Address <b><?php echo strtoupper($email) ;?></b>:<br />
<input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" style="width:380px;">
<input type='submit' value='Verify'><br>
The password will be sent to this email address. 
<?php if( $sErr ) echo "<br /><br /><span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
</fieldset>
</div>
<script type="text/javascript">
    document.frmRequestPassword.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();    
}


function phpfmg_mail_password(){
    phpfmg_admin_header();
    if( defined( 'PHPFMG_USER' ) && defined( 'PHPFMG_PW' ) ){
        $body = "Here is the password for your form admin panel:\n\nUsername: " . PHPFMG_USER . "\nPassword: " . PHPFMG_PW . "\n\n" ;
        if( 'html' == PHPFMG_MAIL_TYPE )
            $body = nl2br($body);
        mailAttachments( PHPFMG_USER, "Password for Your Form Admin Panel", $body, PHPFMG_USER, 'You', "You <" . PHPFMG_USER . ">" );
        echo "<center>Your password has been sent.<br><br><a href='admin.php'>Click here to login again</a></center>";
    };   
    phpfmg_admin_footer();
}


function phpfmg_writable_check(){
 
    if( is_writable( dirname(PHPFMG_SAVE_FILE) ) && is_writable( dirname(PHPFMG_EMAILS_LOGFILE) )  ){
        return ;
    };
?>
<style type="text/css">
    .fmg_warning{
        background-color: #F4F6E5;
        border: 1px dashed #ff0000;
        padding: 16px;
        color : black;
        margin: 10px;
        line-height: 180%;
        width:80%;
    }
    
    .fmg_warning_title{
        font-weight: bold;
    }

</style>
<br><br>
<div class="fmg_warning">
    <div class="fmg_warning_title">Your form data or email traffic log is NOT saving.</div>
    The form data (<?php echo PHPFMG_SAVE_FILE ?>) and email traffic log (<?php echo PHPFMG_EMAILS_LOGFILE?>) will be created automatically when the form is submitted. 
    However, the script doesn't have writable permission to create those files. In order to save your valuable information, please set the directory to writable.
     If you don't know how to do it, please ask for help from your web Administrator or Technical Support of your hosting company.   
</div>
<br><br>
<?php
}


function phpfmg_log_view(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    
    phpfmg_admin_header();
   
    $file = $files[$n];
    if( is_file($file) ){
        if( 1== $n ){
            echo "<pre>\n";
            echo join("",file($file) );
            echo "</pre>\n";
        }else{
            $man = new phpfmgDataManager();
            $man->displayRecords();
        };
     

    }else{
        echo "<b>No form data found.</b>";
    };
    phpfmg_admin_footer();
}


function phpfmg_log_download(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );

    $file = $files[$n];
    if( is_file($file) ){
        phpfmg_util_download( $file, PHPFMG_SAVE_FILE == $file ? 'form-data.csv' : 'email-traffics.txt', true, 1 ); // skip the first line
    }else{
        phpfmg_admin_header();
        echo "<b>No email traffic log found.</b>";
        phpfmg_admin_footer();
    };

}


function phpfmg_log_delete(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    phpfmg_admin_header();

    $file = $files[$n];
    if( is_file($file) ){
        echo unlink($file) ? "It has been deleted!" : "Failed to delete!" ;
    };
    phpfmg_admin_footer();
}


function phpfmg_util_download($file, $filename='', $toCSV = false, $skipN = 0 ){
    if (!is_file($file)) return false ;

    set_time_limit(0);


    $buffer = "";
    $i = 0 ;
    $fp = @fopen($file, 'rb');
    while( !feof($fp)) { 
        $i ++ ;
        $line = fgets($fp);
        if($i > $skipN){ // skip lines
            if( $toCSV ){ 
              $line = str_replace( chr(0x09), ',', $line );
              $buffer .= phpfmg_data2record( $line, false );
            }else{
                $buffer .= $line;
            };
        }; 
    }; 
    fclose ($fp);
  

    
    /*
        If the Content-Length is NOT THE SAME SIZE as the real conent output, Windows+IIS might be hung!!
    */
    $len = strlen($buffer);
    $filename = basename( '' == $filename ? $file : $filename );
    $file_extension = strtolower(substr(strrchr($filename,"."),1));

    switch( $file_extension ) {
        case "pdf": $ctype="application/pdf"; break;
        case "exe": $ctype="application/octet-stream"; break;
        case "zip": $ctype="application/zip"; break;
        case "doc": $ctype="application/msword"; break;
        case "xls": $ctype="application/vnd.ms-excel"; break;
        case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
        case "gif": $ctype="image/gif"; break;
        case "png": $ctype="image/png"; break;
        case "jpeg":
        case "jpg": $ctype="image/jpg"; break;
        case "mp3": $ctype="audio/mpeg"; break;
        case "wav": $ctype="audio/x-wav"; break;
        case "mpeg":
        case "mpg":
        case "mpe": $ctype="video/mpeg"; break;
        case "mov": $ctype="video/quicktime"; break;
        case "avi": $ctype="video/x-msvideo"; break;
        //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
        case "php":
        case "htm":
        case "html": 
                $ctype="text/plain"; break;
        default: 
            $ctype="application/x-download";
    }
                                            

    //Begin writing headers
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public"); 
    header("Content-Description: File Transfer");
    //Use the switch-generated Content-Type
    header("Content-Type: $ctype");
    //Force the download
    header("Content-Disposition: attachment; filename=".$filename.";" );
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".$len);
    
    while (@ob_end_clean()); // no output buffering !
    flush();
    echo $buffer ;
    
    return true;
 
    
}
?>