---
name: Juzaweb Media Uploader Pattern
description: Reference guide for using MediaUploader service (Juzaweb\Modules\Core\FileManager\MediaUploader) to handle file uploads locally and from URLs.
---

# Juzaweb Media Uploader Pattern

`MediaUploader` is the standard service for handling file uploads, moving files to storage, and creating `Media` database records in Juzaweb CMS.

## Basic Usage

### 1. Upload from Request (Form Upload)

```php
use Juzaweb\Modules\Core\FileManager\MediaUploader;

// In a Controller
public function store(Request $request)
{
    if ($request->hasFile('image')) {
        $uploadedFile = $request->file('image');
        
        $media = MediaUploader::make($uploadedFile)
            ->upload();
            
        return $media->id;
    }
}
```

### 2. Upload from URL (Download & Save)

Useful for downloading remote assets (e.g., from Github Release, external APIs) and saving them to local storage.

```php
use Juzaweb\Modules\Core\FileManager\MediaUploader;

$url = 'https://example.com/file.zip';

$media = MediaUploader::make($url)
    ->disk('public') // or 'private'
    ->forcePath('modules/my-module/file.zip') // Optional: Force specific path
    ->upload();

// Access the path
echo $media->path; 
// e.g., modules/my-module/file.zip
```

### 3. Upload from Local Path

If you have a file already on disk (e.g., a generated temporary file).

```php
$tempPath = storage_path('app/tmp/generated-report.pdf');

$media = MediaUploader::make($tempPath)
    ->upload();
```

## Advanced Configuration

### Set Disk (`disk()`)

Specify which filesystem disk to use (`public`, `private`, etc.). Default corresponds to `config('media.disk')` (usually `public`).

```php
$uploader->disk('private');
```

### Force Path (`forcePath()`)

By default, files are saved in date-based directories (e.g., `2024/02/filename.ext`). Use `forcePath` to specify an exact logical path.

```php
// Will save to: storage/app/public/modules/document/v1.0.0.zip
$uploader->forcePath('modules/document/v1.0.0.zip');
```

**Note**: If a file already exists at the forced path, `MediaUploader` will update the existing `Media` record instead of creating a duplicate (if logic permits), or overwrite the file content.

### Set Custom Filename (`name()`)

```php
$uploader->name('custom-filename.jpg');
```

### Parent Folder (`folder()` or `parent()`)

Assign the media to a specific folder ID (for File Manager organization).

```php
$uploader->folder($folderId);
```

## Example: Downloading a Private Asset (Job)

Pattern used in `DownloadGithubReleaseDistJob`:

```php
public function handle(): void
{
    $downloadUrl = 'https://github.com/.../archive.zip';
    $targetPath = "modules/{$moduleName}/{$version}.zip";

    // MediaUploader handles the HTTP download implicitly when passed a URL
    $uploader = MediaUploader::make($downloadUrl);
    $uploader->disk('private'); // Save to private disk
    $uploader->forcePath($targetPath);

    try {
        $media = $uploader->upload(); // Performs download + save + DB record creation
        
        // Access physical path if needed (e.g., for checksum)
        $fullPath = Storage::disk('private')->path($media->path);
        
    } catch (\Throwable $e) {
        // Handle download failures
        throw $e;
    }
}
```
