# Iranian CRM (مدیریت ارتباط با مشتری ایرانی)

A comprehensive, enterprise-grade Customer Relationship Management (CRM) system built specifically for Iranian businesses. It features native Persian support (RTL, Jalali Calendar, Persian Numerals), localized financial tools (Rial/Toman, VAT), and integrations with local services (SMS gateways, Eitaa/Telegram).

## 🚀 Features

### Phase 1: Foundation
- **MVC Architecture**: Custom lightweight router, PDO database layer.
- **Localization**: Full RTL support, Vazirmatn font, Jalali date conversion, Persian numerals.
- **Security**: CSRF protection, XSS sanitization, secure session management.
- **UI**: Modern TailwindCSS interface with Dark Mode.

### Phase 2: Core CRM
- **Company Management**: Iranian specific fields (National ID, Economic Code, Sheba).
- **Contact Management**: Linked contacts with role tracking.
- **Validation**: Native regex for Iranian Mobile (09xx), National IDs, and Postal Codes.
- **360° Profile**: Unified view of company details, contacts, and activity timeline.

### Phase 3: Sales Pipeline
- **Visual Kanban**: Drag-and-drop deal stages using Vanilla JS.
- **Deal Management**: Probability tracking, expected revenue, and closing dates.
- **Financials**: Dynamic calculation of VAT (9%), discounts, and line items.

### Phase 4: Invoicing & Finance
- **Document Generator**: Create Persian Proforma (`پیش‌فاکتور`) and Invoices (`فاکتور`).
- **PDF Engine**: mPDF integration with RTL layout and Vazirmatn font.
- **Number to Words**: Automatic conversion of amounts to Persian text.
- **Financial Instruments**: Track Checks (`چک`) and Promissory Notes (`سفته`).

### Phase 5: Omnichannel Communication
- **Unified Inbox**: SMS, Email, Telegram, Eitaa, WhatsApp in one place.
- **SMS Gateway**: Wrapper for Kavenegar/Melipayamak.
- **Webhooks**: Receivers for Telegram/Eitaa bots.
- **Chat UI**: RTL bubbles, internal notes, and quick replies.

### Phase 6: Support & Automation
- **Ticketing System**: SLA tracking, priority queues, and escalation.
- **Jalali Calendar**: Native task scheduling and meeting planner.
- **Workflow Engine**: Trigger/Action automation (e.g., "Won Deal" → "Send SMS").
- **Cron Jobs**: Automated background processing for SLAs and workflows.

### Phase 7: Analytics & Security
- **Dashboard**: Chart.js with RTL tooltips and Persian number formatting.
- **RBAC**: Role-Based Access Control with field-level permissions.
- **Audit Log**: Immutable trail of all data changes.

### Phase 8: API & Mobile
- **REST API**: Secure JSON endpoints for mobile apps.
- **Global Search**: Full-text search with Persian normalization.
- **PWA**: Installable on mobile devices with offline caching.

---

## 🛠️ Tech Stack

- **Backend**: Pure PHP 8+ (OOP, MVC)
- **Database**: MySQL 8.0+
- **Frontend**: HTML5, CSS3, Vanilla JavaScript (ES6+)
- **Styling**: TailwindCSS (CDN) + Custom CSS Variables
- **Fonts**: Vazirmatn (CDN)
- **Libraries**: Chart.js, mPDF

---

## 📦 Installation Guide

### Prerequisites
- PHP 8.0 or higher
- MySQL 5.7 or higher (MariaDB compatible)
- Web Server (Apache or Nginx)
- Required PHP Extensions: `pdo_mysql`, `curl`, `json`, `mbstring`, `xml`, `zip` (for PDF)

### Step 1: Database Setup
1. Create a new database in MySQL:
   ```sql
   CREATE DATABASE iranian_crm CHARACTER SET utf8mb4 COLLATE utf8mb4_persian_ci;
   ```
2. Import the schema file:
   ```bash
   mysql -u root -p iranian_crm < database.sql
   ```

### Step 2: Configuration
1. Copy the example environment file:
   ```bash
   cp .env.example .env
   ```
2. Edit `.env` with your database credentials and API keys:
   ```ini
   DB_HOST=localhost
   DB_NAME=iranian_crm
   DB_USER=root
   DB_PASS=your_password
   
   # SMS Gateway (Kavenegar)
   SMS_API_KEY=your_kavenegar_api_key
   
   # Telegram Bot
   TELEGRAM_BOT_TOKEN=your_bot_token
   ```

### Step 3: File Permissions
Ensure the web server can read files and write to uploads/logs:
```bash
chmod -R 755 /var/www/html/iranian-crm
chmod -R 777 /var/www/html/iranian-crm/assets/uploads
chmod -R 777 /var/www/html/iranian-crm/logs
```

### Step 4: Web Server Configuration

#### Apache
The project includes a `.htaccess` file in the `public` directory. Ensure `AllowOverride All` is set in your Apache config.
```apache
<Directory "/var/www/html/iranian-crm/public">
    AllowOverride All
    Require all granted
</Directory>
```

#### Nginx
Add this location block to your server config:
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```
*Note: Point your root to the `/public` directory.*

### Step 5: Cron Job Setup
To enable workflow automation and SLA tracking, add a cron job that runs every minute:
```bash
* * * * * /usr/bin/php /path/to/your/project/cron.php >> /path/to/your/project/logs/cron.log 2>&1
```

---

## 🔒 Security Features
- **Prepared Statements**: All DB queries use PDO to prevent SQL Injection.
- **CSRF Tokens**: Included in all state-changing forms.
- **XSS Protection**: Output sanitization via `htmlspecialchars`.
- **Session Security**: Regenerated IDs on login, HttpOnly cookies.
- **RBAC**: Strict permission checks on every controller action.

---

## 📱 PWA Installation
1. Open the CRM in Chrome/Safari on your mobile device.
2. Tap "Share" (iOS) or the Menu (Android).
3. Select "Add to Home Screen".
4. The app will install and work offline for cached pages.

---

## 🤝 Default Credentials
After importing the database, you can log in with:
- **Email**: `admin@crm.local`
- **Password**: `admin123`

*(Change this immediately after first login!)*

---

## 📄 License
Proprietary Software - All Rights Reserved.

## 📞 Support
For issues, please check the `logs/` directory or contact the development team.