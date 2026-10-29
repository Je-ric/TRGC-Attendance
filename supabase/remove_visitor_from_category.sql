-- Remove Visitor from category constraint
-- First, update any existing records with Visitor category to Adults (or appropriate age-based category)
UPDATE people SET category = 'Adults' WHERE category = 'Visitors';

-- Then alter the constraint to remove Visitors
ALTER TABLE people DROP CONSTRAINT people_category_check;
ALTER TABLE people ADD CONSTRAINT people_category_check CHECK (category in ('Kids', 'Youth', 'Adults', 'Seniors'));