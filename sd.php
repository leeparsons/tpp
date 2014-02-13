<?php


Abstract class Me implements PhpDeveloper {

    const _NAME = 'Lee parsons';
    const _DOB = '26 May 2013';

    public $address = '21 Blackthorn Close, Honiton, Devon, EX14 2XZ';
    public $skills = array(
        'code'		=>	array(
            'php53+',
            'javascript',
            'html5'	,
            'css3',
            'jQuery',
            'mootools'
        ),
        'database'	=>	'mysql',
        'server'	=>	array(
            'apache2+',
            'nginx',
            'fastcgi',
            'plesk',
            'cpanel',
            'CentOS 5+',
            'Debian',
            'Ubuntu'
        ),
        'systems'	=>	array(
            'git',
            'capistrano',
            'jira',
            'pivotal tracker',
            'bit bucket',
            'assembla'
        ),
        'practices'	=>	array(
            'agile',
            'scrum',
            'TDD',
            'waterfall'
        )
    );
}

class Job extends Me {
    public $job_title = 'Head Developer';
    public $company = 'The Organic Agency';
}
