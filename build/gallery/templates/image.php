<?php
$words = new MOD_words();
$request = PRequest::get()->request;
$Gallery = new Gallery;
$Gallery_ctrl = new GalleryController;
if ($member = $this->model->getLoggedInMember())
{
    $callbackId = $Gallery_ctrl->editProcess($image);
    $vars =& PPostHandler::getVars($callbackId);
    $callbackIdCom = $Gallery_ctrl->commentProcess($image);
    $varsCom =& PPostHandler::getVars($callbackIdCom);
}
$GalleryRight = MOD_right::get()->hasRight('Gallery');
$d = $image;
$d->user_handle = MOD_member::getUsername($d->user_id_foreign);
$canEdit = ($User && $User->getId() == $d->user_id_foreign) ? true : false;

if (!isset($vars['errors']))
{
    $vars['errors'] = array();
}

echo <<<HTML
<h2 id="g-title">{$d->title}</h2>
HTML;

    if (!$d->description == 0)
    {
        echo <<<HTML
        <p id="g-text">{$d->description}</p>
HTML;
    }
    elseif ($canEdit)
    {
        echo <<<HTML
        <p id="g-text">{$words->getBuffered("GalleryAddDescription")}</p>{$words->flushBuffer()}
HTML;
    }
    if ($canEdit  || ($GalleryRight > 1))
    {
        $title = htmlentities($d->title, ENT_COMPAT, 'utf-8');
        $description = htmlentities($d->description, ENT_COMPAT, 'utf-8');
        echo <<<HTML
    <a href="gallery/show/image/{$d->id}" id="g-title-edit" class="button" style="display:none;">{$words->getSilent("EditTitle")}</a>
    <a href="gallery/show/image/{$d->id}" id="g-text-edit" class="button" style="display:none;">{$words->getSilent("EditDescription")}</a>
    <a style="cursor:pointer" href="gallery/show/image/<?=$d->id?>/delete" class="button" onclick="return confirm('{$words->getFormatted("confirmdeletepicture")}')">{$words->getSilent("Delete")}</a>
    {$words->flushBuffer()}

<form method="post" action="gallery/show/image/{$d->id}/edit" class="def-form">
    <fieldset id="image-edit" class="inline NotDisplayed">
    <legend>{$words->getFormatted('GalleryTitleEdit')}</legend>
    
        <div class="row">
            <label for="image-edit-t">{$words->getFormatted('GalleryLabelTitle')}</label><br/>
            <input type="text" id="image-edit-t" name="t" class="short" value="{$title}" />
            <br/><br/>
            <label for="image-edit-txt">{$words->getFormatted('GalleryLabelText')}</label><br/>
            <textarea id="image-edit-txt" name="txt" cols="30" rows="4">{$description}</textarea>
            <div id="bcomment-text" class="statbtn"></div>
	        <input type="hidden" name="{$callbackId}" value="1"/>
	        <input type="hidden" name="id" value="{$d->id}"/>
            <p class="desc">{$words->getFormatted('GalleryDescTitle')}</p>
            <input type="submit" name="button" value="submit" id="button" />
        </div>
        <div class="row">
        </div>    
</fieldset>
</form>
    <script type="text/javascript">
    $('image-edit').hide();
    $('g-title-edit').show();
    $('g-text-edit').show();

    new Ajax.InPlaceEditor('g-title', 'gallery/ajax/image/', {
            callback: function(form, value) {
                return '?item={$d->id}&title=' + decodeURIComponent(value)
            },
            externalControl: 'g-title-edit',
            formClassName: 'inplaceeditor-form-big',
            cols: '25',
            ajaxOptions: {method: 'get'}
        })

    new Ajax.InPlaceEditor('g-text', 'gallery/ajax/image/', {
            callback: function(form, value) {
                return '?item={$d->id}&text=' + decodeURIComponent(value)
            },
            externalControl: 'g-text-edit',
            rows: '5',
            cols: '25',
            ajaxOptions: {method: 'get'}
        })
    </script>
HTML;
}
echo <<<HTML
<div class="floatbox">
<div class="img">
HTML;

echo '<a id="link_'.$d->id.'" href="gallery/img?id='.$d->id.'" title="'.$d->title.' :: '.$d->description.'" class="lightview" rel="image">
    <img id="thumb_'.$d->id.'" src="gallery/thumbimg?id='.$d->id.'&amp;t=2" class="framed big" alt="image"/>
</a>';
?>
</div>
</div>

<div id="comments" style="padding: 10px 0px">
    <h3><?php echo $words->getFormatted('CommentsTitle'); ?></h3>
    
<?php
$comments = $Gallery->getComments($image->id);
if (!$comments) {
	echo '<p>'.$words->getFormatted('NoComments').'</p>';
} else {
    $count = 0;
    $lastHandle = '';
    foreach ($comments as $comment) {
        require 'comment.php';
        ++$count;
        $lastHandle = $comment->user_handle;
    }
}
?>

<h3><?php echo $words->getFormatted('CommentsAdd'); ?></h3>

<?php
if ($User) {
?>
<form method="post" action="gallery/show/image/<?=$d->id?>/comment" class="def-form" id="gallery-comment-form">
    <div class="row">
    <label for="comment-title"><?php echo $words->getFormatted('CommentsLabel'); ?>:</label><br/>
        <input type="text" id="comment-title" name="ctit" class="long" <?php 
echo isset($varsCom['ctit']) ? 'value="'.htmlentities($varsCom['ctit'], ENT_COMPAT, 'utf-8').'" ' : ''; 
?>/>
        <div id="bcomment-title" class="statbtn"></div>
<?
if (in_array('title', $vars['errors'])) {
    echo '<span class="error">'.$commentsError['title'].'</span>';
}
?>
        <p class="desc"></p>
    </div>
    <div class="row">
        <label for="comment-text"><?php echo $words->getFormatted('CommentsTextLabel'); ?>:</label><br />
        <textarea id="comment-text" name="ctxt" cols="40" rows="5"><?php 
echo isset($varsCom['ctxt']) ? htmlentities($varsCom['ctxt'], ENT_COMPAT, 'utf-8') : ''; 
      ?></textarea>
        <div id="bcomment-text" class="statbtn"></div>
<?
if (in_array('textlen', $vars['errors'])) {
    echo '<span class="error">'.$commentsError['textlen'].'</span>';
}
?>
        <p class="desc"><?php echo $words->getFormatted('CommentsSublineText'); ?></p>
    </div>
    <p>
        <input type="submit" value="<?php echo $words->getFormatted('SubmitForm'); ?>" class="submit" />
        <input type="hidden" name="<?php
// IMPORTANT: callback ID for post data 
echo $callbackIdCom; ?>" value="1"/>
    </p>
</form>
<?
}
else
{
    // not logged in.
    echo '<p>'. $words->getFormatted('PleaseRegister') .'</p>';
}
    echo "</div>";

if ($this->model->getLoggedInMember())
{ 
    PPostHandler::clearVars($callbackId); 
    PPostHandler::clearVars($callbackIdCom); 
}
