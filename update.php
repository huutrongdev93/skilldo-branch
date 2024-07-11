<?php
if(!Admin::is()) return;

function BranchUpdateCore(): void
{
    if(Admin::is() && Auth::check()) {
        $version = Option::get('branch_version');
        $version = (empty($version)) ? '1.1.1' : $version;
        if (version_compare(BRANCH_VERSION, $version) === 1) {
            $update = new BranchUpdateVersion();
            $update->runUpdate($version);
        }
    }
}
add_action('admin_init', 'BranchUpdateCore');

Class BranchUpdateVersion {
    public function runUpdate($DiscountVersion): void
    {
        $listVersion    = ['1.2.0'];
        foreach ($listVersion as $version) {
            if(version_compare($version, $DiscountVersion) == 1) {
                $function = 'version_'.str_replace('.','_',$version);
                if(method_exists($this, $function)) $this->$function();
            }
        }
        Option::update('branch_version', BRANCH_VERSION);
    }
    public function version_1_2_0(): void
    {
        (include BRANCH_PATH.'/database/db_1.2.0.php')->up();
    }
}