<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/logo.png" type="image/icon type">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>ACLC Library | Member Portal</title>
    <link rel="stylesheet" href="css/member-portal.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
</head>
<style>
    body {
        height: 100vh;
        margin: 0;
        padding: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-image: url('images/bg.jpg');
        background-size: cover;
        background-position: center;
        overflow-y: scroll;
    }
    body::-webkit-scrollbar {
        display: none;
    }
</style>
<body>
    <div class="container">
        <div class="title-container">
            <img src="images/logo.png">
            <h1 class="portal-title" >ACLC College of Tacloban - Library</h1>
        </div>
        <div class="time-container">
            <h2>Current Date and Time: <span id="current-time"></span></h2>
            <script>
                setInterval(() => {
                    let now = new Date();
                    let year = now.getFullYear().toString();
                    let month = (now.getMonth() + 1).toString().padStart(2, '0'); //months are zero-based, so add 1
                    let day = now.getDate().toString().padStart(2, '0');
                    let hours = now.getHours().toString().padStart(2, '0');
                    let minutes = now.getMinutes().toString().padStart(2, '0');
                    let seconds = now.getSeconds().toString().padStart(2, '0');
                    let currentDateTime = `${month}/${day}/${year} ${hours}:${minutes}:${seconds}`;
                    document.getElementById('current-time').textContent = currentDateTime;
                }, 1000);
            </script>
        </div>
        <div class="row">
            <div class="column">
                <!--content for the tracking system -->
                <?php include 'tracking.php'; ?>
            </div>
            <div class="column">
                <!--content for the monitoring system -->
                <?php include 'monitoring.php'; ?>
            </div>
        </div>
    </div>
</body>
</html>