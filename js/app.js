/**
 * Barq Delivery Pro - Main Application JavaScript
 * Separated from index.php for better code organization
 */

// ==========================================
// WEB AUDIO API - NOTIFICATION SOUNDS
// ==========================================
let audioContext = null;

function getAudioContext() {
    if (!audioContext) {
        audioContext = new (window.AudioContext || window.webkitAudioContext)();
    }
    return audioContext;
}

function createNotificationSound() {
    try {
        const ctx = getAudioContext();

        // Resume context if suspended (needed for user interaction requirement)
        if (ctx.state === 'suspended') {
            ctx.resume();
        }

        const currentTime = ctx.currentTime;

        // Create oscillator for the main tone
        const osc1 = ctx.createOscillator();
        const osc2 = ctx.createOscillator();
        const gainNode = ctx.createGain();

        // Connect nodes
        osc1.connect(gainNode);
        osc2.connect(gainNode);
        gainNode.connect(ctx.destination);

        // Configure sound - pleasant notification tone
        osc1.type = 'sine';
        osc1.frequency.setValueAtTime(880, currentTime); // A5
        osc1.frequency.setValueAtTime(1046.5, currentTime + 0.1); // C6

        osc2.type = 'sine';
        osc2.frequency.setValueAtTime(659.25, currentTime); // E5
        osc2.frequency.setValueAtTime(783.99, currentTime + 0.1); // G5

        // Envelope
        gainNode.gain.setValueAtTime(0, currentTime);
        gainNode.gain.linearRampToValueAtTime(0.3, currentTime + 0.02);
        gainNode.gain.linearRampToValueAtTime(0.2, currentTime + 0.1);
        gainNode.gain.linearRampToValueAtTime(0.3, currentTime + 0.12);
        gainNode.gain.linearRampToValueAtTime(0, currentTime + 0.3);

        // Start and stop
        osc1.start(currentTime);
        osc2.start(currentTime);
        osc1.stop(currentTime + 0.3);
        osc2.stop(currentTime + 0.3);

        return true;
    } catch(e) {
        console.log('Audio not available:', e);
        return false;
    }
}

// ==========================================
// RTL SUPPORT - PHONE NUMBERS DISPLAY
// ==========================================
const isRTL = document.documentElement.dir === 'rtl';

// Keep phone and number inputs LTR for correct display
document.addEventListener('DOMContentLoaded', function() {
    // Make phone inputs LTR for proper number display (always left-aligned)
    document.querySelectorAll('input[type="tel"], input[name*="phone"]').forEach(input => {
        input.style.direction = 'ltr';
        input.style.textAlign = 'left';
    });

    // Make number inputs LTR (always left-aligned for proper number entry)
    document.querySelectorAll('input[type="number"]').forEach(input => {
        input.style.direction = 'ltr';
        input.style.textAlign = 'left';
    });

    // Ensure phone number displays stay LTR
    document.querySelectorAll('.phone-display, [data-phone]').forEach(el => {
        el.style.direction = 'ltr';
        el.style.unicodeBidi = 'embed';
        el.style.display = 'inline-block';
        el.style.textAlign = 'left';
    });
});

// ==========================================
// NAVBAR SCROLL EFFECT
// ==========================================
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.app-navbar');
    if (navbar) {
        if (window.scrollY > 10) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    }
});

// ==========================================
// AUTH FORM TOGGLE
// ==========================================
function showAuthForm(form) {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const loginToggle = document.getElementById('loginToggle');
    const registerToggle = document.getElementById('registerToggle');
    const loginFooterText = document.getElementById('loginFooterText');
    const registerFooterText = document.getElementById('registerFooterText');

    if (!loginForm || !registerForm) return;

    loginForm.classList.remove('active');
    registerForm.classList.remove('active');
    loginToggle.classList.remove('active');
    registerToggle.classList.remove('active');

    document.getElementById(form + 'Form').classList.add('active');
    document.getElementById(form + 'Toggle').classList.add('active');

    // Toggle footer text
    if (loginFooterText && registerFooterText) {
        if (form === 'login') {
            loginFooterText.style.display = 'block';
            registerFooterText.style.display = 'none';
        } else {
            loginFooterText.style.display = 'none';
            registerFooterText.style.display = 'block';
        }
    }
}

// ==========================================
// FORM VALIDATION
// ==========================================
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        const inputs = this.querySelectorAll('input[required]');
        let valid = true;

        inputs.forEach(input => {
            if (!input.value.trim()) {
                valid = false;
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
            }
        });

        // Password match check for registration
        const password = this.querySelector('input[name="reg_password"]');
        const confirm = this.querySelector('input[name="reg_confirm_password"]');
        if (password && confirm && password.value !== confirm.value) {
            valid = false;
            confirm.classList.add('is-invalid');
        }

        if (!valid) {
            e.preventDefault();
            this.querySelector('button').classList.remove('loading');
        }
    });
});

// Remove invalid class on input
document.querySelectorAll('input').forEach(input => {
    input.addEventListener('input', function() {
        this.classList.remove('is-invalid');
    });
});

// ==========================================
// ADMIN MODALS
// ==========================================
function showAddUserModal(role) {
    document.getElementById('addUserRole').value = role;
    var modal = new bootstrap.Modal(document.getElementById('addUserModal'));
    modal.show();
}

function editUser(user) {
    document.getElementById('edit_user_id').value = user.id;
    document.getElementById('edit_username').value = user.username;
    document.getElementById('edit_role').value = user.role;
    document.getElementById('edit_points').value = user.points;

    var modal = new bootstrap.Modal(document.getElementById('editUserModal'));
    modal.show();
}

// Bulk Recharge Functions
function toggleAllDrivers(checkbox) {
    const driverCheckboxes = document.querySelectorAll('.driver-checkbox');
    driverCheckboxes.forEach(cb => {
        cb.checked = checkbox.checked;
    });
    updateBulkRechargeButton();
}

function updateBulkRechargeButton() {
    const checkedBoxes = document.querySelectorAll('.driver-checkbox:checked');
    const bulkBtn = document.getElementById('bulkRechargeBtn');
    const selectAll = document.getElementById('selectAllDrivers');

    if (checkedBoxes.length > 0) {
        bulkBtn.style.display = 'inline-block';
        const bulkText = (typeof AppTranslations !== 'undefined' && AppTranslations['bulk_recharge']) ? AppTranslations['bulk_recharge'] : 'Bulk Recharge';
        bulkBtn.innerHTML = '<i class="fas fa-coins"></i> ' + bulkText + ' (' + checkedBoxes.length + ')';
    } else {
        bulkBtn.style.display = 'none';
    }

    // Update select all checkbox state
    const allDriverCheckboxes = document.querySelectorAll('.driver-checkbox');
    selectAll.checked = allDriverCheckboxes.length > 0 && checkedBoxes.length === allDriverCheckboxes.length;
}

function showBulkRechargeModal() {
    const checkedBoxes = document.querySelectorAll('.driver-checkbox:checked');
    const driverIds = Array.from(checkedBoxes).map(cb => cb.value).join(',');

    document.getElementById('selectedDriverIds').value = driverIds;
    document.getElementById('selectedDriverCount').textContent = checkedBoxes.length;
    document.getElementById('bulkAmount').value = '';

    var modal = new bootstrap.Modal(document.getElementById('bulkRechargeModal'));
    modal.show();
}

// Promo Code Functions
function showAddPromoCodeModal() {
    document.getElementById('promoCodeForm').reset();
    document.getElementById('promoId').value = '';
    document.getElementById('promoModalTitle').textContent = (typeof AppTranslations !== 'undefined' && AppTranslations['create_promo']) ? AppTranslations['create_promo'] : 'Create Promo Code';
    document.getElementById('isActive').checked = true;
    updateDiscountLabel();

    var modal = new bootstrap.Modal(document.getElementById('promoCodeModal'));
    modal.show();
}

function editPromoCode(promo) {
    document.getElementById('promoId').value = promo.id;
    document.getElementById('promoCode').value = promo.code;
    document.getElementById('discountType').value = promo.discount_type;
    document.getElementById('discountValue').value = promo.discount_value;
    document.getElementById('maxUses').value = promo.max_uses || '';
    document.getElementById('validFrom').value = promo.valid_from ? promo.valid_from.split(' ')[0] : '';
    document.getElementById('validUntil').value = promo.valid_until ? promo.valid_until.split(' ')[0] : '';
    document.getElementById('isActive').checked = promo.is_active == 1;
    document.getElementById('promoModalTitle').textContent = (typeof AppTranslations !== 'undefined' && AppTranslations['edit_promo']) ? AppTranslations['edit_promo'] : 'Edit Promo Code';
    updateDiscountLabel();

    var modal = new bootstrap.Modal(document.getElementById('promoCodeModal'));
    modal.show();
}

function updateDiscountLabel() {
    const type = document.getElementById('discountType').value;
    const label = document.getElementById('discountLabel');
    const help = document.getElementById('discountHelp');

    if (type === 'percentage') {
        label.textContent = (typeof AppTranslations !== 'undefined' && AppTranslations['percentage']) ? AppTranslations['percentage'] : 'Percentage';
        help.textContent = (typeof AppTranslations !== 'undefined' && AppTranslations['percentage_help']) ? AppTranslations['percentage_help'] : 'Enter percentage (e.g., 20 for 20% off)';
        document.getElementById('discountValue').placeholder = 'e.g. 20';
        document.getElementById('discountValue').max = '100';
    } else {
        label.textContent = (typeof AppTranslations !== 'undefined' && AppTranslations['fixed_amount']) ? AppTranslations['fixed_amount'] : 'Fixed Amount (MRU)';
        help.textContent = (typeof AppTranslations !== 'undefined' && AppTranslations['fixed_help']) ? AppTranslations['fixed_help'] : 'Enter fixed discount amount in MRU';
        document.getElementById('discountValue').placeholder = 'e.g. 50';
        document.getElementById('discountValue').removeAttribute('max');
    }
}

function editOrder(order) {
    document.getElementById('edit_order_id').value = order.id;
    document.getElementById('edit_order_customer').value = order.customer_name;
    document.getElementById('edit_order_details').value = order.details;
    document.getElementById('edit_order_address').value = order.address;
    document.getElementById('edit_order_status').value = order.status;
    document.getElementById('edit_order_driver').value = order.driver_id || '';

    var modal = new bootstrap.Modal(document.getElementById('editOrderModal'));
    modal.show();
}

// ==========================================
// ORDER TRACKING FOR CUSTOMERS
// ==========================================
function _showOrderTracking(order, translations) {
    // Reset all steps
    document.querySelectorAll('.progress-step').forEach(step => {
        step.classList.remove('completed', 'active');
    });

    // Mark completed and active steps based on status
    const steps = ['pending', 'accepted', 'picked_up', 'delivered'];
    const currentIndex = steps.indexOf(order.status);

    steps.forEach((step, index) => {
        const stepEl = document.getElementById('step-' + step);
        if (stepEl) {
            if (index < currentIndex) {
                stepEl.classList.add('completed');
            } else if (index === currentIndex) {
                stepEl.classList.add('completed', 'active');
            }
        }
    });

    // Set driver info
    document.getElementById('tracking-driver-name').textContent = order.driver_name;
    document.getElementById('tracking-driver-rating').innerHTML = '<i class="fas fa-star"></i> ' + parseFloat(order.driver_rating).toFixed(1);

    // Set avatar
    const avatarEl = document.getElementById('tracking-driver-avatar');
    if (order.driver_avatar) {
        avatarEl.innerHTML = '<img src="' + order.driver_avatar + '" alt="Driver">';
    } else {
        avatarEl.innerHTML = '<i class="fas fa-user"></i>';
    }

    // Show verified badge
    const verifiedBadge = document.getElementById('tracking-verified-badge');
    verifiedBadge.style.display = order.driver_verified ? 'inline-block' : 'none';

    // Set contact buttons
    if (order.driver_phone) {
        document.getElementById('tracking-call-btn').href = 'tel:+222' + order.driver_phone;
    }

    // Set order details
    document.getElementById('tracking-order-details').textContent = order.details;
    document.getElementById('tracking-order-address').textContent = order.address;

    // Calculate and show time info
    let timeInfo = '';
    if (order.accepted_at) {
        const acceptedTime = new Date(order.accepted_at);
        const now = new Date();
        const diffMins = Math.floor((now - acceptedTime) / 60000);
        if (diffMins < 60) {
            timeInfo = (translations.driver_assigned || 'Driver assigned') + ' ' + diffMins + ' ' + (translations.min || 'min') + ' ago';
        } else {
            const diffHours = Math.floor(diffMins / 60);
            timeInfo = (translations.driver_assigned || 'Driver assigned') + ' ' + diffHours + 'h ago';
        }
    }
    document.getElementById('tracking-time-info').textContent = timeInfo;

    // Show modal
    var modal = new bootstrap.Modal(document.getElementById('orderTrackingModal'));
    modal.show();
}

// ==========================================
// GPS & LOCATION FUNCTIONS
// ==========================================

// Haversine formula for distance calculation
function haversineDistance(lat1, lon1, lat2, lon2) {
    const R = 6371; // Earth's radius in kilometers
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}

// Update driver location (for drivers)
function updateMyLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                fetch('api.php?action=update_location', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ lat, lng })
                }).catch(() => {});
            },
            () => {}
        );
    }
}

// ==========================================
// NOTIFICATION FUNCTIONS
// ==========================================
function showNotification(title, message, type = 'info') {
    const container = document.getElementById('notificationContainer');
    const id = 'toast-' + Date.now();

    const bgClass = type === 'success' ? 'bg-success' : (type === 'warning' ? 'bg-warning' : 'bg-primary');
    const textClass = type === 'warning' ? 'text-dark' : 'text-white';

    const toast = document.createElement('div');
    toast.id = id;
    toast.className = `toast show ${bgClass} ${textClass} mb-2`;
    toast.innerHTML = `
        <div class="toast-header ${bgClass} ${textClass}">
            <i class="fas fa-bell me-2"></i>
            <strong class="me-auto">${title}</strong>
            <button type="button" class="btn-close btn-close-white" onclick="this.closest('.toast').remove()"></button>
        </div>
        <div class="toast-body">${message}</div>
    `;

    container.appendChild(toast);

    // Play notification sound
    playNotificationSound();

    // Auto remove after 5 seconds
    setTimeout(() => {
        const el = document.getElementById(id);
        if (el) el.remove();
    }, 5000);
}

function playNotificationSound() {
    // Use Web Audio API for notification sound
    createNotificationSound();
}

// Initialize audio context on first user interaction (required by browsers)
document.addEventListener('click', function initAudio() {
    getAudioContext();
    document.removeEventListener('click', initAudio);
}, { once: true });

// ==========================================
// UTILITY FUNCTIONS
// ==========================================

// Escape HTML to prevent XSS
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text || '';
    return div.innerHTML;
}

// ==========================================
// DRIVER ORDER BUBBLE FUNCTIONS
// ==========================================

// Track displayed order IDs to prevent duplicates
let displayedOrderIds = new Set();
let driverLat = null;
let driverLng = null;

// Play order ring sound
function playOrderRingSound() {
    try {
        const ctx = getAudioContext();
        const now = ctx.currentTime;

        // Create a pleasant ring tone
        for (let i = 0; i < 3; i++) {
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();

            osc.connect(gain);
            gain.connect(ctx.destination);

            osc.type = 'sine';
            osc.frequency.setValueAtTime(880, now + i * 0.3); // A5 note
            osc.frequency.setValueAtTime(1100, now + i * 0.3 + 0.1); // C#6 note

            gain.gain.setValueAtTime(0.3, now + i * 0.3);
            gain.gain.exponentialRampToValueAtTime(0.01, now + i * 0.3 + 0.25);

            osc.start(now + i * 0.3);
            osc.stop(now + i * 0.3 + 0.3);
        }
    } catch(e) {
        console.log('Sound not available');
    }
}

// Create order bubble HTML
function createOrderBubble(order, translations) {
    const bubble = document.createElement('div');
    bubble.className = 'order-bubble';
    bubble.id = `order-bubble-${order.id}`;
    bubble.dataset.orderId = order.id;

    const distanceText = order.distance ? `${parseFloat(order.distance).toFixed(1)} ${translations.km || 'km'}` : '---';
    const phone = order.client_phone || (translations.no_phone || 'No phone');

    bubble.innerHTML = `
        <div class="order-bubble-header">
            <div class="new-order-badge">
                <i class="fas fa-bell"></i>
                ${translations.new_order_nearby || 'New Order Nearby!'}
            </div>
            <div class="order-bubble-timer"><span class="timer-seconds">10</span>s</div>
        </div>
        <div class="order-bubble-body">
            <div class="order-bubble-distance">
                <i class="fas fa-route"></i>
                ${distanceText}
            </div>
            <div class="order-bubble-details">${escapeHtml(order.details)}</div>
            <div class="order-bubble-address">
                <i class="fas fa-map-marker-alt"></i>
                <span>${escapeHtml(order.address)}</span>
            </div>
            <div class="order-bubble-phone">
                <i class="fas fa-phone"></i>
                <a href="tel:+222${phone}" dir="ltr">+222 ${phone}</a>
            </div>
            <div class="order-bubble-actions">
                <button class="btn btn-accept" onclick="acceptOrderFromBubble(${order.id}, this)">
                    <i class="fas fa-check me-2"></i>${translations.accept || 'Accept'}
                </button>
                <button class="btn btn-decline" onclick="declineOrderBubble(${order.id})">
                    <i class="fas fa-times me-2"></i>${translations.decline || 'Decline'}
                </button>
            </div>
        </div>
        <div class="order-bubble-progress">
            <div class="order-bubble-progress-bar" style="width: 100%"></div>
        </div>
    `;

    return bubble;
}

// Show order bubble with countdown
function showOrderBubble(order, translations) {
    if (displayedOrderIds.has(order.id)) return;

    displayedOrderIds.add(order.id);

    const container = document.getElementById('orderBubbleContainer');
    if (!container) return;

    const bubble = createOrderBubble(order, translations);
    container.appendChild(bubble);

    // Play ring sound
    playOrderRingSound();

    // Start countdown
    let secondsLeft = 10;
    const timerSpan = bubble.querySelector('.timer-seconds');
    const progressBar = bubble.querySelector('.order-bubble-progress-bar');

    const countdown = setInterval(() => {
        secondsLeft--;
        if (timerSpan) timerSpan.textContent = secondsLeft;
        if (progressBar) progressBar.style.width = (secondsLeft / 10 * 100) + '%';

        if (secondsLeft <= 0) {
            clearInterval(countdown);
            removeBubble(order.id);
        }
    }, 1000);

    // Store countdown reference
    bubble.dataset.countdown = countdown;
}

// Decline order bubble (just dismiss it)
function declineOrderBubble(orderId) {
    removeBubble(orderId);
    // Keep in set for this session to avoid showing again
}

// Remove bubble with animation
function removeBubble(orderId) {
    const bubble = document.getElementById(`order-bubble-${orderId}`);
    if (bubble) {
        // Clear countdown
        if (bubble.dataset.countdown) {
            clearInterval(parseInt(bubble.dataset.countdown));
        }

        bubble.classList.add('fade-out');
        setTimeout(() => bubble.remove(), 400);
    }
}

// Fetch nearby orders for driver
function fetchNearbyOrders(translations) {
    if (!driverLat || !driverLng) {
        // Get current position first
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    driverLat = pos.coords.latitude;
                    driverLng = pos.coords.longitude;
                    doFetchNearbyOrders(translations);
                },
                () => {}
            );
        }
        return;
    }
    doFetchNearbyOrders(translations);
}

function doFetchNearbyOrders(translations) {
    fetch(`api.php?action=get_nearby_orders&lat=${driverLat}&lng=${driverLng}&max_distance=7`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.orders && data.orders.length > 0) {
                data.orders.forEach(order => {
                    showOrderBubble(order, translations);
                });
            }
        })
        .catch(() => {});
}

// ==========================================
// DRIVER GPS TOGGLE FUNCTIONALITY
// ==========================================
let gpsEnabled = false;
let gpsWatchId = null;

function _toggleDriverGPS(translations) {
    const btn = document.getElementById('gpsToggleBtn');
    const toggle = document.getElementById('gpsToggle');
    const label = document.getElementById('gpsStatusLabel');
    const detail = document.getElementById('gpsStatusDetail');
    const accuracyBadge = document.getElementById('gpsAccuracyBadge');

    // New dashboard control panel elements
    const controlBtn = document.getElementById('gpsControlBtn');
    const controlIcon = document.getElementById('gpsControlIcon');
    const controlLabel = document.getElementById('gpsControlLabel');
    const controlHint = document.getElementById('gpsControlHint');
    const statusBar = document.getElementById('gpsStatusBar');
    const statusText = document.getElementById('gpsStatusText');

    if (!navigator.geolocation) {
        alert(translations.geolocation_not_supported || 'Geolocation is not supported by your browser');
        return;
    }

    if (gpsEnabled) {
        // Disable GPS
        if (gpsWatchId !== null) {
            navigator.geolocation.clearWatch(gpsWatchId);
            gpsWatchId = null;
        }
        gpsEnabled = false;

        if (btn) {
            btn.classList.remove('on', 'loading');
            btn.classList.add('off');
        }
        if (toggle) toggle.classList.remove('active');
        if (label) {
            label.classList.remove('on');
            label.classList.add('off');
            label.innerHTML = '<i class="fas fa-satellite-dish me-1"></i>' + (translations.gps_disabled || 'GPS Disabled');
        }
        if (detail) detail.textContent = translations.gps_driver_note || 'Enable GPS to see nearby orders';
        if (accuracyBadge) accuracyBadge.style.display = 'none';

        // Update dashboard control panel
        if (controlIcon) {
            controlIcon.classList.remove('gps-on', 'gps-loading');
            controlIcon.classList.add('gps-off');
        }
        if (controlLabel) controlLabel.textContent = translations.gps_disabled || 'GPS Off';
        if (controlHint) controlHint.textContent = translations.tap_to_enable_gps || 'Tap to enable GPS';
        if (statusBar) statusBar.style.display = 'none';
        if (controlBtn) controlBtn.classList.remove('active');

        // Reset driver location variables
        driverLat = null;
        driverLng = null;
    } else {
        // Enable GPS - show loading state
        if (btn) {
            btn.classList.remove('off', 'on');
            btn.classList.add('loading');
            btn.innerHTML = '<i class="fas fa-spinner"></i>';
        }
        if (label) label.innerHTML = '<i class="fas fa-satellite-dish me-1"></i>' + (translations.updating_location || 'Updating location...');

        // Update dashboard control panel - loading state
        if (controlIcon) {
            controlIcon.classList.remove('gps-off', 'gps-on');
            controlIcon.classList.add('gps-loading');
            controlIcon.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        }
        if (controlLabel) controlLabel.textContent = translations.updating_location || 'Locating...';
        if (controlHint) controlHint.textContent = translations.please_wait || 'Please wait...';

        navigator.geolocation.getCurrentPosition(
            (position) => {
                gpsEnabled = true;
                driverLat = position.coords.latitude;
                driverLng = position.coords.longitude;
                const accuracy = Math.round(position.coords.accuracy);

                if (btn) {
                    btn.classList.remove('loading');
                    btn.classList.add('on');
                    btn.innerHTML = '<i class="fas fa-location-crosshairs"></i>';
                }
                if (toggle) toggle.classList.add('active');
                if (label) {
                    label.classList.remove('off');
                    label.classList.add('on');
                    label.innerHTML = '<i class="fas fa-check-circle me-1"></i>' + (translations.gps_enabled || 'GPS Enabled');
                }
                if (detail) detail.textContent = translations.location_updated || 'Location updated';
                if (accuracyBadge) accuracyBadge.style.display = 'inline-flex';
                const accuracyValue = document.getElementById('gpsAccuracyValue');
                if (accuracyValue) accuracyValue.textContent = accuracy;

                // Update dashboard control panel - enabled state
                if (controlIcon) {
                    controlIcon.classList.remove('gps-off', 'gps-loading');
                    controlIcon.classList.add('gps-on');
                    controlIcon.innerHTML = '<i class="fas fa-location-crosshairs"></i>';
                }
                if (controlLabel) controlLabel.textContent = translations.gps_enabled || 'GPS On';
                if (controlHint) controlHint.textContent = translations.location_active || 'Location active';
                if (statusBar) statusBar.style.display = 'block';
                if (statusText) statusText.textContent = (translations.accuracy || 'Accuracy') + ': ' + accuracy + 'm';
                if (controlBtn) controlBtn.classList.add('active');

                // Update location on server
                updateMyLocation();

                // Start watching position
                gpsWatchId = navigator.geolocation.watchPosition(
                    (pos) => {
                        driverLat = pos.coords.latitude;
                        driverLng = pos.coords.longitude;
                        const acc = Math.round(pos.coords.accuracy);
                        const accEl = document.getElementById('gpsAccuracyValue');
                        if (accEl) accEl.textContent = acc;
                        const statusTextEl = document.getElementById('gpsStatusText');
                        if (statusTextEl) statusTextEl.textContent = (translations.accuracy || 'Accuracy') + ': ' + acc + 'm';
                        updateMyLocation();
                    },
                    () => {},
                    { enableHighAccuracy: true, timeout: 30000, maximumAge: 5000 }
                );

                // Start fetching nearby orders
                fetchNearbyOrders(translations);
            },
            (error) => {
                if (btn) {
                    btn.classList.remove('loading');
                    btn.classList.add('off');
                    btn.innerHTML = '<i class="fas fa-location-crosshairs"></i>';
                }
                if (label) label.innerHTML = '<i class="fas fa-exclamation-triangle me-1 text-warning"></i>' + (translations.location_error || 'Location error');

                // Update dashboard control panel - error state
                if (controlIcon) {
                    controlIcon.classList.remove('gps-on', 'gps-loading');
                    controlIcon.classList.add('gps-off');
                    controlIcon.innerHTML = '<i class="fas fa-location-crosshairs"></i>';
                }
                if (controlLabel) controlLabel.textContent = translations.location_error || 'GPS Error';
                if (controlHint) controlHint.textContent = translations.tap_to_retry || 'Tap to retry';

                let msg = translations.location_error || 'Error getting location';
                if (error.code === error.PERMISSION_DENIED) {
                    msg = translations.location_denied || 'Location access denied. Please enable GPS.';
                    if (detail) detail.textContent = msg;
                    if (controlHint) controlHint.textContent = translations.enable_in_settings || 'Enable in settings';
                }
                alert(msg);
            },
            { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
        );
    }
}

// ==========================================
// GET PICKUP LOCATION FOR CUSTOMER ORDERS
// ==========================================
function _getPickupLocation(translations, lang) {
    if (!navigator.geolocation) {
        alert(translations.geolocation_not_supported || 'Geolocation is not supported by your browser');
        return;
    }

    const btn = document.getElementById('gpsBtn');
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    btn.disabled = true;

    navigator.geolocation.getCurrentPosition(
        (position) => {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            document.getElementById('pickupLat').value = lat;
            document.getElementById('pickupLng').value = lng;

            // Reverse geocode to get address
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&accept-language=${lang}`)
                .then(response => response.json())
                .then(data => {
                    if (data.display_name) {
                        let address = data.display_name.split(', ').slice(0, 3).join(', ');
                        document.getElementById('pickupAddress').value = address;
                    } else {
                        document.getElementById('pickupAddress').value = lat.toFixed(5) + ', ' + lng.toFixed(5);
                    }
                    btn.innerHTML = '<i class="fas fa-check"></i>';
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-primary');
                    setTimeout(() => {
                        btn.innerHTML = originalHtml;
                        btn.classList.remove('btn-primary');
                        btn.classList.add('btn-success');
                        btn.disabled = false;
                    }, 2000);
                })
                .catch(() => {
                    document.getElementById('pickupAddress').value = lat.toFixed(5) + ', ' + lng.toFixed(5);
                    btn.innerHTML = '<i class="fas fa-check"></i>';
                    btn.disabled = false;
                });
        },
        (error) => {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
            let msg = translations.location_error || 'Error getting location';
            if (error.code === error.PERMISSION_DENIED) {
                msg = translations.location_denied || 'Location access denied. Please enable GPS.';
            }
            alert(msg);
        },
        { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
    );
}

// ==========================================
// ACCEPT ORDER FROM BUBBLE (FOR DRIVERS)
// ==========================================
function _acceptOrderFromBubble(orderId, btn, translations) {
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    btn.disabled = true;

    // Submit accept request
    fetch('actions.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `accept_order=1&oid=${orderId}`
    })
    .then(response => {
        if (response.redirected || response.ok) {
            // Success - remove bubble and reload page
            removeBubble(orderId);
            showNotification(translations.success || 'Success', translations.order_accepted || 'Order accepted!', 'success');
            setTimeout(() => location.reload(), 1000);
        }
    })
    .catch(() => {
        btn.innerHTML = '<i class="fas fa-check me-2"></i>' + (translations.accept || 'Accept');
        btn.disabled = false;
        showNotification(translations.error || 'Error', translations.try_again || 'Please try again', 'warning');
    });
}

// ==========================================
// REAL-TIME NOTIFICATIONS POLLING
// ==========================================
function initRealtimePolling(userRole, translations) {
    let lastCheck = Math.floor(Date.now() / 1000);

    function checkForUpdates() {
        fetch(`api.php?action=check_orders&last_check=${lastCheck}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    lastCheck = data.timestamp;

                    if (userRole === 'driver' && data.should_notify && data.new_orders > 0) {
                        showNotification(
                            translations.new_order_alert,
                            `${data.new_orders} ${translations.new_order}`,
                            'warning'
                        );

                        // Update pending badge
                        const badge = document.getElementById('pendingBadge');
                        if (badge && data.pending_count > 0) {
                            badge.textContent = data.pending_count;
                            badge.style.display = 'inline';
                        }

                        // Refresh page to show new orders
                        setTimeout(() => location.reload(), 2000);
                    }

                    if (userRole === 'customer' && data.should_notify && data.changed_orders.length > 0) {
                        data.changed_orders.forEach(order => {
                            showNotification(
                                translations.order_status_changed,
                                `Order #${order.order_id}: ${order.status}`,
                                'success'
                            );
                        });

                        // Refresh page to show updated status
                        setTimeout(() => location.reload(), 2000);
                    }
                }
            })
            .catch(err => console.log('Check failed:', err));
    }

    // Check every 10 seconds
    setInterval(checkForUpdates, 10000);

    // Initial check after 3 seconds
    setTimeout(checkForUpdates, 3000);
}

// ==========================================
// DRIVER INITIALIZATION
// ==========================================
function initDriverFeatures(translations, hasExistingLocation) {
    // Update location every minute
    setInterval(updateMyLocation, 60000);
    updateMyLocation(); // Initial update

    // Auto-enable GPS if driver has existing location
    if (hasExistingLocation) {
        setTimeout(() => {
            if (!gpsEnabled) {
                _toggleDriverGPS(translations);
            }
        }, 1000);
    }

    // Initialize GPS and start polling for new orders
    navigator.geolocation.getCurrentPosition(
        (pos) => {
            driverLat = pos.coords.latitude;
            driverLng = pos.coords.longitude;

            // Start polling for new orders every 15 seconds
            fetchNearbyOrders(translations);
            setInterval(() => fetchNearbyOrders(translations), 15000);
        },
        () => {
            console.log('GPS not available - order notifications disabled');
        },
        { enableHighAccuracy: true }
    );
}

// ==========================================
// FORM LOADING STATES
// ==========================================

// Add loading state to all forms on submit
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');

    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // Don't add loading state to forms that shouldn't have it
            if (form.classList.contains('no-loading')) {
                return;
            }

            // Add loading class to form
            form.classList.add('submitting');

            // Find submit button and add loading state
            const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
            if (submitBtn) {
                submitBtn.classList.add('btn-loading');
                submitBtn.disabled = true;

                // Store original text
                if (!submitBtn.dataset.originalText) {
                    submitBtn.dataset.originalText = submitBtn.innerHTML;
                }
            }

            // Show loading overlay for important forms
            if (form.id === 'newOrderForm' || form.classList.contains('show-overlay')) {
                showLoadingOverlay();
            }
        });
    });

    // Modal forms
    const modalForms = document.querySelectorAll('.modal form');
    modalForms.forEach(form => {
        form.addEventListener('submit', function() {
            showLoadingOverlay();
        });
    });
});

// Loading overlay functions
function showLoadingOverlay() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.classList.add('active');
    }
}

function hideLoadingOverlay() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.classList.remove('active');
    }
}

// Hide loading overlay on page load (in case of redirect back)
window.addEventListener('load', function() {
    hideLoadingOverlay();
});

// ==========================================
// ENHANCED ERROR HANDLING
// ==========================================

// Auto-hide flash messages after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px)';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
});

// ==========================================
// PROMO CODE VALIDATION
// ==========================================
function validatePromoCode() {
    const input = document.getElementById('promoCodeInput');
    const feedback = document.getElementById('promoFeedback');
    const btn = document.getElementById('validatePromoBtn');
    const code = input.value.trim().toUpperCase();

    if (!code) {
        feedback.innerHTML = '';
        feedback.className = 'text-muted';
        return;
    }

    // Show loading state
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    feedback.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Validating...';
    feedback.className = 'text-info';

    // Validate promo code via API
    fetch('api.php?action=validate_promo&code=' + encodeURIComponent(code))
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check"></i> Apply';

            if (data.success) {
                feedback.innerHTML = '<i class="fas fa-check-circle me-1"></i>' + data.message;
                feedback.className = 'text-success fw-bold';
                input.classList.add('is-valid');
                input.classList.remove('is-invalid');
            } else {
                feedback.innerHTML = '<i class="fas fa-exclamation-circle me-1"></i>' + data.message;
                feedback.className = 'text-danger';
                input.classList.add('is-invalid');
                input.classList.remove('is-valid');
            }
        })
        .catch(error => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check"></i> Apply';
            feedback.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>Error validating code';
            feedback.className = 'text-warning';
        });
}
