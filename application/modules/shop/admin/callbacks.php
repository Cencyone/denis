<?php

use Propel\Runtime\ActiveQuery\Criteria;

class ShopAdminCallbacks extends ShopAdminController
{

    /**
     * @deprecated since 4.9
     */
    public function index() {
        redirect(site_url('/admin/components/run/callbacks'));

    }

    /**
     * Display list of callback statuses
     *
     * @deprecated since 4.9
     */
    public function statuses() {
        redirect(site_url('/admin/components/run/callbacks/statuses'));

    }

    /**
     * Display list of callback themes
     *
     * @deprecated since 4.9
     */
    public function themes() {
        redirect(site_url('/admin/components/run/callbacks/themes'));
    }

    /**
     * @deprecated since 4.9
     */
    public function search() {
        redirect(site_url('/admin/components/run/callbacks/search'));
    }

}