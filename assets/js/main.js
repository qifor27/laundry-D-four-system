/**
 * Main JavaScript File
 * D'four Laundry Management System
 */

// ============================================
// Mobile Menu Toggle
// ============================================
document.addEventListener('DOMContentLoaded', function () {
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');

    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', function () {
            mobileMenu.classList.toggle('hidden');
        });
    }
});

// ============================================
// Modal Management
// ============================================
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Prevent scrolling
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto'; // Re-enable scrolling
    }
}

// Close modal when clicking overlay
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('modal-overlay')) {
        const modalId = e.target.closest('[id$="-modal"]')?.id;
        if (modalId) {
            closeModal(modalId);
        }
    }
});

// ============================================
// API Helper Functions
// ============================================

/**
 * Fetch data from API
 */
async function fetchAPI(url, options = {}) {
    try {
        const response = await fetch(url, {
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            },
            ...options
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.error('API Error:', error);
        showNotification('Terjadi kesalahan saat memuat data', 'error');
        throw error;
    }
}

/**
 * Submit form data via AJAX
 */
async function submitForm(url, formData) {
    try {
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.error('Form Submission Error:', error);
        showNotification('Gagal mengirim data', 'error');
        throw error;
    }
}

// ============================================
// Notification System
// ============================================
function showNotification(message, type = 'info') {
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-yellow-500',
        info: 'bg-blue-500'
    };

    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-slide-up`;
    notification.textContent = message;

    document.body.appendChild(notification);

    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
        setTimeout(() => notification.remove(), 500);
    }, 3000);
}

// ============================================
// Form Validation
// ============================================
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;

    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('border-red-500');
            isValid = false;
        } else {
            field.classList.remove('border-red-500');
        }
    });

    if (!isValid) {
        showNotification('Mohon lengkapi semua field yang wajib diisi', 'error');
    }

    return isValid;
}

// ============================================
// Currency Formatter
// ============================================
function formatRupiah(amount) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
}

// Auto-format currency input
function formatCurrencyInput(input) {
    let value = input.value.replace(/[^0-9]/g, '');
    input.value = value;

    // Optional: Show formatted preview
    const preview = input.nextElementSibling;
    if (preview && preview.classList.contains('currency-preview')) {
        preview.textContent = formatRupiah(value);
    }
}

// ============================================
// Phone Number Formatter
// ============================================
function formatPhoneNumber(input) {
    let value = input.value.replace(/[^0-9]/g, '');

    // Ensure it starts with 08 or 628
    if (value.startsWith('8')) {
        value = '0' + value;
    }

    input.value = value;
}

// ============================================
// Confirm Dialog
// ============================================
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

// ============================================
// Print Receipt
// ============================================
function printReceipt(transactionId) {
    // Open print window
    const printWindow = window.open(
        `print-receipt.php?id=${transactionId}`,
        'Print Receipt',
        'width=800,height=600'
    );

    printWindow.onload = function () {
        printWindow.print();
    };
}

// ============================================
// Table Search/Filter
// ============================================
function filterTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);

    if (!input || !table) return;

    input.addEventListener('keyup', function () {
        const filter = this.value.toLowerCase();
        const rows = table.getElementsByTagName('tr');

        for (let i = 1; i < rows.length; i++) { // Skip header
            const row = rows[i];
            const text = row.textContent.toLowerCase();

            if (text.includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
}

// ============================================
// Auto-hide alerts
// ============================================
document.addEventListener('DOMContentLoaded', function () {
    const alerts = document.querySelectorAll('[role="alert"]');

    alerts.forEach(alert => {
        setTimeout(() => {
            alert.classList.add('opacity-0', 'transition-opacity', 'duration-500');
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
});

// ============================================
// Loading Spinner
// ============================================
function showLoadingSpinner() {
    const spinner = document.createElement('div');
    spinner.id = 'loading-spinner';
    spinner.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    spinner.innerHTML = `
        <div class="bg-white rounded-lg p-6">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
        </div>
    `;
    document.body.appendChild(spinner);
}

function hideLoadingSpinner() {
    const spinner = document.getElementById('loading-spinner');
    if (spinner) {
        spinner.remove();
    }
}

// ============================================
// Export Functions
// ============================================
window.laundryApp = {
    openModal,
    closeModal,
    fetchAPI,
    submitForm,
    showNotification,
    validateForm,
    formatRupiah,
    formatCurrencyInput,
    formatPhoneNumber,
    confirmAction,
    printReceipt,
    filterTable,
    showLoadingSpinner,
    hideLoadingSpinner
};
