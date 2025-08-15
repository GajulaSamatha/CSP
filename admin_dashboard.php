<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
            body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #34495e;
            color: #fff;
            padding: 15px;
            text-align: center;
            font-size: 22px;
        }

        /* Navbar */
        .navbar {
            background-color: #2c3e50;
            overflow: hidden;
            display: flex;
            justify-content: center;
        }

        .navbar a {
            display: block;
            color: white;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
            transition: background 0.3s;
        }

        .navbar a:hover {
            background-color: #1abc9c;
        }

        .container {
            max-width: 1100px;
            margin: auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0px 3px 6px rgba(0,0,0,0.1);
            margin-top: 20px;
            border-radius: 8px;
        }

        h2 {
            color: #34495e;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th, td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f8f9fa;
        }

        button {
            padding: 8px 15px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }

        .btn-approve {
            background-color: #27ae60;
            color: white;
        }

        .btn-reject {
            background-color: #e74c3c;
            color: white;
        }

        form {
            margin-top: 20px;
        }

        input[type="file"] {
            padding: 8px;
        }

        input[type="submit"] {
            background-color: #2980b9;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 4px;
        }

        input[type="submit"]:hover {
            background-color: #1c5980;
        }

    </style>
</head>
<body>
<header>Admin Dashboard</header>

<div class="navbar">
    <a href="admin.php">Grant Service</a>
    <a href="upload.php">Bulk Upload Services</a>
    <a href="admin_delete.php">Delete Services</a>
</div>

<div class="container">
    <h2>Welcome, Admin</h2>
    <p>Use the menu above to manage service providers and upload service data in bulk.</p>
</div>

</body>
</html>
