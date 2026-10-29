-- Step 1: Add age field to people table
ALTER TABLE people ADD COLUMN IF NOT EXISTS age int;

-- Step 2: Calculate and populate age for existing records
UPDATE people SET age = EXTRACT(YEAR FROM AGE(birthdate)) WHERE birthdate IS NOT NULL AND age IS NULL;

-- Step 3: Remove Visitor from category constraint
-- First, update any existing records with Visitor category to Adults
UPDATE people SET category = 'Adults' WHERE category = 'Visitors';

-- Step 4: Drop and recreate the category constraint
ALTER TABLE people DROP CONSTRAINT IF EXISTS people_category_check;
ALTER TABLE people ADD CONSTRAINT people_category_check CHECK (category in ('Kids', 'Youth', 'Adults', 'Seniors'));

-- Verify the changes
SELECT
  COUNT(*) as total_people,
  COUNT(CASE WHEN age IS NOT NULL THEN 1 END) as people_with_age,
  COUNT(CASE WHEN category IS NOT NULL THEN 1 END) as people_with_category,
  COUNT(CASE WHEN category = 'Visitors' THEN 1 END) as remaining_visitors
FROM people;