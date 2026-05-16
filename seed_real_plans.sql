-- =====================================================
-- GYM DB: Replace placeholder data with real plans
-- =====================================================

-- 1. Clear old data (child tables first)
DELETE FROM tblbooking;
DELETE FROM tbladdpackage;
DELETE FROM tblpackage;
DELETE FROM tblcategory;

-- Reset auto-increment
ALTER TABLE tblcategory AUTO_INCREMENT = 1;
ALTER TABLE tblpackage AUTO_INCREMENT = 1;
ALTER TABLE tbladdpackage AUTO_INCREMENT = 1;
ALTER TABLE tblbooking AUTO_INCREMENT = 1;

-- 2. Insert real categories
INSERT INTO tblcategory (id, category_name, status) VALUES
(1, 'Strength Training', '1'),
(2, 'Weight Loss',       '1'),
(3, 'Cardio & Endurance','1'),
(4, 'Yoga & Flexibility','1');

-- 3. Insert real package types (2 tiers per category)
INSERT INTO tblpackage (id, cate_id, PackageName) VALUES
(1, '1', 'Basic Strength'),
(2, '1', 'Advanced Strength'),
(3, '2', 'Fat Burn Starter'),
(4, '2', 'Transformation Pro'),
(5, '3', 'Cardio Basics'),
(6, '3', 'Endurance Elite'),
(7, '4', 'Beginner Yoga'),
(8, '4', 'Full Wellness');

-- 4. Insert 8 real fitness plans
INSERT INTO tbladdpackage (id, category, titlename, PackageType, PackageDuratiobn, Price, uploadphoto, Description) VALUES
(1, '1', 'Beginner Strength',         '1', '1 Month',  '599',  NULL, 'Intro to gym with guided weight training 3x/week, access to all machines, and a starter nutrition guide.'),
(2, '1', 'Power Builder Pro',          '2', '3 Months', '2499', NULL, 'Full periodized strength program with personal trainer sessions, body composition tracking, and priority locker access.'),
(3, '2', 'Fat Burn Kickstart',         '3', '1 Month',  '699',  NULL, 'High-intensity interval training 4x/week targeting fat loss, includes diet plan and weekly weigh-in consultations.'),
(4, '2', '90-Day Body Transformation', '4', '3 Months', '1999', NULL, 'Structured 12-week transformation combining HIIT, strength, and nutrition coaching with before/after progress photos.'),
(5, '3', 'Cardio Blast',              '5', '1 Month',  '599',  NULL, 'Daily treadmill, bike, and rowing programs with heart-rate zone training and unlimited group cardio classes.'),
(6, '3', 'Endurance Elite',           '6', '6 Months', '3999', NULL, 'Long-term endurance conditioning for runners and cyclists, includes VO2 max testing and custom training calendar.'),
(7, '4', 'Yoga & Flexibility',        '7', '1 Month',  '799',  NULL, 'Daily yoga sessions covering Hatha and Vinyasa styles, improve flexibility, balance, and mental focus.'),
(8, '4', 'Total Wellness Package',    '8', '6 Months', '4499', NULL, 'Comprehensive mind-body program with yoga, meditation, mobility drills, and monthly wellness assessments.');
