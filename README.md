ChartJs Widget
==============
Based on [ChartJs 2](http://www.chartjs.org/docs/) for Yii2 widget

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist cenotia/yii2-chartjs-widget "*"
```

or add

```
"cenotia/yii2-chartjs-widget": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code like this :

In the view

```php
<?php use cenotia\components\chartjs\ChartJs; ?>
<?= ChartJs::widget([
                   'type' => 'pie',
                   'options' => [
                       'height' => 180,
                       'width' => 180,

                   ],
                   'labelPercent' => true, //add percentage to the tooltips
                   'data' => [
                               'labels' => $data4['labels'],
                               'datasets' => [
                                       [
                                           'label'=> 'yourlable',
                                           'data'=> $data4['datasets'],
                                           'backgroundColor' => [
                                                              '#FF6384',
                                                              '#FFAA84',
                                                              '#56BB84',
                                                              '#87AA98',
                                                              '#8899AA',
                                                              '#5499CC'
                                                          ], //for 6 values. It could be set in the controller
                                       ]
                                   ]
                           ],

               ]);?>
```

In the controller

```php
        //your query returns 6 labels and 6 values
        //use function to get values and * 1 to turn then into numerals otherwise
        //it will strings and the if you use the percentages, it won't work.

        $command = $connection->createCommand("
                    select dimension, measure
                    from yourtable
                    where yourfilter
                    group by dimension
                    limit 6;                
	                   ");   
        $result4 = $command->queryAll();

        $data4 = [
            'labels' => ArrayHelper::getColumn($result4,'dimension'),
            'datasets' => ArrayHelper::getColumn($result4,function ($element) {
                    return $element['measure']*1;
                })
        ];
```        

Credits
----------
[2amigos/yii2-chartjs-widget](https://github.com/2amigos/yii2-chartjs-widget)
