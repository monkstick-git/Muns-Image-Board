This directory is used to store your file drivers.
Drivers are used to store files in different storage providers, such as a database, file, s3 etc.

All file drivers must have the following public functions as a minimum.

get(array[] List Of File chunks)
Returns: One complete file of all chunks combined


set(array[] List of File Chunks)
Returns: True or False (Set, or not set)

The file drivers are NOT responsible for storing metadata about files. Only for storing and retrieving blobs.