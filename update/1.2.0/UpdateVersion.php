<?php
namespace Branch\Update;

class UpdateVersion120
{
    public function database(): void
    {
        (include BRANCH_PATH.'/database/db_1.2.0.php')->up();
    }

    public function run(): void
    {
        $this->database();
    }
}