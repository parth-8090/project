# Agora Campus

Agora Campus is a comprehensive student-business ecosystem designed to bridge the gap between academic life and professional opportunities. It serves as a unified platform for campus activities, career development, and peer-to-peer interaction.

**Live Demo:** [Agora Campus Live](http://www.agora-campus.page.gd/business_dashboard.php)

---

## 🚀 Key Features

### 🎓 For Students
A centralized hub for academic and social campus life.

*   **👨‍💻 Jobs & Internships**
    *   Browse active job listings from verified businesses.
    *   Apply directly through the portal with your profile.
    *   Track application status (Pending, Approved, Rejected).

*   **🛒 Campus Marketplace**
    *   **Buy & Sell**: List items like books, electronics, and furniture.
    *   **Inquiries**: Send and receive messages about listed items.
    *   **Management**: Manage your listings and mark items as sold.

*   **📚 Notes & Learning**
    *   **Resource Sharing**: Upload and download lecture notes and study materials.
    *   **Peer Support**: Request specific notes or tutoring help from other students.
    *   **Search**: Find resources by subject or department.

*   **🔍 Lost & Found**
    *   **Report**: Post details about lost or found items with location and description.
    *   **Resolve**: Mark items as "Returned" or "Found" once the issue is settled.

*   **📅 Campus Life**
    *   **Events**: View upcoming campus events, workshops, and seminars.
    *   **Groups**: Join student interest groups and participate in discussions.
    *   **Profile**: Showcase your skills, interests, and academic achievements (Points system).

### 🏢 For Businesses
Tools to connect with fresh talent and engage the student community.

*   **📢 Recruitment**
    *   **Post Jobs**: Create detailed job postings with requirements, salary, and duration.
    *   **Manage Listings**: Edit or close job openings as needed.

*   **📝 Application Management**
    *   **Review Candidates**: View detailed student profiles, including skills and academic stats.
    *   **Status Control**: Approve or reject applications with a single click.
    *   **Dashboard**: Get an overview of active jobs and pending applications.

---

## 🛠️ Technology Stack

*   **Frontend**: HTML5, CSS3 (Bootstrap 5 + Custom), JavaScript (jQuery for AJAX).
*   **Backend**: PHP 8.0+.
*   **Database**: MySQL (Relational Schema).
*   **Design**: Responsive UI with Animate.css for smooth transitions.

---

## 📂 Project Structure

```text
/project
├── /api             # AJAX handlers and backend logic
├── /assets          # CSS, JS, Images, and Fonts
├── /config          # Database connection and environment settings
├── /database        # SQL Schema and Dummy Data
├── /includes        # Reusable UI components (Header, Footer, Navbar)
├── /uploads         # Dynamic user content (Profiles, Notes, Marketplace)
├── *.php            # Core application pages (Modules)
└── README.md        # Documentation
```

---

## ⚙️ Quick Setup

1.  **Clone** the repo to your local server (e.g., `xampp/htdocs/`).
2.  **Import Database**:
    *   Create a database named `p` (or update `config/database.php`).
    *   Import `database/schema.sql`.
3.  **Configure**: Ensure `config/database.php` matches your MySQL credentials.
4.  **Launch**: Visit `http://localhost/project`.

*For detailed installation steps, see [SETUP.md](SETUP.md).*

---

## 👥 User Roles & Access

*   **Student**: Access to all campus features (Marketplace, Notes, Jobs, etc.).
*   **Business**: Access to recruitment tools (Post Jobs, View Applications).
*   **Guest**: Limited access (Login/Register required for main features).
