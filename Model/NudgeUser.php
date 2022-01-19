<?php

App::uses('AppModel', 'Model');

class NudgeUser extends AppModel {

    public $belongsTo = array(
        'Nudge' => array(
            'className' => 'Nudge'
        )
    );

}
