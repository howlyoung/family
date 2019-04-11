<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>错误提示</title>
</head>
<body>
<p><?php echo $message;?></p>
<?php foreach($trace as $t):?>
    <div>
        <p><?php echo $t['file'];?></p>
        <p><?php echo $t['line'];?></p>
        <p><?php echo $t['class'];?></p>
        <p><?php echo $t['function'];?></p>
    </div>
<?php endforeach;?>
</body>
</html>