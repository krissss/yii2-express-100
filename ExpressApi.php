<?php

namespace kriss\express100;

use yii\base\BaseObject;
use yii\base\Exception;
use yii\httpclient\Client;

class ExpressApi extends BaseObject
{
    const COMPONENT_NAME = 'express_100_api';

    /**
     * key
     * @var int|string
     */
    public $key = 1;
    /**
     * 是否解析快递的中文名字为英文
     * @var bool
     */
    public $parseExpress = true;
    /**
     * 额外的快递对照表
     * @var array
     */
    public $otherExpressMap = [];
    /**
     * 快递状态 map
     * @link https://www.kuaidi100.com/openapi/api_post.shtml#d05
     * @var array
     */
    public $stateMap = [
        '0' => '运送中',
        '1' => '已揽件',
        '2' => '寄送故障',
        '3' => '签收成功',
        '4' => '已退签',
        '5' => '派件中',
        '6' => '退回中',
    ];

    /**
     * 发起请求
     * @param $express
     * @param $num
     * @return array
     */
    public function api($express, $num)
    {
        if ($this->parseExpress) {
            $express = $this->expressMap($express);
        }
        $temp = mt_rand() / mt_getrandmax();
        $url = "http://www.kuaidi100.com/query?id={$this->key}&type={$express}&postid={$num}&valicode=&temp={$temp}";

        return $this->getApiResult($url);
    }

    /**
     * 获取接口数据
     * @param $url
     * @return mixed
     * @throws Exception
     */
    protected function getApiResult($url)
    {
        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('get')
            ->setUrl($url)
            ->setOptions([
                'userAgent' => "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/{$this->randomIp()} Safari/536.11"
            ])
            ->send();
        if ($response->isOk) {
            $res = $response->data;
            $res['state_name'] = $this->getStateName($res);
            return $res;
        }

        throw new Exception('请求错误: statusCode:' . $response->statusCode . ',url:' . $url);
    }

    /**
     * 快递状态名称
     * @param $result
     * @return string
     */
    public function getStateName($result)
    {
        $stateMap = $this->stateMap;
        return (isset($result['state']) && isset($stateMap[$result['state']])) ? $stateMap[$result['state']] : '未知';
    }

    /**
     * 快递100 中文对照获取快递单号
     * @param $chinese
     * @return array
     */
    protected function expressMap($chinese)
    {
        $map = [
            '中通快递' => 'zhongtong',
            '圆通速递' => 'yuantong',
            '申通快递' => 'shentong',
            '韵达快递' => 'yunda',
            '顺丰速运' => 'shunfeng',
            '天天快递' => 'tiantian',
            'EMS' => 'ems',
            '宅急送' => 'zhaijisong',
            '速尔快递' => 'suer',
            '如风达' => 'rufengda',
            '全峰快递' => 'quanfengkuaidi',
            '德邦' => 'debangwuliu',
            'AAE全球专递' => 'aae',
            'Aramex' => 'aramex',
            '百世汇通' => 'huitongkuaidi',
            '包裹信件' => 'youzhengguonei',
            '比利时邮政' => 'bpost',
            'DHL中国件' => 'dhl',
            'FedEx国际件' => 'fedex',
            '凡客配送' => 'vancl',
            '凡宇快递' => 'fanyukuaidi',
            'Fedex' => 'fedexcn',
            'FedEx美国件' => 'fedexus',
            '国通快递' => 'guotongkuaidi',
            '韩国邮政' => 'koreapost',
            '佳吉快运' => 'jiajiwuliu',
            '京东快递' => 'jd',
            '加拿大邮政' => 'canpost',
            '加运美' => 'jiayunmeiwuliu',
            '嘉里大通' => 'jialidatong',
            '京广速递' => 'jinguangsudikuaijian',
            '跨越速递' => 'kuayue',
            '快捷速递' => 'kuaijiesudi',
            '民邦速递' => 'minbangsudi',
            '民航快递' => 'minghangkuaidi',
            'OCS' => 'ocs',
            '全一快递' => 'quanyikuaidi',
            '全晨快递' => 'quanchenkuaidi',
            '日本邮政' => 'japanposten',
            '圣安物流' => 'shenganwuliu',
            '盛辉物流' => 'shenghuiwuliu',
            'TNT' => 'tnt',
            'UPS' => 'ups',
            'USPS' => 'usps',
            '万象物流' => 'wanxiangwuliu',
            '新邦物流' => 'xinbangwuliu',
            '信丰物流' => 'xinfengwuliu',
            '优速物流' => 'youshuwuliu',
            '远成物流' => 'yuanchengwuliu',
            '运通中港快递' => 'ytkd',
            '中铁物流' => 'ztky',
            '增益速递' => 'zengyisudi',
        ];
        $map = array_merge($map, $this->otherExpressMap);
        return isset($map[$chinese]) ? $map[$chinese] : $chinese;
    }

    /**
     * 获取国内随机IP地址
     * 注：适用于32位操作系统
     * @return string
     */
    protected function randomIp()
    {
        $ip_long = [
            ['607649792', '608174079'], //36.56.0.0-36.63.255.255
            ['1038614528', '1039007743'], //61.232.0.0-61.237.255.255
            ['1783627776', '1784676351'], //106.80.0.0-106.95.255.255
            ['2035023872', '2035154943'], //121.76.0.0-121.77.255.255
            ['2078801920', '2079064063'], //123.232.0.0-123.235.255.255
            ['-1950089216', '-1948778497'], //139.196.0.0-139.215.255.255
            ['-1425539072', '-1425014785'], //171.8.0.0-171.15.255.255
            ['-1236271104', '-1235419137'], //182.80.0.0-182.92.255.255
            ['-770113536', '-768606209'], //210.25.0.0-210.47.255.255
            ['-569376768', '-564133889'], //222.16.0.0-222.95.255.255
        ];
        $rand_key = mt_rand(0, 9);
        $ip = long2ip(mt_rand($ip_long[$rand_key][0], $ip_long[$rand_key][1]));
        return $ip;
    }
}