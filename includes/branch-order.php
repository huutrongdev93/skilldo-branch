<?php
Class BrandOrderAction {

    public function __construct() {
        add_filter('checkout_order_before_save', array($this, 'checkout'), 10, 2);
        add_filter('pre_insert_order_data', array($this, 'insert'), 10, 2);

        add_filter('admin_order_index_search', array($this, 'searchField'), 9);
        add_filter('admin_order_index_args', array($this, 'searchData'), 9);
    }

    public function insert($data, $order) {
        if (!empty($order['branch_id'])) $data['branch_id'] = (int)$order['branch_id'];
        return $data;
    }

    public function checkout($order, $metadata_order) {

        $branches = Branch::gets(Qr::set('status', 'working'));

        if (have_posts($branches)) {

            if (count($branches) == 1) {
                $order['branch_id'] = $branches[0]->id;
            } else {

                if (!empty($metadata_order['other_delivery_address'])) {
                    $city = $metadata_order['shipping_city'];
                } else {
                    $city = $metadata_order['billing_city'];
                }

                $branch_default = 0;

                foreach ($branches as $branch) {
                    if (Str::isSerialized($branch->area)) {
                        $branch->area = unserialize($branch->area);
                        if (in_array($city, $branch->area) !== false) {
                            $order['branch_id'] = $branch->id;
                            break;
                        }
                    }
                    if ($branch->default == 1) $branch_default = $branch->id;
                }
                if (empty($order['branch_id'])) $order['branch_id'] = $branch_default;
            }
        }
        return $order;
    }

    public function searchField($Form) {

        $branches = Branch::gets(Qr::set('status', 'working'));

        if(have_posts($branches) && count($branches) >= 2) {
            $branch_options = [0 => 'Tất cả chi nhánh'];
            foreach ($branches as $branch) {
                $branch_options[$branch->id] = $branch->name;
            }
            $Form->add('branch_id','select', ['after' => '<div class="form-group">', 'before' => '</div>', 'placeholder' => 'Chi nhánh', 'options' => $branch_options], Request::get('branch_id'));
        }

        return $Form;
    }

    public function searchData($args): Qr
    {
        $branch_id = Str::clear(Request::post('branch_id'));
        if(!empty($branch_id)) {
            $args->where('branch_id', $branch_id);
        }
        return $args;
    }
}