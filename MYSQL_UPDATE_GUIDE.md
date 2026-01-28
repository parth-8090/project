# Update MySQL Database Using phpMyAdmin (XAMPP)

## Method 1: Import Updated Schema (Recommended - Fastest)

### Step 1: Start XAMPP
1. Open XAMPP Control Panel
2. Click **Start** button next to **Apache** and **MySQL**
3. Wait for them to turn green (running)

### Step 2: Open phpMyAdmin
1. Open your browser
2. Go to: `http://localhost/phpmyadmin`
3. You should see the phpMyAdmin dashboard

### Step 3: Select Your Database
1. In the left sidebar, click on database **`p`** (or your database name)
2. You should see all your tables listed

### Step 4: Import the Updated Schema
1. Click on the **"Import"** tab at the top
2. Click **"Choose File"** button
3. Navigate to: `C:\xampp\htdocs\project\database\schema.sql`
4. Select it and click **Open**
5. Scroll down and click **"Import"** button
6. Wait for the import to complete (you'll see a success message)

✅ **Done!** Your database is now updated with the new fields.

---

## Method 2: Manual SQL Update (If you prefer)

If the import doesn't work or you want to do it manually:

### Step 1-3: Same as above (Start XAMPP → Open phpMyAdmin → Select database `p`)

### Step 4: Run SQL Commands
1. Click on the **"SQL"** tab at the top
2. Delete any existing text in the SQL editor
3. Copy and paste **ONE** of these commands at a time:

#### **Command 1: Add creator to groups table**
```sql
ALTER TABLE groups ADD COLUMN created_by INT NOT NULL AFTER description;
ALTER TABLE groups ADD FOREIGN KEY (created_by) REFERENCES students(id) ON DELETE CASCADE;
```

#### **Command 2: Add attachment support to messages table**
```sql
ALTER TABLE messages MODIFY COLUMN message TEXT;
ALTER TABLE messages ADD COLUMN attachment_url VARCHAR(255) AFTER message;
```

### Step 5: Execute
1. Click the **"Go"** button (bottom right)
2. You should see: "Query executed successfully"

✅ **Done!** Your database is now updated.

---

## Verify the Update

### Check if changes were applied:

1. In phpMyAdmin, click on database **`p`**
2. Click on **"groups"** table in the left sidebar
3. Click **"Structure"** tab
4. You should see:
   - `created_by` column (INT)
   - Points to `students` table

5. Click on **"messages"** table
6. Click **"Structure"** tab
7. You should see:
   - `message` column (TEXT, can be NULL)
   - `attachment_url` column (VARCHAR 255)

---

## If Something Goes Wrong

### Error: "Table already exists"
- **Solution:** Click the **"DROP database"** option first if you want a fresh start, OR
- Use Method 2 (Manual SQL) which adds columns only if they don't exist

### Error: "Foreign key constraint fails"
- **Solution:** Make sure the `students` table exists first
- Check that student IDs in `groups` actually exist in `students` table

### Error: "Column already exists"
- **Solution:** Your database might already have the updates! Check the Structure tab to verify.

---

## Quick Checklist

- ✅ XAMPP Apache & MySQL are running (green status)
- ✅ Can access phpMyAdmin at http://localhost/phpmyadmin
- ✅ Database `p` exists with tables
- ✅ `groups` table has `created_by` field
- ✅ `messages` table has `attachment_url` field
- ✅ No error messages in phpMyAdmin

---

## Next Steps After Update

1. **Test Group Creation:**
   - Login to your application: `http://localhost/project`
   - Go to Groups page
   - Create a new group
   - Check if it saves the creator ID

2. **Test Photo Upload:**
   - Open a group chat
   - Try uploading a photo
   - Check if it saves the image URL

3. **Check Database:**
   - In phpMyAdmin, browse the data:
     - Click `groups` table → click "Browse"
     - Check the `created_by` column has your student ID
     - Click `messages` table → click "Browse"
     - Check messages with photos have `attachment_url` filled

---

## Still Having Issues?

Run this diagnostic query in phpMyAdmin SQL tab to see your table structure:

```sql
SHOW CREATE TABLE groups;
SHOW CREATE TABLE messages;
```

This will show you the exact structure of both tables and help identify any missing columns.
