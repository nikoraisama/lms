<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/logo.png" type="image/icon type">
    <title>Library Management System</title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <style>
        body {
            height: 100vh;
            margin: 0;
            background-image: url('images/bg.jpg');
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <img src="images/logo.png">
        <h1>Welcome to the Library Management System</h1>
        <button class="index-btn" onclick="window.location.href = 'admin_login.php';">Login</button>
        <button class="index-btn" onclick="window.location.href = 'member_portal.php';">Book Keep</button>
        <div class="credits" >
            <p>Library Management System</p>
            <p>© <?php echo date('Y'); ?> ACLC College of Tacloban</p>
            <a class="dev" href="about.php">Development Team</a></p>
        </div>
    </div>
</body>
</html>