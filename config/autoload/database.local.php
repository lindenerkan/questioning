<?php
$dbParams = array(
    //'database'  => 'noyangru_ogret',
    //'username'  => 'root',
    //'password'  => '',
    //'hostname'  => 'localhost',
    'database'  => 'questioning',
    'username'  => 'root',
    'password'  => '',
    'hostname'  => 'localhost',
);

return array(
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter' => function ($sm) use ($dbParams) {
                return new Zend\Db\Adapter\Adapter(array(
                    'driver'    => 'pdo',
                    'dsn'       => 'mysql:dbname='.$dbParams['database'].';host='.$dbParams['hostname'],
                    'username'  => $dbParams['username'],
                    'password'  => $dbParams['password'],
                    'driver_options' => array(
                        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
                    ),
                ));
            },
        ),
    ),
);
