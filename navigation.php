
<head>
 <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
   
    

    <title>Guest Registration</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">

    <link href="css/ie10-viewport-bug-workaround.css" rel="stylesheet">

    <link href="css/custom.css" rel="stylesheet">

    <script src="css/ie-emulation-modes-warning.js"></script>

	<?php 
	if (strpos($_SERVER['SCRIPT_NAME'], 'login.php') == true){
		?>
		<link rel="stylesheet" href="css/signin.css">
        
    <?php }?>
</head>
<?php

if(empty($_SESSION['user'])){ } else {
	?>
<body>    


    

    
    		<nav class="navbar navbar-fixed-top navbar-inverse no-print" style="background-color: #0275D8;">
              <div class="container">
                <div class="navbar-header">
                  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                  </button>
                   <a class="navbar-brand" href="index.php"> Guest Registration</a>
                </div>
            
               
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                  <ul class="nav navbar-nav">
                   
                   <li><a href="#"></a></li>
            	   <li><a href="#"></a></li>
                    
                  </ul>
                  
                  <ul class="nav navbar-nav navbar-right">
                     <li><a href="<?php echo $url; ?>index.php?logout">Logout</a></li>
                    
                  </ul>
                </div>
              </div>
            </nav>
    
    
  
    
    
 
        

<?php }?>

        