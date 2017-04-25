<?php
/**
 * author     : x.Villamuera <xavier.villamuera@gmail.com>
 * createTime : 2016/5/16 19:51
 * description:
 */

namespace cenotia\components\chartjs;

use yii\web\AssetBundle;

class ChartJsAsset extends AssetBundle
{
    public $sourcePath = '@bower/chartjs';

    public function init()
    {
        $this->js = YII_DEBUG ? ['dist/Chart.js'] : ['dist/Chart.min.js'];
    }
}
