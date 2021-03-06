<?php
/* @var $model app\models\News */
/* @var $commentForm app\models\Comment */
use app\components\UserPermissions;
use app\widgets\Avatar;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Markdown;
use \yii\helpers\HtmlPurifier;

/* @var yii\web\View $this */

$isFull = isset($isFull) ? $isFull : false;
$displayStatus = isset($displayStatus) ? $displayStatus : false;
$displayUser = isset($displayUser) ? $displayUser : true;
$displayModeratorButtons = isset($displayModeratorButtons) ? $displayModeratorButtons : false;

// OpenGraph metatags
$this->registerMetaTag(['property' => 'og:title', 'content' => Html::encode($model->title)]);
$this->registerMetaTag(['property' => 'og:site_name', 'content' => 'YiiFeed']);
$this->registerMetaTag(['property' => 'og:url', 'content' => Url::canonical()]);

?>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/ru_RU/sdk.js#xfbml=1&appId=444774969003761&version=v2.0";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<div class="row">
    <div class="col-md-2 col-sm-3 post-meta">
        <p class="time">
            <span class="glyphicon glyphicon-time" aria-hidden="true"></span>
            <?= Yii::$app->formatter->asDate($model->created_at) ?>
        </p>
        <?php if ($displayUser && $model->user_id): ?>
        <p class="author">
            <?= Html::a(Avatar::widget(['user' => $model->user]) . ' @' . Html::encode($model->user->username), ['user/view', 'id' => $model->user->id]) ?>
        </p>
        <?php endif ?>

        <?php if ($displayStatus): ?>
        <p><?= Yii::t('news', 'Status') .": ". $model->getStatusLabel() ?></p>
        <?php endif ?>

        <?php if ($displayModeratorButtons): ?>
            <?= Html::a(Yii::t('news', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?php if (UserPermissions::canAdminNews()): ?>
                <?= Html::a(Yii::t('news', 'Delete'), ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => Yii::t('news', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ]) ?>
            <?php endif ?>
        <?php endif ?>
    </div>
    <div class="col-sm-9 col-md-10 post">
        <h1>
            <?= $isFull ? Html::encode($model->title) : Html::a(Html::encode($model->title), ['news/view', 'id' => $model->id]) ?>
        </h1>

        <div class="content">
            <?= HtmlPurifier::process(Markdown::process($model->text, 'gfm'), [
                'HTML.SafeIframe' => true,
                'URI.SafeIframeRegexp' => '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%',
            ]) ?>

            <?php if ($isFull): ?>
            <div class="meta">
                <?php if (!empty($model->link)): ?>
                    <p><?= Html::a(Html::encode($model->link), $model->link) ?></p>
                <?php endif ?>

                <a href="https://twitter.com/share" class="twitter-share-button" data-count="none" data-hashtags="yii" data-url="<?= Url::canonical() ?>" data-text="<?= Html::encode($model->title) ?>">Tweet</a>
                <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>

                <div class="fb-share-button" data-href="<?= Url::canonical() ?>" data-layout="button"></div>
            </div>
            <?php endif ?>
        </div>
    </div>
</div>

<?php if ($isFull): ?>
    <?= $this->render('_comments', [
        'comments' => $model->comments,
        'commentForm' => $commentForm,
    ]) ?>
<?php endif ?>
