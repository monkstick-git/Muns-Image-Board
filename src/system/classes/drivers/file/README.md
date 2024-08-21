# File Drivers

This directory is used to store file drivers, which handle file storage across different storage providers such as databases, local files, S3, etc.

## Required Public Functions

All file drivers must implement the following public functions:

### `get($UniqueID)`

**Description**: Retrieves a complete file by combining all chunks associated with the given unique identifier.

**Parameters**:
- `$UniqueID`: A unique identifier for the file. This is typically an MD5 hash concatenated with the metadata ID (e.g., `md5_hash_metadataID`).

**Returns**: The full file content as a single string or data structure.

### `set($UniqueID, $Content)`

**Description**: Stores the file content in the storage provider.

**Parameters**:
- `$UniqueID`: A unique identifier for the file.
- `$Content`: The content of the file to be stored.

**Returns**: `True` if the content is successfully stored, `False` otherwise.

### `delete($UniqueID)`

**Description**: Deletes the file associated with the given unique identifier.

**Parameters**:
- `$UniqueID`: A unique identifier for the file.

**Returns**: `True` if the file is successfully deleted, `False` otherwise.

## Important Notes

- **File Metadata**: The file drivers are not responsible for storing metadata about the files. They are only responsible for storing and retrieving the binary data (blobs) based on the unique ID.

- **Utility Scripts**: It's a good practice to create a utility script in the `util` directory to run maintenance tasks for the file driver. This script can be scheduled as a cron job to clean up old files and remove files that are no longer needed.

By adhering to these guidelines, you'll ensure consistent behavior across different storage providers and make it easier to maintain and extend your storage capabilities.
