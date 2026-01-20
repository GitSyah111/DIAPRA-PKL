// JavaScript for Data Kepala Dinas Page

// Modal Functions
function openAddModal() {
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-user-tie"></i> Tambah Data Kepala Dinas';
    document.getElementById('actionType').value = 'add';
    document.getElementById('dataForm').reset();
    document.getElementById('dataId').value = '';
    document.getElementById('dataModal').classList.add('show');
}

function openEditModal(no, nama, pangkat, nip) {
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit"></i> Edit Data Kepala Dinas';
    document.getElementById('actionType').value = 'edit';
    document.getElementById('dataId').value = no;
    document.getElementById('nama').value = nama;
    document.getElementById('pangkat').value = pangkat;
    document.getElementById('NIP').value = nip;
    document.getElementById('dataModal').classList.add('show');
}

function closeModal() {
    document.getElementById('dataModal').classList.remove('show');
    document.getElementById('dataForm').reset();
}

// Custom Delete Confirmation Modal
function showDeleteConfirm(message, onConfirm) {
    const modal = document.createElement('div');
    modal.className = 'custom-confirm-overlay';
    modal.innerHTML = `
        <div class="custom-confirm-modal">
            <div class="custom-confirm-icon delete-icon">
                <i class="fas fa-trash-alt"></i>
            </div>
            <h3 class="custom-confirm-title">Konfirmasi Hapus</h3>
            <p class="custom-confirm-message">${message}</p>
            <div class="custom-confirm-buttons">
                <button class="custom-btn custom-btn-cancel" onclick="this.closest('.custom-confirm-overlay').remove()">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button class="custom-btn custom-btn-delete" id="confirmDeleteBtn">
                    <i class="fas fa-trash"></i> Ya, Hapus
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Add styles if not exists
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
            
            .delete-icon {
                background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
            
            .custom-btn-delete {
                background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
                color: white;
            }
            
            .custom-btn-delete:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(245, 87, 108, 0.4);
            }
        `;
        document.head.appendChild(style);
    }
    
    // Handle confirm button
    setTimeout(() => {
        const confirmBtn = document.getElementById('confirmDeleteBtn');
        if (confirmBtn) {
            confirmBtn.addEventListener('click', function() {
                modal.remove();
                onConfirm();
            });
        }
    }, 100);
    
    // Close on overlay click
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

// Delete Confirmation
function confirmDelete(no) {
    showDeleteConfirm('Apakah Anda yakin ingin menghapus data ini?', function() {
        window.location.href = 'proses-kepala-dinas.php?action=delete&no=' + no;
    });
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('dataModal');
    if (event.target == modal) {
        closeModal();
    }
}

// Search Functionality
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchValue = this.value.toLowerCase();
    const tableBody = document.getElementById('tableBody');
    const rows = tableBody.getElementsByTagName('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const cells = row.getElementsByTagName('td');
        let found = false;
        
        // Skip empty data row
        if (cells.length === 1 && cells[0].classList.contains('empty-data')) {
            continue;
        }
        
        // Search in nama, pangkat, and NIP columns (index 1, 2, 3)
        for (let j = 1; j <= 3; j++) {
            if (cells[j]) {
                const cellText = cells[j].textContent.toLowerCase();
                if (cellText.includes(searchValue)) {
                    found = true;
                    break;
                }
            }
        }
        
        row.style.display = found ? '' : 'none';
    }
});

// Format NIP input (auto add space every 8 digits)
document.getElementById('NIP').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\s/g, ''); // Remove spaces
    
    // Only allow numbers
    value = value.replace(/[^\d]/g, '');
    
    // Limit to 18 characters (NIP format: 19730719 199302 1 002)
    if (value.length > 18) {
        value = value.substring(0, 18);
    }
    
    // Format with spaces
    let formatted = '';
    for (let i = 0; i < value.length; i++) {
        if (i === 8 || i === 14 || i === 15) {
            formatted += ' ';
        }
        formatted += value[i];
    }
    
    e.target.value = formatted;
});

// Custom Alert Modal
function showCustomAlert(message) {
    const modal = document.createElement('div');
    modal.className = 'custom-confirm-overlay';
    modal.innerHTML = `
        <div class="custom-confirm-modal">
            <div class="custom-confirm-icon alert-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3 class="custom-confirm-title">Peringatan</h3>
            <p class="custom-confirm-message">${message}</p>
            <div class="custom-confirm-buttons">
                <button class="custom-btn custom-btn-confirm" onclick="this.closest('.custom-confirm-overlay').remove()">
                    <i class="fas fa-check"></i> OK
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Add alert icon style
    if (!document.getElementById('customAlertStyles')) {
        const style = document.createElement('style');
        style.id = 'customAlertStyles';
        style.textContent = `
            .alert-icon {
                background: linear-gradient(135deg, #fa709a 0%, #fee140 100%) !important;
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
    
    // Close on overlay click
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

// Form Validation
document.getElementById('dataForm').addEventListener('submit', function(e) {
    const nama = document.getElementById('nama').value.trim();
    const pangkat = document.getElementById('pangkat').value.trim();
    const nip = document.getElementById('NIP').value.trim();
    
    if (nama === '' || pangkat === '' || nip === '') {
        e.preventDefault();
        showCustomAlert('Semua field harus diisi!');
        return false;
    }
    
    // Validate NIP format (remove spaces for validation)
    const nipClean = nip.replace(/\s/g, '');
    if (nipClean.length !== 18) {
        e.preventDefault();
        showCustomAlert('NIP harus terdiri dari 18 digit!');
        return false;
    }
    
    return true;
});