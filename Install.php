<?php 
use \EvoPhp\Resources\User;

if (preg_match('/\.(?:png|jpg|jpeg|css|js|woff|woff2)$/', $_SERVER['REQUEST_URI'])) {
    return false;
}

if ( !defined('ABSPATH') )
	define('ABSPATH', realpath(dirname(__FILE__)) . '/');

require_once("EvoPhp/autoload.php");

if(isset($_POST['submit'])) {
	extract($_POST);
	$user = str_replace(" ", "", trim($db_user));
    $pass = str_replace(" ", "", trim($db_pass));
	$user = explode("||", $user);
	$pass = explode("||", $pass);
    $db_user = array_map(function($username, $password) {
        $password = ($password == null) ? ($pass[0] ?? "") : $password;
        return ["username" => $username, "password" => $password];
    },$user, $pass);
    $db_user = \EvoPhp\Api\Operations::varExport($db_user, true);
	$myfile = fopen("EvoPhp/Database/Config.php", "w") or die("Unable to open file!");
	$txt = '<?php 

    namespace EvoPhp\Database;
    
    use EvoPhp\Api\Config as EvoConfig;
    
    /**
     * Configuration file
     * Do all your api configuration in this file
     */
    class Config
    {
        /*
        * Database host
        */
        private $db_host = "'.$db_host.'";
    
        /*
        * Database name
        */
        private $db_name = "'.$db_name.'";
    
        /*
        * Array of Database users & passwords
        */
        private $db_users = '.$db_user.';
    
        /*
        * Change to false if you want to go live
        */
        private $devmode = false;
    
        public $evoConfig;
    
    
        public function __construct(){
            $this->evoConfig = new EvoConfig;
        }
    
        public function __get($prop) {
            return $this->$prop;
        }
        
    }';
	fwrite($myfile, $txt);
	fclose($myfile);
	\EvoPhp\Resources\User::createTable();
    \EvoPhp\Resources\Post::createTable();
    \EvoPhp\Resources\Records::createTable();
    \EvoPhp\Resources\Options::createTable();
    \EvoPhp\Actions\Action::createTable();
    \EvoPhp\Actions\Notifications\Log::createTable();
    \EvoPhp\Api\Modules::install();
    $user = new User;
    $user->new([
        "role" => "software_engineer",
        "status" => "active",
        "surname" => $surname,
        "middle_name" => "",
        "other_names" => $other_names,
        "email" => $email,
        "password" => $password
    ]);
	header("Location: \accounts");
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Installation</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
	<div class="container">
		<div class="row justify-content-md-center">
            <?php
                if(file_exists("EvoPhp/Database/Config.php")) :
                    ?>
                    <p>Installation complete <a href="/">Go to index page</a></p>
                    <?php
                else: 
            ?>
			<div class="col-md-8 mt-4">
				<form method="post" action="">
					<fieldset>
						<legend>Database credentials</legend>
						<div class="row mb-2">
							<div class="col-md-6">
								<label for="db_name">Database Name</label>
								<input type="text" name="db_name" id="db_name" class="form-control" required>
							</div>
							<div class="col-md-6">
								<label for="db_user">Database User Name</label>
								<input type="text" name="db_user" id="db_user" class="form-control" required>
								<small>Seperate with || for multiple users</small>
							</div>
						</div>
						<div class="row mb-2">
							<div class="col-md-6">
								<label for="db_pass">Database Password</label>
								<input type="password" name="db_pass" id="db_pass" class="form-control">
								<small>Seperate with || for multiple users</small>
							</div>
							<div class="col-md-6">
								<label for="db_host">Database Host</label>
								<input type="text" name="db_host" id="db_host" class="form-control" value="localhost" required>
							</div>
						</div>
					</fieldset>
					<fieldset>
						<legend>Software engineer credentials</legend>
						<div class="row mb-2">
							<div class="col-md-">
								<label for="surname">Surname</label>
								<input type="text" name="surname" id="surname" class="form-control" required>
							</div>
                            <div class="col-md-">
								<label for="middle_name">Middle Name</label>
								<input type="text" name="middle_name" id="middle_name" class="form-control" required>
							</div>
							<div class="col-md-">
								<label for="other_names">Other Names</label>
								<input type="text" name="other_names" id="other_names" class="form-control" required>
							</div>
						</div>
						<div class="row mb-2">
							<div class="col-md-6">
								<label for="email">Email</label>
								<input type="email" name="email" id="email" class="form-control" required>
							</div>
                            <div class="col-md-6">
                                <label for="password">Password</label>
                                <input type="password" name="password" id="password" class="form-control" required>
							</div>
						</div>
					</fieldset>
					<input type="submit" name="submit" value="Submit" class="btn btn-primary">
				</form>
			</div>
            <?php endif; ?>
		</div>
	</div>
	
</body>
</html>