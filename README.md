# Agora Campus - College Ecosystem Website

A comprehensive college ecosystem platform built with PHP, MySQL, and JavaScript, featuring GSAP animations for a modern, interactive user experience.

## Features

### Core Functionality
- **Authentication System**: Separate registration and login for students and businesses
- **Student Dashboard**: Central hub with quick access to all features
- **Job Portal**: Students can browse and apply for jobs posted by businesses
- **Business Dashboard**: Businesses can post jobs, manage applications, and review students
- **Groups & Chat**: Real-time group chat using PHP + AJAX
- **Notes & Learning**: Upload, share, and request notes/tutoring
- **Events Calendar**: View upcoming college events
- **Marketplace**: Buy and sell items within the campus
- **Lost & Found**: Report and track lost or found items
- **Complaints System**: Submit and track complaints
- **Profile Management**: Edit profile with skills, interests, LinkedIn, GitHub links
- **Notifications**: Real-time notifications for various activities
- **Points System**: Gamification with points for various activities

### Technical Features
- **GSAP Animations**: Smooth animations throughout the site
- **Responsive Design**: Mobile-friendly Bootstrap-based layout
- **AJAX Integration**: Smooth interactions without page reloads
- **Secure Authentication**: Password hashing and session management
- **File Uploads**: Support for notes and marketplace images

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Modern web browser

### Setup Steps

1. **Clone or download the project**
   ```bash
   cd agora_campus
   ```

2. **Create MySQL Database**
   - Open phpMyAdmin or MySQL command line
   - Import the schema file: `database/schema.sql`
   - This will create the database and all required tables

3. **Configure Database Connection**
   - Edit `config/database.php`
   - Update database credentials:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'your_username');
     define('DB_PASS', 'your_password');
     define('DB_NAME', 'agora_campus');
     ```

4. **Set Up File Permissions**
   - Ensure the `uploads/` directory is writable:
     ```bash
     chmod -R 777 uploads/
     ```

5. **Configure Web Server**
   - Point your web server document root to the project directory
   - For Apache, ensure mod_rewrite is enabled
   - For development, you can use PHP's built-in server:
     ```bash
     php -S localhost:8000
     ```

6. **Access the Application**
   - Open your browser and navigate to: `http://localhost:8000` (or your configured URL)
   - Register as a student or business to get started

## Database Schema

The database includes the following main tables:
- `students` - Student information and profiles
- `businesses` - Business accounts
- `jobs` - Job postings
- `applications` - Job applications
- `business_reviews` - Reviews from businesses
- `groups` - Class/department groups
- `group_members` - Group membership
- `messages` - Chat messages
- `notes` - Shared notes
- `note_requests` - Note/tutoring requests
- `events` - College events
- `marketplace_items` - Marketplace listings
- `lost_found` - Lost and found items
- `complaints` - Student complaints
- `mentorship` - Mentorship connections
- `notifications` - System notifications

## File Structure

```
agora_campus/
├── api/                 # API endpoints
│   ├── auth.php
│   ├── jobs.php
│   ├── business.php
│   ├── groups.php
│   ├── notes.php
│   ├── marketplace.php
│   └── student.php
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       ├── auth.js
│       ├── dashboard.js
│       ├── jobs.js
│       ├── business.js
│       ├── groups.js
│       ├── chat.js
│       ├── notes.js
│       ├── events.js
│       ├── marketplace.js
│       ├── lost_found.js
│       ├── complaints.js
│       ├── profile.js
│       └── notifications.js
├── config/
│   ├── database.php
│   └── config.php
├── database/
│   └── schema.sql
├── includes/
│   └── navbar.php
├── uploads/            # Uploaded files
│   ├── notes/
│   └── marketplace/
├── dashboard.php
├── login.php
├── register.php
├── jobs.php
├── job_details.php
├── business_dashboard.php
├── post_job.php
├── business_applications.php
├── view_student.php
├── groups.php
├── group_chat.php
├── notes.php
├── events.php
├── marketplace.php
├── lost_found.php
├── complaints.php
├── profile.php
├── notifications.php
└── README.md
```

## Usage

### For Students
1. Register with full name, email, department, enrollment number, year of admission, and birthdate
2. Browse available jobs and apply
3. Join groups and participate in chats
4. Upload and share notes
5. Request notes or tutoring help
6. View and participate in events
7. Buy/sell items in marketplace
8. Report lost/found items
9. Submit complaints
10. Update profile with skills and interests

### For Businesses
1. Register with business name, email, and business type
2. Post job openings with details
3. View and manage applications
4. Review student profiles
5. Approve/reject applications
6. Leave reviews for students

## Points System

Students earn points for:
- Uploading notes: +5 points
- Job application approved: +10 points
- Receiving business reviews: +2 points per star rating

## Security Features

- Password hashing using PHP's `password_hash()`
- Prepared statements to prevent SQL injection
- Input sanitization
- Session management
- Role-based access control

## GSAP Animations

The site uses GSAP (GreenSock Animation Platform) for:
- Page transitions
- Component reveals
- Hover effects
- Counter animations
- Modal popups
- Chat message animations

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## Troubleshooting

### Database Connection Issues
- Verify database credentials in `config/database.php`
- Ensure MySQL service is running
- Check database exists and user has proper permissions

### File Upload Issues
- Check `uploads/` directory permissions
- Verify PHP `upload_max_filesize` and `post_max_size` settings
- Ensure directory exists and is writable

### Session Issues
- Check PHP session configuration
- Verify session directory is writable
- Clear browser cookies if experiencing login issues

## Future Enhancements

- Email notifications
- Advanced search and filtering
- Admin dashboard
- Mobile app
- Real-time notifications using WebSockets
- Advanced analytics

## License

This project is open source and available for educational purposes.

## Support

For issues or questions, please refer to the documentation or contact the development team.
