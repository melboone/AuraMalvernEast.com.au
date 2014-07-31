<?php
require_once( dirname(__FILE__).'/form.lib.php' );

define( 'PHPFMG_USER', "litcanu@gmail.com" ); // must be a email address. for sending password to you.
define( 'PHPFMG_PW', "48b6da" );

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
    $public_modules   = false !== strpos('|captcha|', "|{$mod}|", "|ajax|");
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
        
    $info =  @unserialize(base64_decode($_REQUEST['filelink']));
    if( !isset($info['recordID']) ){
        return ;
    };
    
    $file = PHPFMG_SAVE_ATTACHMENTS_DIR . $info['recordID'] . '-' . $info['filename'];
    phpfmg_util_download( $file, $info['filename'] );
}


class phpfmgDataManager
{
    var $dataFile = '';
    var $columns = '';
    var $records = '';
    
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
			'B08F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QgMYAhhCGUNDkMQCpjCGMDo6OiCrC2hlbWVtCEQVmyLS6IhQB3ZSaNS0lVmhK0OzkNyHpg5qnkijK7p5WO3AdAvUzShiAxV+VIRY3AcAKNXKeWQVjUEAAAAASUVORK5CYII=',
			'758B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkNFQxlCGUMdkEVbRRoYHR0dAtDEWBsCHUSQxaaIhCCpg7gpaurSVaErQ7OQ3MfowNDoiGYeawNDoyuaeSINIhhiAQ2srehuCWhgDMFw8wCFHxUhFvcBAIDgywLZ16yHAAAAAElFTkSuQmCC',
			'AD7A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDA1qRxVgDRID8gKkOSGIiU0QaHRoCAgKQxAJagWKNjg4iSO6LWjptZdbSlVnTkNwHVjeFEaYODENDgWIBjKEhaOY5OqCqA4q1sjagiwHdjCY2UOFHRYjFfQBj2c0Gph0qZAAAAABJRU5ErkJggg==',
			'CA85' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7WEMYAhhCGUMDkMREWhlDGB0dHZDVBTSytrI2BKKKNYg0Ojo6ujoguS9q1bSVWaEro6KQ3AdR59AggqJXNNQVJINih0ijK9AOERS3gPUGILuPNUSk0SGUYarDIAg/KkIs7gMA5XbMc2THTGwAAAAASUVORK5CYII=',
			'6CA7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WAMYQxmmMIaGIImJTGFtdAgF0khiAS0iDY6ODqhiQB4rkAxAcl9k1LRVS1dFrcxCcl/IFLC6VmR7A1qBYqEBU9DFXBsCAhjQ3OLaEOiA7mZWNLGBCj8qQizuAwDq5M2RvLrJWAAAAABJRU5ErkJggg==',
			'FF4A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QkNFQx0aHVqRxQIaRBoYWh2mOqCLTXUICEAXC3R0EEFyX2jU1LCVmZlZ05DcB1LH2ghXhxALDQwNQTcPizpixAYq/KgIsbgPAFIUzcBDUDMcAAAAAElFTkSuQmCC',
			'3F4F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7RANEQx0aHUNDkMQCpog0MLQ6OqCobAWKTUUTA6kLhIuBnbQyamrYyszM0Cxk9wHVsTZimscaGohpB5o6sFvQxEQDMMUGKvyoCLG4DwAOR8pXQi7HRwAAAABJRU5ErkJggg==',
			'7F0F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkNFQx2mMIaGIIu2ijQwhDI6MKCJMTo6oopNEWlgbQiEiUHcFDU1bOmqyNAsJPcxOqCoA0PWBkwxkQZMOwIaMN0CFpuC5r4BCj8qQizuAwCCQ8k0oOjRqAAAAABJRU5ErkJggg==',
			'45EF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpI37poiGsoY6hoYgi4WINLA2MDogq2PEIsY6RSQESQzspGnTpi5dGroyNAvJfQFTGBpd0fSGhmKKMUwRwSLG2opuL8MUxhCgm1HFBir8qAexuA8AFnvI7CKdAgQAAAAASUVORK5CYII=',
			'5242' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeklEQVR4nM2Quw2FMAxFTeENwj5mg4uUvOJtAFMkhTcIbEBBpsSlEZQg4dsd+XNkapfK9KW84pdiF6nIIo4hs5IKcGLBugYJjo2wyVFycH6/tW37NLe/91OqXKT4G8bACepdoJ3YxupZqJyNwTNGn6QMKX7gfw/mxu8A/nbNZsfnCE4AAAAASUVORK5CYII=',
			'BC71' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QgMYQ1lDA1qRxQKmsDY6NARMRRFrFWkAioWiqhNpYGh0gOkFOyk0atqqVUuBEMl9YHVTGFrRzWMIwBRzdGDAcItrA6oY2M0NDKEBgyD8qAixuA8AKkbOXj/D9J8AAAAASUVORK5CYII=',
			'BAA2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QgMYAhimMEx1QBILmMIYwhDKEBCALNbK2sro6OgggqJOpNG1IaBBBMl9oVHTVqauigJChPug6hpR7GgVDXUNDWhlQBEDq5vCgGlHAKqbQWKBoSGDIPyoCLG4DwCu8s9xi5VC+AAAAABJRU5ErkJggg==',
			'2D4A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WANEQxgaHVqRxUSmiLQytDpMdUASC2gVaQSKBAQg6waJBTo6iCC7b9q0lZmZmVnTkN0XINLo2ghXB4aMDkCx0MDQEGS3NADNQ1Mn0gB0C5pYaCjIzahiAxV+VIRY3AcAI8LMyRxZNLEAAAAASUVORK5CYII=',
			'BE44' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QgNEQxkaHRoCkMQCpog0MLQ6NKKItQLFpjq0YqgLdJgSgOS+0KipYSszs6KikNwHUsfa6OiAbh5raGBoCLod2NyCJobNzQMVflSEWNwHANlRz/LUjqTBAAAAAElFTkSuQmCC',
			'56F4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDAxoCkMQCGlhbWRsYGlHFRBqBYq3IYoEBIg1AsSkBSO4LmzYtbGnoqqgoZPe1igLNY3RA1svQKtLo2sAYGoJsB1iMAcUtIlPAbkERYw0AuhlNbKDCj4oQi/sAphrNQ7onu84AAAAASUVORK5CYII=',
			'E66A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkMYQxhCGVqRxQIaWFsZHR2mOqCIiTSyNjgEBKCKNbA2MDqIILkvNGpa2NKpK7OmIbkvoEG0ldXREaYObp5rQ2BoCKYYmjqQW1D1QtzMiCI2UOFHRYjFfQCRfMxrjuXFawAAAABJRU5ErkJggg==',
			'74C1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QkMZWhlCHVpRRFsZpjI6BExFEwtlbRAIRRGbwujKCpJBdl/U0qVLV61aiuw+RgeRViR1YMjaIBrqiiYmAuQD7UARCwCKAd2CIQZ0c2jAIAg/KkIs7gMA6qzLXEFipIoAAAAASUVORK5CYII=',
			'21F4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WAMYAlhDAxoCkMREpjAGsDYwNCKLBbSygsRakcUYWhlAYlMCkN03bVXU0tBVUVHI7gPZ0cDogKyX0QEsFhqC7JYGsHmobsEiFhrKGoouNlDhR0WIxX0A1/HKSc9ZnVgAAAAASUVORK5CYII=',
			'F309' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QkNZQximMEx1QBILaBBpZQhlCAhAEWNodHR0dBBBFWtlbQiEiYGdFBq1KmzpqqioMCT3QdQFTEXT2+gKsgnDDgc0O7C5BdPNAxV+VIRY3AcAPynNKHYNMiMAAAAASUVORK5CYII=',
			'B5D0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QgNEQ1lDGVqRxQKmiDSwNjpMdUAWawWKNQQEBKCqC2FtCHQQQXJfaNTUpUtXRWZNQ3JfwBSGRleEOqh52MREgGLodrC2orslNIAxBN3NAxV+VIRY3AcAU0XOwbaPZGYAAAAASUVORK5CYII=',
			'17F6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7GB1EQ11DA6Y6IImxOjA0ujYwBAQgiYmCxRgdBFD0MrSygkgk963MWjVtaejK1Cwk9wFVBADVoZjH6MDoANIrgiLG2oApJgIUQ3NLCFgMxc0DFX5UhFjcBwA/s8ghETy9zQAAAABJRU5ErkJggg==',
			'30FC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7RAMYAlhDA6YGIIkFTGEMYW1gCBBBVtnK2srawOjAgiw2RaTRFSiG7L6VUdNWpoauzEJxH6o6qHnYxDDtwOYWsJsbGFDcPFDhR0WIxX0AVGXJ2wbSO08AAAAASUVORK5CYII=',
			'ECB4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7QkMYQ1lDGRoCkMQCGlgbXRsdGlHFRBpcGwJa0cVYGx2mBCC5LzRq2qqloauiopDcB1Hn6IChtyEwNATTDmxuQRHD5uaBCj8qQizuAwCKJtCAtgm6qQAAAABJRU5ErkJggg==',
			'7D41' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QkNFQxgaHVpRRFtFWhlaHaaiiTU6THUIRRGbAhQLhOuFuClq2srMzKylyO5jdBBpdEWzg7UBKBYagCImAhRzQFMX0AB0C4YY2M2hAYMg/KgIsbgPAAYezdTubuc3AAAAAElFTkSuQmCC',
			'EA9C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QkMYAhhCGaYGIIkFNDCGMDo6BIigiLG2sjYEOrCgiIk0ugLFkN0XGjVtZWZmZBay+0DqHELg6qBioqEODehiIo2OWOxwRHNLaAjQPDQ3D1T4URFicR8Ay3DM+KmvKWEAAAAASUVORK5CYII=',
			'B52F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QgNEQxlCGUNDkMQCpog0MDo6OiCrC2gVaWBtCEQVmyISwoAQAzspNGrq0lUrM0OzkNwXMIWh0aGVEc08oNgUdDGRRocANLEprECdqGKhAYwhrKGobhmo8KMixOI+AJEpyrp2LINhAAAAAElFTkSuQmCC',
			'8D64' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7WANEQxhCGRoCkMREpoi0Mjo6NCKLBbSKNLo2OLSiqQOKMUwJQHLf0qhpK1OnroqKQnIfWJ2jowOmeYGhIRhiAdjcgiKGzc0DFX5UhFjcBwChXM8EAm+xUAAAAABJRU5ErkJggg==',
			'B39E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QgNYQxhCGUMDkMQCpoi0Mjo6OiCrC2hlaHRtCEQVm8LQyooQAzspNGpV2MrMyNAsJPeB1DGEBGKY54BuHlDMEcMOTLdgc/NAhR8VIRb3AQAkXMteYLz0TQAAAABJRU5ErkJggg==',
			'0625' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUMDkMRYA1hbGR0dHZDViUwRaWRtCEQRC2gVAZKBrg5I7otaOi1s1crMqCgk9wW0irYytALNQNXb6DAFVQxkh0MAowOyGNgtDgwByO4DuZk1NGCqwyAIPypCLO4DAIW+yjzf86faAAAAAElFTkSuQmCC',
			'2FAF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WANEQx2mMIaGIImJTBFpYAhldEBWF9Aq0sDo6IgixgAUY20IhIlB3DRtatjSVZGhWcjuC0BRB4aMDkCxUFQx1gZMdSJYxEJDMcUGKvyoCLG4DwAP88nCabPnDgAAAABJRU5ErkJggg==',
			'79D9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDGaY6IIu2srayNjoEBKCIiTS6NgQ6iCCLTUERg7gpaunS1FVRUWFI7mN0YAx0bQiYiqyXtYEBqDegAVlMpIEFJIZiR0ADplsCGrC4eYDCj4oQi/sAB57NAhFiraUAAAAASUVORK5CYII=',
			'2D5A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpIn7WANEQ1hDHVqRxUSmiLSyNjBMdUASC2gVaXRtYAgIQNYNEpvK6CCC7L5p01amZmZmTUN2X4BIo0NDIEwdGAJ1gcRCQ5Dd0gCyA1WdSINIK6OjI4pYaKhoCEMoI4rYQIUfFSEW9wEAxuvLsOBfyfcAAAAASUVORK5CYII=',
			'CE1A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WENEQxmmMLQii4m0ijQwhDBMdUASC2gUaWAMYQgIQBZrAKqbwuggguS+qFVTw1ZNW5k1Dcl9aOqQxUJD0OxAVwd2C5oYyM2MoY4oYgMVflSEWNwHAMwlyv3TwMRRAAAAAElFTkSuQmCC',
			'5D69' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkNEQxhCGaY6IIkFNIi0Mjo6BASgijW6Njg6iCCJBQaAxBhhYmAnhU2btjJ16qqoMGT3tQLVOTpMRdYLFgOZimwHRAzFDpEpmG5hDcB080CFHxUhFvcBAE/qzQoRqaBnAAAAAElFTkSuQmCC',
			'612B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGUMdkMREpjAGMDo6OgQgiQW0sAawNgQ6iCCLNQD1AsUCkNwXGbUqatXKzNAsJPeFTAGqa2VENa8VKDaFEdU8kFgAqpjIFJAIql6gS0JZQwNR3DxQ4UdFiMV9AAOcyMzZnbkGAAAAAElFTkSuQmCC',
			'C2D2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nM2QOw7AIAhAceAG9D4s3Wmii6fBwRvYI7h4ytIN045tIiQML3xegPEIhZXyFz+MIWKCkx2jihULizgmhcquB5NnCsZEyfnlMXq3mp2f9TW0DTzPirEK043AxhrMLnq7zM5b2lNIcYH/fZgvfhchAs2liQ4JMwAAAABJRU5ErkJggg==',
			'E97A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDA1qRxQIaWIH8gKkOKGIijQ4NAQEB6GKNjg4iSO4LjVq6NGvpyqxpSO4LaGAMdJjCCFMHFWNodAhgDA1BEWMBmoaujrWVtQFVDOxmNLGBCj8qQizuAwDEoM0FSJWlHAAAAABJRU5ErkJggg==',
			'958A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeUlEQVR4nGNYhQEaGAYTpIn7WANEQxlCGVqRxUSmiDQwOjpMdUASC2gVaWBtCAgIQBULYXR0dBBBct+0qVOXrgpdmTUNyX2srgyNjgh1ENjK0OjaEBgagiQm0CoCEkNRJzKFtZURTS9rAGMIQygjqnkDFH5UhFjcBwAAIssOKtblmQAAAABJRU5ErkJggg==',
			'BBFF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAT0lEQVR4nGNYhQEaGAYTpIn7QgNEQ1hDA0NDkMQCpoi0sjYwOiCrC2gVaXRFF0NVB3ZSaNTUsKWhK0OzkNxHtHmE7UC4GU1soMKPihCL+wC008rZyJqHkAAAAABJRU5ErkJggg==',
			'D4AA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QgMYWhmmADGSWMAUhqkMoQxTHZDFWhlCGR0dAgJQxBhdWRsCHUSQ3Be1FAhWRWZNQ3JfQKtIK5I6qJhoqGtoYGgIqh2Y6qZgioHcjC42UOFHRYjFfQDDhs2JPg/8igAAAABJRU5ErkJggg==',
			'571A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QkNEQx2mMLQiiwU0MDQ6hDBMdUATcwxhCAhAEgsMAOqbwuggguS+sGmrgHBl1jRk97UyBCCpg4oxOgDFQkOQ7WhlbUBXJzJFBEOMNUCkgTHUEdW8AQo/KkIs7gMASAPLGQYKbz0AAAAASUVORK5CYII=',
			'D71B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QgNEQx2mMIY6IIkFTGFodAhhdAhAFmtlaHQEiomgirUyTIGrAzspaumqaaumrQzNQnIfUF0AkjqoGKMDSAzVPNYGDLEpIg3oekMDRBoYQx1R3DxQ4UdFiMV9AK1fzIIAqTpVAAAAAElFTkSuQmCC',
			'FB4C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7QkNFQxgaHaYGIIkFNIi0MrQ6BIigigFVOTqwoKsLdHRAdl9o1NSwlZmZWcjuA6ljbYSrg5vnGhqIIebQiMWORnS3YLp5oMKPihCL+wAiu83Rb4cvpgAAAABJRU5ErkJggg==',
			'F289' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkMZQxhCGaY6IIkFNLC2Mjo6BASgiIk0ujYEOoigiDE0Ojo6wsTATgqNWrV0VeiqqDAk9wHVTQGaNxVNbwAryFQUMUYHoBiaHawNmG4RDXVAc/NAhR8VIRb3AQAC3Mzrd255jQAAAABJRU5ErkJggg==',
			'6C00' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7WAMYQxmmMLQii4lMYW10CGWY6oAkFtAi0uDo6BAQgCzWINLA2hDoIILkvsioaauWrorMmobkvpApKOogeluxi6Hbgc0t2Nw8UOFHRYjFfQAkLsz6d+Zg0wAAAABJRU5ErkJggg==',
			'2040' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WAMYAhgaHVqRxUSmMIYwtDpMdUASC2hlbWWY6hAQgKy7VaTRIdDRQQTZfdOmrczMzMyahuy+AJFG10a4OjBkdACKhQaiiLE2AO1oRLVDpAHolkZUt4SGYrp5oMKPihCL+wATXMwfnPOXdQAAAABJRU5ErkJggg==',
			'A10E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7GB0YAhimMIYGIImxBjAGMIQCZZDERKYARR0dUcQCWhkCWBsCYWJgJ0UtBaHI0Cwk96GpA8PQUEwxkDpsdqC7JaCVNRTdzQMVflSEWNwHAKiOyA6pRGvLAAAAAElFTkSuQmCC',
			'7086' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkMZAhhCGaY6IIu2MoYwOjoEBKCIsbayNgQ6CCCLTRFpdHR0dEBxX9S0lVmhK1OzkNzH6ABWh2Iea4NIoyvQPBEkMZEGiB3IYgENmG4BsjHdPEDhR0WIxX0AT8zK3F5R5bMAAAAASUVORK5CYII=',
			'8338' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7WANYQxhDGaY6IImJTBFpZW10CAhAEgtoZWh0aAh0EEFRxwAShakDO2lp1KqwVVNXTc1Cch+aOpzmYbcD0y3Y3DxQ4UdFiMV9AMXIzX8eD1nvAAAAAElFTkSuQmCC',
			'8D92' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WANEQxhCGaY6IImJTBFpZXR0CAhAEgtoFWl0bQh0EEFVBxQLaBBBct/SqGkrMzOjVkUhuQ+kziEkoNEBzTwHIMmAJubYEDCFAYtbMN3MGBoyCMKPihCL+wDbUM1+HyCJBQAAAABJRU5ErkJggg==',
			'1CCB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7GB0YQxlCHUMdkMRYHVgbHR0CHQKQxEQdRBpcGwSBJLJekQZWIBmA5L6VWdNWLV21MjQLyX1o6lDE0M3DtAOLW0Iw3TxQ4UdFiMV9ABX8yO4umq8iAAAAAElFTkSuQmCC',
			'DB28' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QgNEQxhCGaY6IIkFTBFpZXR0CAhAFmsVaXRtCHQQQRVrBZIwdWAnRS2dGrZqZdbULCT3gdW1MmCY5zCFEd28RocANDGQWxxQ9YLczBoagOLmgQo/KkIs7gMAWcjN2sQTCQMAAAAASUVORK5CYII=',
			'0E3C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7GB1EQxlDGaYGIImxBog0sDY6AEmEmMgUEC/QgQVJLKAVKNbo6IDsvqilU8NWTV2Zhew+NHUIMaB5DATswOYWbG4eqPCjIsTiPgDIcMsV7n4F3wAAAABJRU5ErkJggg==',
			'D864' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QgMYQxhCGRoCkMQCprC2Mjo6NKKItYo0ujY4tKKKsbayNjBMCUByX9TSlWFLp66KikJyH1ido6MDpnmBoSEYYgHY3IIihs3NAxV+VIRY3AcAX0HPmoGmMqoAAAAASUVORK5CYII=',
			'4D3F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpI37poiGMIYyhoYgi4WItLI2Ojogq2MMEWl0aAhEEWOdAhRDqAM7adq0aSuzpq4MzUJyXwCqOjAMDcU0j2EKVjEMt0DdjCo2UOFHPYjFfQANVstGNmF7cAAAAABJRU5ErkJggg==',
			'C963' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WEMYQxhCGUIdkMREWllbGR0dHQKQxAIaRRpdGxwaRJDFGkBiIBrhvqhVS5emTl21NAvJfQENjIGujg4NKOY1MAD1BqCa18iCIYbNLdjcPFDhR0WIxX0A4r/NkrKtp7sAAAAASUVORK5CYII=',
			'ABB6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDGaY6IImxBoi0sjY6BAQgiYlMEWl0bQh0EEASC2gFqXN0QHZf1NKpYUtDV6ZmIbkPqg7FvNBQiHkiqOZhE8NwS0ArppsHKvyoCLG4DwBgls1/czVrgQAAAABJRU5ErkJggg==',
			'7490' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkMZWhlAGFm0lWEqo6PDVAdUsVDWhoCAAGSxKYyurA2BDiLI7otaunRlZmTWNCT3MTqItDKEwNWBIWuDaKhDA6oYkN3KiGZHAEgMzS0BDVjcPEDhR0WIxX0A9CXLXSrxinEAAAAASUVORK5CYII=',
			'D442' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7QgMYWhkaHaY6IIkFTGGYytDqEBCALNbKEMow1dFBBEWM0ZUh0KFBBMl9UUuXLl2ZmbUqCsl9Aa0irayNDo0odrSKhrqGAk1FtQPklikMqG4BiQVgutkxNGQQhB8VIRb3AQBJ3s6nRH4M4QAAAABJRU5ErkJggg==',
			'A16F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGUNDkMRYAxgDGB0dHZDViUxhDWBtQBULaGUAijHCxMBOiloKRFNXhmYhuQ+sDs280FCQ3kAs5mGKobsloJU1FOhmFLGBCj8qQizuAwBfh8e7IRHyrAAAAABJRU5ErkJggg==',
			'07AE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7GB1EQx2mMIYGIImxBjA0OoQyOiCrE5nC0Ojo6IgiFtDK0MraEAgTAzspaumqaUtXRYZmIbkPqC4ASR1UjNGBNTQQzQ7WBnR1rAEiGGKMDmAxFDcPVPhREWJxHwCMbsosM2iKtAAAAABJRU5ErkJggg==',
			'AB23' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7GB1EQxhCGUIdkMRYA0RaGR0dHQKQxESmiDS6NgQ0iCCJBbSKtALJhgAk90UtnRq2amXW0iwk94HVgVUi9IaGijQ6TGFAN6/RIQBDrJXRgRHFLQGtoiGsoQEobh6o8KMixOI+ADQozT9GBMBBAAAAAElFTkSuQmCC',
			'BF5B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QgNEQ11DHUMdkMQCpog0sDYwOgQgi7VCxETQ1U2FqwM7KTRqatjSzMzQLCT3gdQxNARimAcSE8GwIxDDDkZHRxS9oQFAFaGMKG4eqPCjIsTiPgDhk8y3URal0AAAAABJRU5ErkJggg==',
			'8B93' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7WANEQxhCGUIdkMREpoi0Mjo6OgQgiQW0ijS6NgQ0iKCpYwWKBSC5b2nU1LCVmVFLs5DcB1LHEAJXBzfPAc08kJgjFjvQ3YLNzQMVflSEWNwHALH2zXMHm1C0AAAAAElFTkSuQmCC',
			'4CB4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpI37pjCGsoYyNAQgi4WwNro2OjQiizGGiDS4NgS0IouxThFpYG10mBKA5L5p06atWhq6KioKyX0BYHWODsh6Q0OBYg2BoSEobgHbgeqWKWC3oIlhcfNAhR/1IBb3AQCp+c8bNgSq6gAAAABJRU5ErkJggg==',
			'CE47' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WENEQxkaHUNDkMREWkUaGFodGkSQxAIagbypaGIgXqADkEa4L2rV1LCVmVkrs5DcB1LH2ujQyoCmlzU0YAoDuh2NDgEM6G5pdHTA4mYUsYEKPypCLO4DADrRzMt9rY1PAAAAAElFTkSuQmCC',
			'73DC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QkNZQ1hDGaYGIIu2irSyNjoEiKCIMTS6NgQ6sCCLTWFoZQWKobgvalXY0lWRWcjuY3RAUQeGrA0Q85DFRBow7QhowHRLQAMWNw9Q+FERYnEfAIIsy8beCWdGAAAAAElFTkSuQmCC',
			'D431' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QgMYWhlDGVqRxQKmMExlbXSYiiLWyhAKJENRxRhdGRodYHrBTopaunTpqqmrliK7L6BVpBVJHVRMNNQBZCqqHa0M6GJTGFpZ0fRC3RwaMAjCj4oQi/sA1AbOZZAUYV4AAAAASUVORK5CYII=',
			'06D4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDGRoCkMRYA1hbWRsdGpHFRKaINLI2BLQiiwW0ijQAxaYEILkvaum0sKWroqKikNwX0CraytoQ6ICmt9G1ITA0BM0OV6BLsLgFRQybmwcq/KgIsbgPAIBWzic9SMNVAAAAAElFTkSuQmCC',
			'3461' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7RAMYWhlCgRhJLGAKw1RGR4epKCqBqlgbHEJRxKYwurI2wPWCnbQyaunSpVNXLUVx3xSRVlZHh1ZU80RDXRsC0MQYWlnRxIBuaWVE0wt1c2jAIAg/KkIs7gMAF0HLcIvF4+0AAAAASUVORK5CYII=',
			'B62A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QgMYQxhCGVqRxQKmsLYyOjpMdUAWaxVpZG0ICAhAUScCJAMdRJDcFxo1LWzVysysaUjuC5gi2srQyghTBzfPYQpjaAi6WACaOpBbHFDFQG5mDQ1EERuo8KMixOI+AIKKzEMChrPTAAAAAElFTkSuQmCC',
			'FAB1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkMZAlhDGVqRxQIaGENYGx2mooqxtrI2BISiiok0ujY6wPSCnRQaNW1lauiqpcjuQ1MHFRMNdW0IaMUwD5sYhl6gWChDaMAgCD8qQizuAwBGRM8QKkY+GQAAAABJRU5ErkJggg==',
			'4FBE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpI37poiGuoYyhgYgi4WINLA2Ojogq2MEiTUEooixTkFRB3bStGlTw5aGrgzNQnJfwBRM80JDMc1jmIJDDE0vWAzdzQMVftSDWNwHAAUPyozfBAOZAAAAAElFTkSuQmCC',
			'3793' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7RANEQx1CGUIdkMQCpjA0Ojo6OgQgq2xlaHRtCGgQQRabwtDKChQLQHLfyqhV01ZmRi3NQnbfFIYAhhC4Oqh5jA4M6OYBTWNEEwuYItLAiOYW0QCgCjQ3D1T4URFicR8Aw87MhkskMG0AAAAASUVORK5CYII=',
			'B9D6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDGaY6IIkFTGFtZW10CAhAFmsVaXRtCHQQQFEHEUN2X2jU0qWpqyJTs5DcFzCFMRCoDs08BrBeERQxFkwxLG7B5uaBCj8qQizuAwASKs6EpEC4NAAAAABJRU5ErkJggg==',
			'946A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeElEQVR4nGNYhQEaGAYTpIn7WAMYWhlCgRhJTGQKw1RGR4epDkhiAUBVrA0OAQEoYoyurA2MDiJI7ps2denSpVNXZk1Dch+rq0grq6MjTB0EtoqGujYEhoYgiQm0MrSyNgSiqAO6pZURTS/EzYyo5g1Q+FERYnEfAH0EyqdXAv9aAAAAAElFTkSuQmCC',
			'B7B3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QgNEQ11DGUIdkMQCpjA0ujY6OgQgi7UCxRoCGkRQ1bWyNjo0BCC5LzRq1bSloauWZiG5D6guAEkd1DxGB1Z081pZGzDEpog0sKK5JTQAKIbm5oEKPypCLO4DAAvUzygGLj9OAAAAAElFTkSuQmCC',
			'885C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDHaYGIImJTGFtZW1gCBBBEgtoFWl0bWB0YEFXN5XRAdl9S6NWhi3NzMxCdh9IHUNDoAMDmnkOWMRcgWLodjA6OqC4BeRmhlAGFDcPVPhREWJxHwAT6ctXT/vBlwAAAABJRU5ErkJggg==',
			'788E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QkMZQxhCGUMDkEVbWVsZHR0dUFS2ijS6NgSiik1BUQdxU9TKsFWhK0OzkNzH6IBpHmsDpnkiWMQCGjD1BjRgcfMAhR8VIRb3AQCyu8mM+rMzXgAAAABJRU5ErkJggg==',
			'55C9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QkNEQxlCHaY6IIkFNIg0MDoEBASgibE2CDqIIIkFBoiEsAJViiC5L2za1KVLV62KCkN2XytDo2sDw1RkvVCxBmSxgFYRoJgAih0iU1hb0d3CGsAYgu7mgQo/KkIs7gMATuvMOga1ZvsAAAAASUVORK5CYII=',
			'A990' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGVqRxVgDWFsZHR2mOiCJiUwRaXRtCAgIQBILaAWJBTqIILkvaunSpZmZkVnTkNwX0MoY6BACVweGoaEMjQ4NqGIBrSyNjhh2YLoFaB6Gmwcq/KgIsbgPALFHzOAPv5ouAAAAAElFTkSuQmCC',
			'79C8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkMZQxhCHaY6IIu2srYyOgQEBKCIiTS6Ngg6iCCLTQGJMcDUQdwUtXRp6qpVU7OQ3MfowBiIpA4MWRsYgHoZUcwTaWDBsCOgAdMtAQ1Y3DxA4UdFiMV9AC/3zEFZmGhgAAAAAElFTkSuQmCC',
			'FB2F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkNFQxhCGUNDkMQCGkRaGR0dHRhQxRpdGwLRxVoZEGJgJ4VGTQ1btTIzNAvJfWB1rYwY5jlMwSIWgCEG1IkuJhrCGorqloEKPypCLO4DAI/2ysEn5xZpAAAAAElFTkSuQmCC',
			'FD33' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAUUlEQVR4nGNYhQEaGAYTpIn7QkNFQxhDGUIdkMQCGkRaWRsdHQJQxRodQCS6GFgU4b7QqGkrs6auWpqF5D40dfjNwxTD4hZMNw9U+FERYnEfAP+20Bp3MRCRAAAAAElFTkSuQmCC',
			'9962' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGaY6IImJTGFtZXR0CAhAEgtoFWl0bXB0EMEQA6pHct+0qUuXpk5dtSoKyX2sroyBro4Ojch2MLQyAPUGtCK7RaCVBSQ2hQGLWzDdzBgaMgjCj4oQi/sAeDPMSmn4nKAAAAAASUVORK5CYII=',
			'7CE0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QkMZQ1lDHVpRRFtZG10bGKY6oIiJNADFAgKQxaaINLA2MDqIILsvatqqpaErs6YhuQ+kAkkdGLI2YIqJNGDaEdCA6ZaABixuHqDwoyLE4j4ApW7L2qAJv7IAAAAASUVORK5CYII=',
			'8FDB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7WANEQ11DGUMdkMREpog0sDY6OgQgiQW0AsUaAh1E0NUBxQKQ3Lc0amrY0lWRoVlI7kNTh9M8nHaguYU1ACiG5uaBCj8qQizuAwCcDsyH6RQ35AAAAABJRU5ErkJggg==',
			'1295' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUMDkMRYHVhbGR0dHZDViTqINLo2BDqg6mUAibk6ILlvZdaqpSszI6OikNwHVDeFISSgQQRVbwBDA7oYEALtQBVjbWB0dAhAdp9oiGioQyjDVIdBEH5UhFjcBwAuZchbabLBGwAAAABJRU5ErkJggg==',
			'5216' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpIn7QkMYQximMEx1QBILaGBtZQhhCAhAERNpdAxhdBBAEgsMYGh0mMLogOy+sGmrlq6atjI1C9l9rUAbpjCimAcUCwCKOYgg29EKNAtNTGQKawNQN4pe1gDRUMdQBxQ3D1T4URFicR8AKJLLS8C4elUAAAAASUVORK5CYII=',
			'95CA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeElEQVR4nGNYhQEaGAYTpIn7WANEQxlCHVqRxUSmiDQwOgRMdUASC2gVaWBtEAgIQBULYQWqFEFy37SpU5cuXbUyaxqS+1hdGRpdEeogsBUsFhqCJCbQKgIUE0RRJzKFtZXRIRBFjDWAMYQh1BHVvAEKPypCLO4DADptyzJ11BZ3AAAAAElFTkSuQmCC',
			'35F5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7RANEQ1lDA0MDkMQCpog0sDYwOqCobMUiNkUkBCjm6oDkvpVRU5cuDV0ZFYXsvikMja5AWgTFPGxiIkAxRgdksYAprK2sDQwByO4TDWAE2ssw1WEQhB8VIRb3AQA9T8rKnV2OagAAAABJRU5ErkJggg==',
			'9125' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGUMDkMREpjAGMDo6OiCrC2hlDWBtCEQTA+ptCHR1QHLftKmrolatzIyKQnIfqytQXSvQXGSbQXqnoIoJgMQCGB1EUNwCEmEIQHYf0CWhrKEBUx0GQfhREWJxHwA+echCYrGOSwAAAABJRU5ErkJggg==',
			'5756' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7QkNEQ11DHaY6IIkFNDA0ujYwBARgiDE6CCCJBQYwtLJOZXRAdl/YtFXTlmZmpmYhu6+VIQCkGtk8hlaQvkAHEWQ7WlkbWNHERKaINDA6OqDoZQ0AqghlQHHzQIUfFSEW9wEA9HnLwyjgIXYAAAAASUVORK5CYII=',
			'A62C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeElEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGaYGIImxBrC2Mjo6BIggiYlMEWlkbQh0YEESC2gFqQh0QHZf1NJpYatWZmYhuy+gVbSVoZXRAdne0FCRRocpqGJA8xodAhjR7GAF6URxS0ArYwhraACKmwcq/KgIsbgPAEnLyvE3EtZ6AAAAAElFTkSuQmCC',
			'65DA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7WANEQ1lDGVqRxUSmiDSwNjpMdUASC2gBijUEBAQgizWIhLA2BDqIILkvMmrq0qWrIrOmIbkvZApDoytCHURvK1gsNARFTARDncgU1lbWRkcUMdYAxhDWUEYUsYEKPypCLO4DAEj3zPayx967AAAAAElFTkSuQmCC',
			'095A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDHVqRxVgDWFtZGximOiCJiUwRaXRtYAgIQBILaAWKTWV0EEFyX9TSpUtTMzOzpiG5L6CVMdChIRCmDirG0AgUCw1BsYMFaAeqOpBbGB0dUcRAbmYIZUQRG6jwoyLE4j4AD97LHWwxM40AAAAASUVORK5CYII=',
			'B1C3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QgMYAhhCHUIdkMQCpjAGMDoEOgQgi7WyBrA2CDSIoKhjAIoBaST3hUatilq6atXSLCT3oamDmgcRE8EQw7QD3S2hAayh6G4eqPCjIsTiPgDbncvsBo6JjAAAAABJRU5ErkJggg==',
			'FCDF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVElEQVR4nGNYhQEaGAYTpIn7QkMZQ1mBOARJLKCBtdG10dGBAUVMpMG1IRBDjBUhBnZSaNS0VUtXRYZmIbkPTR1eMUw7sLkF7GYUsYEKPypCLO4DALCQzILVx/UCAAAAAElFTkSuQmCC',
			'CF38' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7WENEQx1DGaY6IImJtIo0sDY6BAQgiQU0igDJQAcRZLEGIA+hDuykqFVTw1ZNXTU1C8l9aOoQYujmYbEDm1tYQ0QaGNHcPFDhR0WIxX0AHFDNteJoHMQAAAAASUVORK5CYII=',
			'B5AD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QgNEQxmmMIY6IIkFTBFpYAhldAhAFmsVaWB0dHQQQVUXwtoQCBMDOyk0aurSpasis6YhuS9gCkOjK0Id1DygWCi6mAimuimsrSA7kN0SGsAIshfFzQMVflSEWNwHAKBAzZophqONAAAAAElFTkSuQmCC'        
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