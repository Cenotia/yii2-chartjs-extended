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
                        total = total + (allData[i]*1);
                        
                    }
                    var tooltipPercentage = Math.round((tooltipData / total) * 100);
                    return tooltipLabel + ': ' + tooltipData + ' (' + tooltipPercentage + '%)';
                }
            }
        },";

        $drawpievalues = "function drawSegmentValues()
        {
            for(var i=0; i<chartJS_{$id}.segments.length; i++)
            {
                ctx.fillStyle='white';
                var textSize = canvas.width/10;
                ctx.font= textSize+'px Verdana';
                // Get needed variables
                var value = chartJS_{$id}.segments[i].value;
                var startAngle = chartJS_{$id}.segments[i].startAngle;
                var endAngle = chartJS_{$id}.segments[i].endAngle;
                var middleAngle = startAngle + ((endAngle - startAngle)/2);

                // Compute text location
                var posX = (radius/2) * Math.cos(middleAngle) + midX;
                var posY = (radius/2) * Math.sin(middleAngle) + midY;

                // Text offside by middle
                var w_offset = ctx.measureText(value).width/2;
                var h_offset = textSize/4;

                ctx.fillText(value, posX - w_offset, posY + h_offset);
            }
        }";
        $pievalues = "'onAnimationProgress': drawSegmentValues(),";
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
