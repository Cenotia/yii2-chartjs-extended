<?php
/**
 * author     : x.Villamuera <xavier.villamuera@gmail.com>
 * createTime : 2017/4/25 12:20
 * description: Extended chartjs v2 plugin for yii2
 */

namespace cenotia\components\chartjs;

use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;


/**
 * //in the controller
 * //in the view
 * <?= ChartJs::widget([
 *                 'type' => 'pie',
 *                   'options' => [
 *                       'height' => 180,
 *                       'width' => 180,
 *
 *                   ],
 *                   'labelPercent' => true,
 *                    'data' => [
 *                               'labels' => $dataUsers4['labels'],
 *                               'datasets' => [
 *                                       [
 *                                           'label'=> 'Prestations',
 *                                           'data'=> $dataUsers4['datasets'],
 *                                           'backgroundColor' => [
 *                                                            '#FF6384',
 *                                                            '#FFAA84',
 *                                                              '#56BB84',
 *                                                              '#87AA98',
 *                                                              '#8899AA',
 *                                                              '#5499CC'
 *                                                          ],
 *                                       ]
 *                                   ]
 *                           ],
 *
 *               ]);?>
 *
 * Class ChartJs
 * @package frontend\widgets
 * @link http://www.chartjs.org/docs/
 */
class ChartJs extends Widget
{

    /**
     * @var array
     * widget options
     */
    public $options = [];

    /**
     * @var array
     * actual options of the jQuery plugin
     */
    public $clientOptions = [];

    /**
     * @var array
     * Provides percentages for tooltips
     */
    public $labelPercent = false;

    /**
     * @var array
     */
    public $data = [];

     /**
     * @var array
     * the backgroundcolor for each value in dataset
     */
    public $backgroundColor = [];

     /**
     * @var array
     * the border color for each dataset
     */
    public $borderColor = [];

    /**
     * @var string
     * line, bar, radar, doughnut, polarArea, bubble, pie
     */
    public $type = 'line';

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if ($this->type === null) {
            throw new InvalidConfigException("The 'type' option is required");
        }
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
    }

    public function run()
    {
        echo Html::tag('canvas', '', $this->options);
        $this->registerClientScript();
    }

    protected function registerClientScript()
    {
        $id = $this->options['id'];
        //add percentage to the tooltips
        $percenttooltips = "tooltips: {
            callbacks: {
                label: function(tooltipItem, data) {
                    var allData = data.datasets[tooltipItem.datasetIndex].data;
                    var tooltipLabel = data.labels[tooltipItem.index];
                    var tooltipData = allData[tooltipItem.index];
                    var total = 0;
                    for (var i in allData) {
                        total += allData[i];
                    }
                    var tooltipPercentage = Math.round((tooltipData / total) * 100);
                    return tooltipLabel + ': ' + tooltipData + ' (' + tooltipPercentage + '%)';
                }
            }
        },";
        $type = $this->type;
        $view = $this->getView();
        $backgroundColor = $this->backgroundColor;
        $borderColor = $this->borderColor;
        $data = !empty($this->data) ? Json::encode($this->data) : '{}';
        //$options = !empty($this->clientOptions) ? Json::encode($this->clientOptions) : '{}';
        $options = '{';
        if ($this->labelPercent)
            $options .= $percenttooltips;
        $options .= '}';

        $options = $options;
        ChartJsAsset::register($view);
        $js = ";var chartJS_{$id} = new Chart(document.getElementById('{$id}'), {type: '{$type}', data: {$data}, options: {$options}});";
        $view->registerJs($js);
    }
}
