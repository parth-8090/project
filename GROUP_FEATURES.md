# Group Chat & Collaboration Features

## Overview
The application now has complete group creation and chat functionality with photo sharing capabilities. Groups are created by students and stored in MySQL with full member management.

## Features Implemented

### 1. **Create Groups**
- Students can create new groups from the Groups page
- Groups store:
  - Group name
  - Department (optional)
  - Description
  - **Creator ID** (who created the group)
  - Creation timestamp

**Location:** [groups.php](groups.php) - "Create Group" button

### 2. **Join Groups**
- Students can browse all available groups
- Join groups created by other students
- Automatically tracked in the `group_members` table

### 3. **Group Chat**
- Real-time messaging between group members
- Members can send text messages
- Group chat history is maintained (last 50 messages loaded)
- Automatic polling for new messages (every 3 seconds)

**Location:** [group_chat.php](group_chat.php)

### 4. **Photo Upload in Chat**
- Students can upload images while chatting
- Supported formats: JPEG, PNG, GIF, WebP
- Images are stored in `uploads/chat/` directory
- Images display inline in the chat with proper formatting
- Each message can have either text, image, or both

### 5. **Add Members to Group**
- Group members can add other students by their enrollment number
- Members can manage who joins their group chat
- Prevents duplicate membership

## Database Schema

### Groups Table
```sql
CREATE TABLE groups (
    id INT PRIMARY KEY AUTO_INCREMENT,
    group_name VARCHAR(100) NOT NULL,
    department VARCHAR(50),
    class_year INT,
    description TEXT,
    created_by INT NOT NULL,  -- Student ID of creator
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES students(id) ON DELETE CASCADE
);
```

### Group Members Table
```sql
CREATE TABLE group_members (
    id INT PRIMARY KEY AUTO_INCREMENT,
    group_id INT NOT NULL,
    student_id INT NOT NULL,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    UNIQUE KEY unique_membership (group_id, student_id)
);
```

### Messages Table
```sql
CREATE TABLE messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    group_id INT NOT NULL,
    student_id INT NOT NULL,
    message TEXT,                          -- Can be NULL if only image
    attachment_url VARCHAR(255),           -- Image upload path
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);
```

## API Endpoints

All group operations are handled through [api/groups.php](api/groups.php):

### Create Group
```
POST /api/groups.php
Parameters:
  - action: "create_group"
  - group_name: (required)
  - description: (required)
  - department: (optional)
```

### Send Message
```
POST /api/groups.php
Parameters:
  - action: "send_message"
  - group_id: (required)
  - message: (optional, can be empty if attachment sent)
  - attachment: (optional, image file)
```

### Get Messages
```
GET /api/groups.php
Parameters:
  - action: "get_messages"
  - group_id: (required)
  - last_message_id: (optional, for polling)
```

### Add Member
```
POST /api/groups.php
Parameters:
  - action: "add_member"
  - group_id: (required)
  - enrollment_no: (required, student's enrollment number)
```

### Join Group
```
POST /api/groups.php
Parameters:
  - action: "join_group"
  - group_id: (required)
```

### Update Group
```
POST /api/groups.php
Parameters:
  - action: "update_group"
  - group_id: (required)
  - group_name: (required)
  - description: (required)
  - department: (optional)
```

## File Structure

```
project/
├── groups.php                    # Browse & create groups
├── group_chat.php               # Chat interface
├── api/
│   └── groups.php              # API handlers
├── assets/js/
│   ├── groups.js               # Groups page interactions
│   └── chat.js                 # Chat & messaging logic
├── uploads/
│   └── chat/                   # Photo uploads storage
└── config/
    ├── config.php              # Includes CHAT_DIR definition
    └── database.php            # Database connection
```

## Frontend Components

### [groups.js](assets/js/groups.js)
Handles:
- Group card animations
- Create group form submission
- Edit group form submission
- Join group functionality
- Member management UI

### [chat.js](assets/js/chat.js)
Handles:
- Message sending (text & images)
- Real-time message polling
- File attachment preview
- Chat animations
- Add member to group
- Auto-scroll to latest messages

## Photo Upload Configuration

- **Max file size:** 10MB (configurable in [config/config.php](config/config.php))
- **Allowed formats:** JPEG, PNG, GIF, WebP
- **Storage location:** `uploads/chat/`
- **File naming:** `timestamp_originalname.ext`
- **URL format:** `uploads/chat/filename.ext`

## How to Use

### Step 1: Setup Database
```bash
# Import the updated schema
mysql -u root p < database/schema.sql
```

### Step 2: Create a Group
1. Log in as a student
2. Navigate to "Groups" from the dashboard
3. Click "Create Group" button
4. Fill in:
   - Group Name
   - Department (optional)
   - Description
5. Submit - you'll be added as a member automatically

### Step 3: Add Members
1. Open the group chat
2. Click "Add Member" button
3. Enter the student's enrollment number
4. Click submit

### Step 4: Chat with Photos
1. In the group chat, type a message or select an image
2. Images are optional - you can send text-only or image-only messages
3. Click send (or press Enter for text-only)
4. Images will display in the chat

## Security Features

✅ **Access Control:**
- Only logged-in students can create/join groups
- Only group members can access chat
- Only members can see messages

✅ **Data Validation:**
- SQL injection prevention via prepared statements
- XSS prevention via HTML escaping
- File type validation for uploads
- File size limits enforced

✅ **Database Integrity:**
- Foreign key relationships enforced
- Unique member constraints prevent duplicates
- Cascade delete removes orphaned records

## Troubleshooting

### Photos not uploading?
- Check if `uploads/chat/` directory exists and is writable
- Verify file is an image (JPEG, PNG, GIF, WebP)
- Check MAX_FILE_SIZE setting (currently 10MB)

### Can't see new messages?
- Polling occurs every 3 seconds automatically
- Refresh the page to force manual reload
- Check browser console for errors

### Members not showing in chat?
- Verify member is in `group_members` table
- Check enrollment number is correct when adding
- Ensure student account exists

