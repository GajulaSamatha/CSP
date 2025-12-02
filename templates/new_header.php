<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LocalConnect</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4A00E0;
            --secondary: #8E2DE2;
            --accent: #3498db;
            --text-light: #ffffff;
            --header-height: 80px;
            --mobile-header-height: 70px;
            --transition: all 0.3s ease;
        }
        

        * {margin:0;padding:0;box-sizing:border-box;}

        body {
            font-family: 'Segoe UI', sans-serif;
            padding-top: var(--header-height); /* Changed from margin-top to padding-top */
        }

        /* Fix for Google Translate banner */
        .goog-te-banner-frame {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            bottom:10px;
            z-index: 1 !important; /* Below header */
        }

        header {
            position: absolute;
            top:2px;
            left: 0; 
            right: 0;
            width: 100%;
            height: var(--header-height);
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: var(--text-light);
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            
        }

        .logo {display:flex;align-items:center;gap:10px;text-decoration:none;flex:0 0 auto;}
        .logo-icon {font-size:2rem;color:var(--accent);}
        .logo-text {font-size:1.8rem;font-weight:700;color:var(--text-light);white-space:nowrap;}

        .main-nav {
            display: flex; gap: 2rem;
            position: absolute; left: 50%; transform: translateX(-50%);
        }
        .main-nav a {
            color: var(--text-light); text-decoration:none;font-size:1rem;font-weight:500;
            display:flex;align-items:center;gap:8px;padding:0.5rem 0;position:relative;
            transition: var(--transition); white-space: nowrap;
        }
        .main-nav a i {font-size:1.1rem;}
        .main-nav a::after {
            content:'';position:absolute;bottom:0;left:0;width:0;height:3px;
            background:var(--accent);transition:var(--transition);
        }
        .main-nav a:hover::after {width:100%;}

        .utility-controls {
            display:flex;align-items:center;gap:1.5rem;height:100%;
            position: relative;
        }

        /* User Profile */
        .user-profile {display:flex;align-items:center;gap:8px;cursor:pointer;height:100%;position:relative;}
        .user-avatar {
            width:36px;height:36px;border-radius:50%;
            background-color:var(--accent);display:flex;align-items:center;justify-content:center;
            color:white;font-weight:600;
        }
        .user-dropdown {
            position:absolute;top:100%;right:0;background:white;min-width:180px;
            box-shadow:0 4px 12px rgba(0,0,0,0.2);border-radius:4px;z-index:1001;display:none;
            overflow:hidden;
        }
        .user-profile:hover .user-dropdown {display:block;}
        .user-dropdown a {
            display:block;
            padding:8px 15px; /* Reduced padding */
            color:#333;
            text-decoration:none;
            transition:background 0.2s;
            font-size: 0.9rem; /* Smaller font */
            line-height: 1.3; /* Tighter line height */
        }
        .user-dropdown a:hover {background:#f5f5f5;}
        .user-dropdown a i {
            margin-right:8px;
            width:16px;
            text-align:center;
            font-size: 0.9rem; /* Smaller icons */
        }

        /* Language Selector Globe */
        .translate-icon {
            cursor: pointer;
            font-size: 1.4rem;
            color: var(--text-light);
            position: relative;
        }

        /* Translate Dropdown */
        #google_translate_element {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background: #fff;
            padding: 6px;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            z-index: 2000;
            min-width: 160px;
        }

        /* Hide Google Translate top banner when not needed */
        .goog-te-banner-frame.skiptranslate {
            display: none !important;
        }
        body {
            top: 0 !important;
        }

        .menu-toggle {display:none;cursor:pointer;font-size:1.5rem;}

        @media (max-width:768px) {
            body {padding-top: var(--mobile-header-height);}
            header {height:var(--mobile-header-height);padding:0 1rem;}
            .menu-toggle {display:block;}
            .main-nav {
                position:fixed;top:var(--mobile-header-height);left:0;width:100%;
                background:linear-gradient(135deg, var(--primary), var(--secondary));
                flex-direction:column;align-items:center;padding:1rem 0;
                clip-path:circle(0px at 90% -10%);transition:all 0.5s ease-out;
                pointer-events:none;transform:none;
            }
            .main-nav.active {clip-path:circle(1000px at 50% 50%);pointer-events:all;}
            .main-nav a {padding:1rem;width:100%;justify-content:center;
                border-bottom:1px solid rgba(255,255,255,0.1);}
            .utility-controls {
                position:fixed;bottom:20px;right:20px;flex-direction:column;gap:1rem;
                background:rgba(0,0,0,0.7);padding:1rem;border-radius:12px;z-index:1001;height:auto;
            }
            .user-dropdown {right:auto;left:0;}
        }
    </style>
</head>
<body>
    <header>
        <!-- Logo -->
        <a href="index.php" class="logo">
            <i class="fas fa-map-marker-alt logo-icon"></i>
            <span class="logo-text">LocalConnect</span>
        </a>

        <!-- Mobile Menu -->
        <div class="menu-toggle"><i class="fas fa-bars"></i></div>

        <!-- Main Nav -->
        <nav class="main-nav">
            <a href="about.php"><i class="fas fa-info-circle"></i> About</a>
            <a href="new_services.php"><i class="fas fa-concierge-bell"></i> Services</a>
            <a href="new_categories.php"><i class="fas fa-list"></i> Categories</a>
            <?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'provider'): ?>
                <a href="new_add_services.php"><i class="fas fa-plus-circle"></i> Add Service</a>
            <?php endif; ?>
            <a href="contact.php"><i class="fas fa-envelope"></i> Contact</a>
        </nav>

        <!-- Right Controls -->
        <div class="utility-controls">
            <?php if(isset($_SESSION['user_name'])): ?>
                <div class="user-profile">
                    <div class="user-avatar"><?= strtoupper(substr($_SESSION['user_name'], 0, 1)) ?></div>
                    <div class="user-dropdown">
                        <?php if($_SESSION['user_type'] === 'customer'): ?>
                            <a href="customer_profile.php"><i class="fas fa-user"></i> My Profile</a>
                        <?php elseif($_SESSION['user_type'] === 'provider'): ?>
                            <a href="provider_profile.php"><i class="fas fa-user-tie"></i> Provider Profile</a>
                            <!-- <a href="provider_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a> -->
                            <a href="dash.php"><i class="fas fa-concierge-bell"></i> My Services</a>
                        <?php endif; ?>
                        
                        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            <?php endif; ?>
            <?php if(!isset($_SESSION['user_name'])): ?>
                <a href="new_login.php">Login</a>
            <?php endif; ?>

            <!-- 🌐 Globe Translate -->
            <div class="translate-icon" id="translate-btn">
                <i class="fas fa-globe"></i>
                <div id="google_translate_element"></div>
            </div>
        </div>
    </header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.querySelector('.menu-toggle');
    const mainNav = document.querySelector('.main-nav');

    menuToggle.addEventListener('click', function() {
        mainNav.classList.toggle('active');
        this.querySelector('i').classList.toggle('fa-bars');
        this.querySelector('i').classList.toggle('fa-times');
    });
    
    document.querySelectorAll('.main-nav a').forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                mainNav.classList.remove('active');
                menuToggle.querySelector('i').classList.add('fa-bars');
                menuToggle.querySelector('i').classList.remove('fa-times');
            }
        });
    });

    // Google Translate Init
    function googleTranslateElementInit() {
        new google.translate.TranslateElement({
            pageLanguage: 'en',
            includedLanguages: 'af,sq,am,ar,hy,az,eu,be,bn,bg,ca,ceb,zh-CN,zh-TW,co,hr,cs,da,nl,en,eo,et,fi,fr,fy,gl,ka,de,el,gu,ht,ha,haw,he,hi,hmn,hu,is,ig,id,ga,it,ja,jv,kn,kk,km,rw,ko,ku,ky,lo,la,lv,lt,lb,mk,mg,ms,ml,mt,mi,mr,mn,my,ne,no,ny,or,ps,fa,pl,pt,pa,ro,ru,sm,gd,sr,st,si,sk,sl,so,es,su,sw,sv,tl,tg,ta,tt,te,th,tr,tk,uk,ur,ug,uz,vi,cy,xh,yi,yo,zu',
            layout: google.translate.TranslateElement.InlineLayout.SIMPLE
        }, 'google_translate_element');
    }
    window.googleTranslateElementInit = googleTranslateElementInit;

    // Load Google Translate Script
    const gScript = document.createElement('script');
    gScript.src = "//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit";
    document.body.appendChild(gScript);

    // Globe click → toggle dropdown
    document.getElementById('translate-btn').addEventListener('click', (e) => {
        e.stopPropagation();
        const translateDiv = document.getElementById('google_translate_element');
        translateDiv.style.display = translateDiv.style.display === 'none' ? 'block' : 'none';
    });

    // Close translate dropdown when clicking outside
    document.addEventListener('click', (e) => {
        const translateDiv = document.getElementById('google_translate_element');
        if (!e.target.closest('#translate-btn') && translateDiv.style.display === 'block') {
            translateDiv.style.display = 'none';
        }
    });

    // Hide Google Translate banner when English is selected
    function checkLanguage() {
        const banner = document.querySelector('.goog-te-banner-frame');
        if (banner) {
            const currentLang = document.querySelector('.goog-te-menu-value span');
            if (currentLang && currentLang.textContent.includes('English')) {
                banner.style.display = 'none';
            } else {
                banner.style.display = 'block';
            }
        }
    }

    // Check language periodically
    setInterval(checkLanguage, 500);
});


</script>

</body>
</html>
