<?php
namespace Branch;

use Option;
use Plugin;

class Updater
{
    protected string $version;

    protected ?string $currentVersion;

    protected array $timeline;

    protected $update;

    public function __construct()
    {
        $this->version = Plugin::getInfo(BRANCH_NAME)['version'];

        $this->currentVersion = Option::get('branch_version');

        if(empty($this->currentVersion))
        {
            $this->currentVersion = '1.1.1';
        }

        $this->timeline = ['1.2.0', '1.2.1', '2.0.0'];
    }

    public function setUpdate($version): void
    {
        $this->update = null;

        $className = 'UpdateVersion'.str_replace('.', '', $version);

        if(!class_exists('Branch\\Update\\'.$className))
        {
            if(file_exists(__DIR__.'/'. $version.'/UpdateVersion.php'))
            {
                require_once __DIR__ .'/'. $version.'/UpdateVersion.php';
            }
        }

        if(class_exists('Branch\\Update\\'.$className))
        {
            $className = 'Branch\\Update\\'.$className;

            $this->update = new $className();
        }
    }

    public function checkForUpdates(): void
    {
        if (version_compare($this->version, $this->currentVersion, '>'))
        {
            foreach ($this->timeline as $version)
            {
                if(version_compare($version, $this->currentVersion) == 1)
                {
                    $this->setUpdate($version);

                    if(!empty($this->update))
                    {
                        $this->update->run();

                        Option::update('branch_version', $version);
                    }
                }
            }
        }
        else
        {
            Plugin::setCheckUpdate(BRANCH_NAME, $this->version);
        }
    }
}

if(!request()->ajax())
{
    if(Plugin::getCheckUpdate(BRANCH_NAME) !== BRANCH_VERSION)
    {
        $updater = new \Branch\Updater();

        $updater->checkForUpdates();
    }
}