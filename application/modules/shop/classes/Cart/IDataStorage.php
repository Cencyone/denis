<?php

namespace Cart;

/**
 * @copyright 2013 Siteimage
 * @author <dev@imagecms.net>
 */
abstract class IDataStorage extends \ShopController
{

    /**
     * Get cart data from storage
     * @param int $instance
     * @param integer $id (optional, default NULL)
     * @return array|bool
     */
    abstract public function getData($instance, $id);

    /**
     * Save cart data to storage
     * @param null|array $data
     * @return array|bool
     */
    abstract public function setData($data = null);

    /**
     * Remove items from cart storage by type and id
     * @param int $instance
     * @param integer $id (optional, default NULL)
     * @return bool
     * @internal param int $typeId (optional, default NULL)
     */
    abstract public function remove($instance, $id);

    /**
     *
     * @param array $data
     * @param integer $type
     * @param integer $id
     * @return array
     */
    protected function filterByParams($data, $type, $id = null) {
        $result = [];
        foreach ($data as $key => $value) {
            /** Filter by type and id * */
            //            if ($id != null && $value['instance'] == $type && ($value['variantId'] == $id || $value['kitId'] == $id)) {
            if ($id != null && $value['instance'] == $type && $value['id'] == $id) {
                $result[$key] = $value;
            }
            /** Filter by type * */
            if ($id == null && $value['instance'] == $type) {
                $result[$key] = $value;
            }
            /** If no params * */
            if ($id == null && $type == null) {
                $result = $data;
                break;
            }
        }
        return $result;
    }

    /**
     * @param array $data
     * @param int $type
     * @param null|int $id
     * @return mixed
     */
    protected function removeByParams($data, $type, $id = null) {
        /** Remove by params * */
        foreach ($data as $key => $value) {
            /** Remove by type and id * */
            //            if ($id != null && $value['instance'] == $type && ($value['variantId'] == $id || $value['kitId'] == $id)) {
            if ($id != null && $value['instance'] == $type && $value['id'] == $id) {
                unset($data[$key]);
            }
            /** Remove by type * */
            if ($id == null && $value['instance'] == $type) {
                unset($data[$key]);
            }
            /** Clear cart * */
            if ($id == null && $type == null) {
                unset($data);
                break;
            }
        }

        return $data;
    }

}