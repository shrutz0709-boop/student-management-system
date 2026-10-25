# Viva Preparation — Student Management System

1. **What is the Student Management System?**  
   A web application that stores and manages student details digitally.

2. **Why did you build this project?**  
   To make student records easier to add, search, update and delete than maintaining separate paper records.

3. **Which technologies are used?**  
   HTML, CSS and JavaScript for the frontend; PHP for backend logic; MySQL for storage; PDO for database access.

4. **What is CRUD?**  
   Create, Read, Update and Delete—the four basic data operations.

5. **What is the role of HTML?**  
   It defines the page structure, forms, buttons and table.

6. **What is the role of CSS?**  
   It controls the page's appearance, layout and responsive design.

7. **Why use JavaScript?**  
   To provide quick browser-side validation and a delete confirmation prompt.

8. **What is PHP?**  
   A server-side scripting language that processes form submissions and communicates with MySQL.

9. **What is MySQL?**  
   A relational database management system that stores structured records in tables.

10. **What is a primary key?**  
    A column (or group of columns) that uniquely identifies each row. Here, `id` is the primary key.

11. **Why are roll number and email unique?**  
    To prevent two records from using the same roll number or email address.

12. **What is a prepared statement?**  
    A parameterised SQL query that separates query structure from user-supplied values and helps prevent SQL injection.

13. **What is SQL injection?**  
    An attack where untrusted input changes the meaning of a SQL query. Prepared statements help protect against it.

14. **What is validation?**  
    Checking input before saving it. This project validates data in JavaScript for usability and again in PHP for server-side safety.

15. **Why validate on the server if JavaScript validates too?**  
    Browser-side checks can be bypassed, so the server must independently validate incoming data.

16. **What is PDO?**  
    PHP Data Objects, a PHP interface for connecting to databases and executing prepared statements.

17. **What is a UNIQUE constraint?**  
    A database rule that prevents duplicate values in a column, such as `roll_number` and `email`.

18. **How does search work?**  
    PHP reads the search term and queries name, roll number, course and email using parameterised `LIKE` conditions.

19. **What happens when a student is deleted?**  
    A POST request sends the student ID; PHP validates the request token and deletes that row using a prepared statement.

20. **What is a CSRF token?**  
    A random session-associated value checked on POST actions to help prevent cross-site request forgery.

21. **What is client-side vs server-side validation?**  
    Client-side validation runs in the browser; server-side validation runs on the server and cannot be replaced by browser checks.

22. **What are the limitations of your current project?**  
    It does not yet have real authentication, attendance, marks or production deployment features.

23. **What improvements can be made?**  
    Add secure admin login, attendance, marks, exports, pagination, backup and role-based access.

24. **How did you test it?**  
    By testing add, list, search, edit, delete, empty fields, invalid email, duplicate roll number/email and persistence after refresh. Describe only tests you actually ran.

25. **Why is the app not production-ready?**  
    It is a local academic prototype and needs authentication, authorisation, HTTPS, backups, deployment configuration and stronger operational safeguards before real use.
