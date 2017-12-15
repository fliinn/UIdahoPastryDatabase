# UIdahoPastryDatabase

For the Pastry Database setup, our setup included running the createDatabase file and populateDatabase file, which both contain MySQL queries that create and populate a MySQL server with the appropriate schema and enough data to perform meaningful queries.

After the database has been created, set up the rest of the contents of the repository in the home folder of any type of server that runs an HTML file as localhost. We chose to use WAMP 64bit, and the entirety of the contents in the www folder should be put in the www folder of the WAMP servers directory. Then alter the interface.php first three lines to match the username and password required to connect to your MySQL connection to the server. 

Fair warning, the code of this little project has not been checked or created for security, so it is recommended that a new MySQL server be set up to connect in the hopes of protecting the users own servers and projects.
