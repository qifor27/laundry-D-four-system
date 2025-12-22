# 📋 D'four Laundry - Development TODO List

## 🎨 FRONTEND TASKS

### 🔴 Priority: HIGH (Harus Segera)

#### UI/UX Improvements
- [ ] **Responsive Design Testing**
  - Test di berbagai device (mobile, tablet, desktop)
  - Fix layout issues di mobile view
  - Ensure touch-friendly buttons (minimal 44x44px)

- [ ] **Loading States**
  - Tambah loading spinner saat AJAX request
  - Disable button saat form submission
  - Show skeleton loading untuk table data

- [ ] **Form Validation (Client-Side)**
  - Validate required fields sebelum submit
  - Validate format nomor telepon
  - Validate format email (jika ada)
  - Show error messages di bawah input field

- [ ] **Error Handling UI**
  - Better error messages untuk user
  - Toast notifications untuk success/error
  - Modal untuk confirmations (delete, etc)

#### Tailwind CSS Customization
- [ ] **Custom Color Palette**
  - Define brand colors di `tailwind.config.js`
  - Update primary/secondary colors
  - Ensure color consistency across pages

- [ ] **Custom Components**
  - Refine button styles (btn-primary, btn-secondary, etc)
  - Create consistent card styles
  - Standardize form input styles
  - Create badge variants untuk different statuses

- [ ] **Typography System**
  - Add custom font dari Google Fonts
  - Define text size scale
  - Heading styles consistency

#### Page Enhancements
- [ ] **Dashboard Page**
  - Add charts untuk visualisasi data (Chart.js/ApexCharts)
  - Show trend (naik/turun) di statistics cards
  - Add date range filter untuk reports
  - Add quick actions shortcuts

- [ ] **Customers Page**
  - Add pagination untuk large data sets
  - Improve search functionality (debounce)
  - Add bulk actions (select multiple, delete)
  - Show customer transaction history in detail view

- [ ] **Transactions Page**
  - Better filter UI (by date, status, customer)
  - Sort functionality (by date, price, etc)
  - Inline editing untuk quick updates
  - Print receipt button dengan design yang bagus

- [ ] **Check Order Page**
  - Better progress visualization
  - Add QR code scanning option
  - Show estimated completion time
  - Add notification opt-in (WhatsApp/SMS)

#### New Features
- [ ] **Print System**
  - Print receipt/invoice dengan CSS print
  - Thermal printer friendly format
  - Print packing list

- [ ] **Dark Mode**
  - Toggle dark/light theme
  - Save preference to localStorage
  - Smooth transition animation

- [ ] **PWA Features**
  - Add manifest.json
  - Service worker untuk offline capability
  - Install prompt untuk mobile

### 🟡 Priority: MEDIUM (Penting)

- [ ] **Accessibility (a11y)**
  - Add ARIA labels
  - Keyboard navigation support
  - Screen reader friendly
  - Focus indicators yang jelas

- [ ] **Animation & Transitions**
  - Smooth page transitions
  - Micro-interactions pada buttons
  - Modal open/close animations
  - Loading animations

- [ ] **Image Optimization**
  - Add lazy loading untuk images
  - Compress images
  - Add placeholder images

### � Priority: LOW (Nice to Have)

- [ ] **Advanced UI Components**
  - Drag and drop untuk sorting
  - Advanced date picker
  - Rich text editor untuk notes
  - File upload dengan preview

- [ ] **Internationalization (i18n)**
  - Support multiple languages
  - Currency format sesuai locale

---

## ⚙️ BACKEND TASKS

### 🔴 Priority: HIGH (Harus Segera)

#### Authentication & Login System

- [ ] **Google OAuth 2.0 Login - Setup & Configuration**
  - [ ] Setup Google Cloud Console project
  - [ ] Enable Google+ API
  - [ ] Create OAuth 2.0 credentials (Client ID & Secret)
  - [ ] Add authorized redirect URIs (e.g., `http://localhost:8000/api/google-callback.php`)
  - [ ] Save credentials securely

---

##### 🎨 **FRONTEND Tasks** (HTML, Tailwind CSS, JavaScript)

- [ ] **Login Page UI** (`pages/login.php`)
  - [ ] Create modern login page layout dengan Tailwind CSS
  - [ ] Add logo dan branding
  - [ ] Create responsive container (mobile-first)
  - [ ] Add smooth fade-in animations
  - [ ] Implement glassmorphism atau modern card design
  
- [ ] **Google Sign-In Button**
  - [ ] Add Google Sign-In button dengan official styling
  - [ ] Use Google's brand guidelines untuk button design
  - [ ] Add hover effects dan transitions
  - [ ] Add loading spinner saat authentication process
  - [ ] Implement button disabled state
  
- [ ] **Google Sign-In JavaScript** (`assets/js/google-auth.js`)
  - [ ] Load Google Sign-In JavaScript library
    ```html
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    ```
  - [ ] Initialize Google Sign-In dengan Client ID
    ```javascript
    google.accounts.id.initialize({
      client_id: 'YOUR_CLIENT_ID.apps.googleusercontent.com',
      callback: handleCredentialResponse
    });
    ```
  - [ ] Render Google Sign-In button
  - [ ] Handle credential response dari Google
  - [ ] Send JWT token ke backend via AJAX
  - [ ] Handle success response (redirect to dashboard)
  - [ ] Handle error response (show error message)
  
- [ ] **UI Feedback & States**
  - [ ] Show loading overlay saat authentication
  - [ ] Display success message sebelum redirect
  - [ ] Show error messages dengan styling yang jelas
  - [ ] Add toast notifications untuk feedback
  - [ ] Implement smooth page transitions

---

##### ⚙️ **BACKEND Tasks** (PHP, MySQL)

- [ ] **Database Schema** (`database/init_mysql.php`)
  - [ ] Create `users` table
    ```sql
    CREATE TABLE users (
      id INT AUTO_INCREMENT PRIMARY KEY,
      google_id VARCHAR(255) UNIQUE,
      email VARCHAR(255) UNIQUE NOT NULL,
      name VARCHAR(255) NOT NULL,
      profile_picture TEXT,
      role ENUM('admin', 'cashier', 'user') DEFAULT 'user',
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      last_login TIMESTAMP NULL,
      INDEX idx_google_id (google_id),
      INDEX idx_email (email)
    );
    ```
  - [ ] Run migration untuk create table
  
- [ ] **Google OAuth Config** (`config/google-oauth.php`)
  - [ ] Install Google API PHP Client
    ```bash
    composer require google/apiclient:"^2.0"
    ```
  - [ ] Create configuration file dengan Client ID & Secret
  - [ ] Setup redirect URI
  - [ ] Add helper functions untuk OAuth flow
  
- [ ] **Authentication API** (`api/google-auth.php`)
  - [ ] Receive JWT token dari frontend
  - [ ] Verify token dengan Google API
    ```php
    $client = new Google_Client(['client_id' => CLIENT_ID]);
    $payload = $client->verifyIdToken($token);
    ```
  - [ ] Extract user info (google_id, email, name, picture)
  - [ ] Check if user exists di database
  - [ ] If new user: Insert ke `users` table
  - [ ] If existing user: Update `last_login`
  - [ ] Create PHP session
  - [ ] Store user data di session
    ```php
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_name'] = $user['name'];
    ```
  - [ ] Return JSON response (success/error)
  
- [ ] **Session Management** (`includes/auth.php`)
  - [ ] Create helper function `isLoggedIn()`
  - [ ] Create helper function `requireLogin()`
  - [ ] Create helper function `getUserData()`
  - [ ] Implement session security
    - [ ] Session regeneration after login
    - [ ] Secure session configuration
    - [ ] HttpOnly cookies
    - [ ] Session timeout (30 minutes inactivity)
  
- [ ] **Logout Functionality** (`api/logout.php`)
  - [ ] Destroy PHP session
  - [ ] Clear session cookies
  - [ ] Redirect to login page
  - [ ] Return JSON response untuk AJAX logout
  
- [ ] **Protected Pages Middleware**
  - [ ] Add authentication check di semua protected pages
    ```php
    require_once '../includes/auth.php';
    requireLogin(); // Redirect to login if not authenticated
    ```
  - [ ] Protect dashboard, customers, transactions pages
  - [ ] Allow public access untuk `check-order.php`

---

- [ ] **Interactive Captcha System**
  
  **Pilih salah satu opsi berikut:**
  
  **Opsi 1: Google reCAPTCHA v3** (Recommended - Invisible, score-based)
  
  *Frontend Tasks:*
  - [ ] Add reCAPTCHA script ke `pages/login.php`
    ```html
    <script src="https://www.google.com/recaptcha/api.js?render=YOUR_SITE_KEY"></script>
    ```
  - [ ] Execute reCAPTCHA saat form submit
    ```javascript
    grecaptcha.execute('YOUR_SITE_KEY', {action: 'login'})
      .then(token => {
        // Send token ke backend
      });
    ```
  
  *Backend Tasks:*
  - [ ] Setup reCAPTCHA di Google Admin Console
  - [ ] Get Site Key dan Secret Key
  - [ ] Verify token di `api/google-auth.php`
    ```php
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$token");
    $responseData = json_decode($response);
    if ($responseData->success && $responseData->score >= 0.5) {
      // Allow login
    }
    ```
  - [ ] Set threshold score (0.5 recommended)
  - [ ] Log failed attempts
  
  ---
  
  **Opsi 2: hCaptcha** (Privacy-focused alternative)
  
  *Frontend Tasks:*
  - [ ] Add hCaptcha widget ke login page
    ```html
    <div class="h-captcha" data-sitekey="YOUR_SITE_KEY"></div>
    <script src="https://js.hcaptcha.com/1/api.js" async defer></script>
    ```
  - [ ] Get response token saat form submit
  
  *Backend Tasks:*
  - [ ] Setup hCaptcha account
  - [ ] Get Site Key dan Secret Key
  - [ ] Verify response di backend
    ```php
    $verify = file_get_contents("https://hcaptcha.com/siteverify", false, stream_context_create([
      'http' => [
        'method' => 'POST',
        'content' => http_build_query(['secret' => $secret, 'response' => $token])
      ]
    ]));
    ```
  
  ---
  
  **Opsi 3: Custom Interactive Captcha** (Fun & Engaging! 🎮)
  
  *Pilih salah satu jenis:*
  
  **A. Slider Captcha** (Paling Populer)
  
  *Frontend Tasks:* (`assets/js/slider-captcha.js`)
  - [ ] Create slider UI dengan Tailwind CSS
    ```html
    <div class="slider-captcha">
      <div class="slider-track"></div>
      <div class="slider-thumb">Slide to verify →</div>
    </div>
    ```
  - [ ] Implement drag functionality (mouse & touch)
  - [ ] Add smooth animations saat slide
  - [ ] Generate random target position
  - [ ] Validate slider position (tolerance ±5px)
  - [ ] Show success animation (checkmark, confetti)
  - [ ] Reset slider on failure
  
  *Backend Tasks:* (`api/captcha-verify.php`)
  - [ ] Generate random challenge value
  - [ ] Store challenge di session
  - [ ] Verify slider position dari frontend
  - [ ] Rate limiting (max 5 attempts per minute)
  - [ ] Return success/failure response
  
  ---
  
  **B. Math Captcha** (Simple & Effective)
  
  *Frontend Tasks:*
  - [ ] Generate random math question (e.g., "5 + 3 = ?")
  - [ ] Display dengan animasi yang menarik
  - [ ] Create input field untuk answer
  - [ ] Validate answer di client-side (basic check)
  - [ ] Add countdown timer (30 seconds)
  
  *Backend Tasks:*
  - [ ] Generate math question di backend
  - [ ] Store correct answer di session
  - [ ] Verify user's answer
  - [ ] Regenerate question after 3 failed attempts
  
  ---
  
  **C. Puzzle Captcha** (Most Interactive)
  
  *Frontend Tasks:*
  - [ ] Create puzzle pieces dengan Canvas API
  - [ ] Implement drag & drop functionality
  - [ ] Add snap-to-grid effect
  - [ ] Validate puzzle completion
  - [ ] Add visual feedback (glow effect saat correct)
  
  *Backend Tasks:*
  - [ ] Generate puzzle configuration
  - [ ] Store solution di session
  - [ ] Verify puzzle completion
  
  ---
  
  **D. Drawing Captcha** (Creative)
  
  *Frontend Tasks:*
  - [ ] Create canvas untuk drawing
  - [ ] Implement touch/mouse drawing
  - [ ] Detect shape (circle, line, etc.) dengan algorithm
  - [ ] Show instruction ("Draw a circle")
  - [ ] Visual feedback saat correct
  
  *Backend Tasks:*
  - [ ] Generate random shape challenge
  - [ ] Receive canvas data dari frontend
  - [ ] Verify shape (simple pattern matching)
  
  ---
  
  **General Implementation (untuk Custom Captcha):**
  
  *Frontend:* (`assets/js/captcha.js`)
  - [ ] Create captcha component yang reusable
  - [ ] Add smooth animations dengan Tailwind transitions
  - [ ] Make mobile-friendly (touch gestures)
  - [ ] Add accessibility features (keyboard navigation)
  - [ ] Implement retry mechanism
  - [ ] Show attempt counter
  
  *Backend:* (`api/captcha-verify.php`)
  - [ ] Create captcha verification endpoint
  - [ ] Store captcha state di session
  - [ ] Rate limiting (max attempts per IP/session)
  - [ ] Log failed attempts untuk security
  - [ ] Clear captcha after successful verification
  - [ ] Return JSON response



#### Security & Validation
- [ ] **Input Validation**
  - Server-side validation untuk semua forms
  - Validate data types (integer, string, email, phone)
  - Sanitization untuk prevent XSS
  - Rate limiting untuk API endpoints

- [ ] **Authentication System**
  - Login/logout functionality
  - Password hashing (password_hash/password_verify)
  - Session security (session regeneration, timeout)
  - "Remember me" functionality

- [ ] **Authorization**
  - Role-based access control (Admin, Cashier, etc)
  - Protect API endpoints
  - Permission management

- [ ] **CSRF Protection**
  - Generate CSRF tokens
  - Validate tokens pada form submissions
  - Token expiration

#### Database Optimization
- [ ] **Indexes**
  - Review dan optimize database indexes
  - Add composite indexes jika perlu
  - Analyze slow queries

- [ ] **Data Validation**
  - Enforce foreign key constraints
  - Add CHECK constraints
  - Ensure data integrity

- [ ] **Backup System**
  - Auto-backup database daily
  - Backup rotation (keep 7 days)
  - Restore functionality

#### API Enhancements
- [ ] **Error Handling**
  - Consistent error response format
  - Proper HTTP status codes
  - Detailed error logging

- [ ] **Response Format**
  - Standardize JSON response structure
  - Add metadata (pagination info, total, etc)
  - Versioning untuk API endpoints

- [ ] **Performance**
  - Implement caching (Redis/Memcached optional)
  - Query optimization
  - Lazy loading untuk related data

### 🟡 Priority: MEDIUM (Penting)

#### New Features
- [ ] **Service Types Management**
  - CRUD untuk service types
  - Dynamic pricing configuration
  - Active/inactive status

- [ ] **Reports & Analytics**
  - Daily/weekly/monthly sales report
  - Customer analytics
  - Popular services report
  - Revenue trends

- [ ] **Notification System**
  - Email notifications (order ready, etc)
  - WhatsApp integration (API)
  - SMS notifications (optional)

- [ ] **Payment Management**
  - Multiple payment methods
  - Payment status tracking
  - Partial payments support
  - Payment history

- [ ] **Inventory Management** (if needed)
  - Track detergent, supplies
  - Stock alerts
  - Supplier management

#### Export/Import
- [ ] **Export Data**
  - Export reports to Excel (PHPSpreadsheet)
  - Export to PDF (TCPDF/MPDF)
  - Export customer/transaction data

- [ ] **Import Data**
  - Import customers dari Excel/CSV
  - Bulk import transactions
  - Data validation saat import

#### Logging & Monitoring
- [ ] **Activity Logs**
  - Log semua CRUD operations
  - User activity tracking
  - Error logging ke file

- [ ] **Audit Trail**
  - Who created/updated records
  - Timestamp tracking
  - Change history

### 🟢 Priority: LOW (Nice to Have)

- [ ] **Multi-Branch Support**
  - Branch management
  - Transfer orders between branches
  - Consolidated reporting

- [ ] **Customer Portal**
  - Customer registration
  - Order history
  - Loyalty points/rewards

- [ ] **Integration**
  - Payment gateway integration
  - Accounting software integration
  - POS system integration

- [ ] **Advanced Features**
  - Subscription/membership system
  - Pickup and delivery scheduling
  - Route optimization untuk delivery

---

## 🧪 TESTING & QUALITY ASSURANCE

### Testing
- [ ] **Unit Tests** (PHPUnit)
  - Test helper functions
  - Test database functions
  - Test API endpoints

- [ ] **Integration Tests**
  - Test complete workflows
  - Test API responses

- [ ] **Manual Testing**
  - Create test cases document
  - Test all CRUD operations
  - Test edge cases
  - Browser compatibility testing

### Code Quality
- [ ] **Code Review**
  - Follow PSR standards
  - Code documentation (PHPDoc)
  - Remove debug code

- [ ] **Performance Testing**
  - Load testing dengan banyak data
  - Optimize slow queries
  - Memory usage check

- [ ] **Security Audit**
  - SQL injection testing
  - XSS testing
  - CSRF testing
  - Session security check

---

## 📚 DOCUMENTATION

- [ ] **User Manual**
  - How to use dashboard
  - How to create orders
  - How to manage customers
  - Troubleshooting guide

- [ ] **Developer Documentation**
  - API documentation
  - Database schema documentation
  - Deployment guide

- [ ] **Code Comments**
  - Add comments untuk complex logic
  - Update existing comments
  - PHPDoc untuk functions/classes

---

## 🚀 DEPLOYMENT & DEVOPS

- [ ] **Production Setup**
  - Optimize Tailwind CSS (purge, minify)
  - Minify JavaScript
  - Enable PHP OPcache
  - Gzip compression

- [ ] **Environment Configuration**
  - Separate dev/staging/production configs
  - Environment variables (.env file)
  - Error handling (show errors only in dev)

- [ ] **Server Configuration**
  - Configure Apache/Nginx properly
  - Set file permissions correctly
  - HTTPS setup (SSL certificate)

- [ ] **Monitoring**
  - Setup error monitoring
  - Performance monitoring
  - Uptime monitoring

---

## ✅ QUICK WINS (Easy & High Impact)

Ini adalah tasks yang mudah dilakukan tapi berdampak besar:

1. **Add Loading Spinners** - Better UX saat AJAX requests
2. **Improve Error Messages** - User-friendly error messages
3. **Add Confirmation Modals** - Sebelum delete data
4. **Better Status Badges** - Color-coded, easy to read
5. **Add Timestamps** - Show "created at" dan "updated at" di UI
6. **Print Receipt** - Simple CSS print stylesheet
7. **Search Debounce** - Optimize search performance
8. **Toast Notifications** - Better feedback untuk user actions

---

## 📝 NOTES

**Sebelum Mulai Development:**
1. ✅ Baca `docs/learning_by_doing.md` untuk understand struktur projek & teknologi
2. ✅ Setup development environment (PHP server + Tailwind watch)
3. ✅ Initialize database (`php database/init_mysql.php`)
4. ✅ Test aplikasi di browser

**Development Workflow:**
1. Pick task dari TODO list
2. Create branch (jika pakai Git)
3. Implement & test locally
4. Update documentation jika perlu
5. Mark task as done

**Tips:**
- Focus on HIGH priority tasks first
- Test setiap fitur sebelum move ke next task
- Commit code frequently dengan clear messages
- Ask for help jika stuck!

**New Features Coming:**
- 🔐 Google OAuth Login - Modern authentication
- 🎮 Interactive Captcha - Fun & engaging security
- 🎨 Beautiful Login Page - Premium design

---

*Last Updated: 2025-12-22*
*Prioritas bisa berubah sesuai kebutuhan bisnis*
*Check `docs/learning_by_doing.md` untuk panduan lengkap!*