<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 9/8/14
 * Time: 10:23 AM
 */
return array(
    'acl' => array(
        'roles' => array(
            'guest'   => null,
            'member'  => 'guest',
            'admin'  => 'member',
        ),
        'resources' => array(
            'allow' => array(
                'manageattributes' => array(
                    'manageattributes'	=> 'admin',
                ),
                'Fmi\Controller\Index' => array(
                    'index'	=> 'member'
                ),
                'CsnUser\Controller\UserDoctrine' => array(
                    'all'	=> 'guest'
                ),
                'CsnUser\Controller\UserDoctrineSimpleAuthorizationAcl' => array(
//					'all'   => 'guest',
                    'index'	=> 'guest',
                    'create' => 'member'
                ),
                'CsnUser\Controller\UserDoctrinePureAcl' => array(
                    'all'   => 'member',
                ),
                'Application\Controller\Index' => array(
                    'all'   => 'guest'
                ),
                'Auth\Controller\Index' => array(
                    // 'index' => 'guest',
                    // 'all'   => 'member',
                    'all'   => 'guest'
                ),
                'zfcuser' => array( // zg-commoms ZfcUser
                    // 'index' => 'guest',
                    // 'all'   => 'member',
                    'all'   => 'guest'
                ),
                'Auth\Controller\Hello' => array(
                    'all'   => 'guest'
                ),
                'Auth\Controller\FormTests' => array(
                    'all'   => 'guest'
                ),
                'AuthDoctrine\Controller\Index' => array(
                    'all'   => 'guest'
                    // 'all'   => 'member',
                ),
                'AuthDoctrine\Controller\Registration' => array(
                    'all' => 'guest'
                ),
                'CsnCms\Controller\Index' => array(
                    // 'all'   => 'guest'
                    'view'	=> 'guest',
                    'index' => 'admin',
                    'add'	=> 'admin',
                    'edit'  => 'admin',
                    'delete'=> 'admin',
                ),
                'CsnCms\Controller\Translation' => array(
                    // 'all'   => 'guest'
                    'view'	=> 'guest',
                    'index' => 'admin',
                    'add'	=> 'admin',
                    'edit'  => 'admin',
                    'delete'=> 'admin',
                ),
                'CsnCms\Controller\Comment' => array(
                    // 'all'   => 'guest'
                    'view'	=> 'guest',
                    'index' => 'admin',
                    'add'	=> 'admin',
                    'edit'  => 'admin',
                    'delete'=> 'admin',
                ),
                'AuthDoctrine\Controller\Admin' => array(
                    'all'	=> 'admin',
                ),
                'CsnFileManager\Controller\Index' => array(
                    'all'	=> 'member',
                ),
                // for CMS articles
                'Public Resource' => array(
                    'view'	=> 'guest',
                ),
                'Private Resource' => array(
                    'view'	=> 'member',
                ),
                'Admin Resource' => array(
                    'view'	=> 'admin',
                ),
            )
        )
    )
);