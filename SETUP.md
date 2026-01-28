# Agora Campus - Setup Guide

## Quick Start

### Step 1: Database Setup
1. Open phpMyAdmin or MySQL command line
2. Create a new database (or use existing MySQL installation)
3. Import the schema file:
   ```sql
   source database/schema.sql
   ```
   Or in phpMyAdmin: Import → Select `database/schema.sql` → Go

### Step 2: Configure Database
Edit `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // Your MySQL username
define('DB_PASS', '');           // Your MySQL password
define('DB_NAME', 'p');
```

### Step 3: Create Upload Directories
The application will automatically create these, but you can create them manually:
```bash
mkdir -p uploads/notes
mkdir -p uploads/marketplace
chmod -R 777 uploads/
```

### Step 4: Start the Server

**Option A: PHP Built-in Server (Development)**
```bash
php -S localhost:8000
```
Then open: http://localhost:8000

**Option B: Apache/Nginx**
- Place project in web root (e.g., `/var/www/html/agora_campus` or `C:\xampp\htdocs\agora_campus`)
- Access via: http://localhost/agora_campus

### Step 5: First Use
1. Open the application in your browser
2. Click "Register" to create an account
3. Choose "Student" or "Business" registration
4. Fill in the required information
5. Login and start using the platform!

## Sample Data (Optional)

You can insert sample data for testing:

```sql
-- Sample Group
INSERT INTO groups (group_name, department, description) 
VALUES ('Computer Science 2024', 'Computer Science', 'Group for CS students');

-- Sample Event
INSERT INTO events (title, description, event_type, event_date, event_time, venue)
VALUES ('Tech Fest 2024', 'Annual technical festival', 'Festival', '2024-12-15', '10:00:00', 'Main Auditorium');
```

## Troubleshooting

### "Connection failed" error
- Check MySQL service is running
- Verify database credentials in `config/database.php`
- Ensure database `p` exists

### File upload not working
- Check `uploads/` directory permissions (should be 777 or writable)
- Verify PHP `upload_max_filesize` in php.ini
- Check `post_max_size` setting

### Session issues
- Ensure PHP sessions are enabled
- Check session directory is writable
- Clear browser cookies

### GSAP animations not working
- Check browser console for JavaScript errors
- Ensure GSAP CDN is accessible
- Verify internet connection for CDN resources

## Default Features

Once set up, the platform includes:
- ✅ Student and Business registration/login
- ✅ Job posting and applications
- ✅ Group chat system
- ✅ Notes sharing
- ✅ Events calendar
- ✅ Marketplace
- ✅ Lost & Found
- ✅ Complaints system
- ✅ Points and gamification
- ✅ Profile management

## Next Steps

1. Customize the design in `assets/css/style.css`
2. Add more features as needed
3. Configure email notifications (if needed)
4. Set up SSL for production
5. Add admin panel for managing content

## Support

For issues, check:
- PHP error logs
- Browser console (F12)
- MySQL error logs
- README.md for detailed documentation
