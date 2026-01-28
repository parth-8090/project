    -- Dummy Data for Agora Campus
-- Note: Passwords are hashed using PHP password_hash()
-- Default password for all accounts: password123
-- Hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi

USE p;

-- Insert 10 Students
INSERT INTO students (full_name, email, password, department, enrollment_no, year_of_admission, birthdate, age, points, linkedin_link, github_link, skills, interests) VALUES
('John Doe', 'john.doe@campus.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Computer Science', 'EN2021001', 2021, '2003-05-15', 20, 150, 'https://linkedin.com/in/johndoe', 'https://github.com/johndoe', 'PHP, JavaScript, Python', 'Web Development, AI'),
('Jane Smith', 'jane.smith@campus.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Electrical Engineering', 'EN2021002', 2021, '2003-08-20', 20, 200, 'https://linkedin.com/in/janesmith', 'https://github.com/janesmith', 'C++, Embedded Systems', 'IoT, Robotics'),
('Mike Johnson', 'mike.johnson@campus.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mechanical Engineering', 'EN2020001', 2020, '2002-03-10', 21, 300, 'https://linkedin.com/in/mikejohnson', 'https://github.com/mikejohnson', 'CAD, SolidWorks', 'Automotive, Design'),
('Sarah Williams', 'sarah.williams@campus.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Business Administration', 'EN2022001', 2022, '2004-11-25', 19, 100, 'https://linkedin.com/in/sarahwilliams', 'https://github.com/sarahwilliams', 'Marketing, Finance', 'Entrepreneurship'),
('David Brown', 'david.brown@campus.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Computer Science', 'EN2021003', 2021, '2003-01-30', 20, 250, 'https://linkedin.com/in/davidbrown', 'https://github.com/davidbrown', 'Java, Spring Boot, React', 'Full Stack Development'),
('Emily Davis', 'emily.davis@campus.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Civil Engineering', 'EN2020002', 2020, '2002-07-12', 21, 180, 'https://linkedin.com/in/emilydavis', 'https://github.com/emilydavis', 'AutoCAD, Project Management', 'Infrastructure'),
('Chris Wilson', 'chris.wilson@campus.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Computer Science', 'EN2022002', 2022, '2004-04-18', 19, 120, 'https://linkedin.com/in/chriswilson', 'https://github.com/chriswilson', 'Python, Machine Learning', 'Data Science, AI'),
('Lisa Anderson', 'lisa.anderson@campus.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Electronics Engineering', 'EN2021004', 2021, '2003-09-05', 20, 220, 'https://linkedin.com/in/lisaanderson', 'https://github.com/lisaanderson', 'VHDL, Verilog', 'Digital Design'),
('Robert Taylor', 'robert.taylor@campus.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mechanical Engineering', 'EN2020003', 2020, '2002-12-22', 21, 280, 'https://linkedin.com/in/roberttaylor', 'https://github.com/roberttaylor', 'MATLAB, ANSYS', 'Thermodynamics'),
('Amanda Martinez', 'amanda.martinez@campus.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Business Administration', 'EN2022003', 2022, '2004-06-08', 19, 90, 'https://linkedin.com/in/amandamartinez', 'https://github.com/amandamartinez', 'Excel, Analytics', 'Business Strategy');

-- Insert 5 Businesses
INSERT INTO businesses (business_name, email, password, business_type) VALUES
('Tech Solutions Inc', 'tech.solutions@business.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Information Technology'),
('Campus Bookstore', 'campus.bookstore@business.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Retail'),
('Food Court Management', 'food.court@business.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Food Service'),
('Student Services Co', 'student.services@business.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Services'),
('Digital Marketing Agency', 'digital.marketing@business.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Marketing');
