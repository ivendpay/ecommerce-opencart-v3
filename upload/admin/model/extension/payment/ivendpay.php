<?php

class ModelExtensionPaymentIvendpay extends Model
{
    public function install()
    {
        try {
            $this->db->query("
              CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ivendpay_order` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `order_id` INT(11) NOT NULL,
                `amount_fiat` VARCHAR(40) NOT NULL,
                `currency_fiat` VARCHAR(10) NOT NULL,
                `url` VARCHAR(255) NOT NULL,
                `invoice` VARCHAR(60) NULL,
                `payment_url` VARCHAR(255) NULL,
                `amount` VARCHAR(40) NULL,
                `currency` VARCHAR(20) NULL,
                `status` VARCHAR(25) NOT NULL,
                PRIMARY KEY (`id`)
              ) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;
            ");
        } catch (Exception $exception) {
            $this->log->write('Ivendpay plugin install table failed: ' . $exception->getMessage());
        }

        $this->load->model('setting/setting');

        $defaults = array();

        $defaults['payment_ivendpay_receive_currencies'] = '';
        $defaults['payment_ivendpay_white_label'] = false;
        $defaults['payment_ivendpay_order_status_id'] = 1;
        $defaults['payment_ivendpay_pending_status_id'] = 1;
        $defaults['payment_ivendpay_confirming_status_id'] = 1;
        $defaults['payment_ivendpay_paid_status_id'] = 5;
        $defaults['payment_ivendpay_invalid_status_id'] = 10;
        $defaults['payment_ivendpay_changeback_status_id'] = 13;
        $defaults['payment_ivendpay_expired_status_id'] = 14;
        $defaults['payment_ivendpay_canceled_status_id'] = 7;
        $defaults['payment_ivendpay_sort_order'] = 1;

        $this->model_setting_setting->editSetting('payment_ivendpay', $defaults);
    }

    public function uninstall()
    {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "ivendpay_order`;");
    }
}
