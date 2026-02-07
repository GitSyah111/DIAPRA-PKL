// Dashboard JavaScript
document.addEventListener('DOMContentLoaded', function () {

    // Elements
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main-content');
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const headerMenuBtn = document.getElementById('headerMenuBtn'); // Added
    const userInfoToggle = document.getElementById('userInfoToggle');
    const userDropdown = document.getElementById('userDropdown');

    // AUTO DETECT ACTIVE MENU BASED ON CURRENT PAGE
    setActiveMenu();

    // User Dropdown Toggle
    if (userInfoToggle && userDropdown) {
        userInfoToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            userInfoToggle.classList.toggle('active');
            userDropdown.classList.toggle('active');
        });
    }

    // Antigravity Sidebar Logic
    const hoverTrigger = document.createElement('div');
    hoverTrigger.className = 'hover-trigger';
    document.body.appendChild(hoverTrigger);

    // Toggle Sidebar via Header Button
    if (headerMenuBtn) { // Changed id to match PHP
        headerMenuBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            sidebar.classList.toggle('active');

            // Force DataTable resize
            setTimeout(() => {
                if (typeof $ !== 'undefined') {
                    $(window).trigger('resize');
                }
            }, 300); // Wait for transition
        });
    }

    // Close logic when clicking outside
    document.addEventListener('click', function (event) {
        const isClickInsideSidebar = sidebar.contains(event.target);
        const isClickOnToggle = headerMenuBtn && headerMenuBtn.contains(event.target);

        if (sidebar.classList.contains('active') && !isClickInsideSidebar && !isClickOnToggle) {
            sidebar.classList.remove('active');

            // Force DataTable resize
            setTimeout(() => {
                if (typeof $ !== 'undefined') {
                    $(window).trigger('resize');
                }
            }, 300);
        }
    });

    // Mobile specific logic retained but integrated
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            sidebar.classList.toggle('active');
        });
    }

    // Handle window resize - remove active class on mode switch if needed
    window.addEventListener('resize', function () {
        // Optional: Reset state on extreme resize if desired
    });

    // Smooth scroll untuk anchor links (jika ada)
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#' && document.querySelector(href)) {
                e.preventDefault();
                document.querySelector(href).scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });

    // Add animation to stat cards on scroll (optional)
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function (entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe stat cards
    document.querySelectorAll('.stat-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(card);
    });

});

// Function to set active menu based on current page
function setActiveMenu() {
    // Get current page filename
    const currentPage = window.location.pathname.split('/').pop();

    // Remove all active classes first
    document.querySelectorAll('.nav-item').forEach(item => {
        item.classList.remove('active');
    });

    // Map of pages to their menu items
    const pageMenuMap = {
        'dashboard.php': 'dashboard.php',
        'surat-masuk.php': 'surat-masuk.php',
        'tambah-surat-masuk.php': 'surat-masuk.php',
        'edit-surat-masuk.php': 'surat-masuk.php',
        'detail-surat-masuk.php': 'surat-masuk.php',
        'surat-keluar.php': 'surat-keluar.php',
        'tambah-surat-keluar.php': 'surat-keluar.php',
        'edit-surat-keluar.php': 'surat-keluar.php',
        'spj-umpeg.php': 'spj-umpeg.php',
        'tambah-spj-umpeg.php': 'spj-umpeg.php',
        'edit-spj-umpeg.php': 'spj-umpeg.php',
        'detail-spj-umpeg.php': 'spj-umpeg.php',
        'data-pengguna.php': 'data-pengguna.php',
        'tambah-pengguna.php': 'data-pengguna.php',
        'edit-pengguna.php': 'data-pengguna.php',
        'data-kepala-dinas.php': 'data-kepala-dinas.php',
        'tambah-kepala-dinas.php': 'data-kepala-dinas.php',
        'edit-kepala-dinas.php': 'data-kepala-dinas.php'
    };

    // Get the menu page for current page
    const menuPage = pageMenuMap[currentPage];

    if (menuPage) {
        // Find and activate the corresponding menu item
        document.querySelectorAll('.nav-item').forEach(item => {
            const href = item.getAttribute('href');
            if (href && href.includes(menuPage)) {
                item.classList.add('active');
            }
        });
    }
}

// Custom Confirmation Modal
function showCustomConfirm(message, onConfirm) {
    // Create modal HTML
    const modal = document.createElement('div');
    modal.className = 'custom-confirm-overlay';
    modal.innerHTML = `
        <div class="custom-confirm-modal">
            <div class="custom-confirm-icon">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <h3 class="custom-confirm-title">Konfirmasi Logout</h3>
            <p class="custom-confirm-message">${message}</p>
            <div class="custom-confirm-buttons">
                <button class="custom-btn custom-btn-cancel" onclick="this.closest('.custom-confirm-overlay').remove()">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button class="custom-btn custom-btn-confirm" id="confirmBtn">
                    <i class="fas fa-check"></i> Ya, Keluar
                </button>
            </div>
        </div>
    `;

    document.body.appendChild(modal);

    // Add styles
    if (!document.getElementById('customConfirmStyles')) {
        const style = document.createElement('style');
        style.id = 'customConfirmStyles';
        style.textContent = `
            .custom-confirm-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.6);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 10000;
                animation: fadeIn 0.3s ease;
            }
            
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            
            .custom-confirm-modal {
                background: white;
                border-radius: 12px;
                padding: 30px;
                max-width: 400px;
                width: 90%;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
                animation: slideUp 0.3s ease;
            }
            
            @keyframes slideUp {
                from { 
                    transform: translateY(50px);
                    opacity: 0;
                }
                to { 
                    transform: translateY(0);
                    opacity: 1;
                }
            }
            
            .custom-confirm-icon {
                width: 60px;
                height: 60px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 20px;
                color: white;
                font-size: 24px;
            }
            
            .custom-confirm-title {
                text-align: center;
                color: #333;
                font-size: 20px;
                font-weight: 600;
                margin-bottom: 10px;
            }
            
            .custom-confirm-message {
                text-align: center;
                color: #666;
                margin-bottom: 25px;
                line-height: 1.5;
            }
            
            .custom-confirm-buttons {
                display: flex;
                gap: 10px;
                justify-content: center;
            }
            
            .custom-btn {
                padding: 12px 24px;
                border: none;
                border-radius: 8px;
                font-size: 14px;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                gap: 8px;
            }
            
            .custom-btn-cancel {
                background: #f1f3f5;
                color: #495057;
            }
            
            .custom-btn-cancel:hover {
                background: #e9ecef;
                transform: translateY(-2px);
            }
            
            .custom-btn-confirm {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
            }
            
            .custom-btn-confirm:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            }
        `;
        document.head.appendChild(style);
    }

    // Handle confirm button
    setTimeout(() => {
        const confirmBtn = document.getElementById('confirmBtn');
        if (confirmBtn) {
            confirmBtn.addEventListener('click', function () {
                modal.remove();
                onConfirm();
            });
        }
    }, 100);

    // Close on overlay click
    modal.addEventListener('click', function (e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

// Fungsi untuk konfirmasi logout
function confirmLogout() {
    showCustomConfirm('Apakah Anda yakin ingin keluar?', function () {
        window.location.href = 'logout.php';
    });
}

// Attach logout confirmation ke button logout
const logoutBtn = document.querySelector('.logout-btn');
if (logoutBtn) {
    logoutBtn.addEventListener('click', function (e) {
        e.preventDefault();
        confirmLogout();
    });
}