<?php

use humhub\widgets\Button;

use yii\helpers\Url;
use yii\helpers\Html;

$MODULE = 'AdminModule.views_experiments_view';

function format_date($date)
{
    $formatted = new DateTime($date);
    return $formatted->format("m/d/Y");
}

?>

<div class="panel-body">
    <div class="clearfix hidden-print">
        <div class="pull-right">
            <div class="pull-left" style="padding: 0 15px">
                <?= Html::a('<i class="fa fa-file"></i> Export as PDF', Url::toRoute(["/admin/experiments/report", 'id' => $experiment->id]), [
                    'target' => '_blank',
                    'class' => 'btn btn-primary btn-sm tt',
                ]) ?>
            </div>
            <?= Button::defaultType(Yii::t($MODULE, 'Back to Experiments'))->link(Url::to(['/admin/experiments']))->right()->sm(); ?>
        </div>

        <h4 class="pull-left"><?= Yii::t($MODULE, "Experiment - $experiment->id"); ?></h4>
    </div>

    <hr>

    <div class="row">
        <div class="col-md-6">
            <p>
                <b><?= Yii::t($MODULE, 'Start'); ?></b> <br />
                <span><?= format_date($experiment->start); ?></span>
            </p>
        </div>
        <div class="col-md-6">
            <p>
                <b><?= Yii::t($MODULE, 'End'); ?></b> <br />
                <span><?= format_date($experiment->end); ?></span>
            </p>
        </div>
    </div>

    <?php foreach ($experiment->groups as $key => $group) : ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h2 class="panel-title">Group <?= ($key + 1) ?></h2>
                <hr>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <p>
                            <b><?= Yii::t($MODULE, 'Topic In'); ?></b> <br />
                            <span><?= $group->topic_in !== NULL && in_array($group->topic_in, $topics) ? $topics[$group->topic_in] : "-"; ?></span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p>
                            <b><?= Yii::t($MODULE, 'Topic Probability'); ?></b> <br />
                            <span><?= $group->topic_probability !== NULL ? $group->topic_probability : "-"; ?></span>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <p>
                            <b><?= Yii::t($MODULE, 'Political Opinion In'); ?></b> <br />
                            <span><?= $group->pol_op_in !== NULL && in_array($group->pol_op_in, $political_opinions) ? $political_opinions[$group->pol_op_in] : "-"; ?></span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p>
                            <b><?= Yii::t($MODULE, 'Political Opinion Probability'); ?></b> <br />
                            <span><?= $group->pol_op_probability !== NULL ? $group->pol_op_probability : "-"; ?></span>
                        </p>
                    </div>
                </div>
                <!-- Users -->
                <h3>Users</h3>
                <?php if (count($group->users) > 0) : ?>
                    <table class="table">
                        <thead>
                            <th>Id</th>
                            <th>Name</th>
                            <th>Email</th>
                        </thead>
                        <tbody>
                            <?php foreach ($group->users as $key => $user) : ?>
                                <tr>
                                    <td><?= $user->id ?></td>
                                    <td><?= $user->profile->firstname . " " . $user->profile->lastname ?></td>

                                    <td><?= $user->email ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>