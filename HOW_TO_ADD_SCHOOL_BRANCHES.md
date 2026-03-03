# How to Add School Branches - Step by Step Guide

## Overview
The multi-branch feature allows schools to manage multiple branches/campuses from a single system. Each branch can have its own students, teachers, classes, and sections.

---

## Step-by-Step Instructions

### Step 1: Access Branch Management
1. Login to your school management system
2. From the sidebar menu, navigate to: **Academic Mgmt → Branches**
3. You will see the Branch Management page

### Step 2: Add a New Branch

#### For Master Admin:
1. Click the **"Add Branch"** button (blue button with + icon)
2. A modal form will open
3. Fill in the following details:

   **Required Fields:**
   - **Select School**: Choose the school from dropdown
   - **Branch Name**: Enter branch name (e.g., "Main Campus", "North Branch", "Downtown Branch")

   **Optional Fields:**
   - **Branch Code**: Short code for the branch (e.g., "MC", "NB", "DB")
   - **Address**: Full address of the branch
   - **Phone**: Contact number for the branch
   - **Email**: Email address for the branch
   - **Main Branch**: Check this box if this is the main/primary branch
   - **Active**: Check this box to activate the branch (checked by default)

4. Click **"Save Branch"** button
5. The branch will be created and displayed in the table

#### For School Admin (Non-Master Admin):
1. Click the **"Add Branch"** button
2. The school is automatically selected (your school)
3. Fill in the branch details (same as above)
4. Click **"Save Branch"**

### Step 3: View Branches
- All created branches will be displayed in the table with:
  - School Name
  - Branch Name (with "Main" badge if it's the main branch)
  - Branch Code
  - Contact Information (Phone & Email)
  - Address
  - Status (Active/Inactive badge)
  - Action buttons (Edit/Delete)

### Step 4: Edit a Branch
1. Click the **Edit** button (yellow button with pencil icon) next to the branch
2. The form will open with existing data
3. Modify the required fields
4. Click **"Save Branch"**

### Step 5: Delete a Branch
1. Click the **Delete** button (red button with trash icon)
2. Confirm the deletion
3. **Note**: Main branches cannot be deleted (safety feature)

---

## Important Notes

### Main Branch
- Only ONE branch can be marked as "Main Branch" per school
- When you mark a branch as main, any previously marked main branch will automatically be unmarked
- Main branches cannot be deleted

### Branch Status
- **Active**: Branch is operational and can be used
- **Inactive**: Branch is disabled but data is preserved

### Branch Code
- Use short, memorable codes (2-4 characters recommended)
- Examples: MC (Main Campus), NB (North Branch), SB (South Branch)

---

## Using Branches in Other Modules

Once branches are created, you can assign them to:
- **Students**: During admission, select the branch
- **Teachers**: When adding teacher profiles, select the branch
- **Classes**: Assign classes to specific branches
- **Sections**: Assign sections to specific branches

---

## Filtering by Branch

### For Master Admin:
- Use the school filter dropdown at the top to view branches of a specific school
- Leave it empty to view all branches across all schools

### For School Admin:
- You will only see branches belonging to your school

---

## Troubleshooting

### Issue: Branches not showing after creation
**Solution**: 
1. Refresh the page (F5)
2. Check browser console for JavaScript errors (F12)
3. Verify the branch was created by checking the database

### Issue: Cannot delete a branch
**Possible Reasons**:
- The branch is marked as "Main Branch" (main branches cannot be deleted)
- The branch has associated data (students, teachers, etc.)

### Issue: Form not opening
**Solution**:
1. Clear browser cache
2. Check if jQuery and Bootstrap are loaded properly
3. Check browser console for errors

---

## Database Structure

The branches table includes:
- `id`: Unique identifier
- `school_id`: Associated school
- `name`: Branch name
- `code`: Branch code
- `address`: Branch address
- `phone`: Contact phone
- `email`: Contact email
- `is_main`: Main branch flag (boolean)
- `is_active`: Active status (boolean)
- `created_at`: Creation timestamp
- `updated_at`: Last update timestamp

---

## API Endpoints (for developers)

- **GET** `/admin/branches` - List all branches
- **POST** `/admin/branches` - Create new branch
- **GET** `/admin/branches/{id}` - Get single branch
- **PUT** `/admin/branches/{id}` - Update branch
- **DELETE** `/admin/branches/{id}` - Delete branch

---

## Screenshots Guide

### 1. Accessing Branch Management
```
Sidebar → Academic Mgmt → Branches
```

### 2. Branch List View
- Table showing all branches
- Filter by school (for master admin)
- Add Branch button at top right

### 3. Add Branch Form
- Modal popup with form fields
- School selection (for master admin)
- Branch details fields
- Main Branch checkbox
- Active status checkbox

### 4. Branch Actions
- Edit button: Yellow with pencil icon
- Delete button: Red with trash icon (not shown for main branches)

---

## Best Practices

1. **Always create a Main Branch first** for each school
2. **Use consistent naming conventions** for branches
3. **Keep branch codes short and unique** within a school
4. **Fill in contact information** for better communication
5. **Mark branches as inactive** instead of deleting them to preserve historical data

---

## Support

If you encounter any issues:
1. Check this guide first
2. Verify all migrations have been run: `php artisan migrate`
3. Clear application cache: `php artisan cache:clear`
4. Check the browser console for JavaScript errors
5. Contact your system administrator

---

**Last Updated**: February 27, 2026
**Version**: 1.0
