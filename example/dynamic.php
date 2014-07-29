<!DOCTYPE html>
<html lang="fr_FR">
<head>
	<meta charset="UTF-8" />
	<title>Posibrain</title>
	<meta name="viewport" content="width=device-width, initial-scale=1"/>
	<meta name="author" content="Fylhan" />
		<!--[if lte IE 9]> 
		<script text="text/javascript" src="js/html5.min.js"></script>
		<link rel="stylesheet" type="text/css" href="css/ie.min.css" />
	<![endif]-->
	<link rel="stylesheet" type="text/css" title="Posibrain" href="css/style.css" />
	<link rel="shortcut icon" href="img/favicon.ico" type="image/ico" />
	</head>
<body>
<header id="header" role="header">
	<h1><a href="dynamic.php">Posibrain</a></h1>
</header>
	
<section id="content">

<article role="main-content">
	<h2>Speack to R. Sammy</h2>
	<form>
	<div>
    	<label for="pseudo">Pseudo</label>
    	<input type="text" id="pseudo" name="pseudo" placeholder="Your pseudo" />
    	<label for="msg" id="msgLabel" class="requis">Message</label>
    	<input type="text" id="msg" name="msg" placeholder="Your message" />
	</div>
	<div class="formEnd">
    	<input type="submit" id="sendMsg" value="Ok" />
    </div></form>
    <hr />
    <p id="discussion"></p>
</article>
</section>

<footer id="footer" role="footer">
<p>
	Posibrain - Open source LGPL2.1<br />
</p>
</footer>

<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/dynamic.js"></script>
</body>
</html>