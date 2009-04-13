<?php


class VolunteerbarWidget extends RoxWidget
{
    public function render()
    {
		if (empty($_SESSION['IdMember'])) {
			return ; // Do nothing if user is not identified (thi cannot be a volunteer)
		}

        $R = MOD_right::get();
        if (!$R->hasRightAny()) {
            // donothing
        } else {
            $model = new VolunteerbarModel();
            $args['numberPersonsToBeAccepted'] = $args['numberPersonsToBeChecked'] = 0;
            if ($R->hasRight("Accepter")) {
                $AccepterScope = $R->rightScope('Accepter');
                $args['numberPersonsToBeAccepted']=$numberPersonsToBeAccepted = $model->getNumberPersonsToBeAccepted($AccepterScope);
                $args['numberPersonsToBeChecked']=$numberPersonsToBeChecked =  $model->getNumberPersonsToBeChecked($AccepterScope);
            }
                        
            $args['numberPersonsToAcceptInGroup']=0 ;
            if ($R->hasRight("Group")) {
                $args['numberPersonsToAcceptInGroup'] = $model->getNumberPersonsToAcceptInGroup($R->rightScope('Group'));
            }
            
            $args['numberMessagesToBeChecked'] = 0;
            $args['numberSpamToBeChecked'] = 0;
            if ($R->hasRight("Checker")) {
                $args['numberMessagesToBeChecked'] = $model->getNumberMessagesToBeChecked();
                $args['numberSpamToBeChecked'] = $model->getNumberSpamToBeChecked();
            }

			$args['numberPendingLocalMess']=0 ;
            if ($R->hasRight("ContactLocation")) {
                $args['numberPendingLocalMess'] = $model->getNumberPendingLocalMess();
			}
            
            if ($this->layoutkit) { //quick work-around
                $this->layoutkit->showTemplate('apps/rox/volunteerbar.php', $args);
            } else {
                echo "Please fix volunteerbar.widget.php";
            }
        }
    }
}


?>