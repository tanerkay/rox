<?php 


class MessagesMustloginPage extends MessagesBasePage
{
    private $_redirect_url = 'messages';
    
    // the address after login
    public function setRedirectURL($url)
    {
        $this->_redirect_url = $url;
    }
    
    protected function column_col3()
    {
        $url = $this->_redirect_url;
        ?><h3>Please log in!</h3>
        You tried to open<br>
        <a href="<?=$url ?>"><?=$url ?></a><br><br>
        which is only visible to logged-in members.<br>
        (anonymous people don't have a mailbox)<?php
        
        $login_widget = $this->createWidget('LoginFormWidget');
        
        if ($memory = $this->memory) {
            $login_widget->memory = $memory;
        }
        
        $login_widget->render();
    }
    
    /*
    protected function getColumnNames()
    {
        // we don't need the other columns
        return array('col3');
    }
    */
}


class ReadMessagePage extends MessagesBasePage
{
    protected function column_col3()
    {
        $message = $this->message;
        $contactUsername = $message->senderUsername;
        $direction_in = true;
        if ($contactUsername == $_SESSION['Username']) {
            $contactUsername = $message->receiverUsername;
            $direction_in = false;
        }
        ?><div class="floatbox">
        <div style="float:left">
        <?=MOD_layoutbits::linkWithPicture($contactUsername) ?>
        </div>
        <div>
        <p>
          <span class="grey small"><?=($direction_in ? 'Message from' : 'Message to') ?> : </span>
          <a href="bw/member.php?cid=<?=$contactUsername ?>"><?=$contactUsername ?></a>
        </p>
        <p>
          <span class="grey small">Message date : </span> <?=$message->DateSent ?>
        </p>
        </div>
        </div>
        <p id="messagecontent">
        <?echo str_replace("\n","<br />",$message->Message) ; ?>
        </p>
        <p>
          <?php if ($direction_in) { ?>
          <a class="button" href="messages/<?=$message->id ?>/reply">reply</a>
          <?php } else { ?>
          <a class="button" href="messages/<?=$message->id ?>/edit">edit</a>
          <?php } ?>
        </p>
        <?php
    }
}


class ReplyMessagePage extends ReadMessagePage
{
    
}

class EditMessagePage extends ComposeMessagePage
{
    
}

class MessageSentPage extends ReadMessagePage
{
    protected function column_col3()
    {
        echo 'message has been sent.';
        parent::column_col3();
    }
}




?>