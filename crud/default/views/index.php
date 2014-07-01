<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * @var yii\web\View $this
 * @var yii\gii\generators\crud\Generator $generator
 */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();

echo "<?php\n";
?>

use yii\helpers\Html;
use <?= $generator->indexWidgetType === 'grid' ? "yii\\grid\\GridView" : "yii\\widgets\\ListView" ?>;

/**
* @var yii\web\View $this
* @var yii\data\ActiveDataProvider $dataProvider
* @var <?= ltrim($generator->searchModelClass, '\\') ?> $searchModel
*/

$this->title = '<?= Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass))) ?>';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-index">

    <?=
    "<?php " . ($generator->indexWidgetType === 'grid' ? "// " : "") ?>
    echo $this->render('_search', ['model' =>$searchModel]);
    ?>

    <div class="clearfix">
        <p class="pull-left">
            <?= "<?= " ?>Html::a('New', ['create'], ['class' => 'btn btn-success']) ?>
        </p>

        <div class="pull-right">


            <?php foreach ($generator->getModelRelations() AS $relation): ?>
                <?php
                // ignore pivot tables
                $iconType = ($relation->multiple) ? 'arrow-right' : 'arrow-left';
                if (strstr($relation->modelClass, 'X')) { # TODO: pivot detection, move to getModelRelations
                    $iconType = 'random';
                }
                $controller = strtolower(
                    preg_replace(
                        '/([a-z])([A-Z])/',
                        '$1-$2',
                        $generator->pathPrefix . StringHelper::basename($relation->modelClass)
                    )
                );
                ?>

                <!--
                <?= "<?= " ?>Html::a('<i class="glyphicon glyphicon-<?= $iconType ?>"></i> <?=
                Inflector::camel2words(
                    StringHelper::basename($relation->modelClass)
                ) ?>', ['<?= $controller ?>/index'], ['class' => 'btn btn-default']) ?>
                -->

                <?php
                $label = Inflector::titleize(StringHelper::basename($relation->modelClass),true);
                $items[] = [
                    'label' => '<i class="glyphicon glyphicon-' . $iconType . '"> '.$label.'</i>',
                    'url' => [$controller.'/index']
                ]
                ?>
            <?php endforeach; ?>

            <?php
            echo \yii\bootstrap\ButtonDropdown::widget(
                [
                    'id'       => 'giiant-relations',
                    'label'    => 'Relations',
                    'dropdown' => [
                        'options' => [
                            'class' => 'dropdown-menu-right'
                        ],
                        'encodeLabels' => false,
                        'items'        => $items
                    ],
                ]
            );

            ?>
        </div>
    </div>

    <?php if ($generator->indexWidgetType === 'grid'): ?>
        <?= "<?php " ?>echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
        <?php
        $count = 0;
        foreach ($generator->getTableSchema()->columns as $column) {
            $format = $generator->generateColumnFormat($column);
            if (++$count < 6) {
                echo "\t\t\t'" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
            } else {
                echo "\t\t\t// '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
            }
        }
        ?>

        ['class' => '<?= $generator->actionButtonClass ?>'],
        ],
        ]); ?>
    <?php else: ?>
        <?= "<?php " ?>echo ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
        return Html::a(Html::encode($model-><?= $nameAttribute ?>), ['view', <?= $urlParams ?>]);
        },
        ]); ?>
    <?php endif; ?>

</div>