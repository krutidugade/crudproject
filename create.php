<?php
require_once "config.php";
 
$title = $body = "";
$title_err = $body_err = "";
 
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Title validation
    $input_title = trim($_POST["title"]);
    if(empty($input_title)){
        $title_err = "Please enter a title.";
    } elseif(!filter_var($input_title, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $title_err = "Please enter a valid post title.";
    } else{
        $title = $input_title;
    }
    
    // Body validation
    $input_body = trim($_POST["body"]);
    if(empty($input_body)){
        $body_err = "Please enter the post body.";     
    } else{
        $body = $input_body;
    }
    
    // Looking for errors before inserting in DB
    if(empty($title_err) && empty($body_err)){

        // prepare statement
        $sql = "INSERT INTO posts (title, body) VALUES (?, ?)";
 
        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as params
            $stmt->bind_param("ss", $param_title, $param_body);
            $param_title = $title;
            $param_body = $body;
            
            // Try the prepared statement
            if($stmt->execute()){
                // Post created successfully, redirects to home page
                header("location: index.php");
                exit();
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        $stmt->close();
    }
    $mysqli->close();
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Post</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .wrapper{
            width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mt-5">Create Post</h2>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="title" class="form-control <?php echo (!empty($title_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $title; ?>">
                            <span class="invalid-feedback"><?php echo $title_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Body</label>
                            <textarea name="body" class="form-control <?php echo (!empty($body_err)) ? 'is-invalid' : ''; ?>"><?php echo $body; ?></textarea>
                            <span class="invalid-feedback"><?php echo $body_err;?></span>
                        </div>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="index.php" class="btn btn-secondary ml-2">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>