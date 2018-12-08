<?php
use humhub\modules\friendship\widgets\FriendsPanel;
use humhub\modules\post\widgets\Form;
use humhub\modules\user\widgets\ProfileSidebar;
use humhub\modules\user\widgets\StreamViewer;
use humhub\modules\user\widgets\UserFollower;
use humhub\modules\user\widgets\UserSpaces;
use humhub\modules\user\widgets\UserTags;
use humhub\modules\user\widgets\UserSurveys;

Form::widget(['contentContainer' => $user]);
    echo StreamViewer::widget(['contentContainer' => $user]);
      
    $this->beginBlock('sidebar'); 
    
    echo ProfileSidebar::widget([
            'user' => $user,
            'widgets' => [
                [UserTags::class, ['user' => $user], ['sortOrder' => 10]],
                [UserSurveys::class, ['user' => $user], ['sortOrder' => 20]],
                [UserSpaces::class, ['user' => $user], ['sortOrder' => 30]],
                [FriendsPanel::class, ['user' => $user], ['sortOrder' => 40]],
                [UserFollower::class, ['user' => $user], ['sortOrder' => 50]],
            ]
        ]);
$this->endBlock(); 
?>