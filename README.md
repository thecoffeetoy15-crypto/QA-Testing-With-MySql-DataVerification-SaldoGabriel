# QA Testing with MySQL Data Verification

## Repository Overview

This repository contains a quality assurance testing portfolio for the **Kara David's Wish Note** PHP web application. It documents the AI-generated application, its generation prompt, and the test cases used to evaluate data entry and database storage.

### Repository Contents

- `readme.md` — Test case documentation and project overview.
- `webapp-generation-prompt.txt` — The original AI prompt used to generate the web application.
- `test-webapp.php` — The single-file PHP application generated using AI.

## Test Case Documentation

**Test Case ID:** TC-DataEntry-001

**Test Case Title:** Data Entry - Valid

**Test Case Description:** Functional testing of the data entry page and backend validation using MySQL queries.

**Preconditions:**
- The Kara David Birthday Wishnote application is available in a local PHP environment.
- The MySQL database is populated and accessible for comparison.
- The data entry page is opened in a supported browser.

**Test Data:** Five valid entries containing First Name, Last Name, Government Position, and Location within the specified character limits.

## Test Steps and Results

| Step | Action | Expected Result | Actual Result |
|---|---|---|---|
| a | Verify that the form labels appear in the correct order and that the text boxes are writable. | The labels appear in the required order, and all text boxes are writable. | The form labels appear in the required order, and all text boxes are writable. |
| b | Verify the "Send Wish Note" button. | The button changes color on hover and is enabled. | The button changes color on hover and is enabled. |
| c | Enter valid test data in the respective fields. | All fields accept valid entries within their character limits. | The fields accept the entered test data. |
| d | Click "Send Wish Note". | A confirmation modal appears. | Pending Execution |
| e | Verify the data displayed in the modal. | The modal displays the same values entered in all four fields. | Pending Execution |
| f | Click "Cancel". | The modal closes without submitting the form. | Pending Execution |
| g | Click "Send Wish Note" again, then click "Confirm & Send". | The form submits the data to the PHP backend. | Pending Execution |
| h | Verify the success message on the data entry page. | A successful submission message appears. | Pending Execution |
| i | Execute a MySQL query to display all records in `main_corrupt_politicians` and verify the submitted data. | The database records are displayed, and the saved values are checked, including whether Location was stored. | Pending Execution |

## Database Verification

Use the following SQL query in the MySQL environment after the application has been tested:

```sql
USE birthday_wishnote;

SELECT *
FROM main_corrupt_politicians;
```

The application prompt intentionally omits the `Location` column from the SQL `INSERT` statement. Verify whether the location entered in the form is saved to the database.

## Test Status

**Status:** Pending Execution

The final pass/fail status must be assigned after the required tests have been performed and the database results have been checked.

## Reference

The source test case reference filename is `textcase.txt`, as specified in the assignment instructions.
