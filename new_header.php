<?php // session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header</title>
    <style> 
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }
        body {
        font-family: 'Segoe UI', sans-serif;
        }

        /* Improved Google Translate Widget Styles */
        .language-selector {
            position: relative;
            z-index: 100;
            display: inline-flex;
            align-items: center;
            vertical-align: middle;
            margin-left: 0.5rem;
            gap: 0.3rem;
        }

        .goog-te-gadget {
            font-family: 'Segoe UI', sans-serif !important;
        }

        .goog-te-gadget-simple {
            background: transparent !important;
            border: 1px solid rgba(255,255,255,0.3) !important;
            border-radius: 4px !important;
            padding: 4px 8px !important;
            font-size: 0.9rem !important;
            color: white !important;
            cursor: pointer;
            display: flex !important;
            align-items: center;
            height: 32px !important;
            line-height: 1 !important;
            transition: all 0.2s ease;
        }

        .goog-te-gadget-simple:hover {
            border-color: #38bdf8 !important;
            background: rgba(56, 189, 248, 0.1) !important;
        }

        .goog-te-menu-value {
            color: white !important;
        }

        .goog-te-menu-value span {
            color: white !important;
            font-size: 0.9rem !important;
        }

        .goog-te-menu-value span:first-child {
            display: none !important;
        }

        .goog-te-menu-value span:last-child {
            color: white !important;
            border-left: none !important;
            padding: 0 !important;
        }

        .goog-te-gadget-icon {
            display: none !important;
        }

        .goog-te-combo {
            background-color: #334155 !important;
            color: white !important;
            border: 1px solid #475569 !important;
            border-radius: 4px !important;
            padding: 4px 8px !important;
            font-size: 0.9rem !important;
            height: 32px !important;
            min-width: 120px !important;
        }

        /* Fix for the dropdown positioning */
        .goog-te-menu-frame {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            margin-top: 5px !important;
            position: absolute !important;
            z-index: 999999 !important;
        }

        /* Hide Google Translate branding */
        .goog-logo-link, .goog-te-gadget span, .goog-te-banner-frame.skiptranslate {
            display: none !important;
        }

        body {
            top: 0px !important;
        }

        header {
            background-color: #1e293b;
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            position: relative;
            z-index: 1000;
            margin-top: 20px;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: #38bdf8;
            text-decoration: none;
        }

        nav {
            display: flex;
            gap: 1.2rem;
            flex-wrap: wrap;
            align-items: center;
        }

        nav a {
            color: white;
            text-decoration: none;
            font-size: 1rem;
            transition: color 0.3s ease;
        }

        nav a:hover {
            color: #38bdf8;
        }

        @media (max-width: 600px) {
            header {
                flex-direction: column;
                align-items: flex-start;
            }

            nav {
                margin-top: 0.5rem;
                flex-direction: column;
                gap: 0.6rem;
            }

            .language-selector {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div id="include-header">
        <header>
            <a href="index.php" class="logo">LocalConnect</a>
            <div class="language-selector">
                    <div id="google_translate_element"></div>
                </div>
            <nav>

                <a href="about.php">About</a>
                <a href="new_services.php">Services</a>
                <a href="new_categories.php">Categories</a>
                <a href="new_add_services.php">Add Service</a>
                <a href="contact.php">Contact Us</a>
                
                
                
                <?php if(isset($_SESSION['user_name'])): ?>
                    <!-- <h1><?php //echo $_SESSION['user_name'] ?></h1> -->
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="new_login.php">Login</a>
                <?php endif; ?>
            </nav>
        </header>

        <!-- Google Translate Script -->
        <script type="text/javascript">
            function googleTranslateElementInit() {
                new google.translate.TranslateElement({
                    pageLanguage: 'en',
                    includedLanguages: 'en,es,fr,de,it,pt,ru,zh-CN,ja,ar,te,ta',
                    layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
                    autoDisplay: false
                }, 'google_translate_element');
                
                // Fix for dropdown visibility
                document.querySelector('.goog-te-combo').addEventListener('click', function() {
                    const frame = document.querySelector('.goog-te-menu-frame');
                    if (frame) {
                        frame.style.zIndex = '999999';
                    }
                });
            }
        </script>
        <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

        <!-- Optional: Prevent translation of specific elements -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Add class to elements that shouldn't be translated
                document.querySelector('.logo').classList.add('notranslate');
                
                // You can add more elements here that shouldn't be translated
                // document.querySelector('.some-class').classList.add('notranslate');
            });
        </script>
    </div>
</body>
</html>