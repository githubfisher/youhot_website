<?php

/**
 * Class Admin/Size
 * @author fisher
 * @date 2017-03-15
 */
class Size extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('size_model', 'size');
    }

    // 尺码图管理 - 首页
    public function index(){
        $this->order_list();
    }

    // 尺码图 列表
    public function order_list()
    {
        $this->_data['list'] = $this->getAllSizeUrl();
        $template = 'admin/size/index';
        $this->template($template, $this->_data);
        return;
    }

    /**
     * 获取全部分类、品牌商品的尺码图
     * 返回商品尺码图的阿里云地址
     * @return array
     * author fisher
     * date 2017-03-15
     */
    public function getAllSizeUrl()
    {
        $res = $this->size->get_list_url_new();
        return $res;
    }

    // 新增(上传)尺码图
    public function  create()
    {
        $config['upload_path']      = './tmp/';
        $config['allowed_types']    = 'gif|jpg|png';
        $config['max_size']     = 10000; // 10M
        $this->load->library('upload', $config);
        if (!$this->upload->do_upload('userfile')) {
            $data = array(
                'res' => 1,
                'hint' => array('error' => $this->upload->display_errors())
            );
            $this->_data['info'] = '图片上传失败，请重试！';
            $this->_data['btn'] = '重新上传';
            $this->setOutputTpl('admin/size/info');
            $this->_result(bp_operation_ok, $this->_data);
        } else {
            $file = $this->upload->data();
            if (file_exists($file['full_path'])) {
//                // 拆分图片名称，获取对应分类ID和品牌ID
                list($category, $brand) = $this->get_cate_brand_name($file['raw_name']);
                $data = array(
                    //'category' => $this->size->get_category_id($category),
                    //'brand' => $this->size->get_brand_id($brand), // cancel save with id, save with name
		   'category' => $category,
 		   'brand' => $brand,
                    'userid' => $this->userid,
                    'create_at' => time()
                );
                if (!$data['category'] || !$data['brand']) { // 如果品牌ID或分类ID有一个为空，不允许上传！
                    @unlink($file['full_path']); // 删除原图片
                    $this->_data['info'] = '不存在对应分类或品牌，上传失败！';
                    $this->_data['btn'] = '重新上传';
                    $this->setOutputTpl('admin/size/info');
                    $this->_result(bp_operation_ok, $this->_data);
                    return;
                }

//                // OSS 简单上传
                $file['file_content'] = file_get_contents($file['full_path']);
                $data['url'] = $this->upload($file);

                @unlink($file['full_path']); // 删除原图片
// echo $data['url'];die;               

                if ($this->size->create_size_chart($data)) {
                    $this->_data['info'] = '上传成功！';
                    $this->_data['btn'] = '继续上传';
                    $this->setOutputTpl('admin/size/info');
                    $this->_result(bp_operation_ok, $this->_data);
                } else {
                    $this->_data['info'] = '生成尺码图记录失败，请重试！';
                    $this->_data['btn'] = '重新上传';
                    $this->setOutputTpl('admin/size/info');
                    $this->_result(bp_operation_ok, $this->_data);
                }
            } else {
                // 图片上传后找不到文件
                $this->_data['info'] = '读取上传图片失败，请重试！';
                $this->_data['btn'] = '重新上传';
                $this->setOutputTpl('admin/size/info');
                $this->_result(bp_operation_ok, $this->_data);
            }
        }
    }

    // 拆分文件名，解析出category和brand
    private function get_cate_brand_name($fn)
    {
        // 图片原命名   下划线'_'代表空格，category和brand用'#'连接。  cate_gory#brand ; & 替换为 @
        $name = array();
        $name[] = str_replace('@', '&', str_replace('_', ' ', strchr($fn, '#', true))); // category_name
        $name[] = ltrim(strchr($fn, '#'), '#'); // brand_name
        return $name;
    }

    // 阿里云OSS - 上传
    private function upload($file, $type = 'album')
    {
        $this->load->library('aliyun-oss/Common');
        $bucket = Common::getBucketName();
        $ossClient = @Common::getOssClient();
        if (is_null($ossClient)) exit(json_encode(array('error'=>'is null')));

        $fn = $this->_safe_file_name($file['file_name']);
        $_config = ($type == 'avatar') ? 'aliyun_oss_avatar_dir' : 'aliyun_oss_product_album_dir';
        $this->load->helper('string');
        $filename = $this->config->item($_config) . '/' . (random_string('alnum', 6) . '_' . $fn);

        try{
            $ossClient->putObject($bucket, $filename,  $file['file_content']);
        } catch(OssException $e) {
            $this->_error(bp_operation_fail,$e->getMessage());
            return;
        }
        $file_url = $this->config->item('aliyun_oss_img_service_url') . $filename;
        return $file_url;
    }

    // 阿里云OSS - 上传 - _safe_file_name
    private function _safe_file_name($fn){
        $ext = get_file_ext($fn);
        return str_replace('=', '', base64_encode($fn)).'.'.$ext;
    }

    /**
     * 编辑尺码图
     * @author fisher
     * @date 2017-03-17
     */
    public function edit()
    {
        $id = $this->input->get_post('id');

        $this->_data['info'] = $this->size->get_size_chart_info_new($id);
        $this->_data['cates'] = $this->size->get_all_category();
        $this->_data['brands'] = $this->size->get_all_brand();
        $this->_data['info']['user'] = $this->userid;

        $this->setOutputTpl('admin/size/edit');
        $this->_result(bp_operation_ok, $this->_data);
    }

    /**
     * 更新尺码图
     * @author fisher
     * @date 2017-03-17
     */
    public function update()
    {
        $id = $this->input->get_post('id');
        $data['category'] = $this->input->get_post('category');
        $data['brand'] = $this->input->get_post('brand');
        $data['url'] = $this->input->get_post('url');

        if ($this->size->update_size_chart($id, $data)) {
            $this->_data['info'] = '修改成功！';
            $this->setOutputTpl('admin/size/delete');
            $this->_result(bp_operation_ok, $this->_data);
        } else {
            $this->_data['info'] = '修改失败，请重试！';
            $this->setOutputTpl('admin/size/delete');
            $this->_result(bp_operation_ok, $this->_data);
        }
    }

    /**
     * 删除尺码图
     * @author fisher
     * @date 2017-03-17
     */
    public function delete()
    {
        $id = $this->input->get_post('id');
        $url = $this->input->get_post('url');
        if ($this->size->delete_size_chart($id)) {
            // 删除阿里云OSS存储图片
            $this->delete_oss_file($url);

            $this->_data['info'] = '删除成功！';
            $this->setOutputTpl('admin/size/delete');
            $this->_result(bp_operation_ok, $this->_data);
        } else {
            $this->_data['info'] = '删除失败，请重试！';
            $this->setOutputTpl('admin/size/delete');
            $this->_result(bp_operation_ok, $this->_data);
        }
    }

    /**
     * 删除阿里云OSS存储图片
     * @author fisher
     * @date 2017-03-17
     */
    private function delete_oss_file($file)
    {
        $this->load->library('aliyun-oss/Common');
        $bucket = Common::getBucketName();
        $ossClient = @Common::getOssClient();
        if (is_null($ossClient)) exit(json_encode(array('error'=>'is null')));

        try{
            $ossClient->deleteObject($bucket, $file);
        } catch(OssException $e) {
            $this->_error(bp_operation_fail,$e->getMessage());
            return;
        }
    }

    /**
     * 设某尺码图为默认尺码图
     * @author fisher
     * @date 2017-03-17
     */
    public function set_default()
    {
        $id = $this->input->get_post('id');
        if ($this->size->set_default($id)) {
            $this->_data['info'] = '设置成功！';
            $this->setOutputTpl('admin/size/delete');
            $this->_result(bp_operation_ok, $this->_data);
        } else {
            $this->_data['info'] = '设置失败，请重试！';
            $this->setOutputTpl('admin/size/delete');
            $this->_result(bp_operation_ok, $this->_data);
        }
    }

    /**
     * 批量上传尺码图
     * @author fisher
     * @date 2017-03-17
     */
    public function mass_upload()
    {
        $config['upload_path']      = './tmp/';
        $config['allowed_types']    = 'gif|jpg|png';
        $config['max_size']     = 10000; // 10M
        $this->load->library('upload', $config);
        if (!$this->upload->do_upload('userfile')) {
            $data = array(
                'res' => 1,
                'hint' => array('error' => $this->upload->display_errors())
            );
            $this->_data['info'] = '图片上传失败，请重试！';
            $this->_data['btn'] = '重新上传';
            $this->setOutputTpl('admin/size/info');
            $this->_result(bp_operation_ok, $this->_data);
        } else {
            $file = $this->upload->data();
            echo '<pre>';
            var_dump($file);
        }
        exit(json_encode(array('1'=>'aa')));
    }
}
