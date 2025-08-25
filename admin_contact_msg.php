<?php
session_start();
// Uncomment for production
/*
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit();
}
*/

require_once 'db.php';

// Create database connection
$conn = new mysqli("localhost", "root", "1234", "nandyal_dial");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



// Handle message deletion
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    $stmt = $conn->prepare("DELETE FROM contact_messages WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Message deleted successfully";
    } else {
        $_SESSION['error'] = "Error deleting message: " . $conn->error;
    }
    
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}



// Fetch all messages
$messages = [];
$query = "SELECT * FROM contact_messages ORDER BY created_at DESC";
$result = $conn->query($query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
    $result->free();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Messages - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4895ef;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --success-color: #4cc9f0;
            --warning-color: #f72585;
        }
        
        body {
            background-color: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }
        
        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .navbar {
            background-color: #2c3e50;
            overflow: hidden;
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
            border-radius: 8px;
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
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: none;
            margin-bottom: 2rem;
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid #e0e0e0;
            padding: 1.5rem;
            font-weight: 600;
            font-size: 1.25rem;
            border-radius: 12px 12px 0 0 !important;
        }
        
        .table-container {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background-color: #f8f9fa;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #e0e0e0;
        }
        
        td {
            padding: 1rem;
            border-bottom: 1px solid #e0e0e0;
            vertical-align: top;
        }
        
        tr:hover {
            background-color: #f8f9fa;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
        }
        
        .message-preview {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .status-badge {
            padding: 0.35rem 0.65rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .status-new {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
        
        .status-read {
            background-color: #e3f2fd;
            color: #1565c0;
        }
        
        .status-replied {
            background-color: #f3e5f5;
            color: #7b1fa2;
        }
        
        .action-btn {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            border: none;
            background: transparent;
            cursor: pointer;
            margin-right: 0.5rem;
        }
        
        .view-btn {
            color: var(--primary-color);
        }
        
        .view-btn:hover {
            background-color: #e8f5e9;
        }
        
        .delete-btn {
            color: #dc3545;
        }
        
        .delete-btn:hover {
            background-color: #ffeef0;
        }
        
        .filter-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .search-box {
            position: relative;
            width: 300px;
        }
        
        .search-box input {
            width: 100%;
            padding: 0.5rem 1rem 0.5rem 2.5rem;
            border: 1px solid #e0e0e0;
            border-radius: 20px;
        }
        
        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        
        .filter-options select {
            padding: 0.5rem 1rem;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            margin-left: 0.5rem;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
        }
        
        .modal-content {
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        
        @media (max-width: 992px) {
            .filter-bar {
                flex-direction: column;
                gap: 1rem;
            }
            
            .search-box {
                width: 100%;
            }
        }
    </style>

</head>
<body>
    <div class="navbar">
        <a href="admin_dashboard.php">Dashboard Home</a>
        <a href="admin.php">Grant Service</a>
        <a href="upload.php">Bulk Upload Services</a>
        <a href="admin_delete.php">Delete Services</a>
        <a href="admin_logout.php" class="logout-btn">Logout</a>
    </div>
    
    <div class="admin-container">
        
        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= $_SESSION['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= $_SESSION['error']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <div class="filter-bar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Search messages...">
            </div>
            
        </div>
        
        <div class="card">
            
            <div class="table-container">
                <table id="messagesTable">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Contact Info</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($messages)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-inbox fs-1 text-muted mb-3 d-block"></i>
                                    <h4>No messages found</h4>
                                    <p class="text-muted">There are no contact messages to display.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($messages as $message): ?>
                                    <td>
                                        <div class="user-info">
                                            <?php 
                                            $avatar_bg = ['0d8abc', 'ff6b6b', '4ecdc4', '45b7d1', 'f9ca24', 'eb4d4b', '6c5ce7'];
                                            $bg_color = $avatar_bg[array_rand($avatar_bg)];
                                            ?>
                                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($message['name']); ?>&background=<?php echo $bg_color; ?>&color=fff" class="user-avatar">
                                            <div>
                                                <div><?php echo htmlspecialchars($message['name']); ?></div>
                                                <small class="text-muted">User ID: <?php echo $message['id'] ?? 'N/A'; ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div><?php echo htmlspecialchars($message['email']); ?></div>
                                    </td>
                                    <td><?php echo !empty($message['subject']) ? htmlspecialchars($message['subject']) : 'No subject'; ?></td>
                                    <td>
                                        <div class="message-preview" title="<?php echo htmlspecialchars($message['message']); ?>">
                                            <?php echo htmlspecialchars($message['message']); ?>
                                        </div>
                                    </td>
                                    <td><?php echo date('M j, Y g:i A', strtotime($message['created_at'])); ?></td>
                                    
                                    </td>
                                    <td>
                                        <button class="action-btn view-btn" title="View Message" data-bs-toggle="modal" data-bs-target="#messageModal" data-message="<?php echo htmlspecialchars($message['message']); ?>" data-subject="<?php echo htmlspecialchars($message['subject']); ?>" data-user="<?php echo htmlspecialchars($message['name']); ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="?delete_id=<?php echo $message['id']; ?>" class="action-btn delete-btn" title="Delete Message" onclick="return confirm('Are you sure you want to delete this message?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
       
    </div>

    <!-- Message Modal -->
    <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="messageModalLabel">Message Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6 id="modalSubject" class="text-muted"></h6>
                    <p id="modalMessage" class="mt-3"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Message modal functionality
            const messageModal = document.getElementById('messageModal');
            messageModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const subject = button.getAttribute('data-subject');
                const message = button.getAttribute('data-message');
                const user = button.getAttribute('data-user');
                
                document.getElementById('messageModalLabel').textContent = `Message from ${user}`;
                document.getElementById('modalSubject').textContent = subject || 'No subject';
                document.getElementById('modalMessage').textContent = message;
            });
            
            // Search functionality
            const searchInput = document.getElementById('searchInput');
            searchInput.addEventListener('keyup', function() {
                const searchText = this.value.toLowerCase();
                const rows = document.querySelectorAll('#messagesTable tbody tr');
                
                rows.forEach(row => {
                    const userName = row.querySelector('.user-info div:first-child').textContent.toLowerCase();
                    const userEmail = row.querySelector('td:nth-child(2) div:first-child').textContent.toLowerCase();
                    const subject = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                    const message = row.querySelector('.message-preview').textContent.toLowerCase();
                    
                    if (userName.includes(searchText) || userEmail.includes(searchText) || 
                        subject.includes(searchText) || message.includes(searchText)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
            
            // Status filter functionality
            const statusFilter = document.getElementById('statusFilter');
            statusFilter.addEventListener('change', function() {
                const status = this.value;
                const rows = document.querySelectorAll('#messagesTable tbody tr');
                
                rows.forEach(row => {
                    if (status === 'all' || row.getAttribute('data-status') === status) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
            
            // Sort functionality
            const sortFilter = document.getElementById('sortFilter');
            sortFilter.addEventListener('change', function() {
                // In a real application, this would reload the page with a sort parameter
                alert('Sorting would be implemented with a page reload or AJAX in a production environment');
            });
        });
    </script>
</body>
</html>