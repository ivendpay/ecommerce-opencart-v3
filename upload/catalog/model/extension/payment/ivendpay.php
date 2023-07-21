<?php

class ModelExtensionPaymentIvendpay extends Model
{
    protected $cTable = 'ivendpay_order';

    public function getOrder($order_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX.$this->cTable . " WHERE `order_id` = '" . (int)$order_id . "' ORDER BY `id` DESC LIMIT 1");

        return $query->row;
    }

    public function getInvoice($invoice)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX.$this->cTable . " WHERE `invoice` = '" . $this->db->escape($invoice) . "' ORDER BY `id` DESC LIMIT 1");

        return $query->row;
    }

    public function getMethod($address, $total)
    {
        $this->load->language('extension/payment/ivendpay');

        $method_data = array(
            'code' => 'ivendpay',
            'title' => $this->language->get('text_title'),
            'terms' => '',
            'sort_order' => $this->config->get('payment_ivendpay_sort_order')
        );

        return $method_data;
    }

    public function createOrder($order_id, $amount_fiat, $currency_fiat, $url)
    {
        if (! empty((int)$order_id) &&
            ! empty($amount_fiat) &&
            ! empty($currency_fiat) &&
            ! empty($url))
        {
            try {

                $query = "INSERT INTO `". DB_PREFIX.$this->cTable ."` SET";

                $query .= " `order_id` = '". (int)$order_id ."'";
                $query .= ", `amount_fiat` = '". $this->db->escape($amount_fiat) ."'";
                $query .= ", `currency_fiat` = '". $this->db->escape($currency_fiat) ."'";
                $query .= ", `url` = '". $this->db->escape($url) ."'";
                $query .= ", `status` = 'CREATE'";

                return $this->db->query($query);

            } catch (Exception $e) {
                $this->log->write('ivendPay::createOrder exception: ' . $e->getMessage());
            }
        } else {
            $this->log->write('ivendPay::createOrder fields are missing');
        }

        return false;
    }

    public function setOrderInvoice($order_id, $invoice, $amount, $currency, $payment_url, $status)
    {
        if (! empty((int)$order_id) &&
            ! empty($invoice) &&
            ! empty($amount) &&
            ! empty($currency) &&
            ! empty($payment_url) &&
            ! empty($status))
        {
            try {
                    $query = "UPDATE `". DB_PREFIX.$this->cTable ."` SET";

                    $query .= " `invoice` = '". $this->db->escape($invoice) ."'";
                    $query .= ", `amount` = '". $this->db->escape($amount) ."'";
                    $query .= ", `currency` = '". $this->db->escape($currency) ."'";
                    $query .= ", `payment_url` = '". $this->db->escape($payment_url) ."'";
                    $query .= ", `status` = '". $this->db->escape($status) ."'";

                    $query .= " WHERE `order_id` = '" . (int)$order_id . "'";

                    return $this->db->query($query);

            } catch (Exception $e) {
                $this->log->write('ivendPay::setOrderInvoice exception: ' . $e->getMessage());
            }
        } else {
            $this->log->write('ivendPay::setOrderInvoice fields are missing');
        }

        return false;
    }

    public function changeOrderStatus($order_id, $status)
    {
        if (! empty((int)$order_id) &&
            ! empty($status))
        {
            try {
                $query = "UPDATE `". DB_PREFIX.$this->cTable ."` SET";

                $query .= " `status` = '". $this->db->escape($status) ."'";

                $query .= " WHERE `order_id` = '" . (int)$order_id . "'";

                return $this->db->query($query);

            } catch (Exception $e) {
                $this->log->write('ivendPay::changeOrderStatus exception: ' . $e->getMessage());
            }
        } else {
            $this->log->write('ivendPay::changeOrderStatus fields are missing');
        }

        return false;
    }
}
