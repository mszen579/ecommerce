<?php
// echo the current error reporting level


/*
- Manage members
- Add | Edit | Delete members
*/

/* we will copy the code from dashboard and paste it here as a start. dashboard.php is considered as a template for most of the pages because it has:
1- start session
2- If: there is a user logged in then  include the init.php and footer.php
3- Else: redirect to index.php
*/
//always start with session
session_start();
//adding page title
$pageTitle = 'Members';

if(isset($_SESSION['username'])){

    include 'init.php';
    

    // like what we have done in the page.php page
    $do = isset($_GET['do']) ? $_GET['do'] : 'manage';

    // start the manage page
    if ($do == 'manage'){
       // manage page;

    }elseif($do == 'Edit'){//edit page; 
        // forcing number for do=userid
        $userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0;
        // query from db
        $stmt = $con->prepare('SELECT * FROM users WHERE userid=? LIMIT 1');
        // execute the above query
        $stmt -> execute(array($userid));
        // we need to fetch the logged in user to edit his/her information 
        $row = $stmt -> fetch();
        // I use the rowcount to fetch the data if there is an id in the db
        $count = $stmt->rowCount();
        //incase if the user is available we will do the below action
        if($stmt->rowCount() > 0){?>      
            <!-- starting html to add a form -->
            <h1 class='text-center'>Edit Member</h1>
        <div class="container">
            <form class='form-horizontal' action='?do=update' method='POST'>
            <!-- inorder to update a specific user we need to select a specific ID for that user so we will send it via a hidden input -->
            <input type="hidden" name='userid' value="<?php echo $userid ?>">
            <!--  start add username -->
            <div class="form-group row">
            <label  class="col-sm-2 col-form-label">username</label>
            <div class="col-sm-10 col-md-8">
            <input type="text" name='username' class="form-control"  placeholder="<?php echo $row['username'];?>" autocomplete='off'>
            </div>
            </div><br>
            <!--  End add username -->
            
            <!--  start add Password -->
            <div class="form-group row">
            <label  class="col-sm-2 col-form-label">Password</label>
            <div class="col-sm-10 col-md-8">
            <input type="hidden" name='oldpassword' class="form-control" value="<?php echo $row['password'];?>">
            <input type="password" name='newpassword' class="form-control"  placeholder="Password" autocomplete='new-password'>
            </div>
            </div><br>
            <!--  End add Password -->
            
            <!--  start add Email -->
            <div class="form-group row">
            <label  class="col-sm-2 col-form-label">Email</label>
            <div class="col-sm-10 col-md-8">
            <input type="email" name='email' class="form-control"  placeholder="<?php echo $row['email'];?>">
            </div>
            </div><br>
            <!--  End add Email -->
            
            <!--  start add Full name -->
            <div class="form-group row">
            <label  class="col-sm-2 col-form-label">Full Name</label>
            <div class="col-sm-10 col-md-8">
            <input type="text" name='fullname' class="form-control"  placeholder="<?php echo $row['fullname'];?>">
            </div>
            </div><br>
            <!--  End add Full name -->
            
            <!--  start add Submit -->
            <div class="form-group row">
            <div class="col-sm-offset-2 col-sm-10">
            <input type="submit" value='save' class="btn btn-primary"  placeholder="Full Name">
            </div>
            </div>
            <!--  End add Submit -->
            
            </form>
        </div>
        <?php    
        }else{
            echo 'there is on such user to edit';
        }?>
<?php
   }

    elseif($do == 'update'){//update page

    

      // starting html to add a form
            echo "<h1 class='text-center'>Update Member</h1>";
            echo "<div class='container'>";
      //Security: checking if the user coming from a POST request
      if($_SERVER['REQUEST_METHOD'] == 'POST'){
        //get the variables from the POST
            $id         = $_POST['userid'];
            $user       = $_POST['username'];
            $email      = $_POST['email'];
            $fullname   = $_POST['fullname'];

        //password trick
        $password = '';
        //if the password did not change send the old password
        // if(empty($_POST['newpassword'])){
        //     $password = $_POST['oldpassword'];
        // }
        // else{ $password = sha1($_POST['newpassword']);}//else send the new password

        $password = empty($_POST['newpassword']) ? $_POST['oldpassword'] : sha1($_POST['newpassword']);
        

        //validate the form
        $formErrors = array();

        if(strlen($user)<3){
            //echo 'username can not be empty';
            $formErrors[] = 'username can not be less than 3 characters';
        }elseif(strlen($user)>15){
            $formErrors[] = 'username can not be bigger than 15 characters';
            // prevent special characters
        }elseif(preg_match("/([%\$#\*]+)/", $user)){
            $formErrors[] = 'username can not have special characters';
        }

        if(empty($user)){
            //echo 'username can not be empty';
            $formErrors[] = 'username can not be empty';
        }
        if(empty($fullname)){
            //echo 'fullname can not be empty';
            $formErrors[] = 'fullname can not be empty';
        }elseif(preg_match("/([%\$#\*]+)/", $fullname)){
            $formErrors[] = 'fullname can not have special characters';
        }
        if(empty($email)){
           //echo 'email can not be empty';
           $formErrors[] = 'email can not be empty';
        }

        foreach($formErrors as $error){
            echo '<p class="alert alert-danger">' . $error . '</p>'. '<br>' ;
        }
        
        //prevent updating IF there is an error
        if(empty($formErrors)){
            //update in database with the above info
            $stmt = $con->prepare("UPDATE users SET username = ?, email = ?, fullname = ?, password=? WHERE userid = ?");
            $stmt -> execute(array($user, $email, $fullname, $password, $id));

            //echo success message
            echo '<div class="alert alert-success">'. $stmt -> rowCount() . ' ' . 'record updated' . '<div>' ;
        }
          
      }else{
          echo 'you can not browse this page directly';
      }
    };

    //end styling
    echo '</div>';  

    include $tpl . "footer.php";

    }else{
        //to redirect non authorized users to index page
        header ('location: index.php'); //route
        exit();
    }

  

