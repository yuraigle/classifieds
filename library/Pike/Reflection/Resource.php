<?php
/**
 * Copyright (C) 2011 by Pieter Vogelaar (pietervogelaar.nl) and Kees Schepers (keesschepers.nl)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @category   PiKe
 * @copyright  Copyright (C) 2011 by Pieter Vogelaar (pietervogelaar.nl) and Kees Schepers (keesschepers.nl)
 * @license    MIT
 */

/**
 * Pike reflection resource
 *
 * @category   PiKe
 * @copyright  Copyright (C) 2011 by Pieter Vogelaar (pietervogelaar.nl) and Kees Schepers (keesschepers.nl)
 * @license    MIT
 */
class Pike_Reflection_Resource
{
    /**
     * Attributes
     *
     * @var array
     */
    protected $_attributes = array();

    /**
     * FrontController
     *
     * @var Zend_Controller_Front
     */
    protected $_frontController;

    /**
     * Constructor
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = array())
    {
        $defaultAttributes = array('roles', 'human', 'humanDescription');
        $this->_attributes = array_keys(array_merge(array_flip($defaultAttributes),
            array_flip($attributes)));
    }

    /**
     * Returns the inflected name
     *
     * CamelCase actions are converted to lowercase with the "-" seperator to follow
     * the default behaviour.
     *
     * @param  string $name
     * @return string
     */
    protected function _getInflectedName($name)
    {
        $inflector = new Zend_Filter_Inflector(':string');
        $inflector->addRules(array(':string' => array('Word_CamelCaseToDash', 'StringToLower')));
        $inflectedName = $inflector->filter(array('string' => $name));
        return $inflectedName;
    }

    /**
     * Returns the resources as a multi dimensional array
     *
     * @param  boolean $translate Set to TRUE if the resources must be translated
     * @param  boolean $sort
     * @return array
     */
    public function toArray($translate = false, $sort = true)
    {
        $resources = array();
        $this->_frontController = Zend_Controller_Front::getInstance();

        // Retrieve the controller directory
        $controllerDirectories = $this->_frontController->getControllerDirectory();
        foreach ($controllerDirectories as $moduleName => $controllerDirectory) {

            // Iterate over controllers found in the controller directory
            $directoryIterator = new DirectoryIterator($controllerDirectory);
            foreach ($directoryIterator as $file) {
                // Ignore files that are a dot or start with a dot, or are a directory
                if ($file->isDot() || substr($file->getFilename(), 0, 1) == '.' || $file->isDir()) {
                    continue;
                }

                // Initialize the reflection of the controller class
                require_once $file->getPathname();
                                
                if ('default' == $moduleName) {
                    $className = $file->getBasename('.php');
                } else {
                    $className = $moduleName . '_' . $file->getBasename('.php');
                }
                                
                $controllerName = $this->_getInflectedName($file->getBasename('Controller.php'));
                $classReflection = new Zend_Reflection_Class($className);

                $resourceAttributes = $this->_getResourceAttributes($classReflection);
                if (count($resourceAttributes) > 0) {
                    $resources[$moduleName][$controllerName]['_attributes'] = $resourceAttributes;
                }

                // Iterate over the public methods of the controller class
                $methods = $classReflection->getMethods(ReflectionMethod::IS_PUBLIC);
                foreach ($methods as $method) {
                    $resourceAttributes = array();

                    // Ignore methods that don't have an "Action" suffix
                    if (substr($method->name, -6) == 'Action') {
                        $resourceAttributes = $this->_getResourceAttributes($method);
                        $actionName = $this->_getInflectedName(substr($method->name, 0, -6));
                        $resources[$moduleName][$controllerName][$actionName]
                            = $resourceAttributes;
                    }
                }
            }
        }

        $resources = $this->_excludeErrorHandlerResource($resources);

        if (true === $translate) {
            $resources = self::translateResources($resources);
        }

        if (true === $sort) {
            $resources = self::sortResources($resources);
        }

        return $resources;
    }

    /**
     * Returns the resources as an associative array
     *
     * Format: controller_action => human
     *
     * @param  string  $moduleName
     * @param  boolean $translate Set to TRUE if the resources must be translated
     * @param  boolean $sort
     * @return array
     */
    public function toFlatArray($moduleName = 'default', $translate = false, $sort = true)
    {
        $flatResources = array();
        $resources = $this->toArray($translate);

        if (!isset($resources[$moduleName])) {
            throw new Pike_Exception('Module "' . $moduleName . '" not found');
        }

        foreach ($resources[$moduleName] as $controllerName => $controller) {
            foreach ($controller as $actionName => $action) {
                if ($actionName == '_attributes') continue;

                $value = ucfirst($actionName);
                if (isset($action['human']) && $action['human'] != '') {
                    $value = $action['human'];
                }
                $flatResources[$moduleName . '_' . $controllerName . '_' . $actionName] = $value;
            }
        }

        // Translate
        if (true === $translate) {
            foreach ($flatResources as $key => $value) {
                $flatResources[$key] = Zend_Registry::get('Zend_Translate')->_($value);
            }
        }

        // Sort
        if (true === $sort) {
            asort($flatResources);
        }

        return $flatResources;
    }

    /**
     * Returns resources with the error handler resource excluded
     *
     * @param  array $resources
     * @return array
     */
    protected function _excludeErrorHandlerResource($resources)
    {
        /* @var $errorHandler Zend_Controller_Plugin_ErrorHandler */
        $errorHandler = Zend_Controller_Front::getInstance()->getPlugin('Zend_Controller_Plugin_ErrorHandler');
        if ($errorHandler) {
            // Access is always granted to the error handler, so it can be ignored as resource
            $module =  $errorHandler->getErrorHandlerModule();
            $controller =  $errorHandler->getErrorHandlerController();
            $action =  $errorHandler->getErrorHandlerAction();
            unset($resources[$module][$controller][$action]);
        }
        return $resources;
    }

    /**
     * Returns an array with resource attributes for the specified reflection object
     *
     * @param  Zend_Reflection_Class|Zend_Reflection_Method $reflection
     * @return array
     */
    protected function _getResourceAttributes($reflection)
    {
        $resourceAttributes = array();
        $docComment = $reflection->getDocComment();

        if ($docComment) {
            foreach ($this->_attributes as $attribute) {
                switch ($attribute) {
                    case 'short_description':
                    case 'long_description':
                        $methodName = 'get' . ucfirst(str_replace('_description', 'Description', $attribute));
                        $description = $reflection->getDocblock()->$methodName();
                        if ($description != '') {
                            $resourceAttributes[$attribute] = $description;
                        }
                        break;
                    case 'roles':
                        if ($reflection->getDocblock()->hasTag($attribute)) {
                            $tagValue = $reflection->getDocblock()->getTag($attribute)->getDescription();
                            if (trim($tagValue) != '') {
                                $roles = explode('|', $tagValue);
                                foreach ($roles as &$role) {
                                    $role = strtolower(trim(str_replace(' ', '', $role)));
                                }
                                $resourceAttributes[$attribute] = $roles;
                            }
                        }
                        break;
                    case 'humanDescription':
                        /**
                         * Zend_Reflection_Docblock_Tag doesn't support multiple lines, so for this
                         * tag we make an exception and do some parsing to still be able to specify
                         * a multi line humanDescription tag
                         */
                        if ($reflection->getDocblock()->hasTag($attribute)) {
                            $docblock = $reflection->getDocblock()->getContents();
                            $lines = explode("\n", $docblock);
                            $humanDescription = null;
                            foreach ($lines as $line) {
                                if (substr(trim($line), 0, 17) == '@humanDescription') {
                                    $humanDescription = substr($line, 18);
                                    continue;
                                }

                                /**
                                 * If human description is not NULL, add the follow lines until the line
                                 * starts with another tag
                                 */
                                if (null !== $humanDescription) {
                                    if (substr(trim($line), 0, 1) == '@') {
                                        break;
                                    } else {
                                        if ('' != trim($line)) {
                                            $humanDescription .= ' ' . trim($line);
                                        }
                                    }
                                }
                            }

                            $humanDescription = str_replace('.', '. ', $humanDescription);
                            $resourceAttributes[$attribute] = $humanDescription;
                        }
                        break;
                    default:
                        if ($reflection->getDocblock()->hasTag($attribute)) {
                            $tagValue = $reflection->getDocblock()->getTag($attribute)->getDescription();
                            if (trim($tagValue) != '') {
                                $resourceAttributes[$attribute] = $tagValue;
                            }
                        }
                }
            }
        }

        return $resourceAttributes;
    }

    /**
     * Translates the specified resources
     *
     * @param  array $resources Resources as multi dimensional array
     * @return array
     */
    public static function translateResources($resources)
    {
        if (Zend_Registry::isRegistered('Zend_Translate')) {
            foreach ($resources as $module => $controllers) {
                foreach ($controllers as $controller => $actions) {
                    foreach ($actions as $action => $attributes) {
                        foreach ($attributes as $key => $value) {
                            if (in_array($key, array('human', 'humanDescription'))) {
                                $resources[$module][$controller][$action][$key] =
                                    Zend_Registry::get('Zend_Translate')->_($value);
                            }
                        }
                    }
                }
            }
        }

        return $resources;
    }

    /**
     * Sorts the specified resources
     *
     * @param  array $resources Resources as multi dimensional array
     * @return array
     */
    public static function sortResources($resources)
    {
        foreach ($resources as $module => &$controllers) {
            $controllerIndex = array();

            foreach ($controllers as $controller => &$actions) {
                if (isset($controllers[$controller]['_attributes']['human'])) {
                    $controllerIndex[$controller] = $controllers[$controller]['_attributes']['human'];
                } else {
                    $controllerIndex[$controller] = $controller;
                }

                $actionIndex = array();
                foreach ($actions as $action => $attributes) {
                    if ('_attributes' == $action) {
                        $actionIndex[$action] = null;
                    } elseif (isset($attributes['human'])) {
                        $actionIndex[$action] = $attributes['human'];
                    } else {
                        $actionIndex[$action] = $action;
                    }
                }

                array_multisort($actionIndex, SORT_ASC, $actions);
            }

            array_multisort($controllerIndex, SORT_ASC, $controllers);
        }

        return $resources;
    }
}