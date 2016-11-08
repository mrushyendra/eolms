<?php
include_once('head_start.php');
    $PageTitle = "Login";
?>
    <style type="text/css" >
        body#login{
		width:350px;
		margin:120px auto;
		padding:2%;
	}
	body#login #content{
		min-height:50px;
		padding:8px;
	}
	body#login form label{
		padding:0;
		margin:0;
		width:100%;
	}
	body#login form section{
		width:100%;
	}
	body#login form section div{
		width:90%;
		float:none;
		padding:0 4% 6px 4%;
		border:0;
	}
	body#login form section > div{
		width:90% !important;
	}
	body#login form section div input{
		width:100% !important;
	}
	body#login form section div input#remember{
		width:auto !important;
	}
	body#login form section div.checker{
		width:auto !important;
		padding:0;
		margin:0;
	}
	body#login form section label{
		padding:3% 2% 1%;
		width:90% !important;
		float:none;
	}
	body#login form section label.checkbox div{
		width:10px;
		padding:0;
		margin:0;
	}
	body#login form section div label{
		width:80% !important;
	}
    </style>

<?php
include_once('head_end.php');
include_once('database_connect.php');

/*
// Temp code for creating users.
$a = $mysql->stmt_init();
$a->prepare("INSERT INTO users VALUES(?, ?);");
$u = "admin";
$pw = hash("sha512", $u . "admin");
$a->bind_param("ss", $u, $pw);
$a->execute();
$a->close();
//*/

// If not logged in.
if (!isset($_SESSION["SessionId"]) Or !isset($_SESSION["Username"])) {
?>

<body id="login">
    <header><div id="logo"></div></header>
    <section id="content">
        <form action="login_result.php" method="post">
            <fieldset>                
                    <?php if (isset($_GET["login"]) && $_GET["login"] == "timeout") { ?>
                    <section>
                    <div style='color: red'>Your session has timed out. Please log in again.</div>
                    </section>
                    <?php } else if (isset($_GET["login"]) && $_GET["login"] == "failed") { ?>
                    <section>
                    <div style='color: red'>Incorrect username or password!</div>
                    </section>
                    <?php } else if (isset($_GET["login"]) && $_GET["login"] == "repeat") { ?>
                    <section>
                    <div style='color: red'>This account is already logged into another device/browser.</div>
                    </section>
                    <?php } ?>
                <section>
                    <div><label for="username">Username</label><input type="text" name="username" maxlength="64"></div>
                </section>
                <section>
                    <div><label for="password">Password</label><input type="password" name="password" ></div>	
                </section>
                <section>
                    <div><button name="Login" type="submit">Login</button></div>
                </section>
            </fieldset>
        </form>
    </section>
    <footer>Electronic Ops Log Management System</footer>
</body>

</html>
<?php
// TODO:  Logged in, redirect to home page.
} else {
    header("Location: home.php");
    die();
}
?>


