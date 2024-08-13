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
        $listVersion    = ['1.2.0', '1.2.1'];
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
    public function version_1_2_1(): void
    {
        $branches = Branch::gets();

        if(have_posts($branches)) {

            $model = model();

            $provinces = Cart_Location_Old::cities();

            $changeDistrictsKey = [
                24 => [
                    'HUYEN-THANH-TRI' => 'HUYEN-THANH-TRI-HA-NOI'
                ],
                14 => [
                    'HUYEN-BAO-LAM' => 'HUYEN-BAO-LAM-CAO-BANG'
                ],
                4 => [
                    'HUYEN-CHO-MOI' => 'HUYEN-CHO-MOI-BAC-KAN'
                ],
                34 => [
                    "HUYEN-TAM-DUONG" => "HUYEN-TAM-DUONG-LAI-CHAU"
                ],
                29 => [
                    "HUYEN-KY-SON" => "HUYEN-KY-SON-HOA-BINH"
                ],
                43 => [
                    "HUYEN-PHU-NINH" => "HUYEN-PHU-NINH-PHU-THO",
                    "HUYEN-TAM-NONG" => "HUYEN-TAM-NONG-PHU-THO"
                ],
                27 => [
                    "HUYEN-AN-LAO" => "HUYEN-AN-LAO-HAI-PHONG"
                ],
                56 => [
                    "HUYEN-PHONG-DIEN" => "HUYEN-PHONG-DIEN-THUA-THIEN-HUE"
                ],
                8 => [
                    "HUYEN-VINH-THANH" => "HUYEN-VINH-THANH-BINH-DINH"
                ],
                52 => [
                    "HUYEN-CHAU-THANH" => "HUYEN-CHAU-THANH-TAY-NINH"
                ],
                38 => [
                    "HUYEN-TAN-THANH" => "HUYEN-TAN-THANH-LONG-AN",
                    "HUYEN-CHAU-THANH" => "HUYEN-CHAU-THANH-LONG-AN"
                ],
                57 => [
                    "HUYEN-CHAU-THANH" => "HUYEN-CHAU-THANH-TIEN-GIANG"
                ],
                7 => [
                    "HUYEN-CHAU-THANH" => "HUYEN-CHAU-THANH-BEN-TRE"
                ],
                59 => [
                    "HUYEN-CHAU-THANH" => "HUYEN-CHAU-THANH-TRA-VINH"
                ],
                20 => [
                    "HUYEN-CHAU-THANH" => "HUYEN-CHAU-THANH-DONG-THAP"
                ],
                1 => [
                    "HUYEN-CHAU-THANH" => "HUYEN-CHAU-THANH-AN-GIANG",
                    "HUYEN-PHU-TAN" => "HUYEN-PHU-TAN-AN-GIANG"
                ],
                32 => [
                    "HUYEN-CHAU-THANH" => "HUYEN-CHAU-THANH-KIEN-GIANG"
                ],
                28 => [
                    "HUYEN-CHAU-THANH" => "HUYEN-CHAU-THANH-HAU-GIANG"
                ],
            ];

            $updates = [];

            foreach ($branches as $branch) {

                $branchUp = [];

                foreach ($provinces as $provinceKey => $provinceId) {
                    if($branch->city == $provinceKey) {
                        $branchUp['city'] = $provinceId;
                        break;
                    }
                }

                if(have_posts($branchUp)) {

                    if(!empty($changeDistrictsKey[$branchUp['city']][$branch->district])) {
                        $branchUp['district'] = $changeDistrictsKey[$branchUp['city']][$branch->district];
                    }
                    else {
                        $branchUp['district'] = $branch->district;
                    }

                    $districts = Cart_Location_Old::districts($branchUp['city']);

                    $branchUp['district'] = $districts[$branchUp['district']];

                    $wards = Cart_Location_Old::ward($branchUp['district']);

                    $branchUp['ward'] = $wards[$branch->ward];

                    $branchUp['id'] = $branch->id;

                    $updates[] = $branchUp;
                }

                if(count($updates) >= 500) {
                    $model->table('branchs')::updateBatch($updates, 'id');
                    $updates = [];
                }
            }

            if(have_posts($updates)) {
                $model->table('branchs')::updateBatch($updates, 'id');
            }

            (include BRANCH_PATH.'/database/db_1.2.1.php')->up();
        }
    }
}