<div style="font-family: Helvetica;">
<h2>Setup.md</h2>
<b>Following SQL Queries will help creating the database.<br>Also checkout <i><u>connection.php</u></i> for the connection to the database.</b>
<p>> CREATE database food;</p>
<p>> CREATE TABLE `restaurants` (
  `name` varchar(30) NOT NULL,
  `password` varchar(30) NOT NULL,
  `email` varchar(40) NOT NULL,
  `phone` varchar(12) NOT NULL,
  `address` varchar(50) NOT NULL,
  `description` varchar(100) NOT NULL
);</p>
<p>> CREATE TABLE `users` (
  `name` varchar(30) NOT NULL,
  `password` varchar(30) NOT NULL,
  `email` varchar(40) NOT NULL,
  `phone` varchar(12) NOT NULL,
  `address` varchar(100) NOT NULL);
</p>
</div>