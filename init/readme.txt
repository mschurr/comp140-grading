To initialize the system, fill out the JSON files in this directory (or generate them using another program) and run:
 php server.php init init/instructors.json init/graders.json init/grader_map.json init/students.json 

If you need to run the initialization script again, you may get duplicate key errors. You can fix this by running:
 php server.php clean

Note: The init command should only be run when the database is empty. The clean command will wipe the database; you shouldn't run it after you have started using the system (unless you want to lose all of your data). If you need to make changes after the initial import, you should use a different method.