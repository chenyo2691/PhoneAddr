<?php
namespace Home\Controller;

use Think\Controller;

class IndexController extends Controller
{
    public function index()
    {
        $this->display();
    }

    const PHONE_API = 'https://tcc.taobao.com/cc/json/mobile_tel_segment.htm';

    /**
     * 查询手机归属地
     * @param $phone
     */
    public function query($phone)
    {
        if (self::vertifyPhone($phone)) {
            //缓存查询
            $cache_data = M('cache');
            $where['telString'] = $phone;
            $data = $cache_data->where($where)->find();
            if ($data) {
                $data['msg'] = "由cys提供数据";
            } else {
                //API查询
                $str = self::request(self::PHONE_API, ['tel' => $phone]);
                $data = self::formatData($str);
                if ($data) {
                    $data['msg'] = "由阿里巴巴提供数据";
                }
                $cache_data->add($data);
            }

            if ($data) {
                $data['code'] = 200;
                $data['tel'] = $phone;
            }
        }

        if ($data) {

        } else {
            $data['code'] = 400;
            $data['msg'] = '手机不合法';
        }
        $this->ajaxReturn($data);

    }

    /**
     * 检验手机号码合法性
     * @param $phone
     * @return bool
     */
    public function vertifyPhone($phone)
    {
        $ret = false;
        if ($phone) {
            if (preg_match('/^1[34578]{1}\d{9}/', $phone)) {
                $ret = true;
            }
        }
        return $ret;
    }

    /**
     * 通过API请求数据
     * @param $url
     * @param $params
     * @param $method
     * @return bool|string
     */
    public function request($url, $params, $method)
    {
        if ($url) {
            $method = strtoupper($method);
            if ($method === 'POST') {

            } elseif ($method === 'PUT') {
            } elseif ($method === 'DELETE') {
            } else {
                if (is_array($params) and count($params)) {
                    if (strrpos($url, '?')) {
                        $url = $url . '&' . http_build_query($params);
                    } else {
                        $url = $url . '?' . http_build_query($params);
                    }
                    $response = file_get_contents($url);
                }
            }
        }
        return $response;
    }

    /**
     * 格式化API请求回来的数据
     * @param null $data
     * @return array|bool
     */
    public function formatData($data = null)
    {
        $ret = false;
        if ($data) {
            preg_match_all("/(\w+):'([^']+)/", $data, $res);
            $items = array_combine($res[1], $res[2]);
            foreach ($items as $key => $value) {
                $ret[$key] = iconv("GBK", "UTF-8", $value);
            }
        }
        return $ret;
    }

    public function GetPost()
    {
        $Process_com_name = I("Process_com_name");
        $Process_name = I("Process_name");
        $Process_remark = I("Process_remark");
        $Process_status = I("Process_status");
        $Process_time = I("Process_time");
        $Process_user_name = I("Process_user_name");

        echo $Process_com_name . "\r\n" . $Process_name . "\r\n" . $Process_remark . "\r\n" . $Process_status . "\r\n" . $Process_time . "\r\n" . $Process_user_name;
    }
}