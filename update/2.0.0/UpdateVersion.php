<?php
namespace Branch\Update;

use Storage;

class UpdateVersion200
{
    protected array $structure = [
        'admin/branch-admin.php',
        'includes/branch-order.php',
        'upload.php',
    ];

    public function database(): void
    {
        (include BRANCH_PATH.'/database/db_2.0.0.php')->up();
    }

    public function structure(): void
    {
        $storages = Storage::disk('plugin');

        foreach ($this->structure as $file)
        {
            $file = BRANCH_NAME.'/'.$file;

            if($storages->has($file))
            {
                if($storages->directoryExists($file))
                {
                    $storages->deleteDirectory($file);
                }
                else {
                    $storages->delete($file);
                }
            }
        }
    }

    public function run(): void
    {
        $this->database();
        $this->structure();
    }
}