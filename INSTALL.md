# Installation Guide - Production OS MVP

## System Requirements

- **PHP:** 8.3 or higher
- **MySQL:** 8.0 or higher
- **Web Server:** Apache with mod_rewrite
- **Browser:** Modern browser (Chrome, Firefox, Safari, Edge)

## Step-by-Step Installation

### Step 1: Download & Setup

1. **Clone or download the project**
   ```bash
   git clone https://github.com/webbyart/production-os.git
   cd production-os
   ```

2. **Create folders for uploads**
   ```bash
   mkdir -p assets/uploads
   mkdir -p assets/qrcode
   chmod 755 assets/uploads
   chmod 755 assets/qrcode
   ```

### Step 2: Database Setup

1. **Create database**
   ```bash
   mysql -u root -p
   > CREATE DATABASE production_os;
   > EXIT;
   ```

2. **Import schema**
   ```bash
   mysql -u root -p production_os < database/production_os_mvp.sql
   ```

3. **Verify tables**
   ```bash
   mysql -u root -p
   > USE production_os;
   > SHOW TABLES;
   ```

### Step 3: Configuration

1. **Edit config/config.php**
   ```php
   // Database
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'production_os');
   define('DB_USER', 'root');
   define('DB_PASS', 'your_password');
   
   // Base URL (important for routing)
   define('BASE_URL', 'http://production-os.local/');
   ```

### Step 4: Apache Configuration (XAMPP)

#### Windows XAMPP:

1. **Edit `C:\xampp\apache\conf\extra\httpd-vhosts.conf`**

   Add at the end:
   ```apache
   <VirtualHost *:80>
       ServerName production-os.local
       DocumentRoot "C:/xampp/htdocs/production-os"
       <Directory "C:/xampp/htdocs/production-os">
           Options Indexes FollowSymLinks
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```

2. **Edit `C:\Windows\System32\drivers\etc\hosts`**

   Add:
   ```
   127.0.0.1  production-os.local
   ```

3. **Restart Apache**
   - XAMPP Control Panel → Apache → Restart

#### Linux/Mac:

1. **Create vhost file**
   ```bash
   sudo nano /etc/apache2/sites-available/production-os.conf
   ```

   Add:
   ```apache
   <VirtualHost *:80>
       ServerName production-os.local
       DocumentRoot /var/www/production-os
       <Directory /var/www/production-os>
           Options Indexes FollowSymLinks
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```

2. **Enable site**
   ```bash
   sudo a2ensite production-os
   sudo a2enmod rewrite
   sudo systemctl restart apache2
   ```

3. **Edit `/etc/hosts`**
   ```bash
   sudo nano /etc/hosts
   ```
   Add: `127.0.0.1  production-os.local`

### Step 5: Create .htaccess (if not present)

Create `.htaccess` in root directory:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    
    # Skip for real files and directories
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    
    # Route everything to index.php
    RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
</IfModule>
```

### Step 6: Test Installation

1. **Open browser**
   ```
   http://production-os.local/
   ```

2. **Login with demo credentials**
   - Username: `admin`
   - Password: `password`

3. **Verify dashboard loads**
   - Check statistics cards display correct numbers
   - Charts should render

## Troubleshooting

### Problem: Login page shows but login fails

**Solution:**
- Check database connection: `php config/Database.php`
- Verify credentials in config.php
- Check user table has data: `SELECT * FROM users;`

### Problem: Blank page or 500 error

**Solution:**
1. Check error log:
   ```bash
   # XAMPP Windows
   C:\xampp\apache\logs\error.log
   
   # Linux
   /var/log/apache2/error.log
   ```

2. Enable debugging in config.php:
   ```php
   ini_set('display_errors', 1);
   error_reporting(E_ALL);
   ```

### Problem: Routes not working (404 errors)

**Solution:**
1. Verify mod_rewrite is enabled:
   ```bash
   apache2ctl -M | grep rewrite
   ```

2. Check .htaccess exists in root

3. Verify BASE_URL in config.php matches your URL

### Problem: Database import fails

**Solution:**
1. Check MySQL version: `mysql --version`

2. Try importing with UTF-8:
   ```bash
   mysql -u root -p --default-character-set=utf8mb4 production_os < database/production_os_mvp.sql
   ```

3. Check file size:
   ```bash
   wc -l database/production_os_mvp.sql
   ```

## Default Login Credentials

| Role | Username | Password |
|------|----------|----------|
| Super Admin | admin | password |
| Manager | manager | password |
| Operator | operator | password |
| QC | qc | password |
| Warehouse | warehouse | password |

**⚠️ IMPORTANT:** Change these passwords immediately in production!

```php
// To change password via database:
UPDATE users SET password_hash = '$2y$10$...' WHERE username = 'admin';
```

To generate hash:
```php
<?php
echo password_hash('newpassword', PASSWORD_BCRYPT);
?>
```

## First-Time Setup Checklist

- [ ] Database created and imported
- [ ] config.php configured with correct DB credentials
- [ ] Apache vhost created and hosts file updated
- [ ] .htaccess in place
- [ ] assets/uploads and assets/qrcode folders writable
- [ ] Login works with demo credentials
- [ ] Dashboard shows statistics
- [ ] Changed default passwords

## Performance Optimization

### MySQL Optimization

```sql
-- Add indexes
ALTER TABLE products ADD INDEX idx_status (status);
ALTER TABLE manufacturing_orders ADD INDEX idx_status (status);
ALTER TABLE users ADD INDEX idx_role_id (role_id);
```

### Apache Optimization

Enable compression in `.htaccess`:
```apache
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
</IfModule>
```

### PHP Configuration

```ini
; php.ini
memory_limit = 256M
max_execution_time = 300
upload_max_filesize = 50M
post_max_size = 50M
```

## Backup & Maintenance

### Regular Backups

```bash
# Backup database
mysqldump -u root -p production_os > production_os_backup_$(date +%Y%m%d).sql

# Backup files
tar -czf production-os_backup_$(date +%Y%m%d).tar.gz /path/to/production-os
```

### Update Records

```bash
# Keep activity logs for audit
SELECT * FROM activity_logs WHERE created_at > DATE_SUB(NOW(), INTERVAL 30 DAY);
```

## Next Steps

1. **Test all modules** - Navigate through dashboard, products, etc.
2. **Create sample data** - Add products, materials
3. **Test permissions** - Login with different roles
4. **Configure email** - Setup for notifications (future)
5. **Monitor logs** - Check activity_logs table

## Support

For issues or questions:
1. Check error logs
2. Review README.md
3. Check GitHub issues
4. Contact support

---

**Last Updated:** June 2026
**Version:** 1.0.0
