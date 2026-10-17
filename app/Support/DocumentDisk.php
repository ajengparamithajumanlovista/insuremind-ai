<?php

namespace App\Support;

use Illuminate\Support\Facades\Config;

/**
 * Resolves the filesystem disk used for user-uploaded policy documents.
 *
 * Documents live on a dedicated disk (`documents`, see config/filesystems.php)
 * because on platforms with an ephemeral filesystem (Railway, Heroku, ...) the
 * default storage folder is recreated on every deploy. The disk root can be
 * pointed at a mounted volume through `DOCUMENTS_DISK_ROOT`
 * (e.g. `/data/storage/documents`) so uploaded files survive redeploys.
 */
class DocumentDisk
{
    /**
     * Name of the disk that new uploads are written to.
     *
     * Defaults to the dedicated `documents` disk, but falls back to the plain
     * `local` disk when the configured disk is not defined, so the app keeps
     * working with an older/customised filesystems configuration.
     */
    public static function name(): string
    {
        $disk = (string) Config::get('filesystems.document_disk', env('DOCUMENTS_DISK', 'documents'));

        return $disk !== '' && Config::has("filesystems.disks.{$disk}") ? $disk : 'local';
    }

    /**
     * Root directory of the document disk (used by scripts and diagnostics).
     */
    public static function root(): string
    {
        return (string) Config::get('filesystems.disks.'.self::name().'.root', storage_path('app/private'));
    }

    /**
     * Whether the document disk is backed by persistent storage outside the
     * application directory (e.g. a mounted volume such as `/data`).
     */
    public static function isPersistent(): bool
    {
        return ! str_starts_with(self::root(), base_path());
    }
}
