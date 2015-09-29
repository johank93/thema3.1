<?php  include 'inc/functions.php';  //error_reporting(0); //session_start(); ?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>FirstPage</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- jQuery Version 1.11.1 -->
    <script src="boot/js/jquery.js"></script>
    
    <!-- Bootstrap Core JavaScript -->
    <script src="boot/js/bootstrap.min.js"></script>
    
    <script type="text/javascript" src="js/MooTools-Core-1.5.2.js"></script>
    
    <script src="js/Chart.js"></script>
    <script src="js/Chart.Line.js"></script>

</head>

<body>

    <!-- Navigation -->
<?php include 'inc/navbar.php'; ?>

    <!-- Page Content -->
    <div class="container">
        <!--Content pageload-->
        <?php pageload(); ?>

    </div>
    <!-- /.container -->

</body>

</html>
