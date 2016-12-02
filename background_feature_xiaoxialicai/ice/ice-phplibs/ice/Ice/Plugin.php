<?php
// **********************************************************************
//
// Copyright (c) 2003-2016 ZeroC, Inc. All rights reserved.
//
// This copy of Ice is licensed to you under the terms described in the
// ICE_LICENSE file included in this distribution.
//
// **********************************************************************
//
// Ice version 3.6.2
//
// <auto-generated>
//
// Generated from file `Plugin.ice'
//
// Warning: do not edit this file.
//
// </auto-generated>
//

require_once __DIR__.'/../Ice/LoggerF.php';
require_once __DIR__.'/../Ice/BuiltinSequences.php';

global $Ice__t_Plugin;

if(!interface_exists('Ice_Plugin',false))
{
    interface Ice_Plugin
    {
        public function initialize();
        public function destroy();
    }

    $Ice__t_Plugin = IcePHP_defineClass('::Ice::Plugin', 'Ice_Plugin', -1, true, false, $Ice__t_Object, null, null);
}

global $Ice__t_PluginManager;

if(!interface_exists('Ice_PluginManager',false))
{
    interface Ice_PluginManager
    {
        public function initializePlugins();
        public function getPlugins();
        public function getPlugin($name);
        public function addPlugin($name, $pi);
        public function destroy();
    }

    $Ice__t_PluginManager = IcePHP_defineClass('::Ice::PluginManager', 'Ice_PluginManager', -1, true, false, $Ice__t_Object, null, null);
}
?>