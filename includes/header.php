<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="en">
<head>
    <title><?php if (isset($title)) {echo $title;} else {echo "The Gilbert | 14-18 Gilbert Road, Preston";} ?></title>
    <meta http-equiv="content-type" content="text/html; charset=<?php echo defined('PHPFMG_CHARSET') ? PHPFMG_CHARSET : 'UTF-8'; ?>">
    <link href="css/style.css" rel="stylesheet" type="text/css" media="screen">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js"></script>
    <script type="text/javascript" src="js/ciclye.js"></script>
    <script type="text/javascript">
$("nav li").click(function ( e ) {
    e.preventDefault();
    $("nav li a.active").removeClass("active"); //Remove any "active" class  
    $("a", this).addClass("active"); //Add "active" class to selected tab  

    // $(activeTab).show(); //Fade in the active content  
});
    </script>
</head>
<body>