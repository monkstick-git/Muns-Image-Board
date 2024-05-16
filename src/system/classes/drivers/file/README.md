This directory is used to store your file drivers.
Drivers are used to store files in different storage providers, such as a database, file, s3 etc.

All file drivers must have the following public functions as a minimum.
$UniqueID is a unique identifier for the file.
Currently, this is an MD5 hash plus "_" plus the metadata ID.

get($UniqueID)
Returns: One complete file of all chunks combined

set($UniqueID, $Content)
Returns: True or False (Set, or not set)

delete($UniqueID)
Returns: True or False (Deleted, or not deleted)


The file drivers are NOT responsible for storing metadata about files. Only for storing and retrieving blobs based on the unique ID.

## Note
It is a really good idea to create a util script in the util directory to run the maintenance script for the file driver. This will allow you to run the maintenance script via a cron job.  This helps with the cleanup of old files, and the removal of files that are no longer needed.