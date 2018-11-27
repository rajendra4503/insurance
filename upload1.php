<?php
if(isset($_POST['sub']))
{
$userid	 			= "vishnu";
$file 				= (empty($_FILES['filename']['name'])) 	? '' : $_FILES['filename']['name'];

$path1              = "uploads/folder/";
$path2              = "uploads/folder/$userid/";

	if(!is_dir($path1)){
    mkdir($path1, 0777, true);
    }
    if(!is_dir($path2)){
    mkdir($path2, 0777, true);
    }
    
    if ($file && is_dir($path2)){
        $random_no      = mt_rand();
        $split_filename = explode('.', $file);
        $file_name      = reset($split_filename);
        $file_extension = end($split_filename);
        $fullfilename   = $random_no."~_".$file_name.'.'.$file_extension;
        //echo $fullfilename;exit;
        $file_path      = $path2.$fullfilename;
        move_uploaded_file($_FILES['filename']['tmp_name'], $file_path);

        echo "{".json_encode('ADD_FOLDER').':'.json_encode('1')."}";  
    }
    else
    {
    	echo "{".json_encode('ADD_FOLDER').':'.json_encode('0')."}"; 
    }
    
}	
?>
<html>
<body>
<form action="upload1.php" method="post" enctype="multipart/form-data">
<label for="file">Filename:</label>
<input type="file" name="filename" id="filename"><br>
<input type="submit" name="sub" value="Submit">
</form>

</body>
</html>