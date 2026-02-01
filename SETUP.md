# ⚙️ Setup Instructions

Follow these steps to set up **Agora Campus** on your local machine using XAMPP.

## Prerequisites
- [XAMPP](https://www.apachefriends.org/index.html) (Apache + MySQL + PHP) deployed.

## Installation

### 1. Clone the Repository
Move the project folder to your XAMPP `htdocs` directory.
Assuming your project folder is named `project`:
```bash
C:\xampp\htdocs\project\
```

### 2. Database Setup
1. Open **phpMyAdmin** (usually at `http://localhost/phpmyadmin`).
2. Create a new database named **`p`**.
   > *Note: If you want to use a different database name, update `config/database.php`.*
3. Select the `p` database.
4. Go to the **Import** tab.
5. Choose and import the schema file:
   `database/schema.sql`
6. (Optional) To populate with sample data, import:
   `database/dummy_data.sql`

### 3. Configuration
The database connection is defined in `config/database.php`. The default settings for XAMPP are:
- **Host**: `localhost`
- **User**: `root`
- **Password**: *(empty)*
- **Database**: `p`

If your local setup differs, please update this file.

### 4. File Permissions (Optional)
Ensure the `uploads/` directory and its subdirectories are writable so that users can upload profile pictures and documents.

### 5. Run the Application
Open your browser and navigate to:
[http://localhost/project](http://localhost/project)

## Default Credentials (from Dummy Data)
*Refer to the database or register a new user to start.*
