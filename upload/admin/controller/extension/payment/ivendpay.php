<?php

require_once(DIR_SYSTEM . 'library/ivendpay/IvendPayLibrary.php');

class ControllerExtensionPaymentIvendpay extends Controller
{
    private $error = array();

    public function index()
    {
        $this->load->language('extension/payment/ivendpay');
        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');
        $this->load->model('localisation/order_status');
        $this->load->model('localisation/geo_zone');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('payment_ivendpay', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
        }

        $data['action'] = $this->url->link('extension/payment/ivendpay', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/payment/ivendpay', 'user_token=' . $this->session->data['user_token'], true)
        );

        $fields = array(
            'payment_ivendpay_status',
            'payment_ivendpay_api_key',
            'payment_ivendpay_order_status_id',
            'payment_ivendpay_pending_status_id',
            'payment_ivendpay_confirming_status_id',
            'payment_ivendpay_paid_status_id',
            'payment_ivendpay_invalid_status_id',
            'payment_ivendpay_expired_status_id',
            'payment_ivendpay_changeback_status_id',
            'payment_ivendpay_canceled_status_id',
            'payment_ivendpay_white_label',
            'payment_ivendpay_sort_order',
        );

        $data['white_label_options'] = [
            'false' => 'Disabled',
            'true' => 'Enabled',
        ];

        foreach ($fields as $field) {
            if (isset($this->request->post[$field])) {
                $data[$field] = $this->request->post[$field];
            } else {
                $data[$field] = $this->config->get($field);
            }
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/payment/ivendpay', $data));
    }

    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/payment/ivendpay')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!class_exists('IvendPayLibrary')) {
            $this->error['warning'] = $this->language->get('error_composer');
        }

        return !$this->error;
    }


    public function install()
    {
        $this->load->model('extension/payment/ivendpay');

        $this->model_extension_payment_ivendpay->install();
    }

    public function uninstall()
    {
        $this->load->model('extension/payment/ivendpay');

        $this->model_extension_payment_ivendpay->uninstall();
    }
}
