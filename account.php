<?php
require_once 'db.php'; // Make sure this path is correct!
$pdo = new PDO('mysql:host=localhost;dbname=nandyal_dial', 'root', '1234');

session_start();
if (!isset($_SESSION['customer_id']) && !isset($_SESSION['provider_id'])) {
    echo "<p style='color: red; font-weight: bold;'>🔒 You must log in first.</p>";
    echo "<p><a href='login_register.html'>Go to Login Page</a></p>";
    exit(); // Stop the script if not logged in
}
// echo($_SESSION['customer_id']);
// echo $_SESSION['userID'];

// Handle logout: set active=0 for the current user and destroy session
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    $view = isset($_GET['view']) ? $_GET['view'] : 'customer';
    $userId = isset($_SESSION['userID']) ? $_SESSION['userID'] : null;
    if ($userId) {
        if ($view === 'provider') {
            $stmt = $pdo->prepare("UPDATE providers SET active=0 WHERE id=?");
            $stmt->execute([$userId]);
        } else {
            $stmt = $pdo->prepare("UPDATE customers SET active=0 WHERE id=?");
            $stmt->execute([$userId]);
        }
    }
    session_destroy();
    header('Location: login_register.html');
    exit();
}
// if (isset($_GET['logout']) && $_GET['logout'] == 1) {
//     $view = isset($_GET['view']) ? $_GET['view'] : 'customer';
//     $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
//     if ($userId) {
//         if ($view === 'provider') {
//             $stmt = $pdo->prepare("UPDATE providers SET active=0 WHERE id=?");
//             $stmt->execute([$userId]);
//         } else {
//             $stmt = $pdo->prepare("UPDATE customers SET active=0 WHERE id=?");
//             $stmt->execute([$userId]);
//         }
//     }
//     session_destroy();
//     header('Location: login_register.html');
//     exit();
// }

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['form_type']) && $_POST['form_type'] === 'customer') {
        // Update customers table
        $stmt = $pdo->prepare("UPDATE customers SET first_name=?, last_name=?, email=?, phone=?, location=? WHERE id=?");
        $stmt->execute([
            $_POST['first_name'],
            $_POST['last_name'],
            $_POST['email'],
            $_POST['phone'],
            $_POST['location'],
            $_POST['id']
        ]);
    } elseif (isset($_POST['form_type']) && $_POST['form_type'] === 'provider') {
        // Update providers table
        $stmt = $pdo->prepare("UPDATE providers SET first_name=?, last_name=?, business_name=?, email=?, phone=?, category=?, description=?, location=? WHERE id=?");
        $stmt->execute([
            $_POST['first_name'],
            $_POST['last_name'],
            $_POST['business_name'],
            $_POST['email'],
            $_POST['phone'],
            $_POST['category'],
            $_POST['description'],
            $_POST['location'],
            $_POST['id']
        ]);
    }
}



$formType = $_POST['form_type'] ?? $_GET['form_type'] ?? ($_SESSION['user_type'] ?? '');
// echo $formType;
if (!in_array($formType, ['customer', 'provider'])) {
    echo "Invalid form type";
    exit;
}

// $form_type=isset($_POST['form_type']);
$table=$formType.'s';
// echo $table;

$sql = "SELECT * FROM $table WHERE active = 1 LIMIT 1";
$stmt = $pdo->query($sql);
// echo $stmt;
$row = $stmt->fetch(PDO::FETCH_ASSOC);
// foreach($row as $r){
//     echo $r;
// }
//Debugging
// foreach ($row as $key => $value) {
//     echo "$key: $value<br>";
// }

// if ($row) {
//     echo "Active user: " . htmlspecialchars($row['name']);
// } else {
//     echo "No active user found.";
// }


// Always fetch provider for now (adjust as needed)
// echo($_POST['form_type']);
// $sql_s="SELECT * FROM $_POST['form_type'] WHERE active=1 LIMIT 1";
// $stmt = $pdo->query($sql_s);
// if($stmt){
//     $user = $stmt->fetch(PDO::FETCH_ASSOC);
// }else{
//     echo("no active member or no one loggedIn");
// }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LocalConnect - My Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
        
  .language-selector .goog-te-gadget-simple {
    background: #f8fafc !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 12px !important;
    padding: 1px 6px !important;
    font-size: 0.88rem !important;
    color: #1e293b !important;
    min-height: 24px !important;
    min-width: 40px !important;
    box-shadow: 0 1px 4px rgba(59,130,246,0.07);
    cursor: pointer;
    display: flex !important;
    align-items: center;
    height: 26px !important;
    line-height: 1.1 !important;
    margin-left: 0.5rem;
  }
  .language-selector .goog-te-gadget-simple:hover {
    box-shadow: 0 2px 8px rgba(59,130,246,0.13);
  }
  .language-selector .goog-te-menu-value span {
    color: #3b82f6 !important;
    font-weight: 500;
  }
  .language-selector .goog-te-menu-value {
    padding-right: 8px !important;
  }
  .language-selector .goog-te-gadget-icon {
    display: none !important;
  }
  .language-selector select.goog-te-combo {
    font-size: 0.88rem !important;
    padding: 1px 4px !important;
    border-radius: 8px !important;
    border: 1px solid #e2e8f0 !important;
    background: #f8fafc !important;
    min-height: 20px !important;
    min-width: 35px !important;
    width: 50px !important;
  }

  .goog-logo-link, .goog-te-gadget span {
    display: none !important;
  }
        body {
            background: #f4f6fb;
            color: #1e293b;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .profile-card {
            border-radius: 1.25rem;
            box-shadow: 0 4px 24px rgba(37,99,235,0.07);
            margin-bottom: 2.5rem;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #fff;
            padding: 2.5rem 2rem 2rem 2rem;
            position: relative;
        }
        .profile-avatar {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            background: #fff;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            border: 5px solid #fff;
            margin: 0 auto 1.25rem auto;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        }
        .profile-info {
            text-align: center;
        }
        .role-badge {
            position: absolute;
            top: 2rem;
            right: 2rem;
            padding: 0.5rem 1.25rem;
            border-radius: 20px;
            font-size: 1rem;
            font-weight: 600;
            background: #10b981;
            color: #fff;
            box-shadow: 0 2px 8px rgba(16,185,129,0.08);
        }
        .provider-badge { background: #f59e0b !important; }
        .nav-tabs {
            background: #f8fafc;
            border-radius: 0.75rem 0.75rem 0 0;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.5rem 1rem 0 1rem;
        }
        .nav-tabs .nav-link {
            color: #64748b;
            font-weight: 500;
            border: none;
            border-radius: 0.5rem 0.5rem 0 0;
            margin-right: 0.5rem;
            background: none;
            transition: background 0.2s, color 0.2s;
        }
        .nav-tabs .nav-link.active {
            color: #2563eb;
            background: #fff;
            border-bottom: 2px solid #2563eb;
        }
        .card {
            border-radius: 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            border: none;
            margin-bottom: 2rem;
        }
        .card-header {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 600;
            font-size: 1.1rem;
            color: #2563eb;
        }
        .card-body {
            background: #fff;
            border-radius: 0 0 1rem 1rem;
        }
        .stat-card {
            background: #f8fafc;
            border-radius: 0.75rem;
            padding: 1.5rem 1rem;
            text-align: center;
            border: 1px solid #e2e8f0;
            margin-bottom: 1.25rem;
            min-height: 110px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .stat-number {
            font-size: 2.1rem;
            font-weight: 700;
            color: #2563eb;
        }
        .stat-label {
            color: #64748b;
            font-size: 1rem;
            font-weight: 500;
        }
        .action-buttons .btn {
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
        }
        .service-item {
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 1.25rem 1rem;
            margin-bottom: 1.25rem;
            background: #f8fafc;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        }
        .rating-stars { color: #fbbf24; }
        .empty-state { text-align: center; padding: 2.5rem 1rem; color: #64748b; }
        .tab-content > .tab-pane { padding-top: 1.5rem; }
        .btn-primary, .btn-outline-primary {
            border-radius: 0.5rem;
            font-weight: 600;
        }
        .btn-outline-primary, .btn-outline-warning, .btn-outline-danger {
            background: #fff;
        }
        .btn-primary {
            background: #2563eb;
            border-color: #2563eb;
        }
        .btn-primary:hover, .btn-outline-primary:hover {
            background: #1d4ed8;
            border-color: #1d4ed8;
        }
        .form-label {
            font-weight: 600;
            color: #1e293b;
        }
        .form-control {
            border-radius: 0.5rem;
            border: 1px solid #e2e8f0;
            padding: 0.75rem;
        }
        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 0.2rem rgba(37,99,235,0.08);
        }
        @media (max-width: 991.98px) {
            .profile-card { padding: 2rem 1rem 1.5rem 1rem; }
            .role-badge { position: static; display: block; margin: 1rem auto 0 auto; text-align: center; }
        }
        @media (max-width: 767.98px) {
            .profile-card { padding: 1.5rem 0.5rem 1rem 0.5rem; }
            .stat-card { min-height: 90px; }
        }
    </style>
</head>
<body>
    
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); box-shadow: 0 2px 10px rgba(37,99,235,0.08);">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#"><i class="fas fa-handshake me-2"></i>LocalConnect</a>
         
            <div class="navbar-nav ms-auto">
                <div class="language-selector" style="display:inline-flex; align-items:center; vertical-align:middle; margin-left:0.5rem; gap:0.3rem; min-width:0;">
                    <div id="google_translate_element"></div>
                  </div>
                <a class="nav-link" href="index.html"><i class="fas fa-home me-1"></i>Home</a>
                <a href="account.php?logout=1" class="btn btn-danger mb-2 ms-2">Logout</a>
            </div>
        </div>
    </nav>
    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-10 mx-auto">
                <div class="profile-card mb-5">
                    <div class="profile-avatar"><i class="fas fa-user"></i></div>
                    <div class="profile-info">
                        <h2 class="mb-1 fw-bold" id="userName"><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></h2>
                        <p class="mb-0 opacity-75" id="userEmail"><?php echo htmlspecialchars($row['email']); ?></p>
                    </div>
                    <span class="role-badge customer-badge" id="roleBadge">
                        <i class="fas fa-user me-1"></i>Customer
                    </span>
                    <div class="d-flex justify-content-center mt-4">
                        <div class="btn-group" role="group">
                            <button onclick="switchUserType('customer')" id="customerToggleDesktop" class="btn btn-light btn-sm active">Customer View</button>
                            <button onclick="switchUserType('provider')" id="providerToggleDesktop" class="btn btn-light btn-sm">Provider View</button>
                        </div>
                    </div>
                </div>
                <div class="row g-4">
                    <div class="col-12">
                        <div id="customerContent">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <ul class="nav nav-tabs card-header-tabs" id="customerTabs" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#customerProfile" type="button" role="tab"><i class="fas fa-user-edit me-1"></i>Profile</button>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content" id="customerTabContent">
                                        <div class="tab-pane fade show active" id="customerProfile" role="tabpanel">
                                            <form id="customerProfileForm" method="POST" action="account.php?view=customer">
                                                <input type="hidden" name="form_type" value="customer">
                                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">First Name</label>
                                                        <input type="text" class="form-control" id="customerFirstName" name="first_name" value="<?php echo htmlspecialchars($row['first_name']); ?>" required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Last Name</label>
                                                        <input type="text" class="form-control" id="customerLastName" name="last_name" value="<?php echo htmlspecialchars($row['last_name']); ?>" required>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Email Address</label>
                                                    <input type="email" class="form-control" id="customerEmail" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Phone Number</label>
                                                    <input type="tel" class="form-control" id="customerPhone" name="phone" value="<?php echo htmlspecialchars($row['phone']); ?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Location</label>
                                                    <input type="text" class="form-control" id="customerLocation" name="location" value="<?php echo htmlspecialchars($row['location']); ?>">
                                                </div>
                                                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-save me-1"></i>Save Changes</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="providerContent" style="display: none;">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <ul class="nav nav-tabs card-header-tabs" id="providerTabs" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="provider-profile-tab" data-bs-toggle="tab" data-bs-target="#providerProfile" type="button" role="tab"><i class="fas fa-user-edit me-1"></i>Profile</button>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                 
                                    <div class="tab-content" id="providerTabContent">
                                        <div class="tab-pane fade show active" id="providerProfile" role="tabpanel">
                                            <form id="providerProfileForm" method="POST" action="account.php?view=provider">
                                                <input type="hidden" name="form_type" value="provider">
                                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">First Name</label>
                                                        <input type="text" class="form-control" id="providerFirstName" name="first_name" value="<?php echo htmlspecialchars($row['first_name'] ?? ''); ?>" required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Last Name</label>
                                                        <input type="text" class="form-control" id="providerLastName" name="last_name" value="<?php echo htmlspecialchars($row['last_name'] ?? ''); ?>" required>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Business Name</label>
                                                    <input type="text" class="form-control" id="providerBusinessName" name="business_name" value="<?php echo htmlspecialchars($row['business_name'] ?? ''); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Email Address</label>
                                                    <input type="email" class="form-control" id="providerEmail" name="email" value="<?php echo htmlspecialchars($row['email'] ?? ''); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Phone Number</label>
                                                    <input type="tel" class="form-control" id="providerPhone" name="phone" value="<?php echo htmlspecialchars($row['phone'] ?? ''); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Category</label>
                                                    <input type="text" class="form-control" id="providerCategory" name="category" value="<?php echo htmlspecialchars($row['category'] ?? ''); ?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Description</label>
                                                    <textarea class="form-control" id="providerDescription" name="description" rows="3"><?php echo htmlspecialchars($row['description'] ?? ''); ?></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Location</label>
                                                    <input type="text" class="form-control" id="providerLocation" name="location" value="<?php echo htmlspecialchars($row['location'] ?? ''); ?>">
                                                </div>
                                                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-save me-1"></i>Save Changes</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function switchUserType(type) {
            const customerContent = document.getElementById('customerContent');
            const providerContent = document.getElementById('providerContent');
            const customerToggleDesktop = document.getElementById('customerToggleDesktop');
            const providerToggleDesktop = document.getElementById('providerToggleDesktop');
            const roleBadge = document.getElementById('roleBadge');
            if (type === 'customer') {
                customerContent.style.display = 'block';
                providerContent.style.display = 'none';
                customerToggleDesktop.classList.add('active');
                providerToggleDesktop.classList.remove('active');
                roleBadge.className = 'role-badge customer-badge';
                roleBadge.innerHTML = '<i class="fas fa-user me-1"></i>Customer';
            } else {
                customerContent.style.display = 'none';
                providerContent.style.display = 'block';
                customerToggleDesktop.classList.remove('active');
                providerToggleDesktop.classList.add('active');
                roleBadge.className = 'role-badge provider-badge';
                roleBadge.innerHTML = '<i class="fas fa-tools me-1"></i>Provider';
            }
        }
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `alert alert-${type} notification`;
            notification.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-circle' : 'info-circle'} me-2"></i>${message}`;
            document.body.appendChild(notification);
            setTimeout(() => { notification.remove(); }, 3000);
        }
        function editService(id) { showNotification('Edit service coming soon', 'info'); }
        function deactivateService(id) { showNotification('Service deactivated', 'warning'); }
        function addNewService() { showNotification('Add service form coming soon', 'info'); }
    </script>
        <script type="text/javascript">
            function googleTranslateElementInit() {
              new google.translate.TranslateElement({
                pageLanguage: 'en',
                layout: google.translate.TranslateElement.InlineLayout.HORIZONTAL,
                autoDisplay: false
              }, 'google_translate_element');
            }
          </script>
          <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
       
</body>
</html>