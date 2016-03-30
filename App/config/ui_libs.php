<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/8
 * Time: 11:01
 */

//Register asserts (js, css...) here
//name => ['css' => [], 'js' => [], 'prefix' => ''] or name => ['resource_prefix']
//Use Flight()::Assets()->addLibrary($name) to add js and css
//Use Flight()::Assets()->getResource($name, $prefix) to get resource uri
//global_pre will always be loaded before user added
//global_post will always be loaded after user added
//default_resource is the default search path for resource

return [
    'global_pre'=>[
        'jquery', 'bootstrap'
    ],
    'global_post'=>[
        'custom'
    ],
    'default_resource'=>'/asserts/images',
    'custom'=>[
        'css'=>['common.css'],
        'js'=>['utils.js'],
        'prefix'=>'/asserts/custom'
    ],
    'jquery'=>[
        'js'=>'jquery-1.12.0.min.js',
        'prefix'=>'/asserts/jquery'
    ],
    'uploader'=>[
        'js'=>['dmuploader.min.js'],
        'prefix'=>'/asserts/uploader'
    ],
    'underscore'=>[
        'js'=>['underscore-min.js'],
        'prefix'=>'/asserts/underscore'
    ],
    'semantic'=>[
        'css'=>'semantic.min.css',
        'js'=>'semantic.min.js',
        'prefix'=>'/asserts/semantic'
    ],
    'bootstrap'=>[
        'css'=>'bootstrap.min.css',
        'js'=>'bootstrap.min.js',
        'prefix'=>'/asserts/bootstrap'
    ],
    'bootstrap-table'=>[
        'css'=>'bootstrap-table.min.css',
        'js'=>['bootstrap-table.min.js', 'bootstrap-table-zh-CN.min.js', 'table_utils.js'],
        'prefix'=>'/asserts/bootstrap-table'
    ],
    'bootstrap-dialog'=>[
        'css'=>'bootstrap-dialog.css',
        'js'=>'bootstrap-dialog.js',
        'prefix'=>'/asserts/bootstrap-dialog'
    ],
    'bootstrap-select'=>[
        'css'=>'bootstrap-select.min.css',
        'js'=>'bootstrap-select.min.js',
        'prefix'=>'/asserts/bootstrap-select'
    ],
    'bootstrap-switch'=>[
        'css'=>'bootstrap-switch.min.css',
        'js'=>'bootstrap-switch.min.js',
        'prefix'=>'/asserts/bootstrap-switch'
    ],
    'colors'=>[
        'css'=>'colors.min.css',
        'prefix'=>'/asserts/colors'
    ],
    'fa'=>[
        'css'=>'font-awesome.min.css',
        'prefix'=>'/asserts/fa'
    ],
    'lodash'=>[
        'js'=>'lodash.min.js',
        'prefix'=>'/asserts/lodash'
    ],
    'icheck'=>[
        'css'=>'skins/all.css',
        'js'=>'icheck.min.js',
        'prefix'=>'/asserts/icheck'
    ],
    'buttons'=>[
        'css'=>'buttons.min.css',
        'js'=>'button.js',
        'prefix'=>'/asserts/buttons'
    ],
];